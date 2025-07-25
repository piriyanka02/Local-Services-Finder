<?php
session_start();
require 'config.php';

// Require user login and role check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit();
}

$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

// Fetch categories with optional search
if ($search !== '') {
    $stmt = $conn->prepare("SELECT * FROM categories WHERE name LIKE ?");
    $likeSearch = "%$search%";
    $stmt->bind_param("s", $likeSearch);
} else {
    $stmt = $conn->prepare("SELECT * FROM categories");
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Categories - Find Local Services</title>

  <!-- Bootstrap 5 CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

  <style>
    body {
      background-color: #f8fafc; /* subtle light gray */
      min-height: 100vh;
      padding: 2rem 1rem;
    }
    .card-category {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      cursor: pointer;
      border-radius: 0.75rem;
      overflow: hidden;
      box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);
      background-color: #fff;
    }
    .card-category:hover {
      transform: translateY(-6px);
      box-shadow: 0 8px 20px rgb(0 0 0 / 0.15);
      text-decoration: none;
    }
    .card-category img {
      object-fit: contain;
      max-height: 180px;
      background-color: #fff;
      padding: 1rem;
      border-bottom: 1px solid #eee;
    }
    .card-category h5 {
      font-weight: 600;
      color: #0d6efd;
      margin-bottom: 0.5rem;
    }
    .card-category p {
      color: #555;
      font-size: 0.9rem;
      height: 3rem; /* limit height */
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .search-bar {
      max-width: 500px;
      margin: 0 auto 2rem auto;
    }
    .btn-teal {
      background-color: #14b8a6;
      border-color: #14b8a6;
      color: #fff;
    }
    .btn-teal:hover {
      background-color: #0d9488;
      border-color: #0d9488;
      color: #fff;
    }
  </style>
</head>
<body>

  <div class="container">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 text-primary">Service Categories</h1>
      <a href="user_dashboard.php" class="btn btn-teal">Dashboard</a>
    </div>

    <!-- Search Form -->
    <form method="GET" action="categories.php" class="d-flex justify-content-center search-bar" role="search" aria-label="Category Search Form">
      <input
        class="form-control me-2"
        type="search"
        name="search"
        placeholder="Search categories..."
        value="<?= htmlspecialchars($search) ?>"
        aria-label="Search categories"
        autofocus
      />
      <button class="btn btn-teal" type="submit">Search</button>
    </form>

    <!-- Categories Grid -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
      <?php if ($result->num_rows > 0): ?>
        <?php while ($cat = $result->fetch_assoc()): ?>
          <div class="col">
            <a href="category_details.php?category=<?= urlencode($cat['name']) ?>" class="card-category d-block text-decoration-none shadow-sm">
              <?php if (!empty($cat['image'])): ?>
                <img src="uploads/categories/<?= htmlspecialchars($cat['image']) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" class="w-100" />
              <?php else: ?>
                <div class="d-flex align-items-center justify-content-center bg-light text-muted" style="height:180px;">
                  No Image
                </div>
              <?php endif; ?>
              <div class="p-3">
                <h5><?= htmlspecialchars($cat['name']) ?></h5>
                <p title="<?= htmlspecialchars($cat['description']) ?>"><?= htmlspecialchars($cat['description']) ?></p>
              </div>
            </a>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="text-center text-muted mt-5 fs-5">No categories found.</p>
      <?php endif; ?>
    </div>

  </div>

  <!-- Bootstrap 5 JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
