<?php
session_start();

// Restrict access to admins only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

require 'config.php'; // DB connection

$adminName = htmlspecialchars($_SESSION['name']);

// Fetch summary counts
$userCount = 0;
$providerCount = 0;
$categoryCount = 0;

$resultUsers = $conn->query("SELECT COUNT(*) AS count FROM users");
if ($resultUsers) {
    $userCount = $resultUsers->fetch_assoc()['count'];
}

$resultProviders = $conn->query("SELECT COUNT(*) AS count FROM service_providers");
if ($resultProviders) {
    $providerCount = $resultProviders->fetch_assoc()['count'];
}

$resultCategories = $conn->query("SELECT COUNT(*) AS count FROM categories");
if ($resultCategories) {
    $categoryCount = $resultCategories->fetch_assoc()['count'];
}

$page = $_GET['page'] ?? 'dashboard';

function activeClass($p) {
    global $page;
    return $page === $p ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Panel - Local Service Finder</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Font Awesome CDN for icons -->
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    rel="stylesheet"
  />
  <style>
    body {
      background-color: #f8f9fa;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }
    .sidebar {
      background: linear-gradient(180deg, #0d6efd 0%, #0a58ca 100%);
      min-height: 100vh;
      padding: 1.5rem 1rem;
      box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
      position: sticky;
      top: 0;
      width: 250px;
      display: flex;
      flex-direction: column;
    }
    .sidebar h2 {
      letter-spacing: 1px;
      color: white;
      text-align: center;
      margin-bottom: 2rem;
    }
    .sidebar-link {
      color: #f8f9fa;
      text-decoration: none;
      display: flex;
      align-items: center;
      padding: 0.75rem 1rem;
      border-radius: 0.5rem;
      font-weight: 600;
      font-size: 1.1rem;
      margin-bottom: 0.6rem;
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }
    .sidebar-link:hover {
      background-color: #0747a6;
      box-shadow: 0 4px 8px rgba(7, 71, 166, 0.3);
      color: #ffffff;
      text-decoration: none;
    }
    .sidebar-link.active {
      background-color: #ffc107;
      color: #212529;
      box-shadow: 0 4px 12px rgba(255, 193, 7, 0.6);
    }
    .sidebar-link i {
      font-size: 1.2rem;
      margin-right: 0.8rem;
    }
    .sidebar-logout {
      background-color: #dc3545;
      padding: 0.75rem 1rem;
      border-radius: 0.5rem;
      font-weight: 700;
      font-size: 1.1rem;
      color: #fff !important;
      text-align: center;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-top: auto;
      transition: background-color 0.3s ease;
      text-decoration: none;
    }
    .sidebar-logout:hover {
      background-color: #a71d2a;
      color: #fff !important;
      box-shadow: 0 4px 12px rgba(167, 29, 42, 0.6);
      text-decoration: none;
    }
    main {
      padding: 2rem;
      min-height: 100vh;
      flex-grow: 1;
      background: white;
      border-radius: 0.5rem;
      margin: 1rem;
      box-shadow: 0 0 15px rgb(0 0 0 / 0.1);
    }
  </style>
</head>
<body>

<div class="d-flex">

  <!-- Sidebar -->
  <aside class="sidebar">
    <h2><i class="fas fa-tools"></i> Admin Panel</h2>
    <nav class="flex-grow-1 d-flex flex-column">
      <a href="admin_panel.php?page=dashboard" class="sidebar-link <?= activeClass('dashboard') ?>">
        <i class="fas fa-chart-line"></i> Dashboard
      </a>
      <a href="admin_panel.php?page=users" class="sidebar-link <?= activeClass('users') ?>">
        <i class="fas fa-users"></i> Manage Users
      </a>
      <a href="admin_panel.php?page=providers" class="sidebar-link <?= activeClass('providers') ?>">
        <i class="fas fa-user-tie"></i> Manage Providers
      </a>
      <a href="admin_panel.php?page=categories" class="sidebar-link <?= activeClass('categories') ?>">
        <i class="fas fa-list-alt"></i> Manage Categories
      </a>
      <a href="admin_panel.php?page=admin_settings" class="sidebar-link <?= activeClass('admin_settings') ?>">
        <i class="fas fa-cog"></i> Admin Settings
      </a>
      <a href="logout.php" class="sidebar-logout">
        <i class="fas fa-sign-out-alt"></i> Logout
      </a>
    </nav>
  </aside>

  <!-- Main content -->
  <main>
    <h1 class="mb-4 fw-bold text-primary">Welcome, <?= $adminName ?></h1>

    <?php
    switch ($page) {
        case 'users':
            $file = 'admin_manage_users.php';
            break;
        case 'providers':
            $file = 'admin_manage_providers.php';
            break;
        case 'categories':
            $file = 'admin_manage_categories.php';
            break;
        case 'admin_settings':
            $file = 'admin_settings.php';
            break;
        default:
            $file = null;
            break;
    }

    if ($file && file_exists($file)) {
        include $file;
    } else {
        // Dashboard content
        ?>
        <div class="p-4 bg-light rounded">
          <h2 class="mb-4 fw-semibold">Dashboard Overview</h2>

          <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
              <div class="card text-center text-primary shadow-sm">
                <div class="card-body">
                  <h3 class="card-title display-5 fw-bold"><?= $userCount ?></h3>
                  <p class="card-text fs-5">Registered Users</p>
                </div>
              </div>
            </div>
            <div class="col">
              <div class="card text-center text-primary shadow-sm">
                <div class="card-body">
                  <h3 class="card-title display-5 fw-bold"><?= $providerCount ?></h3>
                  <p class="card-text fs-5">Service Providers</p>
                </div>
              </div>
            </div>
            <div class="col">
              <div class="card text-center text-primary shadow-sm">
                <div class="card-body">
                  <h3 class="card-title display-5 fw-bold"><?= $categoryCount ?></h3>
                  <p class="card-text fs-5">Categories</p>
                </div>
              </div>
            </div>
          </div>

          <p class="mt-4 text-secondary">Use the sidebar to manage users, service providers, categories, and settings.</p>
        </div>
        <?php
    }
    ?>
  </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
