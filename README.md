
---

### ✅ Step 1: Create the Database

```sql
CREATE DATABASE IF NOT EXISTS gptmudhol;
USE gptmudhol;
```

---

### ✅ Step 2: Create `teachers` Table

```sql
CREATE TABLE teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);
```

---

### ✅ Step 3: Create `subjects` Table

```sql
CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT,
    name VARCHAR(100) NOT NULL,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE
);
```

---

### ✅ Step 4: Create `students` Table

```sql
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    roll_no VARCHAR(50),
    phone VARCHAR(20),
    subject_id INT,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
);
```

---

### ✅ Step 5: Create `attendance` Table

```sql
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    subject_id INT,
    date DATE,
    status ENUM('Present', 'Absent'),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
);
```

---

### ✅ Step 6: Create `marks` Table

```sql
CREATE TABLE marks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    sem1 FLOAT,
    sem2 FLOAT,
    sem3 FLOAT,
    sem4 FLOAT,
    sem5 FLOAT,
    sem6 FLOAT,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);
```

---

### ✅ Step 7: Create `old_student_data` Table (for archived students + attendance)

```sql
CREATE TABLE old_student_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT,
    subject_id INT,
    subject_name VARCHAR(100),
    student_id INT,
    student_name VARCHAR(100),
    roll_no VARCHAR(50),
    phone VARCHAR(20),
    attendance_date DATE,
    status ENUM('Present', 'Absent'),
    archived_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

# Attendance-Management-System
attendance management system with php,mysql,css,js 
