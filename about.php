<?php
session_start();
$logged_in = isset($_SESSION['name']);
$name = $logged_in ? $_SESSION['name'] : '';
$role = $_SESSION['role'] ?? '';
$location = "Karainagar";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>About Us - Find Local Services in <?= htmlspecialchars($location) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    body {
      background-color: #f8f9fa;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }
    footer {
      background: #0d6efd;
      color: white;
      padding: 15px 0;
      text-align: center;
      margin-top: 40px;
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">Find Local Services - <?= htmlspecialchars($location) ?></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
      aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNavAltMarkup">
      <div class="navbar-nav align-items-center gap-3">
        <a class="nav-link" href="index.php">Home</a>
        <a class="nav-link active fw-semibold" href="about.php">About</a>

        <?php if ($logged_in): ?>
          <span class="text-white fw-semibold">👤 <?= htmlspecialchars(explode(' ', $name)[0]) ?> (<?= htmlspecialchars($role) ?>)</span>
          <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        <?php else: ?>
          <button class="btn btn-outline-light btn-sm" onclick="window.location.href='index.php#authModal'">Login</button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<!-- MAIN CONTENT -->
<div class="container my-5">
  <div class="bg-white rounded shadow-sm p-5">
    <h1 class="text-primary mb-4 text-center fw-bold">About Find Local Services</h1>
    
    <p class="lead text-secondary mb-4">
      Welcome to <strong>Find Local Services</strong>, your trusted platform dedicated to connecting you with reliable, skilled service providers in the Karainagar area.
    </p>
    
    <h2 class="text-primary fw-semibold mb-3">Our Mission</h2>
    <p class="text-secondary mb-4">
      We strive to make your life easier by providing a convenient way to find professional local electricians, plumbers, tutors, carpenters, beauticians, and more. Our goal is to help you hire trustworthy experts quickly and confidently.
    </p>

    <h2 class="text-primary fw-semibold mb-3">Why Choose Us?</h2>
    <ul class="list-group list-group-flush mb-4">
      <li class="list-group-item">Verified and trusted service providers from your local community.</li>
      <li class="list-group-item">Easy-to-use platform with clear service categories.</li>
      <li class="list-group-item">Secure user authentication for safe interactions.</li>
      <li class="list-group-item">Responsive design accessible from any device.</li>
    </ul>

    <h2 class="text-primary fw-semibold mb-3">Our Team</h2>
    <p class="text-secondary mb-4">
      Our team is passionate about building a community where people can connect and find the help they need. We continuously work to improve our platform and expand our network of skilled professionals.
    </p>

    <h2 class="text-primary fw-semibold mb-3">Get In Touch</h2>
    <p class="text-secondary mb-4">
      Reach out anytime — we’re here to help and support you with your local service needs.
    </p>

    <div class="text-center mt-4">
      <a href="index.php" class="btn btn-primary btn-lg fw-semibold px-5">Back to Home</a>
    </div>
  </div>
</div>

<footer>
  &copy; <?= date('Y') ?> Local Service Finder | Designed for Sri Lanka users
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
