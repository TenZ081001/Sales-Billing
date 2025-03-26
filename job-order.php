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
            <h2>Create / Modify</h2>
            <table>
                <tr><th>Order ID</th><th>Description</th><th>Status</th></tr>
                <tr><td>1</td><td>Website Development</td><td>In Progress</td></tr>
            </table>
        </div>
    </div>
</body>
</html>