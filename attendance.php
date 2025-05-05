<?php
session_start();
include 'db.php';

if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];
$subject_id = $_GET['subject_id'] ?? 0;

// Verify subject ownership
$stmt = $conn->prepare("SELECT * FROM subjects WHERE id = ? AND teacher_id = ?");
$stmt->bind_param("ii", $subject_id, $teacher_id);
$stmt->execute();
$subject = $stmt->get_result()->fetch_assoc();

if (!$subject) {
    echo "Unauthorized access or subject not found.";
    exit();
}

// Handle attendance submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];

    foreach ($_POST['status'] as $student_id => $status) {
        $stmt = $conn->prepare("INSERT INTO attendance (student_id, subject_id, date, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $student_id, $subject_id, $date, $status);
        $stmt->execute();
    }
    echo "✅ Attendance saved successfully.<br>";
}
?>

<h2>Attendance - <?php echo htmlspecialchars($subject['name']); ?></h2>

<form method="POST">
    <label>Date: <input type="date" name="date" required></label><br><br>

    <?php
    // Get all students in subject
    $res = $conn->query("SELECT * FROM students WHERE subject_id = $subject_id");

    if ($res->num_rows > 0) {
        while ($student = $res->fetch_assoc()) {
            echo "<p>
                {$student['name']} (Reg number: {$student['reg_number']})<br>
                <label><input type='radio' name='status[{$student['id']}]' value='Present' required> Present</label>
                <label><input type='radio' name='status[{$student['id']}]' value='Absent' required> Absent</label>
            </p>";
        }
        echo "<button type='submit'>Save Attendance</button>";
    } else {
        echo "<p>No students to mark attendance.</p>";
    }
    ?>
</form>

<br>
<a href="subject.php?id=<?php echo $subject_id; ?>">⬅ Back to Subject</a>
