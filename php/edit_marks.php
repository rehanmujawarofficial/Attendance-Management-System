<?php
session_start();
include 'db.php';

if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_GET['student_id'] ?? 0;

// Fetch student
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo "Student not found.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sem1 = $_POST['sem1_marks'];
    $sem2 = $_POST['sem2_marks'];
    $sem3 = $_POST['sem3_marks'];
    $sem4 = $_POST['sem4_marks'];
    $sem5 = $_POST['sem5_marks'];
    $sem6 = $_POST['sem6_marks'];

    $update = $conn->prepare("UPDATE students SET 
        sem1_marks = ?, sem2_marks = ?, sem3_marks = ?, 
        sem4_marks = ?, sem5_marks = ?, sem6_marks = ? 
        WHERE id = ?");
    $update->bind_param("ddddddi", $sem1, $sem2, $sem3, $sem4, $sem5, $sem6, $student_id);
    $update->execute();

    header("Location: view_students.php?subject_id=" . $student['subject_id']);
    exit();
}
?>
<link rel="stylesheet" href="../styles/edit.css">

<h2>Edit Marks for <?php echo $student['name']; ?></h2>

<form method="POST">
    <label>Sem 1:</label>
    <input type="number" step="0.01" name="sem1_marks" value="<?php echo $student['sem1_marks']; ?>" required><br>

    <label>Sem 2:</label>
    <input type="number" step="0.01" name="sem2_marks" value="<?php echo $student['sem2_marks']; ?>" required><br>

    <label>Sem 3:</label>
    <input type="number" step="0.01" name="sem3_marks" value="<?php echo $student['sem3_marks']; ?>" required><br>

    <label>Sem 4:</label>
    <input type="number" step="0.01" name="sem4_marks" value="<?php echo $student['sem4_marks']; ?>" required><br>

    <label>Sem 5:</label>
    <input type="number" step="0.01" name="sem5_marks" value="<?php echo $student['sem5_marks']; ?>" required><br>

    <label>Sem 6:</label>
    <input type="number" step="0.01" name="sem6_marks" value="<?php echo $student['sem6_marks']; ?>" required><br><br>

    <button type="submit">Update Marks</button>
</form>

<a href="view_students.php?subject_id=<?php echo $student['subject_id']; ?>">⬅ Back to Students</a>
