<?php
session_start();
require 'config.php';

// Ensure user is logged in as 'user'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['id'];
$provider_id = isset($_GET['provider_id']) ? intval($_GET['provider_id']) : 0;

// Fetch provider info
$stmt = $conn->prepare("SELECT * FROM service_providers WHERE id = ? AND status = 'Approved'");
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$provider = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$provider) {
    echo "<p class='alert alert-danger text-center mt-5'>Provider not found or not approved.</p>";
    exit();
}

// Handle booking form submission
$success = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['preferred_date'];
    $time = $_POST['preferred_time'];
    $message = trim($_POST['message']);

    $stmt = $conn->prepare("INSERT INTO bookings (user_id, provider_id, service_type, preferred_date, preferred_time, message) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissss", $user_id, $provider_id, $provider['service_type'], $date, $time, $message);
    $stmt->execute();
    $stmt->close();

    $success = "Booking successful!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Book <?= htmlspecialchars($provider['name']) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #e9f2ff; /* Light blue */
      padding: 2rem;
    }
    .card {
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(13,110,253,0.15);
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

  <div class="container" style="max-width:600px;">
    <div class="card p-4 bg-white">
      <h2 class="mb-4 text-primary">Book Service: <?= htmlspecialchars($provider['name']) ?></h2>

      <!-- Provider Details -->
      <div class="mb-4 p-3 bg-light rounded">
        <p><strong>Service:</strong> <?= htmlspecialchars($provider['service_type']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($provider['email']) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($provider['phone']) ?></p>
        <p><strong>District:</strong> <?= htmlspecialchars($provider['district'] ?? 'N/A') ?></p>
        <p><strong>GN Division:</strong> <?= htmlspecialchars($provider['gn_division'] ?? 'N/A') ?></p>
      </div>

      <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
        <a href="categories.php" class="btn btn-outline-primary">← Back to Categories</a>
      <?php else: ?>
        <!-- Booking Form -->
        <form method="POST" class="needs-validation" novalidate>
          <div class="mb-3">
            <label for="preferred_date" class="form-label">Preferred Date</label>
            <input type="date" id="preferred_date" name="preferred_date" class="form-control" required />
            <div class="invalid-feedback">Please select a date.</div>
          </div>

          <div class="mb-3">
            <label for="preferred_time" class="form-label">Preferred Time</label>
            <input type="time" id="preferred_time" name="preferred_time" class="form-control" required />
            <div class="invalid-feedback">Please select a time.</div>
          </div>

          <div class="mb-3">
            <label for="message" class="form-label">Additional Message (optional)</label>
            <textarea id="message" name="message" rows="3" class="form-control" placeholder="Any additional info..."></textarea>
          </div>

          <div class="d-flex justify-content-between align-items-center">
            <button type="submit" class="btn btn-book text-white px-4 py-2">Book Now</button>
            <a href="categories.php" class="btn btn-link">Cancel</a>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Bootstrap validation example
    (() => {
      'use strict';
      const forms = document.querySelectorAll('.needs-validation');
      Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
          if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
          }
          form.classList.add('was-validated');
        }, false);
      });
    })();
  </script>

</body>
</html>

<?php $conn->close(); ?>
