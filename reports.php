<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="css/report.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h1>Reports</h1>
        <div class="module-content">
            <h2>Collections</h2>
            <table>
                <tr><th>Collection ID</th><th>Amount</th><th>Date</th></tr>
                <tr><td>1</td><td>$300</td><td>2023-11-10</td></tr>
            </table>
            <h2>Back Jobs</h2>
            <table>
                <tr><th>Job ID</th><th>Description</th><th>Status</th></tr>
                <tr><td>1</td><td>Backup</td><td>Pending</td></tr>
            </table>
            <h2>Account Receivables</h2>
            <table>
                <tr><th>Invoice ID</th><th>Amount</th><th>Due Date</th></tr>
                <tr><td>1</td><td>$400</td><td>2023-12-05</td></tr>
            </table>
            <h2>Job Order List</h2>
            <table>
                <tr><th>Order ID</th><th>Description</th><th>Status</th></tr>
                <tr><td>1</td><td>Design</td><td>Completed</td></tr>
            </table>
            <h2>Services</h2>
            <table>
                <tr><th>Service ID</th><th>Service Name</th><th>Revenue</th></tr>
                <tr><td>1</td><td>Consulting</td><td>$1000</td></tr>
            </table>
            <h2>Supplies</h2>
            <table>
                <tr><th>Supply ID</th><th>Item Name</th><th>Usage</th></tr>
                <tr><td>1</td><td>Paper</td><td>200 sheets</td></tr>
            </table>
            <h2>Customer</h2>
            <table>
                <tr><th>Customer ID</th><th>Name</th><th>Total Spent</th></tr>
                <tr><td>1</td><td>Alice Johnson</td><td>$500</td></tr>
            </table>
            <h2>Personnel</h2>
            <table>
                <tr><th>Employee ID</th><th>Name</th><th>Hours Worked</th></tr>
                <tr><td>1</td><td>Jane Smith</td><td>40</td></tr>
            </table>
        </div>
    </div>
</body>
</html>