-- Migration: Add note column to questions table
ALTER TABLE questions ADD COLUMN note TEXT NULL AFTER options;
