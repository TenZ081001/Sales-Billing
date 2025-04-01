<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
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
</style>
    <title>Document</title>
    
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="content">
        <div class="module-content">
            <h1>Billing</h1>
            <h2>Ledger</h2>
            <table>
                <tr>
                    <th>Service ID</th>
                    <th>Customer Name</th>
                    <th>Service Description</th>
                    <th>Status</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
                <tr>
                    <td>1</td>
                    <td>John Doe</td>
                    <td>AC Repair - Cooling Issue</td>
                    <td>Completed</td>
                    <td>$150</td>
                    <td><a href="edit.php?id=1">Edit</a> | <a href="delete.php?id=1">Delete</a></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Jane Smith</td>
                    <td>Refrigerator Repair - Compressor Issue</td>
                    <td>In Progress</td>
                    <td>$200</td>
                    <td><a href="edit.php?id=2">Edit</a> | <a href="delete.php?id=2">Delete</a></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Michael Brown</td>
                    <td>Washing Machine Repair - Drum Issue</td>
                    <td>Pending</td>
                    <td>$120</td>
                    <td><a href="edit.php?id=3">Edit</a> | <a href="delete.php?id=3">Delete</a></td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>Emily Davis</td>
                    <td>Microwave Repair - Heating Issue</td>
                    <td>Completed</td>
                    <td>$90</td>
                    <td><a href="edit.php?id=4">Edit</a> | <a href="delete.php?id=4">Delete</a></td>
                </tr>
            </table>
            <h3>Add New Service</h3>
            <form action="add_service.php" method="POST">
                <label for="customer_name">Customer Name:</label>
                <input type="text" id="customer_name" name="customer_name" required><br><br>
                <label for="service_description">Service Description:</label>
                <input type="text" id="service_description" name="service_description" required><br><br>
                <label for="status">Status:</label>
                <select id="status" name="status">
                    <option value="Pending">Pending</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                </select><br><br>
                <label for="amount">Amount:</label>
                <input type="number" id="amount" name="amount" required><br><br>
                <button type="submit">Add Service</button>
            </form>
        </div>
    </div>
</body>
</html>