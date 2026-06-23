<?php
require_once __DIR__ . '/../Models/Evaluation.php';
require_once __DIR__ . '/../Models/Response.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/BaseController.php';

class EvaluationController extends BaseController {
    private $evaluationModel;
    private $responseModel;
    private $userModel;

    public function __construct() {
        $this->evaluationModel = new Evaluation();
        $this->responseModel = new Response();
        $this->userModel = new User();
    }

    public function index() {
        $user = $_SESSION['user'];
        if ($user['role'] === 'admin') {
            $evaluations = $this->evaluationModel->getAll();
            require_once BASE_PATH . '/views/admin/evaluations.php';
        } else {
            $evaluations = $this->evaluationModel->getActiveForUser($user['id']);
            require_once BASE_PATH . '/views/user/evaluations.php';
        }
    }

    public function show($id) {
        $evaluation = $this->evaluationModel->findById($id);
        if (!$evaluation) {
            $_SESSION['error'] = 'Evaluation not found.';
            header('Location: /evaluations');
            exit;
        }

        $questions = $this->evaluationModel->getQuestions($id);
        $user = $_SESSION['user'];

        if ($user['role'] === 'admin') {
            $assignedUsers = $this->evaluationModel->getAssignedUsersWithStatus($id);
            $totalAssigned = count($assignedUsers);
            $completedCount = count(array_filter($assignedUsers, fn($u) => $u['completed']));
            $pendingUsers = array_values(array_filter($assignedUsers, fn($u) => !$u['completed']));
            require_once BASE_PATH . '/views/admin/evaluation_detail.php';
        } else {
            // Check if assigned
            $assigned = $this->evaluationModel->getActiveForUser($user['id']);
            $isAssigned = array_values(array_filter($assigned, fn($e) => $e['id'] == $id));
            if (empty($isAssigned)) {
                $_SESSION['error'] = 'You are not assigned to this evaluation.';
                $this->redirect('/evaluations');
            }
            $assignmentId = $isAssigned[0]['assignment_id'];
            if ($this->responseModel->hasAnyResponse($assignmentId)) {
                $_SESSION['error'] = 'You have already completed this evaluation.';
                $this->redirect('/evaluations');
            }
            require_once BASE_PATH . '/views/user/evaluation_form.php';
        }
    }

    public function downloadResponses($id) {
        $evaluation = $this->evaluationModel->findById($id);
        if (!$evaluation) {
            $_SESSION['error'] = 'Evaluation not found.';
            $this->redirect('/evaluations');
        }

        $responses = $this->responseModel->getForEvaluation($id);
        $filename = 'evaluation_' . $id . '_responses_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        // Write UTF-8 BOM so Excel and other programs correctly interpret accented characters
        fwrite($output, "\xEF\xBB\xBF");
        fputcsv($output, ['Evaluation ID', 'Title', 'User ID', 'Username', 'Nombre', 'Question ID', 'Question', 'Question Type', 'Answer', 'Responded At']);

        foreach ($responses as $row) {
            fputcsv($output, [
                $id,
                $evaluation['title'],
                $row['user_id'] ?? '',
                $row['username'] ?? '',
                $row['full_name'] ?? '',
                $row['question_id'] ?? '',
                $row['question_text'] ?? '',
                $row['type'] ?? '',
                $row['answer'] ?? '',
                $row['responded_at'] ?? ''
            ]);
        }

        fclose($output);
        exit;
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once BASE_PATH . '/views/admin/evaluation_create.php';
            return;
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $startDate = $_POST['start_date'] ?? '';
        $endDate = $_POST['end_date'] ?? '';
        $status = $_POST['status'] ?? 'draft';

        if (empty($title) || empty($startDate) || empty($endDate)) {
            $_SESSION['error'] = 'Title, start date, and end date are required.';
            $this->redirect('/evaluations/create');
        }

        if (strtotime($startDate) > strtotime($endDate)) {
            $_SESSION['error'] = 'Start date must be before end date.';
            $this->redirect('/evaluations/create');
        }

        $id = $this->evaluationModel->create([
            'title' => $title,
            'description' => $description,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $status,
            'created_by' => $_SESSION['user']['id']
        ]);

        $_SESSION['success'] = 'Evaluation created successfully.';
        $this->redirect('/evaluations/' . $id);
    }

    public function edit($id) {
        $evaluation = $this->evaluationModel->findById($id);
        if (!$evaluation) {
            $_SESSION['error'] = 'Evaluation not found.';
            $this->redirect('/evaluations');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once BASE_PATH . '/views/admin/evaluation_edit.php';
            return;
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $startDate = $_POST['start_date'] ?? '';
        $endDate = $_POST['end_date'] ?? '';
        $status = $_POST['status'] ?? 'draft';

        if (empty($title) || empty($startDate) || empty($endDate)) {
            $_SESSION['error'] = 'Title, start date, and end date are required.';
            $this->redirect('/evaluations/' . $id . '/edit');
        }

        $this->evaluationModel->update($id, [
            'title' => $title,
            'description' => $description,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $status
        ]);

        $_SESSION['success'] = 'Evaluation updated successfully.';
        $this->redirect('/evaluations/' . $id);
    }

    public function delete($id) {
        $evaluation = $this->evaluationModel->findById($id);
        if (!$evaluation) {
            $_SESSION['error'] = 'Evaluation not found.';
            $this->redirect('/evaluations');
        }

        $this->evaluationModel->delete($id);
        $_SESSION['success'] = 'Evaluation deleted successfully.';
        $this->redirect('/evaluations');
    }

    public function addQuestion($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/evaluations/' . $id);
        }

        $questionText = trim($_POST['question_text'] ?? '');
        $type = $_POST['type'] ?? '';
        $options = [];
        if ($type === 'multiple_choice') {
            $optionText = trim($_POST['options'] ?? '');
            $options = array_filter(array_map('trim', explode("\n", $optionText)));
        }

        if (empty($questionText) || empty($type)) {
            $_SESSION['error'] = 'Question text and type are required.';
            $this->redirect('/evaluations/' . $id);
        }

        if ($type === 'multiple_choice' && empty($options)) {
            $_SESSION['error'] = 'Multiple choice questions require at least one option.';
            $this->redirect('/evaluations/' . $id);
        }

        $note = trim($_POST['note'] ?? '');

        $this->evaluationModel->addQuestion($id, [
            'question_text' => $questionText,
            'type' => $type,
            'options' => $options,
            'note' => $note ?: null
        ]);

        $_SESSION['success'] = 'Question added successfully.';
        $this->redirect('/evaluations/' . $id);
    }

    public function assign($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $evaluation = $this->evaluationModel->findById($id);
            $users = $this->userModel->getAll([]); // Sin filtros para mostrar todos los usuarios
            $assignedUsers = $this->evaluationModel->getAssignedUsers($id);
            require_once BASE_PATH . '/views/admin/evaluation_assign.php';
            return;
        }

        $userIds = $_POST['user_ids'] ?? [];
        if (empty($userIds)) {
            $_SESSION['error'] = 'Select at least one user.';
            $this->redirect('/evaluations/' . $id . '/assign');
        }

        $this->evaluationModel->assignToUsers($id, $userIds);
        $_SESSION['success'] = 'Evaluation assigned successfully.';
        $this->redirect('/evaluations/' . $id);
    }

    public function submitResponse($id) {
        $evaluation = $this->evaluationModel->findById($id);
        if (!$evaluation) {
            $_SESSION['error'] = 'Evaluation not found.';
            $this->redirect('/evaluations');
        }

        $user = $_SESSION['user'];
        $assigned = $this->evaluationModel->getActiveForUser($user['id']);
        $assignment = array_values(array_filter($assigned, fn($e) => $e['id'] == $id));
        if (empty($assignment)) {
            $_SESSION['error'] = 'You are not assigned to this evaluation.';
            $this->redirect('/evaluations');
        }

        $assignmentId = $assignment[0]['assignment_id'];
        $questions = $this->evaluationModel->getQuestions($id);

        $savedAny = false;
        foreach ($questions as $question) {
            $answer = trim($_POST['question_' . $question['id']] ?? '');
            if ($answer === '') {
                continue; // Skip if not answered
            }
            if ($this->responseModel->save($assignmentId, $question['id'], $answer)) {
                $savedAny = true;
            }
        }

        if (!$savedAny) {
            $_SESSION['error'] = 'Please answer at least one question.';
            $this->redirect('/evaluations/' . $id);
        }

        $_SESSION['success'] = 'Response submitted successfully.';
        $this->redirect('/evaluations');
    }
}