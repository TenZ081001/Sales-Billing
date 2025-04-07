<?php
require 'db/dbconn.php'; 

// Initialize variables
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (isset($_POST['add'])) {
            // Add new service
            $name = $_POST['service_name'];
            $price = $_POST['service_price'];
            
            $stmt = $db->prepare("INSERT INTO services (service_name, price) VALUES (:service_name, :price)");
            $stmt->bindValue(':service_name', $name, PDO::PARAM_STR);
            $stmt->bindValue(':price', $price, PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                $success = "Service added successfully!";
            } else {
                $error = "Error adding service.";
            }
        } elseif (isset($_POST['update']) && isset($_POST['id']) && !empty($_POST['id'])) {
            // Update existing service
            $id = $_POST['id'];
            $name = $_POST['service_name'];
            $price = $_POST['service_price'];
            
            $stmt = $db->prepare("UPDATE services SET service_name=:service_name, price=:price WHERE id=:id");
            $stmt->bindValue(':service_name', $name, PDO::PARAM_STR);
            $stmt->bindValue(':price', $price, PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $success = "Service updated successfully!";
            } else {
                $error = "Error updating service.";
            }
        }
    } catch (Exception $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Handle delete action
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    try {
        $delete_id = $_GET['delete'];
        if (!empty($delete_id)) {
            $stmt = $db->prepare("DELETE FROM services WHERE id=:id");
            $stmt->bindValue(':id', $delete_id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $success = "Service deleted successfully!";
            } else {
                $error = "Error deleting service.";
            }
        }
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } catch (Exception $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Fetch all services
$services = $db->query("SELECT * FROM services");

// Check if we're editing a service
$edit_id = null;
$edit_service = null;
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    try {
        $edit_id = $_GET['edit'];
        if (!empty($edit_id)) {
            $stmt = $db->prepare("SELECT * FROM services WHERE id=:id");
            $stmt->bindValue(':id', $edit_id, PDO::PARAM_INT);
            $stmt->execute();
            $edit_service = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .module-content table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .module-content table, th, td {
            border: 1px solid #ddd;
        }
        .module-content th, td {
            padding: 10px;
            text-align: left;
        }
        .module-content th {
            background-color: #f2f2f2;
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
            <div id="services" class="section">
                <h1>Records</h1>
                <h2>Services</h2>
                
                <!-- Display messages -->
                <?php if ($error): ?>
                    <div class="message error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="message success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                
                <!-- Add Service Button -->
                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                    Add New Service
                </button>
                
                <!-- Services Table -->
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Service Name</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($services && $services->rowCount() > 0): ?>
                            <?php while($row = $services->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['price']); ?></td>
                                    <td class="action-links">
                                        <a href="#" class="btn btn-sm btn-warning" 
                                           data-bs-toggle="modal" 
                                           data-bs-target="#editServiceModal"
                                           data-id="<?php echo $row['id']; ?>"
                                           data-service-name="<?php echo htmlspecialchars($row['service_name']); ?>"
                                           data-service-price="<?php echo htmlspecialchars($row['price']); ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="?delete=<?php echo $row['id']; ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Are you sure you want to delete this service?')">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No services found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Service Modal -->
    <div class="modal fade" id="addServiceModal" tabindex="-1" aria-labelledby="addServiceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addServiceModalLabel">Add New Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="service_name" class="form-label">Service Name</label>
                            <input type="text" class="form-control" id="service_name" name="service_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="service_price" class="form-label">Price</label>
                            <input type="text" class="form-control" id="service_price" name="service_price" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add" class="btn btn-primary">Add Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Service Modal -->
    <div class="modal fade" id="editServiceModal" tabindex="-1" aria-labelledby="editServiceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editServiceModalLabel">Edit Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label for="edit_service_name" class="form-label">Service Name</label>
                            <input type="text" class="form-control" id="edit_service_name" name="service_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_service_price" class="form-label">Price</label>
                            <input type="text" class="form-control" id="edit_service_price" name="service_price" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="update" class="btn btn-primary">Update Service</button>
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
            var editModal = document.getElementById('editServiceModal');
            editModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var id = button.getAttribute('data-id');
                var serviceName = button.getAttribute('data-service-name');
                var servicePrice = button.getAttribute('data-service-price');
                
                document.getElementById('edit_id').value = id;
                document.getElementById('edit_service_name').value = serviceName;
                document.getElementById('edit_service_price').value = servicePrice;
            });
            
            // Clear add modal fields when closed
            var addModal = document.getElementById('addServiceModal');
            addModal.addEventListener('hidden.bs.modal', function () {
                document.getElementById('service_name').value = '';
                document.getElementById('service_price').value = '';
            });
        });
    </script>
</body>
</html>
