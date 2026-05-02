<?php
session_start();
include "db.php";

// Must be logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// JOIN query (Review + Movie)
$sql = "SELECT Review.Review_ID, Review.Rating, Review.Review_Text, Review.Review_Date,
               Movie.Movie_ID, Movie.Title, Movie.Genre, Movie.Release_Year, Movie.Director
        FROM Review
        JOIN Movie ON Review.Movie_ID = Movie.Movie_ID
        WHERE Review.User_ID = ?
        ORDER BY Review.Review_Date DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reviews</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<header class="navbar">
    <h1>Movie Review</h1>

    <nav>
        <a href="index.php">Search</a>

        <?php if (isset($_SESSION["user_id"])): ?>
            <a href="my_reviews.php">My Reviews</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>

<main>

    <section class="hero-card">
        <p class="eyebrow">MY REVIEWS</p>
        <h2>Your Movie Reviews</h2>
        <p class="subtitle">View the ratings and comments you have submitted.</p>
    </section>

    <section class="section">
        <div class="reviews-list">

            <?php if (mysqli_num_rows($result) === 0): ?>
                <p style="color:#b8beca;">You have not reviewed any movies yet.</p>
            <?php else: ?>

                <?php while ($review = mysqli_fetch_assoc($result)): ?>

                    <div class="review-card">

                        <!-- Movie title -->
                        <h3><?php echo htmlspecialchars($review["Title"]); ?></h3>

                        <!-- Movie info -->
                        <p>
                            <?php echo htmlspecialchars($review["Genre"]); ?> &bull;
                            <?php echo htmlspecialchars($review["Release_Year"]); ?> &bull;
                            <?php echo htmlspecialchars($review["Director"]); ?>
                        </p>

                        <!-- Rating -->
                        <p class="stars">
                            Rating: <?php echo htmlspecialchars($review["Rating"]); ?> / 5
                        </p>

                        <!-- Comment -->
                        <?php if (!empty($review["Review_Text"])): ?>
                            <p><?php echo htmlspecialchars($review["Review_Text"]); ?></p>
                        <?php endif; ?>

                        <!-- Date -->
                        <?php if (!empty($review["Review_Date"])): ?>
                            <p style="color:#9aa3b4; font-size:0.85em;">
                                <?php echo date("M j, Y", strtotime($review["Review_Date"])); ?>
                            </p>
                        <?php endif; ?>

                        <!-- Link back to movie -->
                        <a class="card-link" href="movie.php?id=<?php echo $review["Movie_ID"]; ?>">
                            View Movie
                        </a>

                        <!-- Delete button -->
                        <form method="POST" action="delete_review.php" style="margin-top:15px;">
                            <input type="hidden" name="review_id" value="<?php echo $review["Review_ID"]; ?>">
                            <input type="hidden" name="movie_id" value="<?php echo $review["Movie_ID"]; ?>">
                            <button type="submit">Delete Review</button>
                        </form>

                    </div>

                <?php endwhile; ?>

            <?php endif; ?>

        </div>
    </section>

</main>

</body>
</html>
