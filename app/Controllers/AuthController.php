<?php
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/BaseController.php';

class AuthController extends BaseController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function showLogin() {
        require_once BASE_PATH . '/views/login.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email and password are required.';
            $this->redirect('/');
        }

        $user = $this->userModel->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = $user;
            $this->redirect('/dashboard');
        } else {
            $_SESSION['error'] = 'Invalid email or password.' . $user['password'];
            $this->redirect('/');
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once BASE_PATH . '/views/register.php';
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($username) || empty($email) || empty($password)) {
            $_SESSION['error'] = 'All fields are required.';
            $this->redirect('/register');
        }

        if ($password !== $confirmPassword) {
            $_SESSION['error'] = 'Passwords do not match.';
            $this->redirect('/register');
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Password must be at least 6 characters.';
            $this->redirect('/register');
        }

        if ($this->userModel->findByEmail($email)) {
            $_SESSION['error'] = 'Email already exists.';
            $this->redirect('/register');
        }

        $this->userModel->create([
            'username' => $username,
            'email' => $email,
            'password' => $password
        ]);

        $_SESSION['success'] = 'Registration successful. Please login.';
        $this->redirect('/');
    }

    public function logout() {
        session_destroy();
        $this->redirect('/');
    }

    public function requireAuth() {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/');
        }
    }

    public function requireAdmin() {
        $this->requireAuth();
        if ($_SESSION['user']['role'] !== 'admin') {
            $_SESSION['error'] = 'Access denied.';
            header('Location: /dashboard');
            exit;
        }
    }
}