<?php
require 'config.php';

// Initialize message
$message = '';

// Handle Add Category
if (isset($_POST['save_category'])) {
    $id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $image = $_FILES['image']['name'] ?? '';
    $image_uploaded = false;

    if ($name !== '') {
        // Check if category name already exists (ignore current id if editing)
        if ($id > 0) {
            $stmtCheck = $conn->prepare("SELECT id FROM categories WHERE name = ? AND id != ?");
            $stmtCheck->bind_param("si", $name, $id);
        } else {
            $stmtCheck = $conn->prepare("SELECT id FROM categories WHERE name = ?");
            $stmtCheck->bind_param("s", $name);
        }
        $stmtCheck->execute();
        $stmtCheck->store_result();

        if ($stmtCheck->num_rows > 0) {
            $message = '<div class="alert alert-danger">Category name already exists.</div>';
        } else {
            // Handle image upload if provided
            if ($image && isset($_FILES['image']['tmp_name']) && $_FILES['image']['tmp_name'] != '') {
                $target_dir = 'uploads/categories/';
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                $target_file = $target_dir . basename($image);
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $image_uploaded = true;
                } else {
                    $message = '<div class="alert alert-danger">Failed to upload image.</div>';
                }
            }

            if (empty($message)) {
                if ($id > 0) {
                    // Update existing category
                    if ($image_uploaded) {
                        $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ?, image = ? WHERE id = ?");
                        $stmt->bind_param("sssi", $name, $description, $image, $id);
                    } else {
                        $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
                        $stmt->bind_param("ssi", $name, $description, $id);
                    }
                    if ($stmt->execute()) {
                        $message = '<div class="alert alert-success">Category updated successfully.</div>';
                    } else {
                        $message = '<div class="alert alert-danger">Error updating category.</div>';
                    }
                    $stmt->close();
                } else {
                    // Insert new category
                    $stmt = $conn->prepare("INSERT INTO categories (name, description, image) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $name, $description, $image);
                    if ($stmt->execute()) {
                        $message = '<div class="alert alert-success">Category added successfully.</div>';
                    } else {
                        $message = '<div class="alert alert-danger">Error adding category.</div>';
                    }
                    $stmt->close();
                }
            }
        }
        $stmtCheck->close();
    } else {
        $message = '<div class="alert alert-danger">Category name is required.</div>';
    }
}

// Handle Delete Category
if (isset($_GET['delete_category_id'])) {
    $del_id = intval($_GET['delete_category_id']);
    if ($del_id > 0) {
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->bind_param("i", $del_id);
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Category deleted successfully.</div>';
        } else {
            $message = '<div class="alert alert-danger">Error deleting category.</div>';
        }
        $stmt->close();
    }
}

// Fetch all categories
$result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Categories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<div class="container mt-4">
    <h2 class="mb-4">Manage Categories</h2>

    <!-- Message -->
    <?= $message ?>

    <!-- Add New Category Button -->
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#categoryModal" id="addCategoryBtn">
        Add New Category
    </button>

    <!-- Categories Table -->
    <?php if ($result->num_rows > 0): ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
            <thead class="table-success">
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th style="width: 150px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($cat = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php if ($cat['image']): ?>
                            <img src="uploads/categories/<?= htmlspecialchars($cat['image']) ?>" alt="Category Image" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                        <?php else: ?>
                            <span class="text-muted">No Image</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($cat['name']) ?></td>
                    <td><?= htmlspecialchars($cat['description']) ?></td>
                    <td>
                        <button
                            class="btn btn-primary btn-sm me-2 editCategoryBtn"
                            data-id="<?= $cat['id'] ?>"
                            data-name="<?= htmlspecialchars($cat['name'], ENT_QUOTES) ?>"
                            data-description="<?= htmlspecialchars($cat['description'], ENT_QUOTES) ?>"
                            data-image="<?= htmlspecialchars($cat['image'], ENT_QUOTES) ?>"
                            data-bs-toggle="modal"
                            data-bs-target="#categoryModal"
                        >
                            Edit
                        </button>
                        <a href="?delete_category_id=<?= $cat['id'] ?>" 
                           class="btn btn-danger btn-sm" 
                           onclick="return confirm('Are you sure you want to delete this category?');"
                        >
                            Delete
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <p class="text-muted">No categories found.</p>
    <?php endif; ?>
</div>

<!-- Category Modal for Add/Edit -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" enctype="multipart/form-data" class="modal-content needs-validation" novalidate>
      <div class="modal-header">
        <h5 class="modal-title" id="categoryModalLabel">Add Category</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="category_id" id="category_id" value="">

        <div class="mb-3">
          <label for="name" class="form-label">Category Name</label>
          <input
            type="text"
            class="form-control"
            id="name"
            name="name"
            required
            minlength="2"
            placeholder="e.g. Electricians"
          >
          <div class="invalid-feedback">Please enter category name.</div>
        </div>

        <div class="mb-3">
          <label for="description" class="form-label">Description</label>
          <textarea
            class="form-control"
            id="description"
            name="description"
            rows="3"
            placeholder="Brief description"
          ></textarea>
        </div>

        <div class="mb-3">
          <label for="image" class="form-label">Category Image</label>
          <input type="file" class="form-control" id="image" name="image" accept="image/*">
          <div id="currentImageContainer" class="mt-2" style="display:none;">
            <p>Current Image:</p>
            <img id="currentImage" src="" alt="Current Category Image" style="width: 100px; height: 100px; object-fit: cover; border-radius: 6px;">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" name="save_category" class="btn btn-primary">Save Category</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Bootstrap form validation
  (() => {
    'use strict'
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        form.classList.add('was-validated')
      }, false)
    })
  })();

  // Handle opening modal for add/edit category
  const categoryModal = document.getElementById('categoryModal');
  const categoryModalLabel = document.getElementById('categoryModalLabel');
  const categoryIdInput = document.getElementById('category_id');
  const nameInput = document.getElementById('name');
  const descriptionInput = document.getElementById('description');
  const currentImageContainer = document.getElementById('currentImageContainer');
  const currentImage = document.getElementById('currentImage');

  // When clicking "Add New Category" button
  document.getElementById('addCategoryBtn').addEventListener('click', () => {
    categoryModalLabel.textContent = 'Add Category';
    categoryIdInput.value = '';
    nameInput.value = '';
    descriptionInput.value = '';
    currentImageContainer.style.display = 'none';
    currentImage.src = '';
  });

  // When clicking an Edit button
  document.querySelectorAll('.editCategoryBtn').forEach(button => {
    button.addEventListener('click', () => {
      const id = button.getAttribute('data-id');
      const name = button.getAttribute('data-name');
      const description = button.getAttribute('data-description');
      const image = button.getAttribute('data-image');

      categoryModalLabel.textContent = 'Edit Category';
      categoryIdInput.value = id;
      nameInput.value = name;
      descriptionInput.value = description;

      if(image) {
        currentImage.src = 'uploads/categories/' + image;
        currentImageContainer.style.display = 'block';
      } else {
        currentImageContainer.style.display = 'none';
        currentImage.src = '';
      }
    });
  });
</script>

</body>
</html>
