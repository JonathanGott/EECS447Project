<?php
session_start();
include "db.php";

// Get search input
$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";

// Prepare query (dynamic query requirement)
$sql = "SELECT * FROM Movie WHERE Title LIKE ?";
$stmt = mysqli_prepare($conn, $sql);

// Add wildcards for LIKE
$searchParam = "%" . $search . "%";

mysqli_stmt_bind_param($stmt, "s", $searchParam);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
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

    <section class="hero-card">
        <p class="eyebrow">SEARCH RESULTS</p>
        <h2>Results for "<?php echo htmlspecialchars($search); ?>"</h2>
    </section>

    <section class="section">
        <div class="movie-grid">

            <?php if (mysqli_num_rows($result) === 0): ?>
                <p style="color:#b8beca;">No movies found.</p>

            <?php else: ?>
                <?php while ($movie = mysqli_fetch_assoc($result)): ?>

                    <div class="movie-card">
                        <h3><?php echo htmlspecialchars($movie["Title"]); ?></h3>
                        <p>
                            <?php echo htmlspecialchars($movie["Genre"]); ?> • 
                            <?php echo htmlspecialchars($movie["Release_Year"]); ?>
                        </p>
                        <p><?php echo htmlspecialchars($movie["Director"]); ?></p>

                        <a href="movie.php?id=<?php echo $movie["Movie_ID"]; ?>" class="card-link">
                            View Movie
                        </a>
                    </div>

                <?php endwhile; ?>
            <?php endif; ?>

        </div>
    </section>

</main>

</body>
</html>
