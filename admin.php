<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="css/admin.css">

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
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h1>Admin</h1>
        <div class="module-content">
            <h2>Create / Edit User Account</h2>
            <table>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
                <tr>
                    <td>1</td>
                    <td>John Doe</td>
                    <td>Admin</td>
                    <td><a href="create.php">Create</a> | <a href="edit.php?id=1">Edit</a> | <a href="delete.php?id=1">Delete</a></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>