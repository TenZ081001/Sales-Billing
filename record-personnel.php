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
}
</style>
    <title>Document</title>
</head>
<?php include 'sidebar.php'; ?>
<body>
    <div class="content">
        <div class="module-content">
            <div id="personnel" class="section">
            <h1>Records</h1>
                <h2>Personnel</h2>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Action</th>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Jane Smith</td>
                        <td>Manager</td>
                        <td><a href="add.php">Add</a> | <a href="edit.php?id=1">Edit</a> | <a href="delete.php?id=1">Delete</a></td>
                    </tr>
                </table>
            </div>
</body>
</html>