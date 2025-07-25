<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'config.php';

// Delete provider (soft delete)
if (isset($_GET['delete_provider_id'])) {
    $delete_id = intval($_GET['delete_provider_id']);
    if ($delete_id > 0) {
        $stmt = $conn->prepare("UPDATE service_providers SET status = 'Deleted' WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();
        echo '<div class="alert alert-success">Provider deleted successfully.</div>';
    }
}

// Update provider info
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_provider_id'])) {
    $edit_id = intval($_POST['edit_provider_id']);
    $edit_name = trim($_POST['edit_name']);
    $edit_email = trim($_POST['edit_email']);
    $edit_phone = trim($_POST['edit_phone']);
    $edit_service_type = trim($_POST['edit_service_type']);
    $edit_experience = trim($_POST['edit_experience']);
    $edit_district = trim($_POST['edit_district']);
    $edit_gn_division = trim($_POST['edit_gn_division']);
    $edit_status = trim($_POST['edit_status']);

    if (!empty($edit_name) && !empty($edit_email) && !empty($edit_phone)) {
        $stmt = $conn->prepare("UPDATE service_providers SET name=?, email=?, phone=?, service_type=?, experience=?, district=?, gn_division=?, status=? WHERE id=?");
        $stmt->bind_param("ssssssssi", $edit_name, $edit_email, $edit_phone, $edit_service_type, $edit_experience, $edit_district, $edit_gn_division, $edit_status, $edit_id);
        $stmt->execute();
        $stmt->close();
        echo '<div class="alert alert-info">Provider updated successfully.</div>';
    }
}

// Fetch providers (not deleted)
$result = $conn->query("SELECT * FROM service_providers WHERE status != 'Deleted' ORDER BY id DESC");
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<div class="card shadow-sm mt-4">
  <div class="card-header bg-success text-white">
    <h2 class="h5 mb-0">Manage Service Providers</h2>
  </div>
  <div class="card-body p-0">
    <?php if ($result->num_rows > 0): ?>
    <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead class="table-success">
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Service Type</th>
            <th>Experience</th>
            <th>District</th>
            <th>GN Division</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($provider = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($provider['id']) ?></td>
            <td><?= htmlspecialchars($provider['name']) ?></td>
            <td><?= htmlspecialchars($provider['email']) ?></td>
            <td><?= htmlspecialchars($provider['phone']) ?></td>
            <td><?= htmlspecialchars($provider['service_type']) ?></td>
            <td><?= htmlspecialchars($provider['experience']) ?></td>
            <td><?= htmlspecialchars($provider['district']) ?></td>
            <td><?= htmlspecialchars($provider['gn_division']) ?></td>
            <td><?= htmlspecialchars($provider['status']) ?></td>
            <td>
              <button
                class="btn btn-sm btn-outline-primary me-2"
                data-bs-toggle="modal"
                data-bs-target="#editModal"
                data-id="<?= $provider['id'] ?>"
                data-name="<?= htmlspecialchars($provider['name'], ENT_QUOTES) ?>"
                data-email="<?= htmlspecialchars($provider['email'], ENT_QUOTES) ?>"
                data-phone="<?= htmlspecialchars($provider['phone'], ENT_QUOTES) ?>"
                data-service_type="<?= htmlspecialchars($provider['service_type'], ENT_QUOTES) ?>"
                data-experience="<?= htmlspecialchars($provider['experience'], ENT_QUOTES) ?>"
                data-district="<?= htmlspecialchars($provider['district'], ENT_QUOTES) ?>"
                data-gn_division="<?= htmlspecialchars($provider['gn_division'], ENT_QUOTES) ?>"
                data-status="<?= htmlspecialchars($provider['status'], ENT_QUOTES) ?>"
              >
                <i class="fas fa-edit"></i> Edit
              </button>
              <a
                href="?delete_provider_id=<?= $provider['id'] ?>"
                onclick="return confirm('Are you sure you want to delete this provider?');"
                class="btn btn-sm btn-outline-danger"
              >
                <i class="fas fa-trash"></i> Delete
              </a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
      <div class="p-3">No providers found.</div>
    <?php endif; ?>
  </div>
</div>

<!-- Edit Provider Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" class="modal-content needs-validation" novalidate>
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit Provider</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="edit_provider_id" id="edit_provider_id" required>

        <div class="mb-3">
          <label for="edit_name" class="form-label">Name</label>
          <input
            type="text"
            class="form-control"
            id="edit_name"
            name="edit_name"
            required
            minlength="2"
          />
          <div class="invalid-feedback">Please enter a valid name.</div>
        </div>

        <div class="mb-3">
          <label for="edit_email" class="form-label">Email</label>
          <input
            type="email"
            class="form-control"
            id="edit_email"
            name="edit_email"
            required
          />
          <div class="invalid-feedback">Please enter a valid email.</div>
        </div>

        <div class="mb-3">
          <label for="edit_phone" class="form-label">Phone</label>
          <input
            type="text"
            class="form-control"
            id="edit_phone"
            name="edit_phone"
            required
            pattern="^[0-9]{10}$"
          />
          <div class="invalid-feedback">Please enter a valid 10-digit phone number.</div>
        </div>

        <div class="mb-3">
          <label for="edit_service_type" class="form-label">Service Type</label>
          <input
            type="text"
            class="form-control"
            id="edit_service_type"
            name="edit_service_type"
            required
          />
        </div>

        <div class="mb-3">
          <label for="edit_experience" class="form-label">Experience</label>
          <input
            type="text"
            class="form-control"
            id="edit_experience"
            name="edit_experience"
            required
          />
        </div>

        <div class="mb-3">
          <label for="edit_district" class="form-label">District</label>
          <input
            type="text"
            class="form-control"
            id="edit_district"
            name="edit_district"
            required
          />
        </div>

        <div class="mb-3">
          <label for="edit_gn_division" class="form-label">GN Division</label>
          <input
            type="text"
            class="form-control"
            id="edit_gn_division"
            name="edit_gn_division"
            required
          />
        </div>

        <div class="mb-3">
          <label for="edit_status" class="form-label">Status</label>
          <select
            class="form-select"
            id="edit_status"
            name="edit_status"
            required
          >
            <option value="">Select status</option>
            <option value="Pending">Pending</option>
            <option value="Approved">Approved</option>
            <option value="Rejected">Rejected</option>
          </select>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Update Provider</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // When opening the modal, fill inputs from data attributes
  const editModal = document.getElementById('editModal');
  editModal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;

    document.getElementById('edit_provider_id').value = button.getAttribute('data-id');
    document.getElementById('edit_name').value = button.getAttribute('data-name');
    document.getElementById('edit_email').value = button.getAttribute('data-email');
    document.getElementById('edit_phone').value = button.getAttribute('data-phone');
    document.getElementById('edit_service_type').value = button.getAttribute('data-service_type');
    document.getElementById('edit_experience').value = button.getAttribute('data-experience');
    document.getElementById('edit_district').value = button.getAttribute('data-district');
    document.getElementById('edit_gn_division').value = button.getAttribute('data-gn_division');
    document.getElementById('edit_status').value = button.getAttribute('data-status');
  });

  // Bootstrap validation
  (() => {
    'use strict';
    const form = editModal.querySelector('form');
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  })();
</script>
