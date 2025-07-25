<?php
session_start();
require 'config.php';

// Check if user is logged in and role is 'user'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit();
}

// Get selected category from query parameter and sanitize
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

if ($category === '') {
    // Redirect if no category specified
    header("Location: categories.php");
    exit();
}

// Prepare statement to fetch approved providers for the category (case-insensitive)
$stmt = $conn->prepare("SELECT * FROM service_providers WHERE LOWER(service_type) = LOWER(?) AND status = 'Approved'");
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($category) ?> Providers - Find Local Services</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #e9f2ff; /* Light blue background */
      padding: 20px;
    }
    .provider-card {
      border: 1px solid #0d6efd; /* Bootstrap primary blue border */
      border-radius: 8px;
      transition: box-shadow 0.3s ease;
    }
    .provider-card:hover {
      box-shadow: 0 4px 12px rgba(13, 110, 253, 0.5);
    }
    .provider-image {
      height: 180px;
      object-fit: cover;
      border-top-left-radius: 8px;
      border-top-right-radius: 8px;
    }
    .btn-book {
      background-color: #0d6efd;
      border: none;
      transition: background-color 0.3s ease;
    }
    .btn-book:hover {
      background-color: #084298;
    }
  </style>
</head>
<body>

  <div class="container">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="text-primary"><?= htmlspecialchars($category) ?> Providers</h1>
      <a href="categories.php" class="btn btn-outline-primary">Back to Categories</a>
    </div>

    <!-- Providers Grid -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
      <?php if ($result->num_rows > 0): ?>
        <?php while ($provider = $result->fetch_assoc()): ?>
          <div class="col">
            <div class="card provider-card h-100 d-flex flex-column">
              <!-- Provider Image -->
              <img 
                src="<?= !empty($provider['image']) ? 'uploads/providers/' . htmlspecialchars($provider['image']) : 'uploads/providers/default.jpg' ?>" 
                alt="<?= htmlspecialchars($provider['name']) ?>" 
                class="card-img-top provider-image"
                loading="lazy"
                onerror="this.onerror=null;this.src='uploads/providers/default.jpg';"
              />

              <div class="card-body d-flex flex-column">
                <h5 class="card-title text-primary"><?= htmlspecialchars($provider['name']) ?></h5>
                <p class="card-text mb-1"><strong>Email:</strong> <?= htmlspecialchars($provider['email']) ?></p>
                <p class="card-text mb-1"><strong>Phone:</strong> <?= htmlspecialchars($provider['phone']) ?></p>
                <p class="card-text mb-1"><strong>District:</strong> <?= htmlspecialchars($provider['district'] ?? 'N/A') ?></p>
                <p class="card-text mb-3"><strong>GN Division:</strong> <?= htmlspecialchars($provider['gn_division'] ?? 'N/A') ?></p>

                <a href="book_service.php?provider_id=<?= urlencode($provider['id']) ?>" class="btn btn-book mt-auto text-white">
                  Book Now
                </a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="text-center text-muted fs-5">No approved providers found in this category.</p>
      <?php endif; ?>
    </div>

  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
