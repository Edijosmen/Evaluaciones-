CREATE DATABASE IF NOT EXISTS evaluation_app;
USE evaluation_app;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Evaluations table
CREATE TABLE evaluations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('draft', 'published', 'closed') DEFAULT 'draft',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Questions table
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evaluation_id INT NOT NULL,
    question_text TEXT NOT NULL,
    type ENUM('multiple_choice', 'scale', 'text') NOT NULL,
    options JSON NULL, -- For multiple choice options
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evaluation_id) REFERENCES evaluations(id) ON DELETE CASCADE
);

-- Assignments table
CREATE TABLE assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evaluation_id INT NOT NULL,
    user_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_assignment (evaluation_id, user_id),
    FOREIGN KEY (evaluation_id) REFERENCES evaluations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Responses table
CREATE TABLE responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT NOT NULL,
    question_id INT NOT NULL,
    answer TEXT NOT NULL, -- JSON for multiple choice, number for scale, text for text
    responded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_response (assignment_id, question_id),
    FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

-- Seed data
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@test.com', '$2y$10$wH8GQpH6YpYpGJ9tOTcfWuO7hRkRykGGBqBPdZiZsaAJ2bI7IuP/m', 'admin'),
('user1', 'user1@test.com', '$2y$10$wH8GQpH6YpYpGJ9tOTcfWuO7hRkRykGGBqBPdZiZsaAJ2bI7IuP/m', 'user');

INSERT INTO evaluations (title, description, start_date, end_date, status, created_by) VALUES
('Evaluación de Satisfacción', 'Encuesta para medir la satisfacción del usuario', '2023-01-01', '2023-12-31', 'published', 1);

INSERT INTO questions (evaluation_id, question_text, type, options) VALUES
(1, '¿Cómo calificaría el servicio?', 'scale', NULL),
(1, '¿Qué aspectos le gustaron?', 'text', NULL),
(1, '¿Recomendaría nuestro servicio?', 'multiple_choice', '["Sí", "No", "Tal vez"]');

INSERT INTO assignments (evaluation_id, user_id) VALUES
(1, 2);
