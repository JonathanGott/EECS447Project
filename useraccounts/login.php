<?php
session_start();
include "db.php";

$message = "";

// Runs when login form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = trim($_POST["login"]);
    $password = $_POST["password"];

    // User can log in with username or email
    $sql = "SELECT * FROM `User` WHERE Username = ? OR Email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $login, $login);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Check hashed password
        if (password_verify($password, $user["Password"])) {
            $_SESSION["user_id"] = $user["User_ID"];
            $_SESSION["username"] = $user["Username"];

            header("Location: index.php");
            exit();
        } else {
            $message = "Incorrect password.";
        }
    } else {
        $message = "Account not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="navbar">
    <h1>Movie Review System</h1>
    <nav>
        <a href="index.php">Search</a>
        <a href="register.php">Register</a>
    </nav>
</header>

<main>
    <section class="hero-card">
        <p class="eyebrow">USER LOGIN</p>
        <h2>Login</h2>

        <?php if ($message != ""): ?>
            <p class="error-message"><?php echo $message; ?></p>
        <?php endif; ?>

        <form class="review-form" method="POST" action="login.php">
            <label>Username or Email</label>
            <input type="text" name="login" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </section>
</main>

</body>
</html>