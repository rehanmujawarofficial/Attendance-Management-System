<?php
session_start();
include 'db.php';

if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];
$subject_id = $_GET['subject_id'] ?? 0;

// Verify subject
$stmt = $conn->prepare("SELECT * FROM subjects WHERE id = ? AND teacher_id = ?");
$stmt->bind_param("ii", $subject_id, $teacher_id);
$stmt->execute();
$subject = $stmt->get_result()->fetch_assoc();

if (!$subject) {
    echo "Unauthorized access.";
    exit();
}

// Search
$search = $_GET['search'] ?? '';
$query = "SELECT * FROM students WHERE subject_id = $subject_id";
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $query .= " AND (name LIKE '%$search%' OR roll_no LIKE '%$search%')";
}

$result = $conn->query($query);
?>

<h2>Students – <?php echo htmlspecialchars($subject['name']); ?></h2>

<form method="GET">
    <input type="hidden" name="subject_id" value="<?php echo $subject_id; ?>">
    <input type="text" name="search" placeholder="Search by name or roll no" value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
</form>

<br>

<?php
if ($result->num_rows > 0) {
    while ($student = $result->fetch_assoc()) {
        $student_id = $student['id'];

        // Attendance data
        $att_q = $conn->query("SELECT 
            SUM(status = 'Present') AS present_count,
            SUM(status = 'Absent') AS absent_count,
            COUNT(*) AS total
            FROM attendance 
            WHERE student_id = $student_id AND subject_id = $subject_id");

        $att = $att_q->fetch_assoc();

        $present = $att['present_count'] ?? 0;
        $absent = $att['absent_count'] ?? 0;
        $total = $att['total'] ?? 0;

        $percent_present = $total > 0 ? round(($present / $total) * 100, 2) : 0;
        $percent_absent = $total > 0 ? round(($absent / $total) * 100, 2) : 0;

        echo "<div style='border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;'>
            <strong>{$student['name']}</strong> (Reg number: {$student['reg_number']}, Phone: {$student['phone']})<br>
            ✅ Present: $present | ❌ Absent: $absent | 
            📊 Present %: $percent_present% | Absent %: $percent_absent%
        </div>";
    }
} else {
    echo "<p>No students found.</p>";
}
?>

<br>
<a href="subject.php?id=<?php echo $subject_id; ?>">⬅ Back to Subject</a>
