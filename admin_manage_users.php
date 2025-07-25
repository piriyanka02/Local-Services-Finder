<?php
// admin_manage_users.php
require 'config.php'; // make sure this is included before any DB action

// Delete user
if (isset($_GET['delete_user_id'])) {
    $delete_id = intval($_GET['delete_user_id']);
    if ($delete_id > 0) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();
        echo '<div class="alert alert-success">User deleted successfully.</div>';
    }
}

// Update user info
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user_id'])) {
    $edit_id = intval($_POST['edit_user_id']);
    $edit_name = trim($_POST['edit_name']);
    $edit_phone = trim($_POST['edit_phone']);

    if (!empty($edit_name) && !empty($edit_phone)) {
        $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("ssi", $edit_name, $edit_phone, $edit_id);
        $stmt->execute();
        $stmt->close();
        echo '<div class="alert alert-info">User updated successfully.</div>';
    }
}

// Get all users
$result = $conn->query("SELECT id, name, email, phone, created_at FROM users ORDER BY id DESC");
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<div class="card shadow-sm mt-4">
  <div class="card-header bg-success text-white">
    <h2 class="h5 mb-0">Manage Users</h2>
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
            <th>Joined On</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($user = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($user['id']) ?></td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['phone']) ?></td>
            <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
            <td>
              <button
                class="btn btn-sm btn-outline-primary me-2"
                data-bs-toggle="modal"
                data-bs-target="#editModal"
                data-id="<?= $user['id'] ?>"
                data-name="<?= htmlspecialchars($user['name'], ENT_QUOTES) ?>"
                data-email="<?= htmlspecialchars($user['email'], ENT_QUOTES) ?>"
                data-phone="<?= htmlspecialchars($user['phone'], ENT_QUOTES) ?>"
              >
                <i class="fas fa-edit"></i> Edit
              </button>
              <a
                href="?page=users&delete_user_id=<?= $user['id'] ?>"
                onclick="return confirm('Are you sure you want to delete this user?');"
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
      <div class="p-3">No users found.</div>
    <?php endif; ?>
  </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" class="modal-content needs-validation" novalidate>
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="edit_user_id" id="edit_user_id" required>

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
          <label for="edit_email" class="form-label">Email (readonly)</label>
          <input
            type="email"
            class="form-control"
            id="edit_email"
            name="edit_email"
            readonly
          />
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
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Update User</button>
      </div>
    </form>
  </div>
</div>

<script>
  const editModal = document.getElementById('editModal');

  editModal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;
    document.getElementById('edit_user_id').value = button.getAttribute('data-id');
    document.getElementById('edit_name').value = button.getAttribute('data-name');
    document.getElementById('edit_email').value = button.getAttribute('data-email');
    document.getElementById('edit_phone').value = button.getAttribute('data-phone');
  });

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
