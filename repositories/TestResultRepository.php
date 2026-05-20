<?php
class TestResultRepository {
    private $pdo;

    public function __construct() {
        try {
            $dbPath = __DIR__ . '/../database.sqlite';
            $this->pdo = new PDO("sqlite:" . $dbPath);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            die("Ошибка базы данных: " . $e->getMessage());
        }
    }

    public function findAllWithNames(): array {
        $sql = "SELECT tr.*, 
                       (c.last_name || ' ' || c.first_name) AS client_name, 
                       co.title AS course_title,
                       co.min_score
                FROM test_results tr
                JOIN clients c ON tr.client_id = c.id
                JOIN courses co ON tr.course_id = co.id
                ORDER BY tr.id DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function create(array $data): void {
        $sql = "INSERT INTO test_results (client_id, course_id, score, test_date) 
                VALUES (:client_id, :course_id, :score, :test_date)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':client_id' => $data['client_id'],
            ':course_id' => $data['course_id'],
            ':score' => $data['score'],
            ':test_date' => $data['test_date']
        ]);
    }

    public function delete(int $id): void {
        $sql = "DELETE FROM test_results WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    public function getQueueReport(): array {
        $sql = "SELECT 
                    co.id,
                    co.title,
                    co.level,
                    co.max_places,
                    COUNT(tr.id) AS total_passed,
                    (COUNT(tr.id) - co.max_places) AS queue_length
                FROM courses co
                JOIN test_results tr ON co.id = tr.course_id
                WHERE tr.score >= co.min_score
                GROUP BY co.id
                HAVING total_passed > co.max_places";
        return $this->pdo->query($sql)->fetchAll();
    }
}