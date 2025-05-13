<?php
// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db/dbconn.php';

// Define constants for configuration
define('MAX_QUANTITY', 100);
define('MAX_SERVICES', 20);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $clientName = trim(filter_input(INPUT_POST, 'client-name', FILTER_SANITIZE_STRING));
    $address = trim(filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING));
    $contact = trim(filter_input(INPUT_POST, 'contact', FILTER_SANITIZE_STRING));
    $date = trim(filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING));
    $servicesRaw = $_POST['services'] ?? '';
    $total = filter_input(INPUT_POST, 'total', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $amountPaid = filter_input(INPUT_POST, 'amount-paid', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $changeAmount = filter_input(INPUT_POST, 'change_amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // Validate required fields
    if (empty($clientName) || empty($address) || empty($contact) || empty($date)) {
        sendErrorResponse('All customer information fields are required.');
    }

    // Validate services
    $services = json_decode($servicesRaw, true);
    if (!is_array($services)) {
        sendErrorResponse('Invalid services data format.');
    }

    // Validate each service
    foreach ($services as $service) {
        if (empty($service['service']) || 
            !is_numeric($service['amount']) || 
            !is_numeric($service['quantity']) || 
            $service['quantity'] <= 0 || 
            $service['quantity'] > MAX_QUANTITY) {
            sendErrorResponse('Invalid service data.');
        }
    }

    // Validate financial values
    if ($total <= 0 || $amountPaid < 0 || $changeAmount < 0) {
        sendErrorResponse('Invalid financial values.');
    }

    try {
        $db->beginTransaction();
    
        // Insert customer with prepared statement
        $stmt = $db->prepare("INSERT INTO customers (name, address, contact) VALUES (?, ?, ?)");
        $stmt->execute([$clientName, $address, $contact]);
        $customerId = $db->lastInsertId();
    
        $db->commit();
    
        // Redirect to record-customer.php with the new customer's ID
        header("Location: record-customer.php?customer_id=$customerId");
        exit;
    } catch (PDOException $e) {
        $db->rollBack();
        sendErrorResponse('Database error: ' . $e->getMessage());
    }
}

function sendErrorResponse($message) {
    $_SESSION['error_message'] = $message;
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

// Get services for dropdown
$services = [];
try {
    $stmt = $db->query("SELECT id, service_name, price FROM services WHERE active = 1 ORDER BY service_name");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching services: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashiering System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/casheirng.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --success-color: #1cc88a;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
            --light-gray: #f8f9fc;
        }
        
        body {
            font-family: 'Nunito', sans-serif;
            background-color: var(--light-gray);
        }
        
        .content {
            margin-left: 220px;
            padding: 20px;
            transition: margin-left 0.3s;
        }
        
        
        @media (max-width: 768px) {
            .content {
                margin-left: 0;
            }
        }
        
        .module-content {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        h1, h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #5a5c69;
        }
        
        .form-control, .form-select {
            border-radius: 0.35rem;
            padding: 0.75rem 1rem;
            border: 1px solidrgb(20, 22, 41);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2653d4;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }
        
        .table {
            margin-top: 1.5rem;
            border-radius: 0.35rem;
            overflow: hidden;
        }
        
        .table th {
            background-color: var(--primary-color);
            color: #5a5c69;
            font-weight: 600;
        }
        
        .alert {
            border-radius: 0.35rem;
        }
        
        .input-group-text {
            background-color: #eaecf4;
        }
        
        #services-table tbody tr:hover {
            background-color: rgba(78, 115, 223, 0.05);
        }
        
        .readonly-input {
            background-color: #f8f9fc;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            #receipt, #receipt * {
                visibility: visible;
            }
            #receipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 20px;
                font-size: 14px;
            }
            .no-print {
                display: none !important;
            }
            .receipt-header {
                text-align: center;
                margin-bottom: 20px;
                border-bottom: 2px dashed #ccc;
                padding-bottom: 10px;
            }
            .receipt-title {
                font-size: 24px;
                font-weight: bold;
                margin-bottom: 5px;
            }
            .receipt-subtitle {
                font-size: 16px;
                color: #666;
            }
            .receipt-details {
                margin-bottom: 20px;
            }
            .receipt-table {
                width: 100%;
                border-collapse: collapse;
                margin: 15px 0;
            }
            .receipt-table th {
                text-align: left;
                border-bottom: 1px solid #ddd;
                padding: 8px 0;
            }
            .receipt-table td {
                padding: 8px 0;
                border-bottom: 1px solid #eee;
            }
            .receipt-table .text-right {
                text-align: right;
            }
            .receipt-totals {
                margin-top: 20px;
                border-top: 2px dashed #ccc;
                padding-top: 10px;
            }
            .receipt-footer {
                text-align: center;
                margin-top: 30px;
                font-size: 12px;
                color: #888;
            }
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Cashiering System</h1>
        <div id="current-date" class="text-muted"></div>
    </div>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['error_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    
    <form method="POST" id="cashier-form" onsubmit="return prepareForm()" novalidate>
        <div class="module-content">
            <h2 class="mb-4">Customer Information</h2>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="client-name" class="form-label">Client's Name</label>
                    <input type="text" class="form-control" id="client-name" name="client-name" required>
                    <div class="invalid-feedback">Please provide client's name.</div>
                </div>
                
                <div class="col-md-6">
                    <label for="contact" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="contact" name="contact" required>
                    <div class="invalid-feedback">Please provide contact number.</div>
                </div>
                
                <div class="col-12">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name="address" required>
                    <div class="invalid-feedback">Please provide address.</div>
                </div>
                
                <div class="col-md-4">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date" required>
                    <div class="invalid-feedback">Please select a date.</div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <h2 class="mb-4">Service Details</h2>
            
            <div class="row g-3">
                <div class="col-md-6">
                <div class="form-group">
                <select class="form-control" id="service-dropdown" name="service-dropdown" required onchange="updateAmount()">
                    <option value="">Select Service</option>
                    <?php
                    $services = $db->query("SELECT id, service_name, price FROM services");
                    if ($services && $services->rowCount() > 0):
                        while ($service = $services->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $service['id']; ?>" data-price="<?php echo $service['price']; ?>">
                                <?php echo htmlspecialchars($service['service_name']); ?>
                            </option>
                        <?php endwhile;
                    endif;
                    ?>
                </select>
                    <div class="invalid-feedback">Please select a service.</div>
                </div>
                
                <div class="col-md-4">
                    <label for="amount" class="form-label">Amount</label>
                    <div class="input-group">
                        <span class="input-group-text">₱</span>
                        <input type="number" class="form-control readonly-input" id="amount" step="0.01" readonly>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="quantity" min="1" max="<?= MAX_QUANTITY ?>" value="1">
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                <button type="button" class="btn btn-primary me-md-2" onclick="addService()">
                    <i class="fas fa-plus-circle"></i> Add Service
                </button>
            </div>
            
            <div class="table-responsive mt-4">
                <table class="table table-bordered table-hover" name="services-table" id="services-table">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Amount</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-6 offset-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="total-amount" class="form-label">Total Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="text" class="form-control readonly-input" id="total-amount" readonly>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="amount-paid" class="form-label">Amount Paid</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" class="form-control" id="amount-paid" name="amount-paid" 
                                           required step="0.01" min="0" oninput="calculateChange()">
                                </div>
                                <div class="invalid-feedback">Please enter amount paid.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="change" class="form-label">Change</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="text" class="form-control readonly-input" id="change" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <input type="hidden" name="services" id="services-json" required>
            <input type="hidden" name="total" id="total-hidden">
            <input type="hidden" name="change_amount" id="change-hidden">
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <button type="reset" class="btn btn-secondary me-md-2">
                    <i class="fas fa-undo"></i> Reset
                </button>
                <button type="button" class="btn btn-primary me-md-2 no-print" onclick="generateReceipt()">
            <i class="fas fa-print"></i> Print Receipt
        </button>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Submit Transaction
                </button>
            </div>
        </div>
    </form>
</div>
    <!-- Hidden receipt div that will be printed -->
    <div id="receipt" style="display: none;">
        <div class="receipt-header">
            <div class="receipt-title">Gerry's Refrigeration and Aircon Repair Shop</div>
            <div class="receipt-subtitle">Official Receipt</div>
            <div>123 Business Address, City</div>
            <div>Contact: (123) 456-7890</div>
        </div>
        
        <div class="receipt-details">
            <div><strong>Date:</strong> <span id="receipt-date"></span></div>
            <div><strong>Customer:</strong> <span id="receipt-customer"></span></div>
            <div><strong>Contact:</strong> <span id="receipt-contact"></span></div>
            <div><strong>Address:</strong> <span id="receipt-address"></span></div>
        </div>
        
        <table class="receipt-table">
            <thead>
                <tr>
                    <th>Service</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody id="receipt-services">
                <!-- Services will be added here by JavaScript -->
            </tbody>
        </table>
        
        <div class="receipt-totals">
            <div style="float: right; width: 200px;">
                <div style="display: flex; justify-content: space-between;">
                    <strong>Subtotal:</strong>
                    <span id="receipt-subtotal">₱0.00</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <strong>Amount Paid:</strong>
                    <span id="receipt-paid">₱0.00</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 16px;">
                    <strong>Change:</strong>
                    <span id="receipt-change">₱0.00</span>
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>
        
        <div class="receipt-footer">
            <div>Thank you for your business!</div>
            <div>This receipt is computer generated and does not require a signature.</div>
        </div>
    </div>
</div>

<!-- Font Awesome for icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    let services = [];
    const maxServices = <?= MAX_SERVICES ?>;

    // Set current date as default
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('date').value = today;
        document.getElementById('current-date').textContent = 'Today: ' + new Date().toLocaleDateString();
        
        // Initialize form validation
        initFormValidation();
    });

    function initFormValidation() {
        const form = document.getElementById('cashier-form');
        
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    }

    function addService() {
        const dropdown = document.getElementById("service-dropdown");
        const selectedOption = dropdown.options[dropdown.selectedIndex];
        
        if (!selectedOption.value) {
            showAlert('Please select a service first.', 'danger');
            return;
        }
        
        if (services.length >= maxServices) {
            showAlert(`Maximum of ${maxServices} services per transaction.`, 'warning');
            return;
        }

        const service = selectedOption.textContent.split('(')[0].trim();
        const amount = parseFloat(document.getElementById("amount").value);
        const quantity = parseInt(document.getElementById("quantity").value);

        if (isNaN(amount) || amount <= 0 || isNaN(quantity) || quantity <= 0) {
            showAlert('Please enter valid amount and quantity (greater than 0).', 'danger');
            return;
        }

        const total = amount * quantity;
        services.push({ 
            service, 
            amount, 
            quantity, 
            total 
        });

        updateServicesTable();
        document.getElementById("quantity").value = "1";
        showAlert('Service added successfully!', 'success');
    }

    function removeService(index) {
        services.splice(index, 1);
        updateServicesTable();
        showAlert('Service removed.', 'info');
    }

    function updateServicesTable() {
        const tbody = document.querySelector("#services-table tbody");
        tbody.innerHTML = "";

        if (services.length === 0) {
            const row = document.createElement("tr");
            row.innerHTML = `<td colspan="5" class="text-center text-muted">No services added yet</td>`;
            tbody.appendChild(row);
            return;
        }

        services.forEach((item, index) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${escapeHtml(item.service)}</td>
                <td class="text-end">₱${item.amount.toFixed(2)}</td>
                <td class="text-center">${item.quantity}</td>
                <td class="text-end">₱${item.total.toFixed(2)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeService(${index})">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });

        calculateTotals();
    }

    function calculateTotals() {
        const total = services.reduce((sum, s) => sum + s.total, 0);
        document.getElementById('total-amount').value = total.toFixed(2);
        document.getElementById('total-hidden').value = total.toFixed(2);
        calculateChange();
    }

    function calculateChange() {
        const total = parseFloat(document.getElementById('total-amount').value) || 0;
        const paid = parseFloat(document.getElementById('amount-paid').value) || 0;
        const change = paid - total;
        
        document.getElementById('change').value = change.toFixed(2);
        document.getElementById('change-hidden').value = change.toFixed(2);
        
        // Highlight if change is negative
        const changeField = document.getElementById('change');
        if (change < 0) {
            changeField.classList.add('text-danger');
            changeField.classList.remove('text-success');
        } else {
            changeField.classList.add('text-success');
            changeField.classList.remove('text-danger');
        }
    }

    function prepareForm() {
        if (services.length === 0) {
            showAlert('Please add at least one service.', 'danger');
            return false;
        }
        
        const paid = parseFloat(document.getElementById('amount-paid').value) || 0;
        const total = parseFloat(document.getElementById('total-amount').value) || 0;
        
        if (paid < total) {
            if (!confirm('Amount paid is less than total. Are you sure you want to proceed?')) {
                return false;
            }
        }
        
        document.getElementById("services-json").value = JSON.stringify(services);
        return true;
    }

    function updateAmount() {
        const dropdown = document.getElementById("service-dropdown");
        const selectedOption = dropdown.options[dropdown.selectedIndex];
        const price = selectedOption.getAttribute("data-price");
        document.getElementById("amount").value = price ? parseFloat(price).toFixed(2) : '';
    }

    function showAlert(message, type) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alert.style.top = '20px';
        alert.style.right = '20px';
        alert.style.zIndex = '9999';
        alert.style.maxWidth = '400px';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        document.body.appendChild(alert);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 150);
        }, 3000);
    }

    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function generateReceipt() {
        if (services.length === 0) {
            showAlert('Please add at least one service before printing.', 'warning');
            return;
        }
        
        // Get form values
        const customerName = document.getElementById('client-name').value;
        const contact = document.getElementById('contact').value;
        const address = document.getElementById('address').value;
        const date = document.getElementById('date').value;
        const amountPaid = parseFloat(document.getElementById('amount-paid').value) || 0;
        const total = parseFloat(document.getElementById('total-amount').value) || 0;
        const change = parseFloat(document.getElementById('change').value) || 0;
        
        // Format date
        const formattedDate = new Date(date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        // Update receipt content
        document.getElementById('receipt-date').textContent = formattedDate;
        document.getElementById('receipt-customer').textContent = customerName;
        document.getElementById('receipt-contact').textContent = contact;
        document.getElementById('receipt-address').textContent = address;
        
        // Add services to receipt
        const receiptServices = document.getElementById('receipt-services');
        receiptServices.innerHTML = '';
        
        services.forEach(service => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${escapeHtml(service.service)}</td>
                <td class="text-right">₱${service.amount.toFixed(2)}</td>
                <td class="text-right">${service.quantity}</td>
                <td class="text-right">₱${service.total.toFixed(2)}</td>
            `;
            receiptServices.appendChild(row);
        });
        
        // Update totals
        document.getElementById('receipt-subtotal').textContent = `₱${total.toFixed(2)}`;
        document.getElementById('receipt-paid').textContent = `₱${amountPaid.toFixed(2)}`;
        document.getElementById('receipt-change').textContent = `₱${change.toFixed(2)}`;
        
        // Show and print the receipt
        const receipt = document.getElementById('receipt');
        receipt.style.display = 'block';
        window.print();
        receipt.style.display = 'none';
    }
</script>
</body>
</html>