<?php
require 'db/dbconn.php';

// Initialize variables
$error = '';
$success = '';

// ADD
if (isset($_POST['add'])) {
    try {
        $stmt = $db->prepare("INSERT INTO personnel (name, position) VALUES (?, ?)");
        $stmt->execute([$_POST['name'], $_POST['position']]);
        $success = "Personnel added successfully!";
    } catch (PDOException $e) {
        $error = "Error adding personnel: " . $e->getMessage();
    }
}

// EDIT
if (isset($_POST['edit'])) {
    try {
        $stmt = $db->prepare("UPDATE personnel SET name=?, position=? WHERE id=?");
        $stmt->execute([$_POST['name'], $_POST['position'], $_POST['id']]);
        $success = "Personnel updated successfully!";
    } catch (PDOException $e) {
        $error = "Error updating personnel: " . $e->getMessage();
    }
}

// DELETE
if (isset($_GET['delete'])) {
    try {
        $stmt = $db->prepare("DELETE FROM personnel WHERE id=?");
        $stmt->execute([$_GET['delete']]);
        $success = "Personnel deleted successfully!";
    } catch (PDOException $e) {
        $error = "Error deleting personnel: " . $e->getMessage();
    }
}

// Fetch all personnel
$personnel = $db->query("SELECT * FROM personnel ORDER BY id ASC");

// GET record for edit
$editData = null;
if (isset($_GET['edit'])) {
    try {
        $stmt = $db->prepare("SELECT * FROM personnel WHERE id=?");
        $stmt->execute([$_GET['edit']]);
        $editData = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error fetching personnel data: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personnel Records</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            margin: 0;
        }

        .content {
            padding: 20px;
        }

        .module-content {
            width: 100%;
            margin: 0;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-sizing: border-box;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .module-content h1, .module-content h2 {
            margin: 0 0 10px;
            color: #333;
        }

        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        
        .error {
            background-color: #ffdddd;
            color: #d8000c;
        }
        
        .success {
            background-color: #ddffdd;
            color: #4F8A10;
        }

        .action-links a {
            margin-right: 10px;
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>

<div class="content">
    <div class="module-content">
        <div id="personnel" class="section">
            <h1>Records</h1>
            <h2>Personnel</h2>
            
            <!-- Display messages -->
            <?php if ($error): ?>
                <div class="message error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="message success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <!-- Add Button -->
            <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addPersonnelModal">
                <i class="fas fa-plus"></i> Add Personnel
            </button>

            <!-- Table -->
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($personnel && $personnel->rowCount() > 0): ?>
                        <?php while ($row = $personnel->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['position']); ?></td>
                                <td class="action-links">
                                    <a href="#" class="btn btn-sm btn-warning" 
                                       data-bs-toggle="modal" 
                                       data-bs-target="#editPersonnelModal"
                                       data-personnel-id="<?php echo $row['id']; ?>"
                                       data-personnel-name="<?php echo htmlspecialchars($row['name']); ?>"
                                       data-personnel-position="<?php echo htmlspecialchars($row['position']); ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?delete=<?php echo $row['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this personnel?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No personnel records found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Personnel Modal -->
<div class="modal fade" id="addPersonnelModal" tabindex="-1" aria-labelledby="addPersonnelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPersonnelModalLabel">Add New Personnel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="position" class="form-label">Position</label>
                        <select class="form-select" id="position" name="position" required>
                            <option value="" selected disabled>Select Position</option>
                            <option value="Technician">Technician</option>
                            <option value="Cleaner">Cleaner</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add" class="btn btn-primary">Add Personnel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Personnel Modal -->
<div class="modal fade" id="editPersonnelModal" tabindex="-1" aria-labelledby="editPersonnelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPersonnelModalLabel">Edit Personnel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_personnel_id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_position" class="form-label">Position</label>
                        <select class="form-select" id="edit_position" name="position" required>
                            <option value="Technician">Technician</option>
                            <option value="Cleaner">Cleaner</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="edit" class="btn btn-primary">Update Personnel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Handle edit modal data population
    document.addEventListener('DOMContentLoaded', function() {
        var editModal = document.getElementById('editPersonnelModal');
        editModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var personnelId = button.getAttribute('data-personnel-id');
            var personnelName = button.getAttribute('data-personnel-name');
            var personnelPosition = button.getAttribute('data-personnel-position');
            
            document.getElementById('edit_personnel_id').value = personnelId;
            document.getElementById('edit_name').value = personnelName;
            document.getElementById('edit_position').value = personnelPosition;
        });
        
        // Clear add modal fields when closed
        var addModal = document.getElementById('addPersonnelModal');
        addModal.addEventListener('hidden.bs.modal', function () {
            document.getElementById('name').value = '';
            document.getElementById('position').selectedIndex = 0;
        });
    });
</script>
</body>
</html>

<?php 
if (isset($db)) {
    $db = null; // Close PDO connection
}
?>