<?php
require 'db/dbconn.php';

// Constants for job order statuses
define('STATUS_PENDING', 'Pending');
define('STATUS_IN_PROGRESS', 'In Progress');
define('STATUS_COMPLETED', 'Completed');
$valid_statuses = [STATUS_PENDING, STATUS_IN_PROGRESS, STATUS_COMPLETED];

// Constants for equipment types
define('EQUIPMENT_AC', 'Air Conditioner');
define('EQUIPMENT_REFRIGERATOR', 'Refrigerator');
define('EQUIPMENT_OTHER', 'Other');
$valid_equipment = [EQUIPMENT_AC, EQUIPMENT_REFRIGERATOR, EQUIPMENT_OTHER];

// Helper function to sanitize output
function safe_output($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        switch ($_POST['action'] ?? '') {
            case 'add':
                handleAddJobOrder($db, $valid_statuses, $valid_equipment);
                break;
            case 'edit':
                handleEditJobOrder($db, $valid_statuses, $valid_equipment);
                break;
            case 'delete':
                handleDeleteJobOrder($db);
                break;
            default:
                throw new Exception("Invalid action");
        }
        
        // Redirect after successful operation
        header("Location: job-order.php");
        exit();
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Fetch job orders with optional filtering
$status_filter = '';
if (isset($_GET['status']) && in_array($_GET['status'], $valid_statuses)) {
    $status_filter = $_GET['status'];
}

$orders = fetchJobOrders($db, $status_filter);

/**
 * Fetches job orders from the database with optional status filter
 */
function fetchJobOrders($db, $status_filter = '') {
    $sql = "SELECT * FROM job_orders 
            WHERE (:status_filter = '' OR status = :status_filter) 
            ORDER BY created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute([':status_filter' => $status_filter]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Handles adding a new job order
 */
function handleAddJobOrder($db, $valid_statuses, $valid_equipment) {
    $required_fields = ['customer_name', 'address', 'contact', 'equipment', 'issue', 'status'];
    
    // Validate required fields
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("$field is required");
        }
    }
    
    // Validate status and equipment
    if (!in_array($_POST['status'], $valid_statuses)) {
        throw new Exception("Invalid status");
    }
    
    if (!in_array($_POST['equipment'], $valid_equipment)) {
        throw new Exception("Invalid equipment type");
    }
    
    // Sanitize inputs
    $customer_name = trim($_POST['customer_name']);
    $address = trim($_POST['address']);
    $contact = trim($_POST['contact']);
    $equipment = $_POST['equipment'];
    $issue = trim($_POST['issue']);
    $status = $_POST['status'];
    
    // Insert into database
    $stmt = $db->prepare("INSERT INTO job_orders 
                         (customer_name, address, contact, equipment, issue, status)
                         VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$customer_name, $address, $contact, $equipment, $issue, $status]);
}

/**
 * Handles editing an existing job order
 */
function handleEditJobOrder($db, $valid_statuses, $valid_equipment) {
    if (empty($_POST['id'])) {
        throw new Exception("Job order ID is required");
    }
    
    $required_fields = ['customer_name', 'address', 'contact', 'equipment', 'issue', 'status'];
    
    // Validate required fields
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("$field is required");
        }
    }
    
    // Validate status and equipment
    if (!in_array($_POST['status'], $valid_statuses)) {
        throw new Exception("Invalid status");
    }
    
    if (!in_array($_POST['equipment'], $valid_equipment)) {
        throw new Exception("Invalid equipment type");
    }
    
    // Sanitize inputs
    $id = (int)$_POST['id'];
    $customer_name = trim($_POST['customer_name']);
    $address = trim($_POST['address']);
    $contact = trim($_POST['contact']);
    $equipment = $_POST['equipment'];
    $issue = trim($_POST['issue']);
    $status = $_POST['status'];
    
    // Update database
    $stmt = $db->prepare("UPDATE job_orders 
                         SET customer_name = ?, address = ?, contact = ?, 
                             equipment = ?, issue = ?, status = ?
                         WHERE id = ?");
    $stmt->execute([$customer_name, $address, $contact, $equipment, $issue, $status, $id]);
}

/**
 * Handles deleting a job order
 */
function handleDeleteJobOrder($db) {
    if (empty($_POST['id'])) {
        throw new Exception("Job order ID is required");
    }
    
    $id = (int)$_POST['id'];
    
    $stmt = $db->prepare("DELETE FROM job_orders WHERE id = ?");
    $stmt->execute([$id]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Order Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/job-order.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --light-color: #ecf0f1;
            --dark-color: #34495e;
            --white: #ffffff;
            --gray: #95a5a6;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: var(--dark-color);
            line-height: 1.6;
        }
        
        .content {
            padding: 2rem;
            margin-left: 250px;
            transition: margin-left 0.3s;
        }
        
        h1, h2, h3 {
            color: var(--secondary-color);
            margin-bottom: 1.5rem;
        }
        
        /* Header and Filter Section */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .filter-container {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .filter-container label {
            font-weight: 600;
            color: var(--secondary-color);
        }
        
        select, input, textarea {
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        
        select:focus, input:focus, textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
        
        /* Table Styling */
        .table-container {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background-color: var(--light-color);
            font-weight: 600;
            color: var(--secondary-color);
            position: sticky;
            top: 0;
        }
        
        tr:hover td {
            background-color: rgba(52, 152, 219, 0.05);
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-in-progress {
            background-color: #cce5ff;
            color: #004085;
        }
        
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        
        /* Button Styling */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.25rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            gap: 0.5rem;
        }
        
        .btn-sm {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-success {
            background-color: var(--success-color);
            color: white;
        }
        
        .btn-success:hover {
            background-color: #219653;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .btn-group {
            display: flex;
            gap: 0.5rem;
        }
        
        /* Form Styling */
        .form-container {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--secondary-color);
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        /* Modal Styling */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            overflow-y: auto;
        }
        
        .modal-content {
            background-color: var(--white);
            margin: 2rem auto;
            width: 90%;
            max-width: 800px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            animation: modalFadeIn 0.3s ease;
        }
        
        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .modal-footer {
            padding: 1.5rem;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }
        
        .close {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--gray);
            cursor: pointer;
            transition: color 0.3s;
        }
        
        .close:hover {
            color: var(--danger-color);
        }
        
        /* Alert Messages */
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #f5c6cb;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid #c3e6cb;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .content {
                margin-left: 0;
                padding: 1rem;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .filter-container {
                width: 100%;
                flex-direction: column;
                align-items: flex-start;
            }
        }
        
        @media (max-width: 768px) {
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
            
            .modal-content {
                width: 95%;
                margin: 1rem auto;
            }
        }
        
        /* Utility Classes */
        .text-center {
            text-align: center;
        }
        
        .mb-3 {
            margin-bottom: 1rem;
        }
        
        .mt-3 {
            margin-top: 1rem;
        }
        
        .d-flex {
            display: flex;
        }
        
        .justify-content-between {
            justify-content: space-between;
        }
        
        .align-items-center {
            align-items: center;
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="content">
    <div class="page-header">
        <h1><i class="fas fa-tasks"></i> Job Order Management</h1>
        
        <div class="filter-container">
            <form method="GET" action="job-order.php" class="d-flex align-items-center">
                <label for="status">Filter by Status:</label>
                <select name="status" id="status" onchange="this.form.submit()" class="form-control">
                    <option value="">All Orders</option>
                    <option value="<?= STATUS_PENDING ?>" <?= ($status_filter === STATUS_PENDING) ? 'selected' : '' ?>>
                        <?= STATUS_PENDING ?>
                    </option>
                    <option value="<?= STATUS_IN_PROGRESS ?>" <?= ($status_filter === STATUS_IN_PROGRESS) ? 'selected' : '' ?>>
                        <?= STATUS_IN_PROGRESS ?>
                    </option>
                    <option value="<?= STATUS_COMPLETED ?>" <?= ($status_filter === STATUS_COMPLETED) ? 'selected' : '' ?>>
                        <?= STATUS_COMPLETED ?>
                    </option>
                </select>
            </form>
            
            <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Create Job Order
            </button>
        </div>
    </div>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?= safe_output($error_message) ?>
        </div>
    <?php endif; ?>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Address</th>
                    <th>Contact</th>
                    <th>Equipment</th>
                    <th>Issue</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="7" class="text-center">No job orders found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td>
                                <strong><?= safe_output($order['customer_name']) ?></strong><br>
                            </td>
                            <td>
                            <strong><?= safe_output($order['address']) ?></strong><br>
                            </td>
                            <td><?= safe_output($order['contact']) ?></td>
                            <td><?= safe_output($order['equipment']) ?></td>
                            <td><?= strlen($order['issue']) > 50 ? substr(safe_output($order['issue']), 0, 50) . '...' : safe_output($order['issue']) ?></td>
                            <td>
                                <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $order['status'])) ?>">
                                    <?= safe_output($order['status']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-primary btn-sm" onclick='openEditModal(<?= json_encode($order) ?>)'>
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $order['id'] ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this job order?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Job Order Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-plus-circle"></i> Add New Job Order</h2>
            <span class="close" onclick="closeAddModal()">&times;</span>
        </div>
        <form method="POST">
            <div class="modal-body">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label for="customer_name">Customer Name</label>
                    <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" class="form-control" id="address" name="address" required>
                </div>
                
                <div class="form-group">
                    <label for="contact">Contact Number</label>
                    <input type="text" class="form-control" id="contact" name="contact" required>
                </div>
                
                <div class="form-group">
                    <label for="equipment">Equipment Type</label>
                    <select class="form-control" id="equipment" name="equipment" required>
                        <option value="">Select Equipment</option>
                        <option value="<?= EQUIPMENT_AC ?>"><?= EQUIPMENT_AC ?></option>
                        <option value="<?= EQUIPMENT_REFRIGERATOR ?>"><?= EQUIPMENT_REFRIGERATOR ?></option>
                        <option value="<?= EQUIPMENT_OTHER ?>"><?= EQUIPMENT_OTHER ?></option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="issue">Issue Description</label>
                    <textarea class="form-control" id="issue" name="issue" rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="<?= STATUS_PENDING ?>" selected><?= STATUS_PENDING ?></option>
                        <option value="<?= STATUS_IN_PROGRESS ?>"><?= STATUS_IN_PROGRESS ?></option>
                        <option value="<?= STATUS_COMPLETED ?>"><?= STATUS_COMPLETED ?></option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="closeAddModal()">Cancel</button>
                <button type="submit" class="btn btn-success">Save Job Order</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Job Order Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-edit"></i> Edit Job Order</h2>
            <span class="close" onclick="closeEditModal()">&times;</span>
        </div>
        <form method="POST">
            <div class="modal-body">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="form-group">
                    <label for="edit_customer_name">Customer Name</label>
                    <input type="text" class="form-control" id="edit_customer_name" name="customer_name" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_address">Address</label>
                    <input type="text" class="form-control" id="edit_address" name="address" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_contact">Contact Number</label>
                    <input type="text" class="form-control" id="edit_contact" name="contact" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_equipment">Equipment Type</label>
                    <select class="form-control" id="edit_equipment" name="equipment" required>
                        <option value="<?= EQUIPMENT_AC ?>"><?= EQUIPMENT_AC ?></option>
                        <option value="<?= EQUIPMENT_REFRIGERATOR ?>"><?= EQUIPMENT_REFRIGERATOR ?></option>
                        <option value="<?= EQUIPMENT_OTHER ?>"><?= EQUIPMENT_OTHER ?></option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_issue">Issue Description</label>
                    <textarea class="form-control" id="edit_issue" name="issue" rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="edit_status">Status</label>
                    <select class="form-control" id="edit_status" name="status" required>
                        <option value="<?= STATUS_PENDING ?>"><?= STATUS_PENDING ?></option>
                        <option value="<?= STATUS_IN_PROGRESS ?>"><?= STATUS_IN_PROGRESS ?></option>
                        <option value="<?= STATUS_COMPLETED ?>"><?= STATUS_COMPLETED ?></option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn btn-success">Update Job Order</button>
            </div>
        </form>
    </div>
</div>

<script>
// Modal functions
function openAddModal() {
    document.getElementById('addModal').style.display = 'block';
}

function closeAddModal() {
    document.getElementById('addModal').style.display = 'none';
}

function openEditModal(order) {
    document.getElementById('edit_id').value = order.id;
    document.getElementById('edit_customer_name').value = order.customer_name;
    document.getElementById('edit_address').value = order.address;
    document.getElementById('edit_contact').value = order.contact;
    document.getElementById('edit_equipment').value = order.equipment;
    document.getElementById('edit_issue').value = order.issue;
    document.getElementById('edit_status').value = order.status;
    document.getElementById('editModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

// Close modals when clicking outside
window.onclick = function(event) {
    if (event.target.className === 'modal') {
        event.target.style.display = 'none';
    }
}

// Close modals with ESC key
document.onkeydown = function(evt) {
    evt = evt || window.event;
    if (evt.key === "Escape") {
        closeAddModal();
        closeEditModal();
    }
};
</script>

</body>
</html>