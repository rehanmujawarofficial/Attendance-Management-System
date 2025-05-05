to create a bd in phpmyadmin 
enter this quiry

-------------------------------------------------------------------------------
-- ✅ Step 1: Database create karo (agar nahi hai to)
CREATE DATABASE IF NOT EXISTS student_db;
USE student_db;

-- ✅ Step 2: Students table (student info + marks)
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    roll_number VARCHAR(50),
    branch VARCHAR(100),
    admission_date DATE,
    sem1 FLOAT,
    sem2 FLOAT,
    sem3 FLOAT,
    sem4 FLOAT,
    sem5 FLOAT,
    sem6 FLOAT
);

-- ✅ Step 3: Attendance table (daily attendance)
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    date DATE,
    status ENUM('Present', 'Absent', 'Late'),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- ✅ Step 4 (Optional): Admin table (for login system, if needed later)
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255)
);
---------------------------------------------------------------------------------------


# Attendance-Management-System
attendance management system with php,mysql,css,js 
