<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <style>.module-content table {
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
}</style>
    <title>Document</title>
</head>
<?php include 'sidebar.php'; ?>
<body>
    <div class="content">
        <div class="module-content">
        <div id="services" class="section">
        <h1>Records</h1>
                <h2>Services</h2>
                <table>
                    <tr>
                        <th>Service ID</th>
                        <th>Service Name</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Consulting</td>
                        <td>$100</td>
                        <td><a href="edit.php?id=1">Edit</a> | <a href="delete.php?id=1">Delete</a></td>
                    </tr>
                </table></tr>
            </div>
</body>
</html>