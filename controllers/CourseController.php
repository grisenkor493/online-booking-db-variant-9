<?php
class CourseController {
    private $repository;

    public function __construct() {
        $this->repository = new CourseRepository();
    }

    public function handle(string $action): void {
        switch ($action) {
            case 'list':
                $this->listAction();
                break;
            case 'create':
                $this->createAction();
                break;
            case 'edit':
                $this->editAction();
                break;
            case 'delete':
                $this->deleteAction();
                break;
            default:
                $this->listAction();
                break;
        }
    }

    private function listAction(): void {
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'id';
        $direction = $_GET['direction'] ?? 'asc';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $allowedSorts = ['id', 'title', 'level', 'max_places', 'min_score'];
        if (!in_array($sort, $allowedSorts)) { $sort = 'id'; }
        $direction = ($direction === 'desc') ? 'desc' : 'asc';

        $courses = $this->repository->findAllPaginated($search, $sort, $direction, $limit, $offset);
        $totalItems = $this->repository->countAll($search);
        $totalPages = ceil($totalItems / $limit);

        require 'views/courses/index.view.php';
    }

    private function createAction(): void {
        $errors = [];
        $data = ['title' => '', 'level' => '', 'max_places' => '', 'min_score' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
                die('CSRF token validation failed.');
            }

            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'level' => trim($_POST['level'] ?? ''),
                'max_places' => intval($_POST['max_places'] ?? 0),
                'min_score' => intval($_POST['min_score'] ?? 0)
            ];

            $errors = $this->validate($data);

            if (empty($errors)) {
                $this->repository->create($data);
                $_SESSION['flash_success'] = 'Курс успешно добавлен.';
                header('Location: index.php?entity=course&action=list');
                exit;
            }
        }

        require 'views/courses/form.view.php';
    }

    private function editAction(): void {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: index.php?entity=course&action=list');
            exit;
        }

        $course = $this->repository->findById($id);
        if (!$course) { die('Курс не найден.'); }

        $errors = [];
        $data = $course;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
                die('CSRF token validation failed.');
            }

            $data = [
                'id' => $id,
                'title' => trim($_POST['title'] ?? ''),
                'level' => trim($_POST['level'] ?? ''),
                'max_places' => intval($_POST['max_places'] ?? 0),
                'min_score' => intval($_POST['min_score'] ?? 0)
            ];

            $errors = $this->validate($data);

            if (empty($errors)) {
                $this->repository->update($data);
                $_SESSION['flash_success'] = 'Данные курса обновлены.';
                header('Location: index.php?entity=course&action=list');
                exit;
            }
        }

        require 'views/courses/form.view.php';
    }

    private function deleteAction(): void {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: index.php?entity=course&action=list');
            exit;
        }

        $course = $this->repository->findById($id);
        if (!$course) { die('Курс не найден.'); }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
                die('CSRF token validation failed.');
            }

            $this->repository->delete($id);
            $_SESSION['flash_success'] = 'Курс успешно удален.';
            header('Location: index.php?entity=course&action=list');
            exit;
        }

        require 'views/courses/delete.view.php';
    }

    private function validate(array $data): array {
        $errors = [];
        if (empty($data['title'])) { $errors['title'] = 'Название курса обязательно.'; }
        if (empty($data['level'])) { $errors['level'] = 'Уровень обязателен.'; }
        if ($data['max_places'] <= 0) { $errors['max_places'] = 'Количество мест должно быть больше 0.'; }
        if ($data['min_score'] < 0) { $errors['min_score'] = 'Минимальный балл не может быть отрицательным.'; }
        return $errors;
    }
}