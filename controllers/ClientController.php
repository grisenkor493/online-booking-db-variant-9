<?php
class ClientController {
    private $repository;

    public function __construct() {
        $this->repository = new ClientRepository();
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

        $allowedSorts = ['id', 'last_name', 'first_name', 'phone', 'email'];
        if (!in_array($sort, $allowedSorts)) { $sort = 'id'; }
        $direction = ($direction === 'desc') ? 'desc' : 'asc';

        $clients = $this->repository->findAllPaginated($search, $sort, $direction, $limit, $offset);
        $totalItems = $this->repository->countAll($search);
        $totalPages = ceil($totalItems / $limit);

        require 'views/clients/index.view.php';
    }

    private function createAction(): void {
        $errors = [];
        $data = ['last_name' => '', 'first_name' => '', 'phone' => '', 'email' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
                die('CSRF token validation failed.');
            }

            $data = [
                'last_name' => trim($_POST['last_name'] ?? ''),
                'first_name' => trim($_POST['first_name'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'email' => trim($_POST['email'] ?? '')
            ];

            $errors = $this->validate($data);

            if (empty($errors)) {
                $this->repository->create($data);
                $_SESSION['flash_success'] = 'Клиент успешно зарегистрирован.';
                header('Location: index.php?entity=client&action=list');
                exit;
            }
        }

        require 'views/clients/form.view.php';
    }

    private function editAction(): void {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: index.php?entity=client&action=list');
            exit;
        }

        $client = $this->repository->findById($id);
        if (!$client) { die('Клиент не найден.'); }

        $errors = [];
        $data = $client;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
                die('CSRF token validation failed.');
            }

            $data = [
                'id' => $id,
                'last_name' => trim($_POST['last_name'] ?? ''),
                'first_name' => trim($_POST['first_name'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'email' => trim($_POST['email'] ?? '')
            ];

            $errors = $this->validate($data);

            if (empty($errors)) {
                $this->repository->update($data);
                $_SESSION['flash_success'] = 'Данные клиента обновлены.';
                header('Location: index.php?entity=client&action=list');
                exit;
            }
        }

        require 'views/clients/form.view.php';
    }

    private function deleteAction(): void {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: index.php?entity=client&action=list');
            exit;
        }

        $client = $this->repository->findById($id);
        if (!$client) { die('Клиент не найден.'); }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
                die('CSRF token validation failed.');
            }

            $this->repository->delete($id);
            $_SESSION['flash_success'] = 'Клиент успешно удален.';
            header('Location: index.php?entity=client&action=list');
            exit;
        }

        require 'views/clients/delete.view.php';
    }

    private function validate(array $data): array {
        $errors = [];
        if (empty($data['last_name'])) { $errors['last_name'] = 'Фамилия обязательна.'; }
        if (empty($data['first_name'])) { $errors['first_name'] = 'Имя обязательно.'; }
        if (empty($data['phone'])) { $errors['phone'] = 'Телефон обязателен.'; }
        return $errors;
    }
}