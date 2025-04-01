<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashiering</title>
    <link rel="stylesheet" href="css/casheirng.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        
        .content {
            margin-left: 220px; /* Adjust based on sidebar width */
            padding: 20px;
        }
        
        h1 {
            color: #333;
        }
        
        .module-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .customer-info, .services-section {
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input[type="text"], input[type="date"], input[type="number"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #f2f2f2;
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
        <div class="module-content">
            <h2>Customer Information</h2>
            <div class="customer-info">
                <div class="form-group">
                    <label for="client-name">Client's name</label>
                    <input type="text" id="client-name" name="client-name">
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address">
                </div>
                <div class="form-group">
                    <label for="phone">Phone number</label>
                    <input type="text" id="phone" name="phone">
                </div>
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" placeholder="dd/mm/yyyy">
                </div>
            </div>
            
            <h2>Services</h2>
            <div class="services-section">
                <div class="form-group">
                    <label for="service">Service</label>
                    <input type="text" id="service" name="service">
                </div>
                <div class="form-row">
                    <div class="form-group" style="display: inline-block; width: 48%;">
                        <label for="amount">Amount</label>
                        <input type="number" id="amount" name="amount">
                    </div>
                    <div class="form-group" style="display: inline-block; width: 48%;">
                        <label for="quantity">Quantity</label>
                        <input type="number" id="quantity" name="quantity">
                    </div>
                </div>
                <button class="btn">Add Services</button>
                
                <table>
                    <tr>
                        <th>Service</th>
                        <th>Amount</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                    <!-- Example row -->
                    <tr>
                        <td>Sample Service</td>
                        <td>$50.00</td>
                        <td>1</td>
                        <td>$50.00</td>
                        <td><button class="action-btn">Remove</button></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>