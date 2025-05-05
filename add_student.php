<?php
session_start();
include 'db.php';

if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];
$subject_id = $_GET['subject_id'] ?? 0;

// Verify that subject belongs to this teacher
$stmt = $conn->prepare("SELECT * FROM subjects WHERE id = ? AND teacher_id = ?");
$stmt->bind_param("ii", $subject_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Unauthorized access or invalid subject.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $roll_no = $_POST['reg_number'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("INSERT INTO students (name, reg_number, phone, subject_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $name, $roll_no, $phone, $subject_id);

    if ($stmt->execute()) {
        echo "✅ Student added successfully.<br>";
    } else {
        echo "❌ Error: " . $stmt->error;
    }
}
?>

<h2>Add Student</h2>
<form method="POST">
    <input type="text" name="name" placeholder="Student Name" required><br><br>
    <input type="text" name="reg_number" placeholder="Reg Number" required><br><br>
    <input type="text" name="phone" placeholder="Phone Number" required><br><br>
    <button type="submit">Add Student</button>
</form>

<br>
<a href="subject.php?id=<?php echo $subject_id; ?>">⬅ Back to Subject</a>
<br><br>