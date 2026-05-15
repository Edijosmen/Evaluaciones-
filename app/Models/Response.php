<?php
require_once __DIR__ . '/../DB.php';

class Response {
    private $db;

    public function __construct() {
        $this->db = DB::c();
    }

    public function hasResponded($assignmentId, $questionId) {
        $stmt = $this->db->prepare("SELECT id FROM responses WHERE assignment_id = ? AND question_id = ?");
        $stmt->execute([$assignmentId, $questionId]);
        return $stmt->fetch() !== false;
    }

    public function hasAnyResponse($assignmentId) {
        $stmt = $this->db->prepare("SELECT id FROM responses WHERE assignment_id = ? LIMIT 1");
        $stmt->execute([$assignmentId]);
        return $stmt->fetch() !== false;
    }

    public function save($assignmentId, $questionId, $answer) {
        if ($this->hasResponded($assignmentId, $questionId)) {
            return false; // Already responded
        }
        $stmt = $this->db->prepare("INSERT INTO responses (assignment_id, question_id, answer) VALUES (?, ?, ?)");
        return $stmt->execute([$assignmentId, $questionId, $answer]);
    }

    public function getForEvaluation($evaluationId) {
        $stmt = $this->db->prepare("
            SELECT r.*, q.question_text, q.type, u.username
            FROM responses r
            JOIN questions q ON r.question_id = q.id
            JOIN assignments a ON r.assignment_id = a.id
            JOIN users u ON a.user_id = u.id
            WHERE q.evaluation_id = ?
            ORDER BY r.responded_at DESC
        ");
        $stmt->execute([$evaluationId]);
        return $stmt->fetchAll();
    }
}