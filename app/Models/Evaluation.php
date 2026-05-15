<?php
require_once __DIR__ . '/../DB.php';

class Evaluation {
    private $db;

    public function __construct() {
        $this->db = DB::c();
    }

    private function syncStatus() {
        $stmt = $this->db->prepare(
            "UPDATE evaluations SET status = 'closed' WHERE status != 'closed' AND end_date < CURDATE()"
        );
        $stmt->execute();
    }

    public function getAll() {
        $this->syncStatus();
        $stmt = $this->db->query("SELECT * FROM evaluations ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $this->syncStatus();
        $stmt = $this->db->prepare("SELECT * FROM evaluations WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO evaluations (title, description, start_date, end_date, status, created_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data['title'], $data['description'], $data['start_date'], $data['end_date'], $data['status'] ?? 'draft', $data['created_by']]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];
        if (isset($data['title'])) {
            $fields[] = "title = ?";
            $values[] = $data['title'];
        }
        if (isset($data['description'])) {
            $fields[] = "description = ?";
            $values[] = $data['description'];
        }
        if (isset($data['start_date'])) {
            $fields[] = "start_date = ?";
            $values[] = $data['start_date'];
        }
        if (isset($data['end_date'])) {
            $fields[] = "end_date = ?";
            $values[] = $data['end_date'];
        }
        if (isset($data['status'])) {
            $fields[] = "status = ?";
            $values[] = $data['status'];
        }
        $values[] = $id;
        $stmt = $this->db->prepare("UPDATE evaluations SET " . implode(', ', $fields) . " WHERE id = ?");
        return $stmt->execute($values);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM evaluations WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getQuestions($evaluationId) {
        $stmt = $this->db->prepare("SELECT * FROM questions WHERE evaluation_id = ? ORDER BY id");
        $stmt->execute([$evaluationId]);
        return $stmt->fetchAll();
    }

    public function addQuestion($evaluationId, $data) {
        $stmt = $this->db->prepare("INSERT INTO questions (evaluation_id, question_text, type, options) VALUES (?, ?, ?, ?)");
        $options = isset($data['options']) ? json_encode($data['options']) : null;
        $stmt->execute([$evaluationId, $data['question_text'], $data['type'], $options]);
        return $this->db->lastInsertId();
    }

    public function getActiveForUser($userId) {
        $this->syncStatus();
        $stmt = $this->db->prepare("
            SELECT e.*, a.id as assignment_id
            FROM evaluations e
            JOIN assignments a ON e.id = a.evaluation_id
            WHERE a.user_id = ? AND e.status = 'published' AND CURDATE() BETWEEN e.start_date AND e.end_date
            ORDER BY e.end_date
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function assignToUsers($evaluationId, $userIds) {
        $stmt = $this->db->prepare("INSERT IGNORE INTO assignments (evaluation_id, user_id) VALUES (?, ?)");
        foreach ($userIds as $userId) {
            $stmt->execute([$evaluationId, $userId]);
        }
    }

    public function getAssignedUsers($evaluationId) {
        $stmt = $this->db->prepare("SELECT u.id, u.username, u.email FROM users u JOIN assignments a ON u.id = a.user_id WHERE a.evaluation_id = ?");
        $stmt->execute([$evaluationId]);
        return $stmt->fetchAll();
    }
}