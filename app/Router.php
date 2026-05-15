<?php
require_once __DIR__ . '/Controllers/AuthController.php';
require_once __DIR__ . '/Controllers/DashboardController.php';
require_once __DIR__ . '/Controllers/EvaluationController.php';
require_once __DIR__ . '/Controllers/UserController.php';

class Router {
    private $authController;
    private $dashboardController;
    private $evaluationController;
    private $userController;

    public function __construct() {
        $this->authController = new AuthController();
        $this->dashboardController = new DashboardController();
        $this->evaluationController = new EvaluationController();
        $this->userController = new UserController();
    }

    public function run() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        if ($basePath !== '' && $basePath !== '/') {
            $uri = preg_replace('#^' . preg_quote($basePath, '#') . '#', '', $uri);
        }
        $uri = trim($uri, '/');
        $segments = explode('/', $uri);
        $method = $_SERVER['REQUEST_METHOD'];

        // Public routes
        if ($uri === '' || $uri === 'login') {
            if ($method === 'POST') {
                $this->authController->login();
            } else {
                $this->authController->showLogin();
            }
            return;
        }

        if ($uri === 'register') {
            $this->authController->register();
            return;
        }

        if ($uri === 'logout') {
            $this->authController->logout();
            return;
        }

        // Protected routes
        $this->authController->requireAuth();

        if ($uri === 'dashboard') {
            $this->dashboardController->index();
            return;
        }

        if ($uri === 'evaluations') {
            $this->evaluationController->index();
            return;
        }

        if (preg_match('/^evaluations\/(\d+)$/', $uri, $matches)) {
            $id = $matches[1];
            if ($method === 'POST') {
                $this->evaluationController->submitResponse($id);
            } else {
                $this->evaluationController->show($id);
            }
            return;
        }

        if (preg_match('/^evaluations\/(\d+)\/edit$/', $uri, $matches)) {
            $this->authController->requireAdmin();
            $id = $matches[1];
            $this->evaluationController->edit($id);
            return;
        }

        if (preg_match('/^evaluations\/(\d+)\/delete$/', $uri, $matches)) {
            $this->authController->requireAdmin();
            $id = $matches[1];
            $this->evaluationController->delete($id);
            return;
        }

        if (preg_match('/^evaluations\/(\d+)\/add-question$/', $uri, $matches)) {
            $this->authController->requireAdmin();
            $id = $matches[1];
            $this->evaluationController->addQuestion($id);
            return;
        }

        if (preg_match('/^evaluations\/(\d+)\/assign$/', $uri, $matches)) {
            $this->authController->requireAdmin();
            $id = $matches[1];
            $this->evaluationController->assign($id);
            return;
        }

        if ($uri === 'evaluations/create') {
            $this->authController->requireAdmin();
            $this->evaluationController->create();
            return;
        }

        // Admin only routes
        $this->authController->requireAdmin();

        if ($uri === 'users') {
            $this->userController->index();
            return;
        }

        if ($uri === 'users/create') {
            $this->userController->create();
            return;
        }

        if ($uri === 'users/bulk-upload') {
            $this->userController->bulkUpload();
            return;
        }

        if (preg_match('/^users\/(\d+)\/edit$/', $uri, $matches)) {
            $id = $matches[1];
            $this->userController->edit($id);
            return;
        }

        if (preg_match('/^users\/(\d+)\/delete$/', $uri, $matches)) {
            $id = $matches[1];
            $this->userController->delete($id);
            return;
        }

        // 404
        http_response_code(404);
        echo '404 Not Found';
    }
}
