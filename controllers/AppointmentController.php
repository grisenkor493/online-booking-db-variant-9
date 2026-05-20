<?php
class AppointmentController {
    private $repository;
    private $clientRepo;

    public function __construct() {
        $this->repository = new AppointmentRepository();
        $this->clientRepo = new ClientRepository();
    }

    public function handle(string $action): void {
        switch ($action) {
            case 'list': $this->listAction(); break;
            case 'create': $this->createAction(); break;
            case 'status': $this->statusAction(); break;
            case 'reschedule': $this->rescheduleAction(); break;
            case 'report': $this->reportAction(); break;
            default: $this->listAction(); break;
        }
    }

    private function listAction(): void {
        $dateFilter = $_GET['date_filter'] ?? '';
        $statusFilter = $_GET['status_filter'] ?? '';
        $appointments = $this->repository->getAppointmentsWithFilters($dateFilter, $statusFilter);
        require 'views/appointments/index.view.php';
    }

    private function createAction(): void {
        $errors = [];
        $clients = $this->clientRepo->findAllPaginated('', 'last_name', 'asc', 100, 0);
        try {
            $dbPath = __DIR__ . '/../database.sqlite';
            $pdo = new PDO("sqlite:" . $dbPath);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $courses = $pdo->query("SELECT * FROM courses ORDER BY title ASC")->fetchAll();
        } catch (\PDOException $e) { $courses = []; }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) { die('CSRF failure.'); }
            $clientId = intval($_POST['client_id'] ?? 0);
            $courseId = intval($_POST['course_id'] ?? 0);
            $date = $_POST['appointment_date'] ?? '';
            $time = $_POST['appointment_time'] ?? '';

            if ($clientId <= 0) $errors['client_id'] = 'Выберите клиента.';
            if ($courseId <= 0) $errors['course_id'] = 'Выберите курс.';
            if (empty($date)) $errors['appointment_date'] = 'Выберите дату.';
            if (empty($time)) $errors['appointment_time'] = 'Выберите время.';

            if (empty($errors)) {
                try {
                    $fullDatetime = $date . ' ' . $time;
                    $code = $this->repository->createAppointment($clientId, $courseId, $fullDatetime);
                    $_SESSION['flash_success'] = "Запись создана! Код: " . strtoupper(substr($code, 0, 8));
                    header('Location: index.php?entity=appointment&action=list');
                    exit;
                } catch (Exception $e) { $errors['global'] = $e->getMessage(); }
            }
        }
        require 'views/appointments/form.view.php';
    }

    private function rescheduleAction(): void {
        $id = intval($_GET['id'] ?? 0);
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) { die('CSRF failure.'); }
            $date = $_POST['appointment_date'] ?? '';
            $time = $_POST['appointment_time'] ?? '';

            if (empty($date) || empty($time)) {
                $errors['global'] = 'Укажите новые дату и время.';
            } else {
                try {
                    $this->repository->reschedule($id, $date . ' ' . $time);
                    $_SESSION['flash_success'] = "Запись успешно перенесена.";
                    header('Location: index.php?entity=appointment&action=list');
                    exit;
                } catch (Exception $e) { $errors['global'] = $e->getMessage(); }
            }
        }
        require 'views/appointments/reschedule.view.php';
    }

    private function reportAction(): void {
        $byDays = $this->repository->getReportByDays();
        $byCourses = $this->repository->getReportByCourses();

        if (isset($_GET['export']) && $_GET['export'] === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=report.csv');
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Тип отчета', 'Параметр', 'Количество записей']);
            foreach ($byDays as $d) { fputcsv($output, ['По дням', $d['date_row'], $d['total_bookings']]); }
            foreach ($byCourses as $c) { fputcsv($output, ['По курсам', $c['course_title'], $c['total_bookings']]); }
            fclose($output);
            exit;
        }
        require 'views/appointments/report.view.php';
    }

    private function statusAction(): void {
        $id = intval($_GET['id'] ?? 0);
        $status = $_GET['status'] ?? '';
        if ($id > 0 && in_array($status, ['confirmed', 'cancelled', 'completed'])) {
            $this->repository->changeStatus($id, $status);
            $_SESSION['flash_success'] = "Статус записи изменен.";
        }
        header('Location: index.php?entity=appointment&action=list');
        exit;
    }
}