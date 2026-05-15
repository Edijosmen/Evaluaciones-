<?php
require_once __DIR__ . '/../Models/Evaluation.php';

class DashboardController {
    private $evaluationModel;

    public function __construct() {
        $this->evaluationModel = new Evaluation();
    }

    public function index() {
        $user = $_SESSION['user'];
        if ($user['role'] === 'admin') {
            $evaluations = $this->evaluationModel->getAll();
            require_once BASE_PATH . '/views/admin/dashboard.php';
        } else {
            $evaluations = $this->evaluationModel->getActiveForUser($user['id']);
            require_once BASE_PATH . '/views/user/dashboard.php';
        }
    }
}