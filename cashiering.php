<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db/dbconn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientName = trim($_POST['client-name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $date = trim($_POST['date']);
    $servicesRaw = $_POST['services'] ?? '';
    
    // Optional debug log
    file_put_contents('debug_services.txt', print_r($servicesRaw, true));

    $services = json_decode($servicesRaw, true);

    if (!is_array($services) || empty($services)) {
        echo "<script>alert('No valid services were provided.'); window.history.back();</script>";
        exit;
    }

    try {
        $db->beginTransaction();

        // Insert customer
        $stmt = $db->prepare("INSERT INTO customers (name, address, phone, date) VALUES (?, ?, ?, ?)");
        $stmt->execute([$clientName, $address, $phone, $date]);
        $customerId = $db->lastInsertId();

        file_put_contents('debug_log.txt', "Customer ID inserted: $customerId\n", FILE_APPEND);

        // Insert services
        $stmt = $db->prepare("INSERT INTO customer_services (customer_id, service_name, amount, quantity, total) VALUES (?, ?, ?, ?, ?)");

        foreach ($services as $service) {
            $name = trim($service['service']);
            $amount = floatval($service['amount']);
            $quantity = intval($service['quantity']);
            $total = floatval($service['total']);

            $stmt->execute([$customerId, $name, $amount, $quantity, $total]);
        }

        $db->commit();

        echo "<script>alert('Data saved successfully!'); window.location.href='cashiering.php';</script>";
        exit;

    } catch (PDOException $e) {
        $db->rollBack();
        echo "<script>alert('Error: " . $e->getMessage() . "'); window.history.back();</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cashiering</title>
    <link rel="stylesheet" href="css/casheirng.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .content {
            margin-left: 220px;
            padding: 20px;
        }

        .module-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h1, h2 {
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"], input[type="date"], input[type="number"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        .action-btn {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content">
    <h1>Cashiering</h1>
    <form method="POST" onsubmit="return prepareForm();">
        <div class="module-content">
            <h2>Customer Information</h2>
            <div class="form-group">
                <label for="client-name">Client's Name</label>
                <input type="text" id="client-name" name="client-name" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" required>
            </div>
            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" id="date" name="date" required>
            </div>

            <h2>Services</h2>
            <div class="form-group">
                <label for="service">Service</label>
                <input type="text" id="service">
            </div>
            <div class="form-row">
                <div class="form-group" style="display: inline-block; width: 48%;">
                    <label for="amount">Amount</label>
                    <input type="number" id="amount" step="0.01">
                </div>
                <div class="form-group" style="display: inline-block; width: 48%;">
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" min="1">
                </div>
            </div>
            <button type="button" class="btn" onclick="addService()">Add Service</button>

            <table id="services-table">
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

            <input type="hidden" name="services" id="services-json" required>
            <br><br>
            <button type="submit" class="btn">Submit</button>
        </div>
    </form>
</div>

<script>
    let services = [];

    function addService() {
        const service = document.getElementById("service").value.trim();
        const amount = parseFloat(document.getElementById("amount").value);
        const quantity = parseInt(document.getElementById("quantity").value);
        const total = amount * quantity;

        if (!service || isNaN(amount) || isNaN(quantity)) {
            alert("Please enter valid service, amount, and quantity.");
            return;
        }

        services.push({ service, amount, quantity, total });
        updateServicesTable();

        document.getElementById("service").value = "";
        document.getElementById("amount").value = "";
        document.getElementById("quantity").value = "";
    }

    function removeService(index) {
        services.splice(index, 1);
        updateServicesTable();
    }

    function updateServicesTable() {
        const tbody = document.querySelector("#services-table tbody");
        tbody.innerHTML = "";

        services.forEach((item, index) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${item.service}</td>
                <td>$${item.amount.toFixed(2)}</td>
                <td>${item.quantity}</td>
                <td>$${item.total.toFixed(2)}</td>
                <td><button type="button" class="action-btn" onclick="removeService(${index})">Remove</button></td>
            `;
            tbody.appendChild(row);
        });
    }

    function prepareForm() {
        if (services.length === 0) {
            alert("Please add at least one service.");
            return false;
        }
        document.getElementById("services-json").value = JSON.stringify(services);
        console.log("Submitting services:", services);
        return true;
    }
</script>
</body>
</html>
