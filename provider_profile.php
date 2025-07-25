<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'config.php';

$provider_id = $_SESSION['id'];
$errors = [];
$success_msg = "";

// Fetch provider info for display & prefill (before processing POST)
$stmt = $conn->prepare("SELECT * FROM service_providers WHERE id = ?");
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Provider profile not found.");
}
$provider = $result->fetch_assoc();
$stmt->close();

// Handle Profile Update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $service_type = trim($_POST['service_type']);
    $experience = trim($_POST['experience']);
    $district = trim($_POST['district']);
    $gn_division = trim($_POST['gn_division']);

    // Simple validations
    if (strlen($name) < 2) $errors[] = "Name must be at least 2 characters.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    if (!preg_match('/^[0-9]{10}$/', $phone)) $errors[] = "Phone must be 10 digits.";

    // Handle image upload if a file was submitted
    $new_image_name = $provider['image']; // keep old image if no new uploaded
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $max_size = 2 * 1024 * 1024; // 2 MB

        if ($_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Error uploading image.";
        } elseif (!in_array($_FILES['profile_image']['type'], $allowed_types)) {
            $errors[] = "Only JPG and PNG images are allowed.";
        } elseif ($_FILES['profile_image']['size'] > $max_size) {
            $errors[] = "Image size must be less than 2MB.";
        } else {
            // Generate unique filename
            $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $new_image_name = "provider_" . $provider_id . "_" . time() . "." . $ext;
            $upload_dir = __DIR__ . "/uploads/providers/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $target_path = $upload_dir . $new_image_name;

            if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_path)) {
                $errors[] = "Failed to move uploaded image.";
            } else {
                // Optionally delete old image file if exists and not default
                if (!empty($provider['image']) && file_exists($upload_dir . $provider['image'])) {
                    @unlink($upload_dir . $provider['image']);
                }
            }
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE service_providers SET name=?, email=?, phone=?, service_type=?, experience=?, district=?, gn_division=?, image=? WHERE id=?");
        $stmt->bind_param("ssssssssi", $name, $email, $phone, $service_type, $experience, $district, $gn_division, $new_image_name, $provider_id);
        if ($stmt->execute()) {
            $success_msg = "Profile updated successfully.";
            $_SESSION['name'] = $name;
            // Refresh provider data after update
            $provider['image'] = $new_image_name;
            $provider['name'] = $name;
            $provider['email'] = $email;
            $provider['phone'] = $phone;
            $provider['service_type'] = $service_type;
            $provider['experience'] = $experience;
            $provider['district'] = $district;
            $provider['gn_division'] = $gn_division;
        } else {
            $errors[] = "Database update failed: " . $conn->error;
        }
        $stmt->close();
    }
}

// Handle Password Change form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch current hashed password from DB
    $stmt = $conn->prepare("SELECT password FROM service_providers WHERE id = ?");
    $stmt->bind_param("i", $provider_id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($current_password, $hashed_password)) {
        $errors[] = "Current password is incorrect.";
    } elseif ($new_password !== $confirm_password) {
        $errors[] = "New password and confirm password do not match.";
    } elseif (strlen($new_password) < 6) {
        $errors[] = "New password must be at least 6 characters.";
    }

    if (empty($errors)) {
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE service_providers SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $new_hashed_password, $provider_id);
        if ($stmt->execute()) {
            $success_msg = "Password changed successfully.";
        } else {
            $errors[] = "Failed to update password: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Provider Profile</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
    .profile-img {
        max-width: 150px;
        max-height: 150px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #0d6efd;
    }
</style>
</head>
<body>
<div class="container mt-4 mb-5">
    <h2>My Profile</h2>

    <?php if ($errors): ?>
      <div class="alert alert-danger">
          <ul>
            <?php foreach ($errors as $error) echo "<li>" . htmlspecialchars($error) . "</li>"; ?>
          </ul>
      </div>
    <?php elseif ($success_msg): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
    <?php endif; ?>

    <div class="card p-4 mb-4 shadow-sm">
        <form method="POST" novalidate enctype="multipart/form-data">
            <input type="hidden" name="update_profile" value="1" />

            <div class="mb-3 text-center">
                <?php
                $img_path = !empty($provider['image']) && file_exists(__DIR__ . "/uploads/providers/" . $provider['image'])
                    ? "uploads/providers/" . htmlspecialchars($provider['image'])
                    : "uploads/providers/default.jpg";
                ?>
                <img src="<?= $img_path ?>" alt="Profile Image" class="profile-img mb-3" />
            </div>

            <div class="mb-3">
                <label for="profile_image" class="form-label">Change Profile Image</label>
                <input type="file" id="profile_image" name="profile_image" accept=".jpg,.jpeg,.png" class="form-control" />
                <div class="form-text">Allowed types: JPG, PNG. Max size: 2MB.</div>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" name="name" class="form-control" required minlength="2" value="<?= htmlspecialchars($provider['name']) ?>" />
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" required value="<?= htmlspecialchars($provider['email']) ?>" />
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone (10 digits)</label>
                <input type="text" id="phone" name="phone" pattern="^[0-9]{10}$" class="form-control" required value="<?= htmlspecialchars($provider['phone']) ?>" />
            </div>
            <div class="mb-3">
                <label for="service_type" class="form-label">Service Type</label>
                <input type="text" id="service_type" name="service_type" class="form-control" value="<?= htmlspecialchars($provider['service_type']) ?>" />
            </div>
            <div class="mb-3">
                <label for="experience" class="form-label">Experience</label>
                <input type="text" id="experience" name="experience" class="form-control" value="<?= htmlspecialchars($provider['experience']) ?>" />
            </div>
            <div class="mb-3">
                <label for="district" class="form-label">District</label>
                <input type="text" id="district" name="district" class="form-control" value="<?= htmlspecialchars($provider['district']) ?>" />
            </div>
            <div class="mb-3">
                <label for="gn_division" class="form-label">GN Division</label>
                <input type="text" id="gn_division" name="gn_division" class="form-control" value="<?= htmlspecialchars($provider['gn_division']) ?>" />
            </div>
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>

    <h3>Change Password</h3>
    <div class="card p-4 shadow-sm">
        <form method="POST" novalidate>
            <input type="hidden" name="change_password" value="1" />
            <div class="mb-3">
                <label for="current_password" class="form-label">Current Password</label>
                <input type="password" id="current_password" name="current_password" class="form-control" required minlength="6" />
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" id="new_password" name="new_password" class="form-control" required minlength="6" />
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required minlength="6" />
            </div>
            <button type="submit" class="btn btn-warning">Change Password</button>
        </form>
    </div>

    <a href="provider_dashboard.php" class="btn btn-secondary mt-4">Back to Dashboard</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Basic client-side validation for password match (optional)
document.querySelector('form[novalidate][method="POST"]:nth-of-type(2)')?.addEventListener('submit', function(e) {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    if (newPassword !== confirmPassword) {
        e.preventDefault();
        alert('New password and confirm password must match.');
    }
});
</script>

</body>
</html>
