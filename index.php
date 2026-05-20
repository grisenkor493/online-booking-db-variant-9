<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function esc(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

class AppointmentRepository {
    private $pdo;

    public function __construct() {
        $dbPath = __DIR__ . '/database.sqlite';
        $this->pdo = new PDO("sqlite:" . $dbPath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->initTables();
    }

    private function initTables(): void {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS appointments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            client_id INTEGER NOT NULL,
            course_id INTEGER NOT NULL,
            appointment_datetime TEXT NOT NULL,
            status TEXT NOT NULL DEFAULT 'waiting',
            booking_code TEXT NOT NULL
        );");
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS appointment_log (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            appointment_id INTEGER NOT NULL,
            action_type TEXT NOT NULL,
            old_datetime TEXT,
            new_datetime TEXT,
            log_date TEXT NOT NULL
        );");
    }

    public function getAvailableSlots(string $date): array {
        $workingHours = ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00'];
        $sql = "SELECT strftime('%H:%M', appointment_datetime) as booked_time FROM appointments WHERE date(appointment_datetime) = :date AND status != 'cancelled'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':date' => $date]);
        $bookedTimes = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return array_values(array_diff($workingHours, $bookedTimes));
    }

    public function createAppointment(int $clientId, int $courseId, string $datetime): string {
        $this->pdo->beginTransaction();
        try {
            $sqlCheck = "SELECT COUNT(*) FROM appointments WHERE appointment_datetime = :dt AND status != 'cancelled'";
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->execute([':dt' => $datetime]);
            if ((int)$stmtCheck->fetchColumn() > 0) {
                throw new Exception("К сожалению, это время только что было занято другим клиентом.");
            }
            $bookingCode = md5($clientId . $courseId . $datetime . time());
            $sqlInsert = "INSERT INTO appointments (client_id, course_id, appointment_datetime, status, booking_code) VALUES (:client_id, :course_id, :appointment_datetime, 'waiting', :booking_code)";
            $stmtInsert = $this->pdo->prepare($sqlInsert);
            $stmtInsert->execute([':client_id' => $clientId, ':course_id' => $courseId, ':appointment_datetime' => $datetime, ':booking_code' => $bookingCode]);
            $this->pdo->commit();
            return $bookingCode;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getAppointmentsWithFilters(string $dateFilter, string $statusFilter): array {
        $sql = "SELECT a.*, (c.last_name || ' ' || c.first_name) as client_name, co.title as course_title FROM appointments a JOIN clients c ON a.client_id = c.id JOIN courses co ON a.course_id = co.id WHERE 1=1";
        $params = [];
        if (!empty($dateFilter)) { $sql .= " AND date(a.appointment_datetime) = :date"; $params[':date'] = $dateFilter; }
        if (!empty($statusFilter)) { $sql .= " AND a.status = :status"; $params[':status'] = $statusFilter; }
        $sql .= " ORDER BY a.appointment_datetime ASC";
        $stmt = $this->pdo->prepare($sql); $stmt->execute($params); return $stmt->fetchAll();
    }

    public function changeStatus(int $id, string $status): void {
        $sql = "UPDATE appointments SET status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql); $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function reschedule(int $id, string $newDatetime): void {
        $stmtOld = $this->pdo->prepare("SELECT appointment_datetime FROM appointments WHERE id = :id");
        $stmtOld->execute([':id' => $id]);
        $oldDatetime = $stmtOld->fetchColumn() ?: '';

        $this->pdo->beginTransaction();
        try {
            $sqlCheck = "SELECT COUNT(*) FROM appointments WHERE appointment_datetime = :dt AND id != :id AND status != 'cancelled'";
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->execute([':dt' => $newDatetime, ':id' => $id]);
            if ((int)$stmtCheck->fetchColumn() > 0) {
                throw new Exception("Это время уже занято другим клиентом.");
            }

            $sqlUp = "UPDATE appointments SET appointment_datetime = :dt, status = 'waiting' WHERE id = :id";
            $stmtUp = $this->pdo->prepare($sqlUp);
            $stmtUp->execute([':dt' => $newDatetime, ':id' => $id]);

            $sqlLog = "INSERT INTO appointment_log (appointment_id, action_type, old_datetime, new_datetime, log_date) 
                       VALUES (:id, 'reschedule', :old, :new, datetime('now'))";
            $stmtLog = $this->pdo->prepare($sqlLog);
            $stmtLog->execute([':id' => $id, ':old' => $oldDatetime, ':new' => $newDatetime]);

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getReportByDays(): array {
        $sql = "SELECT date(appointment_datetime) as date_row, COUNT(*) as total_bookings 
                FROM appointments 
                WHERE status != 'cancelled' 
                GROUP BY date_row 
                ORDER BY date_row DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function getReportByCourses(): array {
        $sql = "SELECT co.title as course_title, COUNT(a.id) as total_bookings 
                FROM courses co
                LEFT JOIN appointments a ON a.course_id = co.id AND a.status != 'cancelled'
                GROUP BY co.id 
                ORDER BY total_bookings DESC";
        return $this->pdo->query($sql)->fetchAll();
    }
}

require 'repositories/ClientRepository.php';
require 'controllers/ClientController.php';
require 'repositories/CourseRepository.php';
require 'controllers/CourseController.php';
require 'repositories/TestResultRepository.php';
require 'controllers/TestResultController.php';
require 'controllers/AppointmentController.php';

$entity = $_GET['entity'] ?? 'client';
$action = $_GET['action'] ?? 'list';

switch ($entity) {
    case 'client':
        $controller = new ClientController();
        break;
    case 'course':
        $controller = new CourseController();
        break;
    case 'test':
        $controller = new TestResultController();
        break;
    case 'appointment':
        $controller = new AppointmentController();
        break;
    default:
        $controller = new ClientController();
        break;
}

$controller->handle($action);