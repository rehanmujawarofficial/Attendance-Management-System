<?php
session_start();
include 'db.php';

if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

// Fetch subjects
$sql = "SELECT * FROM subjects WHERE teacher_id = $teacher_id";
$result = $conn->query($sql);
?>

<h2>Welcome</h2>
<link rel="stylesheet" href="../styles/dash.css">


<!-- Add Subject Form -->
<form method="POST" action="add_subject.php">
    <input type="text" name="subject_name" placeholder="New Subject Name" required>
    <button type="submit">Add Subject</button>
</form>

<!-- Subject List -->
<h3>Your Subjects:</h3>
<ul>
<?php while ($row = $result->fetch_assoc()) { ?>
    <li>
        <?php echo htmlspecialchars($row['name']); ?>
        <a href="subject.php?id=<?php echo $row['id']; ?>">[Manage]</a>
        <a href="remove_subject.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this subject?')">[Remove]</a>
    </li>
<?php } ?>
<div class="buttons">
  <a href="logout.php">Logout</a>
  <a href="old_student.php">View Old Student Data</a>
</div>
</ul>



