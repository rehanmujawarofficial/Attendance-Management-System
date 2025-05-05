<?php
session_start();
include 'db.php';

if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];
$subject_id = $_GET['id'] ?? 0;

// Check if subject belongs to this teacher
$stmt = $conn->prepare("SELECT * FROM subjects WHERE id = ? AND teacher_id = ?");
$stmt->bind_param("ii", $subject_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Subject not found or not yours.";
    exit();
}

$subject = $result->fetch_assoc();
?>

<h2><?php echo htmlspecialchars($subject['name']); ?></h2>

<!-- Menu -->
<ul>
    <li><a href="attendance.php?subject_id=<?php echo $subject_id; ?>">✅ Take Attendance</a></li>
    <li><a href="view_students.php?subject_id=<?php echo $subject_id; ?>">🔍 View Students</a></li> 
    <li><a href="add_student.php?subject_id=<?php echo $subject_id; ?>">➕ Add Student</a></li>
    <li><a href="remove_student.php?subject_id=<?php echo $subject_id; ?>">➖ Remove Student</a></li>
</ul>

<a href="dashboard.php">⬅ Back to Dashboard</a>
