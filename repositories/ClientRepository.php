<?php
class ClientRepository {
    private $pdo;

    public function __construct() {
        try {
            $dbPath = __DIR__ . '/../database.sqlite';
            $this->pdo = new PDO("sqlite:" . $dbPath);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS clients (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                last_name TEXT NOT NULL,
                first_name TEXT NOT NULL,
                phone TEXT NOT NULL,
                email TEXT
            );");

            $this->pdo->exec("CREATE TABLE IF NOT EXISTS courses (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                level TEXT NOT NULL,
                max_places INTEGER NOT NULL,
                min_score INTEGER NOT NULL
            );");

            $this->pdo->exec("CREATE TABLE IF NOT EXISTS test_results (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                client_id INTEGER NOT NULL,
                course_id INTEGER NOT NULL,
                score INTEGER NOT NULL,
                test_date TEXT NOT NULL,
                FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
                FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
            );");

        } catch (\PDOException $e) {
            die("Ошибка базы данных: " . $e->getMessage());
        }
    }

    public function findAllPaginated(string $search, string $sort, string $direction, int $limit, int $offset): array {
        $allowedSorts = ['id', 'last_name', 'first_name', 'phone', 'email'];
        if (!in_array($sort, $allowedSorts)) { $sort = 'id'; }
        $direction = ($direction === 'desc') ? 'DESC' : 'ASC';

        $sql = "SELECT * FROM clients WHERE last_name LIKE :search OR first_name LIKE :search OR phone LIKE :search ORDER BY {$sort} {$direction} LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAll(string $search): int {
        $sql = "SELECT COUNT(*) FROM clients WHERE last_name LIKE :search OR first_name LIKE :search OR phone LIKE :search";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':search' => "%$search%"]);
        return (int)$stmt->fetchColumn();
    }

    public function findById(int $id): ?array {
        $sql = "SELECT * FROM clients WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ? $result : null;
    }

    public function create(array $data): void {
        $sql = "INSERT INTO clients (last_name, first_name, phone, email) VALUES (:last_name, :first_name, :phone, :email)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':last_name' => $data['last_name'],
            ':first_name' => $data['first_name'],
            ':phone' => $data['phone'],
            ':email' => !empty($data['email']) ? $data['email'] : null
        ]);
    }

    public function update(array $data): void {
        $sql = "UPDATE clients SET last_name = :last_name, first_name = :first_name, phone = :phone, email = :email WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $data['id'],
            ':last_name' => $data['last_name'],
            ':first_name' => $data['first_name'],
            ':phone' => $data['phone'],
            ':email' => !empty($data['email']) ? $data['email'] : null
        ]);
    }

    public function delete(int $id): void {
        $sql = "DELETE FROM clients WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
    }
}