<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'provider') {
    header("Location: index.php");
    exit();
}

require 'config.php';

$providerId = (int) $_SESSION['id'];
$providerName = htmlspecialchars($_SESSION['name']);

// Toggle availability if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
    $statusResult = $conn->query("SELECT availability FROM service_providers WHERE id = $providerId");
    $currentStatus = $statusResult ? $statusResult->fetch_assoc()['availability'] : 'unavailable';
    $newStatus = ($currentStatus === 'available') ? 'unavailable' : 'available';

    $conn->query("UPDATE service_providers SET availability = '$newStatus' WHERE id = $providerId");

    // Redirect to avoid form resubmission
    header("Location: provider_dashboard.php?page=dashboard");
    exit();
}

$page = $_GET['page'] ?? 'dashboard';

// Fetch provider details
$providerDetailsResult = $conn->query("SELECT name, email, phone, experience, availability FROM service_providers WHERE id = $providerId");
$providerDetails = $providerDetailsResult ? $providerDetailsResult->fetch_assoc() : null;

// Booking counts
$totalBookings = $conn->query("SELECT COUNT(*) AS total FROM bookings WHERE provider_id = $providerId")->fetch_assoc()['total'];
$approvedBookings = $conn->query("SELECT COUNT(*) AS approved FROM bookings WHERE provider_id = $providerId AND status = 'approved'")->fetch_assoc()['approved'];
$rejectedBookings = $conn->query("SELECT COUNT(*) AS rejected FROM bookings WHERE provider_id = $providerId AND status = 'rejected'")->fetch_assoc()['rejected'];
$pendingBookings = $conn->query("SELECT COUNT(*) AS pending FROM bookings WHERE provider_id = $providerId AND status = 'pending'")->fetch_assoc()['pending'];

function activeClass($p) {
    global $page;
    return $page === $p ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Provider Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #212529;
    }
    .sidebar {
      background: linear-gradient(180deg, #0d6efd 0%, #0a58ca 100%);
      min-height: 100vh;
      padding: 1.5rem 1rem;
      width: 250px;
      display: flex;
      flex-direction: column;
      color: white;
      box-shadow: 3px 0 10px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
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
      font-weight: 600;
      font-size: 1.1rem;
      border-radius: 0.5rem;
      margin-bottom: 0.5rem;
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
      background: white;
      border-radius: 0.5rem;
      margin: 1rem;
      flex-grow: 1;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      min-height: 100vh;
      color: #212529;
    }
    /* Card styles */
    .card {
      border-radius: 0.5rem;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }
    .text-primary {
      color: #0d6efd !important;
    }
    .display-6 {
      font-weight: 700;
      color: #0d6efd;
    }
  </style>
</head>
<body>
<div class="d-flex">
  <!-- Sidebar -->
  <aside class="sidebar">
    <h2><i class="fas fa-user-cog"></i> Provider</h2>
    <nav class="flex-grow-1 d-flex flex-column">
      <a href="provider_dashboard.php?page=dashboard" class="sidebar-link <?= activeClass('dashboard') ?>">
        <i class="fas fa-chart-line"></i> Dashboard
      </a>
      <a href="provider_dashboard.php?page=bookings" class="sidebar-link <?= activeClass('bookings') ?>">
        <i class="fas fa-calendar-check"></i> My Bookings
      </a>
      <a href="provider_dashboard.php?page=profile" class="sidebar-link <?= activeClass('profile') ?>">
        <i class="fas fa-user"></i> My Profile
      </a>
      <a href="logout.php" class="sidebar-logout">
        <i class="fas fa-sign-out-alt"></i> Logout
      </a>
    </nav>
  </aside>

  <!-- Main content -->
  <main>
    <h1 class="mb-4 fw-bold text-primary">Welcome, <?= $providerName ?></h1>

    <?php
    if ($page === 'bookings') {
        include 'provider_bookings.php';
    } elseif ($page === 'profile') {
        include 'provider_profile.php';
    } else {
        // DASHBOARD PAGE

        if (!$providerDetails) {
            echo '<div class="alert alert-danger">Provider details not found.</div>';
        } else {
    ?>

    <!-- Provider Details Card -->
    <div class="card mb-4 shadow-sm">
      <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Your Profile Details</h4>
      </div>
      <div class="card-body">
        <p><strong>Name:</strong> <?= htmlspecialchars($providerDetails['name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($providerDetails['email']) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($providerDetails['phone']) ?></p>
        <p><strong>Experience:</strong> <?= htmlspecialchars($providerDetails['experience']) ?> years</p>

        <!-- Availability toggle button -->
        <form method="post" action="" class="mt-3">
          <button type="submit" name="toggle_status" class="btn btn-<?= $providerDetails['availability'] === 'available' ? 'success' : 'secondary' ?>">
            <?= ucfirst($providerDetails['availability']) ?>
          </button>
        </form>
      </div>
    </div>

    <!-- Booking Stats -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card text-center text-primary">
          <div class="card-body">
            <h5>Total Bookings</h5>
            <p class="display-6"><?= $totalBookings ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center text-success">
          <div class="card-body">
            <h5>Approved</h5>
            <p class="display-6"><?= $approvedBookings ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center text-warning">
          <div class="card-body">
            <h5>Pending</h5>
            <p class="display-6"><?= $pendingBookings ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center text-danger">
          <div class="card-body">
            <h5>Rejected</h5>
            <p class="display-6"><?= $rejectedBookings ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter Dropdown -->
    <form method="GET" class="mb-3">
      <input type="hidden" name="page" value="dashboard">
      <select name="filter" class="form-select w-25" onchange="this.form.submit()">
        <option value="">-- Filter by Status --</option>
        <option value="all" <?= ($_GET['filter'] ?? '') === 'all' ? 'selected' : '' ?>>All</option>
        <option value="pending" <?= ($_GET['filter'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="approved" <?= ($_GET['filter'] ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
        <option value="rejected" <?= ($_GET['filter'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
      </select>
    </form>

    <!-- Booking Table -->
    <table class="table table-bordered">
      <thead class="table-primary">
        <tr>
          <th>User</th>
          <th>Service</th>
          <th>Preferred Date</th>
          <th>Preferred Time</th>
          <th>Message</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $filter = $_GET['filter'] ?? 'all';
        $sql = "SELECT b.*, u.name AS user_name FROM bookings b 
                JOIN users u ON b.user_id = u.id 
                WHERE b.provider_id = $providerId";
        if (in_array($filter, ['pending', 'approved', 'rejected'])) {
            $sql .= " AND b.status = '$filter'";
        }
        $sql .= " ORDER BY b.date DESC";

        $results = $conn->query($sql);
        if ($results && $results->num_rows > 0) {
            while ($row = $results->fetch_assoc()) {
                echo "<tr>
                        <td>".htmlspecialchars($row['user_name'])."</td>
                        <td>".htmlspecialchars($row['service_type'])."</td>
                        <td>".htmlspecialchars($row['preferred_date'])."</td>
                        <td>".htmlspecialchars($row['preferred_time'])."</td>
                        <td>".htmlspecialchars($row['message'])."</td>
                        <td><span class='badge bg-" . 
                            ($row['status'] == 'approved' ? 'success' : ($row['status'] == 'pending' ? 'warning' : 'danger')) .
                            "'>" . ucfirst(htmlspecialchars($row['status'])) . "</span></td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='6' class='text-center'>No bookings found</td></tr>";
        }
        ?>
      </tbody>
    </table>

    <?php
        } // end else provider details found
    } // end page dashboard else
    ?>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
