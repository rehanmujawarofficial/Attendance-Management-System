<?php
session_start();
include 'db.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=old_students_export.xls");

$teacher_id = $_SESSION['teacher_id'];

$roll_search = $_POST['reg_number'] ?? '';
$year_search = $_POST['year'] ?? '';
$date_from = $_POST['from'] ?? '';
$date_to = $_POST['to'] ?? '';

$where = "WHERE teacher_id = $teacher_id";
if ($roll_search !== '') {
    $where .= " AND reg_number LIKE '%" . $conn->real_escape_string($roll_search) . "%'";
}
if ($year_search !== '') {
    $where .= " AND YEAR(attendance_date) = " . intval($year_search);
}
if ($date_from && $date_to) {
    $where .= " AND attendance_date BETWEEN '$date_from' AND '$date_to'";
}

$sql = "SELECT 
            student_id, 
            student_name, 
            reg_number, 
            phone, 
            subject_id, 
            subject_name,
            COUNT(*) AS total_classes,
            SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS presents
        FROM old_student_data
        $where
        GROUP BY student_id, subject_id, student_name, reg_number, phone, subject_name";

$result = $conn->query($sql);

echo "ID\tName\treg_number\tPhone\tSubject\tAttendance %\n";

while ($row = $result->fetch_assoc()) {
    $total = $row['total_classes'];
    $present = $row['presents'];
    $percent = $total > 0 ? round(($present / $total) * 100, 2) : 0;
    echo "{$row['student_id']}\t{$row['student_name']}\t{$row['reg_number']}\t{$row['phone']}\t{$row['subject_name']}\t{$percent}%\n";
}
?>
