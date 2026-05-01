<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />

  <!-- Makes site responsive -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <title>Movie Review</title>

  <!-- Link CSS -->
  <link rel="stylesheet" href="style.css" />
</head>
<body>

<!--  NAVBAR   -->
<header class="navbar">
  <h1>Movie Review </h1>

  <!-- Required: login/register (from your PDF) -->
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

  <!--  HERO / SEARCH SECTION  -->
  <!-- This is the main entry point of the app -->
  <section class="hero-card">

    <p class="eyebrow">MOVIE REVIEWS</p>

    <h2>Search any movie</h2>

    <p class="subtitle">
      Search for movies, view ratings, and submit your own review.
    </p>

    <!-- FORM: connects to backend search -->
    <!-- This will become a dynamic query -->
    <form class="search-box" method="GET" action="search.php">

      <!-- User input -->
      <input 
        type="text" 
        name="search" 
        placeholder="Search by movie title..." 
        required 
      />

      <button type="submit">Search</button>

    </form>
  </section>


  <!--  SEARCH RESULTS  -->
  <!-- PHP will dynamically generate movie results here -->
  <section class="section">

    <h2>Search Results</h2>

    <div class="movie-grid">

      <!-- Example structure ONLY (will be replaced by PHP loop) -->
      <!-- Each movie should link to its own page -->

      <div class="movie-card">
        <h3>Movie Title</h3>
        <p>Genre • Year</p>
        <p>Director</p>

        <!-- Pass movie ID to backend -->
        <a href="movie.php?id=1" class="card-link">View Movie</a>
      </div>

    </div>
  </section>


  <!--  MOVIE DETAILS  -->
  <!-- This section will be its own page (movie.php) -->
  <section class="section">

    <div class="details-card">

      <!-- LEFT: Movie info from database -->
      <div>

        <p class="eyebrow">MOVIE DETAILS</p>

        <h2>Movie Title</h2>

        <p>Genre • Year • Director</p>

        <!-- Must calculate average rating (requirement) -->
        <p class="average-rating">Average Rating: 0 / 5</p>

      </div>


      <!-- RIGHT: Review form -->
      <!-- This fulfills CREATE REVIEW requirement -->
      <form class="review-form" method="POST" action="submit_review.php">

        <h3>Rate this movie</h3>

        <!-- Hidden movie ID -->
        <input type="hidden" name="movie_id" value="1" />

        <!-- Rating input -->
        <label>Rating</label>
        <select name="rating" required>
          <option value="">Choose rating</option>
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

        <!-- Optional comment -->
        <label>Comment (optional)</label>
        <textarea name="comment" placeholder="Write your review..."></textarea>

        <button type="submit">Submit Review</button>

      </form>

    </div>
  </section>


  <!--  REVIEWS  -->
  <!-- PHP will pull reviews using JOIN (User + Review) -->
  <section class="section">

    <h2>User Reviews</h2>

    <div class="reviews-list">

      <!-- Example ONLY -->
      <!-- Will be generated with SQL JOIN -->
      <div class="review-card">
        <p class="stars">Rating: 0 / 5</p>
        <h3>Username</h3>
        <p>Review text will appear here.</p>
      </div>

    </div>

  </section>

</main>

</body>
</html>
