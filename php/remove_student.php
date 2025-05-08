<?php session_start(); 
include 'db.php'; 

if (!isset($_SESSION['teacher_id'])) { 
    header("Location: login.php"); 
    exit(); 
} 

$teacher_id = $_SESSION['teacher_id']; 
$subject_id = $_GET['subject_id'] ?? 0; 

if (!$subject_id) { 
    echo "❌ subject_id missing in URL."; 
    exit(); 
} 

// Verify subject 
$stmt = $conn->prepare("SELECT * FROM subjects WHERE id = ? AND teacher_id = ?"); 
$stmt->bind_param("ii", $subject_id, $teacher_id); 
$stmt->execute(); 
$subject = $stmt->get_result()->fetch_assoc(); 

if (!$subject) { 
    echo "❌ Subject not found or not yours."; 
    exit(); 
} 

// Handle deletion 
if (isset($_GET['remove_id'])) { 
    $remove_id = intval($_GET['remove_id']); 
    
    // Check if student exists and belongs to this subject 
    $check = $conn->prepare("SELECT * FROM students WHERE id = ? AND subject_id = ?"); 
    $check->bind_param("ii", $remove_id, $subject_id); 
    $check->execute(); 
    $student = $check->get_result()->fetch_assoc(); 
    
    if ($student) { 
        $delete = $conn->prepare("DELETE FROM students WHERE id = ?"); 
        $delete->bind_param("i", $remove_id); 
        
        if ($delete->execute()) { 
            echo "✅ Student '{$student['name']}' removed successfully.<br><br>"; 
        } else { 
            echo "❌ Delete failed: " . $delete->error . "<br><br>"; 
        } 
    } else { 
        echo "❌ Student not found or doesn't belong to this subject.<br><br>"; 
    } 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove Students</title>
    <link rel="stylesheet" href="../styles/remove_stu.css">
</head>
<body>
    <div class="container">
        <h2>Remove Students – <?php echo htmlspecialchars($subject['name']); ?></h2>
        
        <?php 
        $res = $conn->query("SELECT * FROM students WHERE subject_id = $subject_id"); 
        
        if ($res->num_rows > 0) { 
            while ($student = $res->fetch_assoc()) { 
                echo "<p>
                    <span>{$student['name']} (Reg number: {$student['reg_number']})</span>
                    <a href='remove_student.php?subject_id=$subject_id&remove_id={$student['id']}'
                        onclick=\"return confirm('Are you sure you want to remove this student?')\">[Remove]</a>
                </p>"; 
            } 
        } else { 
            echo "<p>No students in this subject.</p>"; 
        } 
        ?>
    </div>
    
    <a href="subject.php?id=<?php echo $subject_id; ?>">Back to Subject</a>
</body>
</html>