<?php
require 'db/dbconn.php';

// Initialize variables
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (isset($_POST['add'])) {
            // Add new customer
            $name = $_POST['name'];
            $contact = $_POST['contact'];
            
            $stmt = $db->prepare("INSERT INTO customers (name, contact) VALUES (:name, :contact)");
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->bindValue(':contact', $contact, PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                $success = "Customer added successfully!";
            } else {
                $error = "Error adding customer.";
            }
        } elseif (isset($_POST['update']) && isset($_POST['id']) && !empty($_POST['id'])) {
            // Update existing customer
            $id = $_POST['id'];
            $name = $_POST['name'];
            $contact = $_POST['contact'];
            
            $stmt = $db->prepare("UPDATE customers SET name=:name, contact=:contact WHERE id=:id");
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->bindValue(':contact', $contact, PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $success = "Customer updated successfully!";
            } else {
                $error = "Error updating customer.";
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
            $stmt = $db->prepare("DELETE FROM customers WHERE id=:id");
            $stmt->bindValue(':id', $delete_id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $success = "Customer deleted successfully!";
            } else {
                $error = "Error deleting customer.";
            }
        }
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } catch (Exception $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Fetch all customers
$customers = $db->query("SELECT * FROM customers ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Records</title>
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
        input[type="tel"] {
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
        
        /* Contact formatting */
        .contact-cell {
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<?php include 'sidebar.php'; ?>
<body>
    <div class="content">
        <div class="module-content">
            <h1><i class="fas fa-clipboard-list"></i> Records</h1>
            <h2><i class="fas fa-users"></i> Customers</h2>
            
            <!-- Display messages -->
            <?php if ($error): ?>
                <div class="message error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="message success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <!-- Add Button -->
            <button id="addBtn" class="btn-primary">
                <i class="fas fa-user-plus"></i> Add New Customer
            </button>

            <!-- Table -->
            <table>
                <thead>
                    <tr>
                        <th>Customer ID</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($customers && $customers->rowCount() > 0): ?>
                        <?php while($row = $customers->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td class="contact-cell"><?php echo htmlspecialchars($row['contact']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="editBtn btn-warning" 
                                           data-id="<?php echo $row['id']; ?>"
                                           data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                           data-contact="<?php echo htmlspecialchars($row['contact']); ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <a href="?delete=<?php echo $row['id']; ?>" 
                                           class="delete-btn"
                                           onclick="return confirm('Are you sure you want to delete this customer?');">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No customers found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div id="addCustomerModal" class="modal">
        <div class="modal-content">
            <span class="close" id="addClose">&times;</span>
            <h2><i class="fas fa-user-plus"></i> Add New Customer</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Name:</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Contact:</label>
                    <input type="tel" name="contact" required>
                </div>
                <button type="submit" name="add" class="submit-btn">
                    <i class="fas fa-save"></i> Add Customer
                </button>
            </form>
        </div>
    </div>

    <!-- Edit Customer Modal -->
    <div id="editCustomerModal" class="modal">
        <div class="modal-content">
            <span class="close" id="editClose">&times;</span>
            <h2><i class="fas fa-user-edit"></i> Edit Customer</h2>
            <form method="POST" action="">
                <input type="hidden" name="id" id="edit_id">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Name:</label>
                    <input type="text" name="name" id="edit_name" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Contact:</label>
                    <input type="tel" name="contact" id="edit_contact" required>
                </div>
                <button type="submit" name="update" class="submit-btn">
                    <i class="fas fa-save"></i> Update Customer
                </button>
            </form>
        </div>
    </div>

    <script>
        // Modal logic
        const addModal = document.getElementById("addCustomerModal");
        const addBtn = document.getElementById("addBtn");
        const addClose = document.getElementById("addClose");

        const editModal = document.getElementById("editCustomerModal");
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
                const name = this.dataset.name;
                const contact = this.dataset.contact;

                document.getElementById("edit_id").value = id;
                document.getElementById("edit_name").value = name;
                document.getElementById("edit_contact").value = contact;

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
            document.querySelector('#addCustomerModal input[name="name"]').value = '';
            document.querySelector('#addCustomerModal input[name="contact"]').value = '';
        });
    </script>
</body>
</html>

<?php 
if (isset($db)) {
    $db = null; // Close PDO connection
}
?>