<?php
session_start();
include 'db.php';

if (isset($_GET['id'])) {
    $subject_id = $_GET['id'];
    $teacher_id = $_SESSION['teacher_id'];

    // Only allow deletion if subject belongs to current teacher
    $stmt = $conn->prepare("DELETE FROM subjects WHERE id = ? AND teacher_id = ?");
    $stmt->bind_param("ii", $subject_id, $teacher_id);

    if ($stmt->execute()) {
        // Delete related students and attendance (optional)
        $conn->query("DELETE FROM students WHERE subject_id = $subject_id");
        $conn->query("DELETE FROM attendance WHERE subject_id = $subject_id");
    }

    header("Location: dashboard.php");
}
?>
