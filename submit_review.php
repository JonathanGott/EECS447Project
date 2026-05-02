<?php
session_start();
include "db.php";

// Make sure user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Make sure it is a POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$movie_id = isset($_POST["movie_id"]) ? intval($_POST["movie_id"]) : 0;
$rating = isset($_POST["rating"]) ? $_POST["rating"] : "";
$comment = isset($_POST["comment"]) ? trim($_POST["comment"]) : "";

// Validate movie id and rating
if ($movie_id === 0 || $rating === "") {
    header("Location: movie.php?id=$movie_id&error=1");
    exit();
}

// Validate rating is a legal value
$validRatings = ["1", "1.5", "2", "2.5", "3", "3.5", "4", "4.5", "5"];

if (!in_array($rating, $validRatings)) {
    header("Location: movie.php?id=$movie_id&error=1");
    exit();
}

// Make sure user has not already reviewed movie
$checkSql = "SELECT Review_ID FROM Review WHERE User_ID = ? AND Movie_ID = ?";
$checkStmt = mysqli_prepare($conn, $checkSql);
mysqli_stmt_bind_param($checkStmt, "ii", $user_id, $movie_id);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);

if (mysqli_num_rows($checkResult) > 0) {
    header("Location: movie.php?id=$movie_id&error=1");
    exit();
}

// Insert review with today's date
$insertSql = "INSERT INTO Review (Rating, Review_Text, Review_Date, User_ID, Movie_ID)
              VALUES (?, ?, CURDATE(), ?, ?)";

$insertStmt = mysqli_prepare($conn, $insertSql);
mysqli_stmt_bind_param($insertStmt, "dsii", $rating, $comment, $user_id, $movie_id);

if (mysqli_stmt_execute($insertStmt)) {
    header("Location: movie.php?id=$movie_id&success=1");
} else {
    header("Location: movie.php?id=$movie_id&error=1");
}

exit();
?>
