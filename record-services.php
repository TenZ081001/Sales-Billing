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
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Base styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .module-content {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        h1, h2 {
            color: #333;
            margin-bottom: 20px;
        }
        
        h1 {
            font-size: 24px;
        }
        
        h2 {
            font-size: 20px;
            color: #555;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        /* Button styles */
        button, .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-primary {
            background-color: #28a745;
            color: white;
            margin-bottom: 20px;
        }
        
        .btn-primary:hover {
            background-color: #218838;
        }
        
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background-color: #e0a800;
        }

        .editBtn {
            background-color: #17a2b8;
            color: white;
        }
        
        .editBtn:hover {
            background-color: #138496;
        }
        
        .delete-btn {
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 4px;
            display: inline-block;
        }
        
        .delete-btn:hover {
            background-color: #c82333;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
            animation: fadeIn 0.3s;
        }
        
        @keyframes fadeIn {
            from {opacity: 0;}
            to {opacity: 1;}
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 25px;
            width: 50%;
            max-width: 500px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            animation: slideDown 0.3s;
        }
        
        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .close {
            float: right;
            font-size: 28px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
            transition: color 0.3s;
        }
        
        .close:hover {
            color: #333;
        }
        
        /* Form styles */
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        label {
            font-weight: 500;
            color: #495057;
        }
        
        input[type="text"],
        input[type="number"] {
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 16px;
        }
        
        input[type="submit"],
        .submit-btn {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        
        input[type="submit"]:hover,
        .submit-btn:hover {
            background-color: #0069d9;
        }
        
        /* Action buttons container */
        .action-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        /* Message styles */
        .message {
            padding: 12px 15px;
            margin: 15px 0;
            border-radius: 4px;
            font-weight: 500;
        }
        
        .error {
            background-color: #ffdddd;
            color: #d8000c;
            border-left: 4px solid #d8000c;
        }
        
        .success {
            background-color: #ddffdd;
            color: #4F8A10;
            border-left: 4px solid #4F8A10;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .modal-content {
                width: 90%;
                margin: 20% auto;
            }
            
            .content {
                margin-left: 0;
                padding: 15px;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 5px;
                align-items: flex-start;
            }
        }
        
        /* Price formatting */
        .price-cell {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
    </style>
</head>
<?php include 'sidebar.php'; ?>
<body>
    <div class="content">
        <div class="module-content">
            <h1><i class="fas fa-clipboard-list"></i> Records</h1>
            <h2><i class="fas fa-concierge-bell"></i> Services</h2>
            
            <!-- Display messages -->
            <?php if ($error): ?>
                <div class="message error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="message success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <!-- Add Button -->
            <button id="addBtn" class="btn-primary">
                <i class="fas fa-plus-circle"></i> Add New Service
            </button>

            <!-- Table -->
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Service Name</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($services && $services->rowCount() > 0): ?>
                        <?php while($row = $services->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                                <td class="price-cell">₱<?php echo number_format(htmlspecialchars($row['price']), 2); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="editBtn btn-warning" 
                                           data-id="<?php echo $row['id']; ?>"
                                           data-service-name="<?php echo htmlspecialchars($row['service_name']); ?>"
                                           data-service-price="<?php echo htmlspecialchars($row['price']); ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <a href="?delete=<?php echo $row['id']; ?>" 
                                           class="delete-btn"
                                           onclick="return confirm('Are you sure you want to delete this service?');">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </a>
                                    </div>
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

    <!-- Add Service Modal -->
    <div id="addServiceModal" class="modal">
        <div class="modal-content">
            <span class="close" id="addClose">&times;</span>
            <h2><i class="fas fa-plus-circle"></i> Add New Service</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Service Name:</label>
                    <input type="text" name="service_name" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-money-bill-wave"></i> Price:</label>
                    <input type="number" name="service_price" step="0.01" min="0" required>
                </div>
                <button type="submit" name="add" class="submit-btn">
                    <i class="fas fa-save"></i> Add Service
                </button>
            </form>
        </div>
    </div>

    <!-- Edit Service Modal -->
    <div id="editServiceModal" class="modal">
        <div class="modal-content">
            <span class="close" id="editClose">&times;</span>
            <h2><i class="fas fa-edit"></i> Edit Service</h2>
            <form method="POST" action="">
                <input type="hidden" name="id" id="edit_id">
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Service Name:</label>
                    <input type="text" name="service_name" id="edit_service_name" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-money-bill-wave"></i> Price:</label>
                    <input type="number" name="service_price" id="edit_service_price" step="0.01" min="0" required>
                </div>
                <button type="submit" name="update" class="submit-btn">
                    <i class="fas fa-save"></i> Update Service
                </button>
            </form>
        </div>
    </div>

    <script>
        // Modal logic
        const addModal = document.getElementById("addServiceModal");
        const addBtn = document.getElementById("addBtn");
        const addClose = document.getElementById("addClose");

        const editModal = document.getElementById("editServiceModal");
        const editClose = document.getElementById("editClose");
        const editBtns = document.querySelectorAll(".editBtn");

        // Open Add Modal
        addBtn.onclick = () => {
            addModal.style.display = "block";
            document.body.style.overflow = "hidden"; // Prevent scrolling
        };

        // Close Add Modal
        addClose.onclick = () => {
            addModal.style.display = "none";
            document.body.style.overflow = "auto";
        };

        // Close Edit Modal
        editClose.onclick = () => {
            editModal.style.display = "none";
            document.body.style.overflow = "auto";
        };

        // Close modals when clicking outside
        window.onclick = (event) => {
            if (event.target == addModal) {
                addModal.style.display = "none";
                document.body.style.overflow = "auto";
            }
            if (event.target == editModal) {
                editModal.style.display = "none";
                document.body.style.overflow = "auto";
            }
        };

        // Open Edit Modal with data
        editBtns.forEach(btn => {
            btn.addEventListener("click", function () {
                const id = this.dataset.id;
                const serviceName = this.dataset.serviceName;
                const servicePrice = this.dataset.servicePrice;

                document.getElementById("edit_id").value = id;
                document.getElementById("edit_service_name").value = serviceName;
                document.getElementById("edit_service_price").value = servicePrice;

                editModal.style.display = "block";
                document.body.style.overflow = "hidden";
            });
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                addModal.style.display = "none";
                editModal.style.display = "none";
                document.body.style.overflow = "auto";
            }
        });

        // Clear add modal fields when closed
        addModal.addEventListener('hidden', function() {
            document.querySelector('#addServiceModal input[name="service_name"]').value = '';
            document.querySelector('#addServiceModal input[name="service_price"]').value = '';
        });
    </script>
</body>
</html>

<?php 
if (isset($db)) {
    $db = null; // Close PDO connection
}
?>