<?php
class TestResultController {
    private $repository;
    private $clientRepo;
    private $courseRepo;

    public function __construct() {
        $this->repository = new TestResultRepository();
        $this->clientRepo = new ClientRepository();
        $this->courseRepo = new CourseRepository();
    }

    public function handle(string $action): void {
        switch ($action) {
            case 'list':
                $this->listAction();
                break;
            case 'create':
                $this->createAction();
                break;
            case 'delete':
                $this->deleteAction();
                break;
            case 'queue':
                $this->queueAction();
                break;
            default:
                $this->listAction();
                break;
        }
    }

    private function listAction(): void {
        $results = $this->repository->findAllWithNames();
        require 'views/tests/index.view.php';
    }

    private function createAction(): void {
        $errors = [];
        $data = ['client_id' => '', 'course_id' => '', 'score' => '', 'test_date' => date('Y-m-d')];

        $clients = $this->clientRepo->findAllPaginated('', 'last_name', 'asc', 100, 0);
        
        try {
            $dbPath = __DIR__ . '/../database.sqlite';
            $pdo = new PDO("sqlite:" . $dbPath);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $courses = $pdo->query("SELECT * FROM courses ORDER BY title ASC")->fetchAll();
        } catch (\PDOException $e) {
            $courses = [];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
                die('CSRF token validation failed.');
            }

            $data = [
                'client_id' => intval($_POST['client_id'] ?? 0),
                'course_id' => intval($_POST['course_id'] ?? 0),
                'score' => intval($_POST['score'] ?? 0),
                'test_date' => $_POST['test_date'] ?? ''
            ];

            if ($data['client_id'] <= 0) { $errors['client_id'] = 'Выберите клиента.'; }
            if ($data['course_id'] <= 0) { $errors['course_id'] = 'Выберите курс.'; }
            if ($data['score'] < 0) { $errors['score'] = 'Балл не может быть отрицательным.'; }
            if (empty($data['test_date'])) { $errors['test_date'] = 'Укажите дату.'; }

            if (empty($errors)) {
                $this->repository->create($data);
                $_SESSION['flash_success'] = 'Результат теста успешно добавлен.';
                header('Location: index.php?entity=test&action=list');
                exit;
            }
        }

        require 'views/tests/form.view.php';
    }

    private function deleteAction(): void {
        $id = (int)($_GET['id'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
                die('CSRF token validation failed.');
            }
            $this->repository->delete($id);
            $_SESSION['flash_success'] = 'Запись удалена.';
            header('Location: index.php?entity=test&action=list');
            exit;
        }
        require 'views/tests/delete.view.php';
    }

    private function queueAction(): void {
        $report = $this->repository->getQueueReport();
        require 'views/tests/queue.view.php';
    }
}