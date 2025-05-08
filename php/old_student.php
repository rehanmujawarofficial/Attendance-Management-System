<?php
session_start();
include 'db.php';

$show_all = isset($_GET['all']) && $_GET['all'] == 1;
$teacher_id = $_SESSION['teacher_id'] ?? 0;

// --- Search filters ---
$roll_search = $_GET['reg_number'] ?? '';
$year_search = $_GET['year'] ?? '';
$date_from = $_GET['from'] ?? '';
$date_to = $_GET['to'] ?? '';

// --- Build filter query ---
$where = "WHERE 1";  // Start with true condition

if (!$show_all) {
    $where .= " AND teacher_id = $teacher_id";
}

if ($roll_search !== '') {
    $where .= " AND reg_number LIKE '%" . $conn->real_escape_string($roll_search) . "%'";
}
if ($year_search !== '') {
    $where .= " AND YEAR(attendance_date) = " . intval($year_search);
}
if ($date_from && $date_to) {
    $where .= " AND attendance_date BETWEEN '$date_from' AND '$date_to'";
}

// Query
$sql = "SELECT 
            osd.student_id, 
            osd.student_name, 
            osd.reg_number, 
            osd.phone, 
            osd.subject_id, 
            osd.subject_name,
            COUNT(*) AS total_classes,
            SUM(CASE WHEN osd.status = 'Present' THEN 1 ELSE 0 END) AS presents,
            t.name AS teacher_name
        FROM old_student_data osd
        LEFT JOIN teachers t ON osd.teacher_id = t.id
        $where
        GROUP BY osd.student_id, osd.subject_id, osd.student_name, osd.reg_number, osd.phone, osd.subject_name, t.name
        ORDER BY osd.subject_id DESC";

$result = $conn->query($sql);
if (!$result) {
    die("❌ Query Failed: " . $conn->error);
}
?>
<link rel="stylesheet" href="../styles/old_stu.css">
<h2>📦 Archived Student Attendance Summary <?php echo $show_all ? "(All Teachers)" : ""; ?></h2>

<!-- Search & Filter Form -->
<form method="GET" style="margin-bottom: 15px;">
    Roll No: <input type="text" name="reg_number" value="<?php echo htmlspecialchars($roll_search); ?>">
    Year: <input type="text" name="year" value="<?php echo htmlspecialchars($year_search); ?>">
    From: <input type="date" name="from" value="<?php echo htmlspecialchars($date_from); ?>">
    To: <input type="date" name="to" value="<?php echo htmlspecialchars($date_to); ?>">
    <button type="submit">Search</button>
    <a href="old_student.php"><button type="button">Reset</button></a>
</form>

<!-- Export to Excel -->
<form method="post" action="export_old_student.php" style="margin-bottom: 15px;">
    <input type="hidden" name="reg_number" value="<?php echo htmlspecialchars($roll_search); ?>">
    <input type="hidden" name="year" value="<?php echo htmlspecialchars($year_search); ?>">
    <input type="hidden" name="from" value="<?php echo htmlspecialchars($date_from); ?>">
    <input type="hidden" name="to" value="<?php echo htmlspecialchars($date_to); ?>">
    <input type="hidden" name="all" value="<?php echo $show_all ? '1' : '0'; ?>">
    <button type="submit">📤 Export to Excel</button>
</form>

<!-- Show All Data Link -->
<p>
    <?php if (!$show_all): ?>
        <a href="old_student.php?all=1">👀 Show All Teachers' Data</a>
    <?php else: ?>
        <a href="old_student.php">🔙 Show My Data Only</a>
    <?php endif; ?>
</p>

<!-- Table -->
<table border="1" cellpadding="6" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Reg number</th>
        <th>Phone</th>
        <th>Subject</th>
        <th>% Attendance</th>
        <?php if ($show_all): ?>
            <th>Teacher</th>
        <?php endif; ?>
    </tr>
    <?php while ($row = $result->fetch_assoc()) {
        $total = $row['total_classes'];
        $present = $row['presents'];
        $percent = $total > 0 ? round(($present / $total) * 100, 2) : 0;
    ?>
        <tr>
            <td><?php echo $row['student_id']; ?></td>
            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
            <td><?php echo htmlspecialchars($row['reg_number']); ?></td>
            <td><?php echo htmlspecialchars($row['phone']); ?></td>
            <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
            <td><?php echo $percent; ?>%</td>
            <?php if ($show_all): ?>
                <td><?php echo htmlspecialchars($row['teacher_name']); ?></td>
            <?php endif; ?>
        </tr>
    <?php } ?>
    </table>

<br><br>
<a href="dashboard.php">
    <button type="button">⬅️ Back to Dashboard</button>
</a>
