<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM teachers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_pass);

    if ($stmt->fetch() && password_verify($pass, $hashed_pass)) {
        $_SESSION['teacher_id'] = $id;
        header("Location: dashboard.php");
    } else {
        echo "Invalid login";
    }
}
?>
<link rel="stylesheet" href="../styles/login.css">

<section>
<div class="login-container">
<form method="POST">
    <h2>Login</h2>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Login</button>
    <a href="register.php">Don't have an account? Register</a>
</form>
</div>
</select>

