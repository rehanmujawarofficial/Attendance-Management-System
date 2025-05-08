<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject = $_POST['subject_name'];
    $teacher_id = $_SESSION['teacher_id'];

    $stmt = $conn->prepare("INSERT INTO subjects (name, teacher_id) VALUES (?, ?)");
    $stmt->bind_param("si", $subject, $teacher_id);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
