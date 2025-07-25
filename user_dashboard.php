<?php
session_start();
require 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user' || !isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['id'];
$userName = htmlspecialchars($_SESSION['name']);

// Fetch bookings for this user
$sql = "SELECT b.id, sp.name AS provider_name, sp.service_type, sp.phone, b.date, b.preferred_time, b.status 
        FROM bookings b 
        JOIN service_providers sp ON b.provider_id = sp.id 
        WHERE b.user_id = ? 
        ORDER BY b.date DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL prepare error: " . $conn->error);
}
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}
$stmt->close();

// Handle cancel booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking_id'])) {
    $cancelId = intval($_POST['cancel_booking_id']);
    $delStmt = $conn->prepare("DELETE FROM bookings WHERE id = ? AND user_id = ?");
    $delStmt->bind_param("ii", $cancelId, $userId);
    $delStmt->execute();
    $delStmt->close();
    header("Location: user_dashboard.php"); // refresh after delete
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Dashboard - Find Local Services</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  
  <style>
    body {
      min-height: 100vh;
      display: flex;
      background: #f8f9fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .sidebar {
      background: linear-gradient(180deg, #0d6efd 0%, #0a58ca 100%);
      min-height: 100vh;
      padding: 1.5rem 1rem;
      width: 250px;
      display: flex;
      flex-direction: column;
      color: white;
      position: sticky;
      top: 0;
    }
    .sidebar h4 {
      margin-bottom: 2rem;
      font-weight: 700;
      text-align: center;
      text-transform: uppercase;
      letter-spacing: 1.5px;
    }
    .sidebar a {
      color: white;
      text-decoration: none;
      padding: 0.75rem 1rem;
      border-radius: 0.375rem;
      margin-bottom: 0.5rem;
      font-weight: 600;
      transition: background-color 0.3s ease;
      display: block;
    }
    .sidebar a:hover,
    .sidebar a.active {
      background-color: rgba(255, 255, 255, 0.2);
    }
    .btn-new-booking {
      margin-top: auto;
      background-color: #ffc107;
      color: #212529;
      font-weight: 600;
      padding: 0.75rem 1rem;
      border-radius: 0.375rem;
      text-align: center;
      cursor: pointer;
      text-decoration: none;
      transition: background-color 0.3s ease;
      display: block;
    }
    .btn-new-booking:hover {
      background-color: #e0a800;
      color: white;
    }
    .sidebar a.logout-btn {
      margin-top: 1rem;
      font-weight: 700;
      text-align: center;
      background: transparent;
      color: #ff4d4d;
      border-radius: 0.375rem;
      display: block;
      padding: 0.75rem 1rem;
      transition: background-color 0.3s ease;
    }
    .sidebar a.logout-btn:hover {
      background-color: rgba(255, 77, 77, 0.15);
      color: #ff0000;
      text-decoration: none;
    }
    main.content-area {
      flex-grow: 1;
      padding: 2rem;
      overflow-y: auto;
    }
    .booking-card {
      box-shadow: 0 4px 6px rgb(0 0 0 / 0.1);
      border-radius: 0.5rem;
      transition: box-shadow 0.3s ease;
    }
    .booking-card:hover {
      box-shadow: 0 6px 10px rgb(0 0 0 / 0.15);
    }
    .booking-status {
      font-weight: 700;
      padding: 0.2rem 0.6rem;
      border-radius: 0.375rem;
      font-size: 0.9rem;
    }
    .status-approved {
      background-color: #d1e7dd;
      color: #0f5132;
    }
    .status-pending {
      background-color: #fff3cd;
      color: #664d03;
    }
    .status-rejected {
      background-color: #f8d7da;
      color: #842029;
    }
  </style>
</head>
<body>

  <nav class="sidebar">
    <h4>USER PANEL</h4>
    <a href="user_dashboard.php" class="active">Dashboard</a>
    <a href="user_settings.php">User Settings</a>
    <a href="categories.php" class="btn-new-booking">+ New Booking</a>
    <a href="logout.php" class="logout-btn">Logout</a>
  </nav>

  <main class="content-area">
    <h2 class="mb-4">Welcome, <?= $userName ?> 👋</h2>

    <h3>Your Bookings</h3>

    <?php if (count($bookings) > 0): ?>
      <div class="row g-4 mt-3">
        <?php foreach ($bookings as $booking): ?>
          <div class="col-md-6 col-lg-4">
            <div class="booking-card p-4 bg-white">
              <h5 class="mb-2 text-primary"><?= htmlspecialchars($booking['provider_name']) ?></h5>
              <p class="mb-1"><strong>Service:</strong> <?= htmlspecialchars($booking['service_type']) ?></p>
              <p class="mb-1"><strong>Phone:</strong> <?= htmlspecialchars($booking['phone']) ?></p>
              <p class="mb-1"><strong>Date:</strong> <?= date('M d, Y', strtotime($booking['date'])) ?></p>
              <p class="mb-3"><strong>Time:</strong> <?= htmlspecialchars($booking['preferred_time']) ?></p>

              <?php
                $statusClass = '';
                if ($booking['status'] === 'Approved') {
                  $statusClass = 'status-approved';
                } elseif ($booking['status'] === 'Pending') {
                  $statusClass = 'status-pending';
                } elseif ($booking['status'] === 'Rejected') {
                  $statusClass = 'status-rejected';
                }
              ?>
              <span class="booking-status <?= $statusClass ?>"><?= htmlspecialchars($booking['status']) ?></span>

              <form method="POST" class="mt-3" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                <input type="hidden" name="cancel_booking_id" value="<?= $booking['id'] ?>" />
                <button type="submit" class="btn btn-sm btn-danger w-100">Cancel Booking</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="alert alert-info mt-4" role="alert">
        You haven't made any bookings yet. <a href="categories.php" class="alert-link">Book a service now!</a>
      </div>
    <?php endif; ?>
  </main>

  <!-- Bootstrap JS bundle (with Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
