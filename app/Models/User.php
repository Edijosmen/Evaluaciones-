<?php
require_once __DIR__ . '/../DB.php';

class User {
    private $db;

    public function __construct() {
        $this->db = DB::c();
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
       
        $stmt = $this->db->prepare("INSERT INTO users (username, password, role, cef, nombre, cargo, grupo)
        VALUES ( ?, ?, ?, ?, ?, ?, ?)");
    
        $stmt->execute([
            $data['username'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'] ?? 'user',
            $data['cef'] ?? null,
            $data['nombre'] ?? null,
            $data['cargo'] ?? null,
            $data['grupo'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];
        if (isset($data['username'])) {
            $fields[] = "username = ?";
            $values[] = $data['username'];
        }
        if (isset($data['password'])) {
            $fields[] = "password = ?";
            $values[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        if (isset($data['role'])) {
            $fields[] = "role = ?";
            $values[] = $data['role'];
        }
        if (isset($data['cef'])) {
            $fields[] = "cef = ?";
            $values[] = $data['cef'];
        }
        if (isset($data['nombre'])) {
            $fields[] = "nombre = ?";
            $values[] = $data['nombre'];
        }
        if (isset($data['cargo'])) {
            $fields[] = "cargo = ?";
            $values[] = $data['cargo'];
        }
        if (isset($data['grupo'])) {
            $fields[] = "grupo = ?";
            $values[] = $data['grupo'];
        }
        $values[] = $id;
        $stmt = $this->db->prepare("UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?");
        return $stmt->execute($values);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getAll($filters = []) {
        $sql = "SELECT id, username, cef, nombre, grupo, role, created_at FROM users";
        $conditions = [];
        $values = [];

        if (!empty($filters['cedula'])) {
            $conditions[] = "username LIKE ?";
            $values[] = '%' . $filters['cedula'] . '%';
        }

        if (!empty($filters['cef'])) {
            $conditions[] = "cef LIKE ?";
            $values[] = '%' . $filters['cef'] . '%';
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);
        return $stmt->fetchAll();
    }
}