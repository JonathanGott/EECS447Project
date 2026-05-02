<?php
session_start();
include "db.php";

//Make sure user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

//Make sure POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$review_id = isset($_POST["review_id"]) ? intval($_POST["review_id"]) : 0;
$movie_id = isset($_POST["movie_id"]) ? intval($_POST["movie_id"]) : 0;

if ($review_id === 0 || $movie_id === 0) {
    header("Location: index.php");
    exit();
}

//Verify review belongs to current user
$checkSql = "SELECT Review_ID FROM Review WHERE Review_ID = ? AND User_ID = ?";
$checkStmt = mysqli_prepare($conn, $checkSql);
mysqli_stmt_bind_param($checkStmt, "ii", $review_id, $user_id);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);

//If review doesnt exist or belong to current user, dont delete
if (mysqli_num_rows($checkResult) === 0) {
    header("Location: movie.php?id=$movie_id&error=1");
    exit();
}


//Delete
$deleteSql = "DELETE FROM Review WHERE Review_ID = ? AND User_ID = ?";
$deleteStmt = mysqli_prepare($conn, $deleteSql);
mysqli_stmt_bind_param($deleteStmt, "ii", $review_id, $user_id);

if (mysqli_stmt_execute($deleteStmt)) {
    header("Location: movie.php?id=$movie_id&deleted=1");
} else {
    header("Location: movie.php?id=$movie_id&error=1");
}

exit();
?>
