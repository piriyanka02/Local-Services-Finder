<?php
session_start();
require 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user' || !isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['id'];
$userName = htmlspecialchars($_SESSION['name']);
$success_message = "";
$error_message = "";

// Fetch current user info
$userSql = "SELECT name, email, phone FROM users WHERE id = ?";
$stmt = $conn->prepare($userSql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();
$stmt->close();

// Handle form submission to update user info
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $newName = trim($_POST['name']);
    $newEmail = trim($_POST['email']);
    $newPhone = trim($_POST['phone']);

    // Simple validation
    if (empty($newName) || empty($newEmail)) {
        $error_message = "Name and Email cannot be empty.";
    } else {
        // Update query
        $updateSql = "UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("sssi", $newName, $newEmail, $newPhone, $userId);
        if ($stmt->execute()) {
            $success_message = "Profile updated successfully.";
            $_SESSION['name'] = $newName; // update session name
            // Refresh user data
            $user['name'] = $newName;
            $user['email'] = $newEmail;
            $user['phone'] = $newPhone;
        } else {
            $error_message = "Error updating profile.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Settings - Find Local Services</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  
  <style>
    body {
      min-height: 100vh;
      display: flex;
      background: #f8f9fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
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
      user-select: none;
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
      background: white;
      border-radius: 0.5rem;
      box-shadow: 0 4px 6px rgb(0 0 0 / 0.1);
      max-width: 700px;
      margin: 2rem auto;
    }
    h2, h3 {
      color: #0d6efd;
    }
  </style>
</head>
<body>

  <nav class="sidebar">
    <h4>USER PANEL</h4>
    <a href="user_dashboard.php">Dashboard</a>
    <a href="user_settings.php" class="active">User Settings</a>
    <a href="categories.php" class="btn-new-booking">+ New Booking</a>
    <a href="logout.php" class="logout-btn">Logout</a>
  </nav>

  <main class="content-area">
    <h2>User Settings</h2>
    <p>Welcome, <?= $userName ?></p>

    <?php if ($success_message): ?>
      <div class="alert alert-success"><?= $success_message ?></div>
    <?php endif; ?>
    <?php if ($error_message): ?>
      <div class="alert alert-danger"><?= $error_message ?></div>
    <?php endif; ?>

    <form method="POST" class="mt-4">
      <input type="hidden" name="update_user" value="1" />
      <div class="mb-3">
        <label for="name" class="form-label">Name *</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email *</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="phone" class="form-label">Phone</label>
        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" class="form-control" />
      </div>

      <button type="submit" class="btn btn-primary w-100">Update Profile</button>
    </form>
  </main>

  <!-- Bootstrap JS bundle (with Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
