CREATE DATABASE IF NOT EXISTS slo_tool;
USE slo_tool;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  role ENUM('admin','faculty','mentor','parent') NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE courses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(30) NOT NULL UNIQUE,
  name VARCHAR(160) NOT NULL,
  program_name VARCHAR(160) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reg_no VARCHAR(40) NOT NULL UNIQUE,
  name VARCHAR(120) NOT NULL,
  course_id INT NOT NULL,
  mentor_id INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE RESTRICT,
  FOREIGN KEY (mentor_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE parents (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL UNIQUE,
  name VARCHAR(120) NOT NULL,
  phone VARCHAR(20),
  email VARCHAR(150) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

CREATE TABLE course_outcomes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  course_id INT NOT NULL,
  code VARCHAR(30) NOT NULL,
  description TEXT NOT NULL,
  target_level TINYINT NOT NULL DEFAULT 2,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

CREATE TABLE program_outcomes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(30) NOT NULL UNIQUE,
  description TEXT NOT NULL,
  outcome_type ENUM('PO','PSO') NOT NULL DEFAULT 'PO',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE co_po_mapping (
  id INT AUTO_INCREMENT PRIMARY KEY,
  co_id INT NOT NULL,
  po_id INT NOT NULL,
  weight TINYINT NOT NULL CHECK (weight BETWEEN 1 AND 3),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_mapping (co_id, po_id),
  FOREIGN KEY (co_id) REFERENCES course_outcomes(id) ON DELETE CASCADE,
  FOREIGN KEY (po_id) REFERENCES program_outcomes(id) ON DELETE CASCADE
);

CREATE TABLE marks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  course_id INT NOT NULL,
  co_id INT NOT NULL,
  internal_mark DECIMAL(5,2) NOT NULL,
  assignment_mark DECIMAL(5,2) NOT NULL,
  exam_mark DECIMAL(5,2) NOT NULL,
  total_mark DECIMAL(5,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
  FOREIGN KEY (co_id) REFERENCES course_outcomes(id) ON DELETE CASCADE
);

CREATE TABLE attainment_results (
  id INT AUTO_INCREMENT PRIMARY KEY,
  mark_id INT NULL,
  student_id INT NOT NULL,
  course_id INT NOT NULL,
  co_id INT NOT NULL,
  po_id INT NOT NULL,
  co_attainment DECIMAL(4,2) NOT NULL,
  po_attainment DECIMAL(4,2) NOT NULL,
  calculated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (mark_id) REFERENCES marks(id) ON DELETE CASCADE,
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
  FOREIGN KEY (co_id) REFERENCES course_outcomes(id) ON DELETE CASCADE,
  FOREIGN KEY (po_id) REFERENCES program_outcomes(id) ON DELETE CASCADE
);

CREATE TABLE mentor_feedback (
  id INT AUTO_INCREMENT PRIMARY KEY,
  mentor_id INT NOT NULL,
  student_id INT NOT NULL,
  problem_statement TEXT NOT NULL,
  solution_provided TINYINT(1) NOT NULL DEFAULT 0,
  satisfaction_status ENUM('Not Met','Partially Met','Met') NOT NULL DEFAULT 'Not Met',
  remark TEXT NOT NULL,
  action_plan TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (mentor_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sender_id INT NOT NULL,
  parent_id INT NOT NULL,
  student_id INT NOT NULL,
  subject VARCHAR(200) NOT NULL,
  body TEXT NOT NULL,
  sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  read_flag TINYINT(1) DEFAULT 0,
  FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (parent_id) REFERENCES parents(id) ON DELETE CASCADE,
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

INSERT INTO users (name, role, email, password) VALUES
('System Admin', 'admin', 'admin@slo.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Dr. Faculty', 'faculty', 'faculty@slo.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Mentor Kumar', 'mentor', 'mentor@slo.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Parent Asha', 'parent', 'parent@slo.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT INTO courses (code, name, program_name) VALUES ('CS301', 'Data Structures', 'B.Tech CSE');
INSERT INTO students (reg_no, name, course_id, mentor_id) VALUES ('21CSE001', 'Arun Kumar', 1, 3),('21CSE002', 'Bala Priya', 1, 3);
INSERT INTO parents (student_id, name, phone, email) VALUES (1, 'Asha Kumar', '9876543210', 'parent@slo.edu');
INSERT INTO course_outcomes (course_id, code, description, target_level) VALUES (1, 'CO1', 'Understand linear data structures', 2),(1, 'CO2', 'Apply tree and graph algorithms', 3);
INSERT INTO program_outcomes (code, description, outcome_type) VALUES ('PO1', 'Engineering knowledge', 'PO'),('PO2', 'Problem analysis', 'PO'),('PSO1', 'Apply computing tools effectively', 'PSO');
INSERT INTO co_po_mapping (co_id, po_id, weight) VALUES (1, 1, 3),(1, 2, 2),(2, 2, 3),(2, 3, 2);

