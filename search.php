<?php
session_start();
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['name'])) {
    // User not logged in, redirect to login or show message
    $_SESSION['error'] = "Please login to view service provider details.";
    header('Location: index.php'); // or login.php if you have separate login page
    exit();
}

$name = $_SESSION['name'] ?? '';

// Get search query
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Search Results</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container py-4">
  <div class="mb-4">
    <h1>Welcome, <span class="text-primary"><?= htmlspecialchars(explode(' ', $name)[0]) ?></span>!</h1>
    <p class="lead">Here are your search results:</p>
  </div>

  <h3 class="mb-4">Search Results for: <em><?= htmlspecialchars($query) ?></em></h3>

  <?php
  if ($query === '') {
      echo "<p class='text-warning'>Please enter a search keyword.</p>";
  } else {
      $stmt = $conn->prepare("SELECT * FROM service_providers WHERE (service_type LIKE ? OR name LIKE ?) AND status = 'Approved'");
      $searchTerm = "%$query%";
      $stmt->bind_param("ss", $searchTerm, $searchTerm);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows === 0) {
          echo "<p class='text-muted'>No service providers found for your query.</p>";
      } else {
          echo '<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">';
          while ($row = $result->fetch_assoc()) {
              ?>
              <div class="col">
                <div class="card h-100 shadow-sm">
                  <?php if (!empty($row['image'])): ?>
                    <img src="uploads/providers/<?= htmlspecialchars($row['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>" style="height:200px; object-fit:cover;">
                  <?php else: ?>
                    <img src="uploads/providers/default.jpg" class="card-img-top" alt="Default Image" style="height:200px; object-fit:cover;">
                  <?php endif; ?>
                  <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                    <p class="card-text mb-1"><strong>Service:</strong> <?= htmlspecialchars($row['service_type']) ?></p>
                    <?php if (!empty($row['experience'])): ?>
                      <p class="card-text mb-2"><strong>Experience:</strong> <?= htmlspecialchars($row['experience']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($row['district'])): ?>
                      <p class="card-text mb-2"><strong>District:</strong> <?= htmlspecialchars($row['district']) ?></p>
                    <?php endif; ?>
                    <a href="book_service.php?provider_id=<?= urlencode($row['id']) ?>" class="btn btn-primary mt-auto">Book Now</a>
                  </div>
                </div>
              </div>
              <?php
          }
          echo '</div>';
      }

      $stmt->close();
  }
  $conn->close();
  ?>
</div>

<!-- Bootstrap JS Bundle CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
