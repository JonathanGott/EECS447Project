<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <title>Movie Review</title>

  <link rel="stylesheet" href="style.css" />
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
    <p class="eyebrow">MOVIE REVIEWS</p>

    <h2>Search any movie</h2>

    <p class="subtitle">
      Search for movies, view ratings, and submit your own review.
    </p>

    <form class="search-box" method="GET" action="search.php">
      <input 
        type="text" 
        name="search" 
        placeholder="Search by movie title..." 
        required 
      />

      <button type="submit">Search</button>
    </form>
  </section>

</main>

</body>
</html>
