<?php
session_start();
include "db.php";

$message = "";

// Runs when user submits the form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Hash password before storing it
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if username or email already exists
    $checkSql = "SELECT * FROM `User` WHERE Username = ? OR Email = ?";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($checkStmt, "ss", $username, $email);
    mysqli_stmt_execute($checkStmt);
    $result = mysqli_stmt_get_result($checkStmt);

    if (mysqli_num_rows($result) > 0) {
        $message = "Username or email already exists.";
    } else {
        // Insert new user into database
        $sql = "INSERT INTO `User` (Username, Email, Password) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashedPassword);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: login.php");
            exit();
        } else {
            $message = "Registration failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="navbar">
    <h1>Movie Review System</h1>
    <nav>
        <a href="index.php">Search</a>
        <a href="login.php">Login</a>
    </nav>
</header>

<main>
    <section class="hero-card">
        <p class="eyebrow">CREATE ACCOUNT</p>
        <h2>Register</h2>

        <?php if ($message != ""): ?>
            <p class="error-message"><?php echo $message; ?></p>
        <?php endif; ?>

        <form class="review-form" method="POST" action="register.php">
            <label>Username</label>
            <input type="text" name="username" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit">Register</button>
        </form>
    </section>
</main>

</body>
</html>