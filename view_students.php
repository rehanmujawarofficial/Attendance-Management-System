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

// Search functionality
$search = $_GET['search'] ?? '';
$query = "SELECT * FROM students WHERE subject_id = $subject_id";
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $query .= " AND (name LIKE '%$search%' OR reg_number LIKE '%$search%')";
}

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student View - <?php echo htmlspecialchars($subject['name']); ?></title>
    <link rel="stylesheet" href="../styles/student_view.css">
</head>
<body>
    <h2>Students – <?php echo htmlspecialchars($subject['name']); ?></h2>

    <form method="GET">
        <input type="hidden" name="subject_id" value="<?php echo $subject_id; ?>">
        <input type="text" name="search" placeholder="Search by name or registration number..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

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
            $total_attendance = $att['total'] ?? 0;

            $percent_present = $total_attendance > 0 ? round(($present / $total_attendance) * 100, 2) : 0;
            $percent_absent = $total_attendance > 0 ? round(($absent / $total_attendance) * 100, 2) : 0;

            // Calculate percentage based on marks
            $total_marks = $student['sem1_marks'] + $student['sem2_marks'] + $student['sem3_marks'] + 
                        $student['sem4_marks'] + $student['sem5_marks'] + $student['sem6_marks'];
            $percentage = round($total_marks / 6, 2);  // Average percentage

            // Determine performance class based on percentage
            $performance_class = 'poor';
            if ($percentage >= 90) {
                $performance_class = 'excellent';
            } elseif ($percentage >= 75) {
                $performance_class = 'good';
            } elseif ($percentage >= 60) {
                $performance_class = 'average';
            }

            echo "<div class='student-card'>
                <div class='student-header'>
                    <div class='student-name'>{$student['name']}</div>
                    <div class='student-info'>Reg: {$student['reg_number']} | Phone: {$student['phone']}</div>
                </div>
                
                <div class='attendance-section'>
                    <div class='attendance-item present'>✅ Present: $present</div>
                    <div class='attendance-item absent'>❌ Absent: $absent</div>
                    <div class='attendance-item percentage'>Present: $percent_present%</div>
                    <div class='attendance-item percentage'>Absent: $percent_absent%</div>
                </div>
                
                <div class='marks-section'>
                    <div class='marks-header'>Academic Performance</div>
                    <div class='semester-marks'>
                        <div class='semester'>Sem 1: {$student['sem1_marks']}</div>
                        <div class='semester'>Sem 2: {$student['sem2_marks']}</div>
                        <div class='semester'>Sem 3: {$student['sem3_marks']}</div>
                        <div class='semester'>Sem 4: {$student['sem4_marks']}</div>
                        <div class='semester'>Sem 5: {$student['sem5_marks']}</div>
                        <div class='semester'>Sem 6: {$student['sem6_marks']}</div>
                    </div>
                    <div class='cgpa'>CGPA: $percentage%</div>
                    <div class='performance-indicator'>
                        <div class='performance-bar $performance_class' style='width: $percentage%'></div>
                    </div>
                </div>
                
                <div class='student-actions'>
                    <a class='action-link' href='edit_marks.php?student_id={$student['id']}'>Edit Marks</a>
                </div>
            </div>";
        }
    } else {
        echo "<div class='no-results'>No students found matching your search criteria</div>";
    }
    ?>

    <a href="subject.php?id=<?php echo $subject_id; ?>" class="back-link">Back to Subject</a>

    <script>
    // Function to show toast notifications
    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 3500);
    }

    // Uncomment below line to test toast notification
    // showToast('Student data loaded successfully!');
    </script>
</body>
</html>