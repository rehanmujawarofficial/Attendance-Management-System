<?php
session_start();
include 'db.php';

if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

// Fetch subjects of this teacher
$sql = "SELECT * FROM subjects WHERE teacher_id = $teacher_id";
$result = $conn->query($sql);
?>

<h2>Welcome, Teacher</h2>

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
            <?php echo $row['name']; ?>
            <a href="subject.php?id=<?php echo $row['id']; ?>">[Manage]</a>
            <a href="remove_subject.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">[Remove]</a>
        </li>
    <?php } ?>
</ul>

<a href="logout.php">Logout</a>
