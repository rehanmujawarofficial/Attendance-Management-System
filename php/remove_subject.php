<?php
session_start();
include 'db.php';

if (!isset($_SESSION['teacher_id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];
$subject_id = $_GET['id'];

// Get subject info
$stmt = $conn->prepare("SELECT * FROM subjects WHERE id = ? AND teacher_id = ?");
$stmt->bind_param("ii", $subject_id, $teacher_id);
$stmt->execute();
$subject = $stmt->get_result()->fetch_assoc();

if (!$subject) {
    echo "❌ Subject not found or unauthorized.";
    exit();
}

$subject_name = $subject['name'];

// Archive all students and their attendance
$students = $conn->query("SELECT * FROM students WHERE subject_id = $subject_id");

while ($stu = $students->fetch_assoc()) {
    $student_id = $stu['id'];

    $att = $conn->query("SELECT * FROM attendance WHERE student_id = $student_id AND subject_id = $subject_id");

    while ($a = $att->fetch_assoc()) {
        $archive = $conn->prepare("INSERT INTO old_student_data 
            (teacher_id, subject_id, subject_name, student_id, student_name, reg_number, phone, attendance_date, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $archive->bind_param("iisisssss",
            $teacher_id, $subject_id, $subject_name,
            $student_id, $stu['name'], $stu['reg_number'], $stu['phone'],
            $a['date'], $a['status']
        );
        $archive->execute();
    }
}

// Delete attendance, students, then subject
$conn->query("DELETE FROM attendance WHERE subject_id = $subject_id");
$conn->query("DELETE FROM students WHERE subject_id = $subject_id");
$conn->query("DELETE FROM subjects WHERE id = $subject_id");

header("Location: dashboard.php?msg=deleted");
exit();
?>
