<?php
class CourseRepository {
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

    public function findAllPaginated(string $search, string $sort, string $direction, int $limit, int $offset): array {
        $allowedSorts = ['id', 'title', 'level', 'max_places', 'min_score'];
        if (!in_array($sort, $allowedSorts)) { $sort = 'id'; }
        $direction = ($direction === 'desc') ? 'DESC' : 'ASC';

        $sql = "SELECT * FROM courses WHERE title LIKE :search OR level LIKE :search ORDER BY {$sort} {$direction} LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAll(string $search): int {
        $sql = "SELECT COUNT(*) FROM courses WHERE title LIKE :search OR level LIKE :search";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':search' => "%$search%"]);
        return (int)$stmt->fetchColumn();
    }

    public function findById(int $id): ?array {
        $sql = "SELECT * FROM courses WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ? $result : null;
    }

    public function create(array $data): void {
        $sql = "INSERT INTO courses (title, level, max_places, min_score) VALUES (:title, :level, :max_places, :min_score)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':title' => $data['title'],
            ':level' => $data['level'],
            ':max_places' => $data['max_places'],
            ':min_score' => $data['min_score']
        ]);
    }

    public function update(array $data): void {
        $sql = "UPDATE courses SET title = :title, level = :level, max_places = :max_places, min_score = :min_score WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $data['id'],
            ':title' => $data['title'],
            ':level' => $data['level'],
            ':max_places' => $data['max_places'],
            ':min_score' => $data['min_score']
        ]);
    }

    public function delete(int $id): void {
        $sql = "DELETE FROM courses WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
    }
}