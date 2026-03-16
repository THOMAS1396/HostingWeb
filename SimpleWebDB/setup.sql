-- =============================================
--  Run this SQL in phpMyAdmin or MySQL terminal
--  to set up your database and tables
-- =============================================

-- 1. Create the database
CREATE DATABASE IF NOT EXISTS classpractice_db;
USE classpractice_db;

-- 2. Users table (for signup & login)
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    email      VARCHAR(100) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Teacher applications table (for tech.php)
CREATE TABLE IF NOT EXISTS teacher_applications (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    fullname        VARCHAR(100) NOT NULL,
    email           VARCHAR(100) NOT NULL,
    experience      VARCHAR(255),
    qualifications  VARCHAR(255),
    subject         VARCHAR(100) NOT NULL,
    mode            VARCHAR(20),
    intro           TEXT,
    submitted_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
--  ADMIN DASHBOARD ADDITIONS
--  Run this after the original setup.sql
-- =============================================

-- Add role column to users table (if not already there)
ALTER TABLE users ADD COLUMN IF NOT EXISTS role VARCHAR(20) DEFAULT 'user';

-- Courses table (dynamic courses managed by admin)
CREATE TABLE IF NOT EXISTS courses (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(150) NOT NULL,
    category    VARCHAR(100) NOT NULL,
    description TEXT,
    image_url   VARCHAR(255),
    video_url   VARCHAR(255),
    created_by  INT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Create a default admin user (password: admin123)
INSERT IGNORE INTO users (username, email, password, role)
VALUES ('admin', 'admin@classpractice.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- =============================================
--  NEXT FEATURES - Run this section next
-- =============================================

-- Enrollments table (user course enrollment)
CREATE TABLE IF NOT EXISTS enrollments (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    course_id   INT NOT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_enrollment (user_id, course_id),
    FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id)  ON DELETE CASCADE
);

-- =============================================
--  INSERT DEFAULT COURSES
--  Run this to populate the homepage
-- =============================================
INSERT IGNORE INTO courses (id, title, category, description, image_url, video_url, created_by) VALUES
(1, 'Python Programming',  'Programming',     'Python: high-level, interpreted, versatile. Clean syntax, huge ecosystem — perfect for beginners & pros.', 'images/python.png',      'https://www.youtube.com/watch?v=kqtD5dpn9C8', 1),
(2, 'Django Web Dev',      'Web Development', 'Django: high-level Python framework. Build secure, scalable web apps in record time. Batteries included.',  'images/django.png',      'https://www.youtube.com/watch?v=kqtD5dpn9C8', 1),
(3, 'Data Science',        'Data Science',    'Data science: extract insights, build models, tell stories with data. pandas, scikit-learn, and more.',      'images/datascience.png', 'https://www.youtube.com/watch?v=kqtD5dpn9C8', 1),
(4, 'C++ Programming',     'Programming',     'High performance, system programming, games. C++ gives you control and speed.',                              'images/cpp.png',         'https://www.youtube.com/watch?v=kqtD5dpn9C8', 1),
(5, 'C# Programming',      'Programming',     'Modern, object-oriented, built for .NET. Used for apps, games (Unity), and cloud.',                          'images/csharp.png',      'https://www.youtube.com/watch?v=kqtD5dpn9C8', 1),
(6, 'JavaScript',          'Web Development', 'Language of the web. From interactive pages to fullstack (Node.js).',                                        'images/javascript.png',  'https://www.youtube.com/watch?v=kqtD5dpn9C8', 1);
