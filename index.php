<?php
session_start();
require 'config.php'; // $conn is your mysqli connection

$logged_in = isset($_SESSION['name']);
$name = $logged_in ? $_SESSION['name'] : '';
$role = $_SESSION['role'] ?? '';
$location = "Sri Lanka";

// Fetch all districts
$districts_result = $conn->query("SELECT id, district_name FROM districts ORDER BY district_name ASC");
$districts = $districts_result ? $districts_result->fetch_all(MYSQLI_ASSOC) : [];

// Categories array
$categories = [
    ["Electricians", "bolt", "electrician", "Electrical repairs and installations"],
    ["Mechanics", "tools", "mechanic", "Vehicle repairs and maintenance"],
    ["Home Tutors", "chalkboard-teacher", "tutor", "Personalized teaching at home"],
    ["Carpenters", "hammer", "carpenter", "Woodwork and furniture repairs"],
    ["Tailors", "cut", "tailor", "Clothing alterations and stitching"],
    ["Beauticians", "spa", "beautician", "Beauty treatments and care"],
    ["Painters", "brush", "painter", "House and furniture painting"],
    ["Plumbers", "wrench", "plumber", "Plumbing repairs and installations"]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Find Local Services in <?= htmlspecialchars($location) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<style>
  .category-card:hover { box-shadow: 0 0 15px rgba(0,123,255,.5); cursor: pointer; }
  footer { background: #0d6efd; color: white; padding: 15px 0; text-align: center; }
  /* Navbar custom */
  .navbar-brand {
    font-weight: 700;
    font-size: 1.5rem;
  }
  .nav-link {
    font-weight: 500;
    font-size: 1.1rem;
    margin-left: 1rem;
    transition: color 0.3s ease;
  }
  .nav-link:hover, .nav-link.active {
    color: #ffc107 !important; /* Bootstrap warning color for highlight */
  }
  .btn-outline-light {
    border-radius: 25px;
    padding: 5px 20px;
    font-weight: 600;
    transition: background-color 0.3s ease, color 0.3s ease;
  }
  .btn-outline-light:hover {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
  }
</style>
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="#">Local Service Finder - Sri Lanka</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarRight" aria-controls="navbarRight" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarRight">
      <ul class="navbar-nav align-items-center">
        <li class="nav-item">
          <a href="#" class="nav-link active">Home</a>
        </li>
        <li class="nav-item">
          <a href="about.php" class="nav-link">About</a>
        </li>
        <li class="nav-item ms-3">
          <?php if ($logged_in): ?>
            <span class="text-white me-3">👤 <?= htmlspecialchars(explode(' ', $name)[0]) ?> (<?= htmlspecialchars($role) ?>)</span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
          <?php else: ?>
            <button class="btn btn-outline-light btn-sm" onclick="openModal()">Login</button>
          <?php endif; ?>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Messages -->
<div class="container mt-3">
  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>
  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>
</div>

<!-- Hero Section -->
<div class="container text-center my-5 p-5 bg-white rounded shadow-sm">
  <h1 class="mb-3">Need a Plumber, Electrician or Tutor in <?= htmlspecialchars($location) ?>?</h1>
  <p class="lead mb-4">Find trusted service providers in your local area.</p>
  <form class="row g-2 justify-content-center" id="searchForm" action="search.php" method="GET">
    <div class="col-md-3">
      <select id="districtSelect" name="district" class="form-select" required>
        <option value="">-- Select District --</option>
        <?php foreach ($districts as $district): ?>
          <option value="<?= htmlspecialchars($district['id']) ?>"><?= htmlspecialchars($district['district_name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <select id="gnSelect" name="gn_division" class="form-select" required disabled>
        <option value="">-- Select GN Division --</option>
      </select>
    </div>
    <div class="col-md-3">
      <input type="text" name="q" class="form-control" placeholder="Search services..." required />
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-primary w-100">Search</button>
    </div>
  </form>
</div>

<!-- Categories -->
<div class="container mb-5">
  <h2 class="mb-4 text-center">Browse Categories</h2>
  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
    <?php foreach ($categories as [$label, $icon, $type, $desc]): ?>
      <div class="col">
        <div class="card category-card h-100 text-center p-3" data-category="<?= htmlspecialchars($type) ?>" tabindex="0" role="button" title="<?= htmlspecialchars($label) ?>">
          <i class="fas fa-<?= htmlspecialchars($icon) ?> fa-3x text-primary mb-3"></i>
          <h5><?= htmlspecialchars($label) ?></h5>
          <p class="text-muted"><?= htmlspecialchars($desc) ?></p>
          <button class="btn btn-primary book-now-btn mt-auto" type="button">Book Now</button>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- Footer -->
<footer>
  &copy; <?= date('Y') ?> Local Service Finder | Designed for Sri Lanka users
</footer>

<!-- Auth Modal -->
<div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="authModalLabel">Login / Register</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Tabs -->
        <ul class="nav nav-tabs" id="authTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#loginTab" type="button" role="tab">Login</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#registerTab" type="button" role="tab">Register</button>
          </li>
        </ul>
        <div class="tab-content pt-3">
          <!-- Login Form -->
          <form id="loginTab" action="login.php" method="POST" class="tab-pane fade show active" role="tabpanel" aria-labelledby="login-tab">
            <div class="mb-3">
              <label for="loginRole" class="form-label">Select Role</label>
              <select name="role" id="loginRole" class="form-select" required>
                <option value="" disabled selected>Select Role</option>
                <option value="user">User</option>
                <option value="admin">Admin</option>
                <option value="provider">Provider</option>
              </select>
            </div>
            <div class="mb-3">
              <input type="email" name="email" placeholder="Email" class="form-control" required />
            </div>
            <div class="mb-3">
              <input type="password" name="password" placeholder="Password" class="form-control" required />
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
          </form>

          <!-- Register Form -->
          <form id="registerTab" action="register.php" method="POST" class="tab-pane fade" role="tabpanel" aria-labelledby="register-tab">
            <div class="mb-3">
              <label for="registerRole" class="form-label">Register as</label>
              <select name="role" id="registerRole" class="form-select" required>
                <option value="" disabled selected>Select Role</option>
                <option value="user">User</option>
                <option value="provider">Provider</option>
              </select>
            </div>

            <!-- User fields -->
            <div id="userFields" class="mb-3 d-none">
              <input type="text" name="user_name" placeholder="Name" class="form-control mb-2" />
              <input type="email" name="user_email" placeholder="Email" class="form-control mb-2" />
              <input type="password" name="user_password" placeholder="Password" class="form-control mb-2" />
              <input type="password" name="user_confirm_password" placeholder="Confirm Password" class="form-control" />
            </div>

            <!-- Provider fields -->
            <div id="providerFields" class="mb-3 d-none">
              <input type="text" name="provider_name" placeholder="Name" class="form-control mb-2" />
              <input type="text" name="service_type" placeholder="Service Type (e.g. Plumber)" class="form-control mb-2" />
              <input type="text" name="phone" placeholder="Phone" class="form-control mb-2" />
              <input type="number" name="experience" placeholder="Experience (years)" class="form-control mb-2" min="0" />
              <select name="district" id="providerDistrict" class="form-select mb-2">
                <option value="">-- Select District --</option>
                <?php foreach ($districts as $district): ?>
                  <option value="<?= htmlspecialchars($district['id']) ?>"><?= htmlspecialchars($district['district_name']) ?></option>
                <?php endforeach; ?>
              </select>
              <select name="gn_division" id="providerGnDivision" class="form-select mb-2" disabled>
                <option value="">-- Select GN Division --</option>
              </select>
              <input type="email" name="provider_email" placeholder="Email" class="form-control mb-2" />
              <input type="password" name="provider_password" placeholder="Password" class="form-control mb-2" />
              <input type="password" name="provider_confirm_password" placeholder="Confirm Password" class="form-control" />
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Load GN divisions when district changes in search form
  $('#districtSelect').on('change', function() {
    let districtId = $(this).val();
    if (!districtId) {
      $('#gnSelect').html('<option value="">-- Select GN Division --</option>').prop('disabled', true);
      return;
    }
    $.ajax({
      url: 'get_gn_divisions.php',
      method: 'POST',
      data: { district_id: districtId },
      dataType: 'json',
      success: function(data) {
        if (Array.isArray(data) && data.length > 0) {
          let options = '<option value="">-- Select GN Division --</option>';
          data.forEach(gn => {
            options += `<option value="${gn.id}">${gn.gn_division_name}</option>`;
          });
          $('#gnSelect').html(options).prop('disabled', false);
        } else {
          $('#gnSelect').html('<option value="">No GN divisions found</option>').prop('disabled', true);
        }
      },
      error: function() {
        alert('GN divisions load செய்ய முடியவில்லை. தயவு செய்து மீண்டும் முயற்சிக்கவும்.');
        $('#gnSelect').html('<option value="">-- Select GN Division --</option>').prop('disabled', true);
      }
    });
  });

  // Load GN divisions when district changes in provider registration form
  $('#providerDistrict').on('change', function() {
    let districtId = $(this).val();
    if (!districtId) {
      $('#providerGnDivision').html('<option value="">-- Select GN Division --</option>').prop('disabled', true);
      return;
    }
    $.ajax({
      url: 'get_gn_divisions.php',
      method: 'POST',
      data: { district_id: districtId },
      dataType: 'json',
      success: function(data) {
        if (Array.isArray(data) && data.length > 0) {
          let options = '<option value="">-- Select GN Division --</option>';
          data.forEach(gn => {
            options += `<option value="${gn.id}">${gn.gn_division_name}</option>`;
          });
          $('#providerGnDivision').html(options).prop('disabled', false);
        } else {
          $('#providerGnDivision').html('<option value="">No GN divisions found</option>').prop('disabled', true);
        }
      },
      error: function() {
        alert('Failed to load GN divisions.');
        $('#providerGnDivision').html('<option value="">-- Select GN Division --</option>').prop('disabled', true);
      }
    });
  });

  // Show login or provider fields based on role selection in registration form
  $('#registerRole').on('change', function() {
    let val = $(this).val();
    $('#userFields, #providerFields').addClass('d-none');
    if (val === 'user') {
      $('#userFields').removeClass('d-none');
    } else if (val === 'provider') {
      $('#providerFields').removeClass('d-none');
    }
  });

  // Open login/register modal
  function openModal() {
    let authModal = new bootstrap.Modal(document.getElementById('authModal'));
    authModal.show();
  }

  // Book Now button logic
  $(document).on('click', '.book-now-btn', function() {
    let category = $(this).closest('.category-card').data('category');
    let districtId = $('#districtSelect').val();
    let gnId = $('#gnSelect').val();

    if (!districtId) {
      alert('Please select a district.');
      return;
    }
    if (!gnId) {
      alert('Please select a GN division.');
      return;
    }

    let loggedIn = <?= json_encode($logged_in) ?>;
    if (!loggedIn) {
      openModal();
    } else {
      // Redirect to providers list page
      window.location.href = `category_details.php?category=${encodeURIComponent(category)}&district=${encodeURIComponent(districtId)}&gn=${encodeURIComponent(gnId)}`;
    }
  });

  // Keyboard accessibility for category cards
  $('.category-card').on('keypress', function(e) {
    if (e.key === 'Enter') {
      $(this).find('.book-now-btn').click();
    }
  });
</script>

</body>
</html>
