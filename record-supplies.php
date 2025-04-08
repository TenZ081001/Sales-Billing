<?php
require 'db/dbconn.php';

// Handle Add Item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_item'])) {
    $item_name = $_POST['item_name'];
    $quantity = $_POST['quantity'];

    $insert_sql = "INSERT INTO supplies (item_name, quantity) VALUES (:item_name, :quantity)";
    $stmt = $db->prepare($insert_sql);
    $stmt->execute([':item_name' => $item_name, ':quantity' => $quantity]);
    header("Location: record-supplies.php");
    exit;
}

// Handle Edit Item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_item'])) {
    $id = $_POST['edit_id'];
    $item_name = $_POST['item_name'];
    $quantity = $_POST['quantity'];

    $update_sql = "UPDATE supplies SET item_name = :item_name, quantity = :quantity WHERE id = :id";
    $stmt = $db->prepare($update_sql);
    $stmt->execute([':item_name' => $item_name, ':quantity' => $quantity, ':id' => $id]);
    header("Location: record-supplies.php");
    exit;
}

// Handle Delete
if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] === 'delete') {
    $delete_sql = "DELETE FROM supplies WHERE id = :id";
    $stmt = $db->prepare($delete_sql);
    $stmt->execute([':id' => $_GET['id']]);
    header("Location: record-supplies.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Management</title>
    <!-- Font Awesome for icons -->
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
        button {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        #addBtn {
            background-color: #28a745;
            color: white;
            margin-bottom: 20px;
        }
        
        #addBtn:hover {
            background-color: #218838;
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
        
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        
        input[type="submit"]:hover {
            background-color: #0069d9;
        }
        
        /* Action buttons container */
        .action-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
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
        }
    </style>
</head>
<?php include 'sidebar.php'; ?>
<body>
    <div class="content">
        <div class="module-content">
            <h1><i class="fas fa-clipboard-list"></i> Records</h1>
            <h2><i class="fas fa-boxes"></i>Supplies</h2>
            <button id="addBtn"><i class="fas fa-plus-circle"></i> Add New Item</button>
            <table>
                <tr>
                    <th>Item ID</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
                <?php
                $stmt = $db->query("SELECT * FROM supplies");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['item_name']}</td>
                        <td>{$row['quantity']}</td>
                        <td>
                            <div class='action-buttons'>
                                <button class='editBtn' data-id='{$row['id']}' data-name='" . htmlspecialchars($row['item_name']) . "' data-quantity='{$row['quantity']}'>
                                    <i class='fas fa-edit'></i> Edit
                                </button>
                                <a href='?id={$row['id']}&action=delete' onclick='return confirm(\"Are you sure you want to delete this item?\")' class='delete-btn'>
                                    <i class='fas fa-trash-alt'></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>";
                }
                ?>
            </table>
        </div>
    </div>

    <!-- Add Modal -->
    <div id="addItemModal" class="modal">
        <div class="modal-content">
            <span class="close" id="addClose">&times;</span>
            <h2><i class="fas fa-plus-circle"></i> Add New Item</h2>
            <form method="POST">
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Item Name:</label>
                    <input type="text" name="item_name" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-calculator"></i> Quantity:</label>
                    <input type="number" name="quantity" required min="0">
                </div>
                <button type="submit" name="add_item" class="submit-btn">
                    <i class="fas fa-save"></i> Add Item
                </button>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editItemModal" class="modal">
        <div class="modal-content">
            <span class="close" id="editClose">&times;</span>
            <h2><i class="fas fa-edit"></i> Edit Item</h2>
            <form method="POST">
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Item Name:</label>
                    <input type="text" name="item_name" id="edit_name" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-calculator"></i> Quantity:</label>
                    <input type="number" name="quantity" id="edit_quantity" required min="0">
                </div>
                <button type="submit" name="edit_item" class="submit-btn">
                    <i class="fas fa-save"></i> Update Item
                </button>
            </form>
        </div>
    </div>

    <script>
        // Modal logic
        const addModal = document.getElementById("addItemModal");
        const addBtn = document.getElementById("addBtn");
        const addClose = document.getElementById("addClose");

        const editModal = document.getElementById("editItemModal");
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
                const quantity = this.dataset.quantity;

                document.getElementById("edit_id").value = id;
                document.getElementById("edit_name").value = name;
                document.getElementById("edit_quantity").value = quantity;

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
    </script>
</body>
</html>