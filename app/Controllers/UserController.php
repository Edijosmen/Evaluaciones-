<?php
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/BaseController.php';

class UserController extends BaseController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function index() {
        $filters = [
            'cedula' => trim($_GET['cedula'] ?? ''),
            'cef' => trim($_GET['cef'] ?? ''),
        ];

        $users = $this->userModel->getAll($filters);
        require_once BASE_PATH . '/views/admin/users.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once BASE_PATH . '/views/admin/user_create.php';
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $cef = trim($_POST['cef'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $cargo = trim($_POST['cargo'] ?? '');
        $grupo = trim($_POST['grupo'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'user';

        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Username and password are required.';
            $this->redirect('/users/create');
        }

        $this->userModel->create([
            'username' => $username,
            'cef' => $cef,
            'nombre' => $nombre,
            'cargo' => $cargo,
            'grupo' => $grupo,
            'password' => $password,
            'role' => $role
        ]);

        $_SESSION['success'] = 'User created successfully.';
        $this->redirect('/users');
    }

    public function edit($id) {
        $user = $this->userModel->findById($id);
        if (!$user) {
            $_SESSION['error'] = 'User not found.';
            $this->redirect('/users');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once BASE_PATH . '/views/admin/user_edit.php';
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $cef = trim($_POST['cef'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $cargo = trim($_POST['cargo'] ?? '');
        $grupo = trim($_POST['grupo'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'user';

        if (empty($username)) {
            $_SESSION['error'] = 'Username is required.';
            $this->redirect('/users/' . $id . '/edit');
        }

        $data = [
            'username' => $username,
            'role' => $role
        ];
        if (!empty($cef)) {
            $data['cef'] = $cef;
        }
        if (!empty($nombre)) {
            $data['nombre'] = $nombre;
        }
        if (!empty($cargo)) {
            $data['cargo'] = $cargo;
        }
        if (!empty($grupo)) {
            $data['grupo'] = $grupo;
        }
        if (!empty($password)) {
            $data['password'] = $password;
        }

        $this->userModel->update($id, $data);
        $_SESSION['success'] = 'User updated successfully.';
        $this->redirect('/users');
    }

    public function delete($id) {
        if ($id == $_SESSION['user']['id']) {
            $_SESSION['error'] = 'Cannot delete yourself.';
            $this->redirect('/users');
        }

        $this->userModel->delete($id);
        $_SESSION['success'] = 'User deleted successfully.';
        $this->redirect('/users');
    }

    public function bulkUpload() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/dashboard');
        }

        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Please select a valid CSV file.';
            $this->redirect('/dashboard');
        }

        $fileTmpPath = $_FILES['csv_file']['tmp_name'];
        $fileName = $_FILES['csv_file']['name'];
        $fileSize = $_FILES['csv_file']['size'];
        $fileType = $_FILES['csv_file']['type'];

        // Validate file type
        if ($fileType !== 'text/csv' && !preg_match('/\.csv$/i', $fileName)) {
            $_SESSION['error'] = 'Only CSV files are allowed.';
            $this->redirect('/dashboard');
        }

        // Validate file size (max 5MB)
        if ($fileSize > 5 * 1024 * 1024) {
            $_SESSION['error'] = 'File size must be less than 5MB.';
            $this->redirect('/dashboard');
        }

        try {
            $handle = fopen($fileTmpPath, 'r');
            if ($handle === false) {
                throw new Exception('Unable to open CSV file.');
            }

            $header = fgetcsv($handle, 1000, ',');
            if ($header === false || empty($header)) {
                throw new Exception('CSV file must contain a header row.');
            }

            $header = array_map('strtolower', array_map('trim', $header));
/* 
            $requiredColumns = ['username', 'email', 'password'];
     
            // Check required columns
            foreach ($requiredColumns as $col) {
                if (!in_array($col, $header)) {
                    $_SESSION['error'] = "Required column '$col' not found in CSV file.";
                    $this->redirect('/dashboard');
                }
            }
*/
            $defaultRole = $_POST['default_role'] ?? 'user';
            $createdCount = 0;
            $errors = [];
            $rowNumber = 1; // Header is row 1

            // Process each row
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $rowNumber++;
                if (empty(array_filter($row))) continue; // Skip empty rows

                $userData = [];
                foreach ($header as $index => $colName) {
                    $userData[$colName] = trim($row[$index] ?? '');
                }
/*
                // Validate required fields
                if (empty($userData['username']) || empty($userData['email']) || empty($userData['password'])) {
                    $errors[] = "Row $rowNumber: Missing required fields (username, email, password)";
                    continue;
                }

                // Validate email format
                if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Row $rowNumber: Invalid email format";
                    continue;
                }

                // Check if email already exists
                if ($this->userModel->findByEmail($userData['cedula'])) {
                    $errors[] = "Row $rowNumber: usuario already exists";
                    continue;
                }
*/
                // Set role
                $role = $userData['role'] ?? $defaultRole;
                if (!in_array($role, ['user', 'admin'])) {
                    $role = $defaultRole;
                }

                // Create user
                $result = $this->userModel->create([
                    'cef' => $userData['cef'],
                    'username' => $userData['cedula'],
                    'password' => $userData['password'],
                    'nombre' => $userData['nombre'],
                    'cargo' => $userData['cargo'],
                    'grupo' => $userData['grupo'],
                    'role' => $role
                ]);

                if ($result) {
                    $createdCount++;
                } else {
                    $errors[] = "Row $rowNumber: Failed to create user";
                }
            }

            fclose($handle);

            if ($createdCount > 0) {
                $_SESSION['success'] = "Successfully created $createdCount users.";
            }

            if (!empty($errors)) {
                $_SESSION['error'] = 'Some users could not be created:<br>' . implode('<br>', $errors);
            }

        } catch (Exception $e) {
            $_SESSION['error'] = 'Error processing CSV file: ' . $e->getMessage();
        }

        $this->redirect('/dashboard');
    }
}