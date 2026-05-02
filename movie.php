<?php
session_start();
include "db.php";

// Get movie ID from URL
$movie_id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

if ($movie_id === 0) {
    header("Location: index.php");
    exit();
}

// Fetch movie attributes
$movieSql = "SELECT * FROM Movie WHERE Movie_ID = ?";
$movieStmt = mysqli_prepare($conn, $movieSql);
mysqli_stmt_bind_param($movieStmt, "i", $movie_id);
mysqli_stmt_execute($movieStmt);

$movieResult = mysqli_stmt_get_result($movieStmt);

if (mysqli_num_rows($movieResult) === 0) {
    header("Location: index.php");
    exit();
}

$movie = mysqli_fetch_assoc($movieResult);

// Fetch movie average rating
$avgSql = "SELECT ROUND(AVG(Rating), 1) AS avg_rating, COUNT(*) AS review_count 
           FROM Review 
           WHERE Movie_ID = ?";

$avgStmt = mysqli_prepare($conn, $avgSql);
mysqli_stmt_bind_param($avgStmt, "i", $movie_id);
mysqli_stmt_execute($avgStmt);

$avgResult = mysqli_stmt_get_result($avgStmt);
$avgData = mysqli_fetch_assoc($avgResult);

$avgRating = $avgData["avg_rating"];
$reviewCount = $avgData["review_count"];

// Fetch all reviews with usernames
$reviewSql = "SELECT Review.Review_ID, Review.Rating, Review.Review_Text, Review.Review_Date, Review.User_ID, User.Username
              FROM Review
              JOIN User ON Review.User_ID = User.User_ID
              WHERE Review.Movie_ID = ?
              ORDER BY Review.Review_Date DESC";

$reviewStmt = mysqli_prepare($conn, $reviewSql);
mysqli_stmt_bind_param($reviewStmt, "i", $movie_id);
mysqli_stmt_execute($reviewStmt);
$reviewsResult = mysqli_stmt_get_result($reviewStmt);

// Check if logged-in user has already reviewed movie
$alreadyReviewed = false;

if (isset($_SESSION["user_id"])) {
    $checkSql = "SELECT Review_ID FROM Review WHERE User_ID = ? AND Movie_ID = ?";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($checkStmt, "ii", $_SESSION["user_id"], $movie_id);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);

    $alreadyReviewed = mysqli_num_rows($checkResult) > 0;
}

// Handle success/error messages
$message = "";
$messageClass = "";

if (isset($_GET["success"])) {
    $message = "Your review was submitted!";
    $messageClass = "success-message";
} elseif (isset($_GET["error"])) {
    $message = "There was a problem submitting your review. Please try again.";
    $messageClass = "error-message";
} elseif (isset($_GET["deleted"])) {
    $message = "Your review has been deleted.";
    $messageClass = "success-message";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($movie["Title"]); ?> - Movie Review</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<header class="navbar">
    <h1>Movie Review</h1>

    <nav>
        <a href="index.php">Search</a>

        <?php if (isset($_SESSION["user_id"])): ?>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>

<main>

    <!-- Movie details -->
    <section class="section">
        <div class="details-card">

            <!-- Left side movie info -->
            <div>
                <p class="eyebrow">MOVIE DETAILS</p>

                <h2><?php echo htmlspecialchars($movie["Title"]); ?></h2>

                <p>
                    <?php echo htmlspecialchars($movie["Genre"]); ?> &bull;
                    <?php echo htmlspecialchars($movie["Release_Year"]); ?> &bull;
                    <?php echo htmlspecialchars($movie["Director"]); ?>
                </p>

                <?php if ($reviewCount > 0): ?>
                    <p class="average-rating">
                        Average Rating: <?php echo htmlspecialchars($avgRating); ?> / 5
                        (<?php echo $reviewCount; ?> review<?php echo $reviewCount != 1 ? "s" : ""; ?>)
                    </p>
                <?php else: ?>
                    <p class="average-rating">No ratings yet - be the first!</p>
                <?php endif; ?>
            </div>

            <!-- Right side review form/message -->
            <div>
                <?php if (!isset($_SESSION["user_id"])): ?>

                    <div class="review-form">
                        <h3>Rate this movie</h3>
                        <p style="color:#b8beca;">
                            You must be <a href="login.php" style="color:#8fb4f5;">logged in</a> to leave a review.
                        </p>
                    </div>

                <?php elseif ($alreadyReviewed): ?>

                    <div class="review-form">
                        <h3>Rate this movie</h3>
                        <p style="color:#b8beca;">You have already reviewed this movie.</p>
                    </div>

                <?php else: ?>

                    <?php if ($message !== ""): ?>
                        <p class="<?php echo $messageClass; ?>"><?php echo $message; ?></p>
                    <?php endif; ?>

                    <form class="review-form" method="POST" action="submit_review.php">
                        <h3>Rate this movie</h3>

                        <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">

                        <label>Rating</label>
                        <select name="rating" required>
                            <option value="">Choose Rating</option>
                            <option value="1">1</option>
                            <option value="1.5">1.5</option>
                            <option value="2">2</option>
                            <option value="2.5">2.5</option>
                            <option value="3">3</option>
                            <option value="3.5">3.5</option>
                            <option value="4">4</option>
                            <option value="4.5">4.5</option>
                            <option value="5">5</option>
                        </select>

                        <label>Comment (optional)</label>
                        <textarea name="comment" placeholder="Write your review..."></textarea>

                        <button type="submit">Submit Review</button>
                    </form>

                <?php endif; ?>
            </div>

        </div>
    </section>

    <!-- User reviews -->
    <section class="section">
        <h2>User Reviews</h2>

        <div class="reviews-list">

            <?php if (mysqli_num_rows($reviewsResult) === 0): ?>

                <p style="color:#b8beca;">No reviews yet - be the first!</p>

            <?php else: ?>

                <?php while ($review = mysqli_fetch_assoc($reviewsResult)): ?>

                    <div class="review-card">
                        <p class="stars">
                            Rating: <?php echo htmlspecialchars($review["Rating"]); ?> / 5
                        </p>

                        <h3><?php echo htmlspecialchars($review["Username"]); ?></h3>

                        <?php if (!empty($review["Review_Text"])): ?>
                            <p><?php echo htmlspecialchars($review["Review_Text"]); ?></p>
                        <?php endif; ?>

                        <?php if (!empty($review["Review_Date"])): ?>
                            <p style="color:#9aa3b4; font-size:0.85em;">
                                <?php echo date("M j, Y", strtotime($review["Review_Date"])); ?>
                            </p>
                        <?php endif; ?>

                        <?php if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] == $review["User_ID"]): ?>
                            <form method="POST" action="delete_review.php">
                                <input type="hidden" name="review_id" value="<?php echo $review["Review_ID"]; ?>">
                                <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
                                <button type="submit">Delete Review</button>
                            </form>
                        <?php endif; ?>
                    </div>

                <?php endwhile; ?>

            <?php endif; ?>

        </div>
    </section>

</main>

</body>
</html>
