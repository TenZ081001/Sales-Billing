<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Order</title>
    <link rel="stylesheet" href="css/job-order.css">

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

</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content">
    <h1>Job Order</h1>
    <div class="module-content">
        <h2>Create/Modify</h2>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Contact</th>
                <th>Equipment</th>
                <th>Issue</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <tr>
                <td>1</td>
                <td>John Doe</td>
                <td>123-456-7890</td>
                <td>Air Conditioner</td>
                <td>Not Cooling</td>
                <td>In Progress</td>
                <td>
                    <button>Edit</button>
                    <button>Delete</button>
                </td>
            </tr>
            <tr>
                <td>2</td>
                <td>Jane Smith</td>
                <td>987-654-3210</td>
                <td>Refrigerator</td>
                <td>Leaking Water</td>
                <td>Pending</td>
                <td>
                    <button>Edit</button>
                    <button>Delete</button>
                </td>
            </tr>
        </table>
    </div>

    <div class="module-content">
        <h2>Add New Job Order</h2>
        <form action="add-job-order.php" method="POST">
            <label for="customer_name">Customer Name:</label>
            <input type="text" id="customer_name" name="customer_name" required><br><br>

            <label for="contact">Contact:</label>
            <input type="text" id="contact" name="contact" required><br><br>

            <label for="equipment">Equipment:</label>
            <select id="equipment" name="equipment" required>
                <option value="Air Conditioner">Air Conditioner</option>
                <option value="Refrigerator">Refrigerator</option>
                <option value="Other">Other</option>
            </select><br><br>

            <label for="issue">Issue:</label>
            <textarea id="issue" name="issue" rows="4" required></textarea><br><br>

            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="Pending">Pending</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
            </select><br><br>

            <button type="submit">Add Job Order</button>
        </form>
    </div>
</div>
</html>