<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo '<div class="alert alert-danger p-3 m-3">Access Denied.</div>';
    exit;
}

$adminEmail = $_SESSION['email'] ?? '';

if (empty($adminEmail)) {
    echo '<div class="alert alert-danger p-3 m-3">Admin not found.</div>';
    exit;
}

// Fetch admin details
$stmt = $conn->prepare("SELECT name, email, password, profile_pic, email_alerts FROM admin WHERE email = ?");
$stmt->bind_param("s", $adminEmail);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    echo '<div class="alert alert-danger p-3 m-3">Admin not found.</div>';
    exit;
}

$admin = $result->fetch_assoc();

$successMsg = '';
$errorMsg = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $newPassword = $_POST['password'];
    $email_alerts = isset($_POST['email_alerts']) ? 1 : 0;

    // Profile pic upload
    $profile_pic = $admin['profile_pic'];
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
        $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($ext), $allowed)) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $imgName = $uploadDir . 'admin_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $imgName)) {
                $profile_pic = $imgName;
            } else {
                $errorMsg = 'Failed to upload profile picture.';
            }
        } else {
            $errorMsg = 'Invalid image file type. Allowed: jpg, jpeg, png, gif.';
        }
    }

    if (empty($errorMsg)) {
        if (!empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE admin SET name=?, password=?, profile_pic=?, email_alerts=? WHERE email=?");
            $update->bind_param("sssis", $name, $hashedPassword, $profile_pic, $email_alerts, $adminEmail);
        } else {
            $update = $conn->prepare("UPDATE admin SET name=?, profile_pic=?, email_alerts=? WHERE email=?");
            $update->bind_param("ssis", $name, $profile_pic, $email_alerts, $adminEmail);
        }

        if ($update->execute()) {
            $successMsg = 'Profile updated successfully!';
            // Refresh admin data after update
            $admin['name'] = $name;
            $admin['profile_pic'] = $profile_pic;
            $admin['email_alerts'] = $email_alerts;
        } else {
            $errorMsg = 'Error updating profile. Please try again.';
        }
    }
}
?>

<div class="container my-4" style="max-width: 600px;">
  <div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
      <h4 class="mb-0"><i class="fas fa-cog"></i> Admin Settings & Profile</h4>
    </div>
    <div class="card-body">
      
      <?php if ($successMsg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($successMsg) ?></div>
      <?php endif; ?>
      <?php if ($errorMsg): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errorMsg) ?></div>
      <?php endif; ?>

      <form action="" method="POST" enctype="multipart/form-data" novalidate>
        <div class="mb-3">
          <label for="name" class="form-label fw-semibold">Name</label>
          <input
            type="text"
            class="form-control"
            id="name"
            name="name"
            required
            value="<?= htmlspecialchars($admin['name']) ?>"
          >
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Email (read-only)</label>
          <input type="email" class="form-control" value="<?= htmlspecialchars($admin['email']) ?>" readonly>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label fw-semibold">
            New Password <small class="text-muted">(Leave blank to keep current)</small>
          </label>
          <input
            type="password"
            class="form-control"
            id="password"
            name="password"
            placeholder="Enter new password"
          >
        </div>

        <div class="mb-3">
          <label for="profile_pic" class="form-label fw-semibold">Profile Picture</label>
          <input
            class="form-control"
            type="file"
            id="profile_pic"
            name="profile_pic"
            accept="image/*"
          >
          <?php if (!empty($admin['profile_pic'])): ?>
            <div class="mt-3 text-center">
              <img
                src="<?= htmlspecialchars($admin['profile_pic']) ?>"
                alt="Profile Picture"
                class="rounded-circle border border-primary"
                style="width: 120px; height: 120px; object-fit: cover;"
              >
            </div>
          <?php endif; ?>
        </div>

        <div class="form-check mb-4">
          <input
            class="form-check-input"
            type="checkbox"
            id="email_alerts"
            name="email_alerts"
            <?= $admin['email_alerts'] ? 'checked' : '' ?>
          >
          <label class="form-check-label" for="email_alerts">
            Email me when a provider registers or a booking is made
          </label>
        </div>

        <button type="submit" class="btn btn-primary w-100 fw-bold">Save Changes</button>
      </form>
    </div>
  </div>
</div>
