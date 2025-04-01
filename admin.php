<?php
require 'db/dbconn.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $age = intval($_POST['age']);
    $role = trim($_POST['role']);
    $password = isset($_POST['password']) && !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

    try {
        if ($user_id) {
            // Update existing user
            if ($password) {
                $stmt = $db->prepare("UPDATE users SET first_name=?, last_name=?, email=?, username=?, age=?, role=?, password_hash=? WHERE id=?");
                $stmt->execute([$first_name, $last_name, $email, $username, $age, $role, $password, $user_id]);
            } else {
                $stmt = $db->prepare("UPDATE users SET first_name=?, last_name=?, email=?, username=?, age=?, role=? WHERE id=?");
                $stmt->execute([$first_name, $last_name, $email, $username, $age, $role, $user_id]);
            }
            echo "<script>alert('Updated Successfully!'); window.location.href = 'admin.php';</script>";
        } else {
            // Check if username or email already exists
            $checkStmt = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $checkStmt->execute([$username, $email]);
            
            if ($checkStmt->rowCount() > 0) {
                echo "<script>
                    alert('Registration Failed: Username or email already exists');
                    window.history.back();
                </script>";
                exit();
            } else {
                // Insert new user
                $stmt = $db->prepare("INSERT INTO users (first_name, last_name, email, username, age, role, password_hash) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$first_name, $last_name, $email, $username, $age, $role, $password]);
                
                echo "<script>
                    alert('Added Successfully!');
                    window.location.href = 'admin.php';
                </script>";
                exit();
            }
        }
    } catch (PDOException $e) {
        echo "<script>
            alert('Operation Failed: " . addslashes($e->getMessage()) . "');
            window.history.back();
        </script>";
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="css/admin.css">
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
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            width: 50%;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .close {
            float: right;
            font-size: 24px;
            cursor: pointer;
        }
        .form-group {
            margin-bottom: 10px;
        }
        .form-group label {
            display: block;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .btn {
            background-color: #3498db;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        .edit-btn {
            background-color: #f39c12;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            margin-right: 5px;
        }
        .delete-btn {
    background-color: #e74c3c;
    color: white;
    padding: 5px 10px;
    border: none;
    cursor: pointer;
    border-radius: 4px;
}
/* Modal Enhancements */
.modal-content {
    background-color: #f8f9fa;
    margin: 5% auto;
    padding: 30px;
    width: 50%;
    max-width: 600px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.modal-header {
    color: #2c3e50;
    margin-bottom: 25px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eaeaea;
}

.close {
    color: #7f8c8d;
    float: right;
    font-size: 28px;
    font-weight: bold;
    transition: color 0.3s;
}

.close:hover {
    color: #e74c3c;
}

.form-row {
    display: flex;
    gap: 20px;
}

.form-row .form-group {
    flex: 1;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #34495e;
}

.form-input {
    width: 100%;
    padding: 12px;
    border: 2px solid #dfe6e9;
    border-radius: 6px;
    font-size: 16px;
    transition: border-color 0.3s;
    background-color: #fff;
}

.form-input:focus {
    border-color: #3498db;
    outline: none;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
}

.form-select {
    width: 100%;
    padding: 12px;
    border: 2px solid #dfe6e9;
    border-radius: 6px;
    font-size: 16px;
    background-color: #fff;
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 1em;
}

.form-hint {
    display: block;
    margin-top: 5px;
    font-size: 12px;
    color: #7f8c8d;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 25px;
}

.btn-cancel {
    background-color: #95a5a6;
    color: white;
    padding: 12px 20px;
    border: none;
    cursor: pointer;
    border-radius: 6px;
    font-weight: 600;
    transition: background-color 0.3s;
}

.btn-cancel:hover {
    background-color: #7f8c8d;
}

.btn-submit {
    background-color: #2ecc71;
    color: white;
    padding: 12px 20px;
    border: none;
    cursor: pointer;
    border-radius: 6px;
    font-weight: 600;
    transition: background-color 0.3s;
}

.btn-submit:hover {
    background-color: #27ae60;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .modal-content {
        width: 90%;
        padding: 20px;
    }
    
    .form-row {
        flex-direction: column;
        gap: 15px;
    }
}
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h1>Admin</h1>
        <div class="module-content">
            <h2>Create / Edit User Account</h2>
            <button class="btn" id="openModal">Add New User</button>
            <table>
                <tr>
                    <th>User ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
                <?php
                try {
                    $stmt = $db->query("SELECT * FROM users ORDER BY id DESC");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['first_name']}</td>
                            <td>{$row['last_name']}</td>
                            <td>{$row['username']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['role']}</td>
                            <td>
                                <button class='edit-btn' data-user='" . htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') . "'>Edit</button>
                                <button class='delete-btn' data-id='{$row['id']}' style='background-color:#e74c3c'>Delete</button>
                            </td>
                        </tr>";
                    }
                } catch (PDOException $e) {
                    echo "<tr><td colspan='7'>Error: " . $e->getMessage() . "</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>

    <!-- Modal -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 id="modalTitle" class="modal-header">Add New User</h2>
        <form method="POST" action="" class="modal-form">
            <input type="hidden" id="user_id" name="user_id">
            <div class="name-fields form-row">
                <div class="form-group">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" id="first_name" name="first_name" placeholder="Enter first name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" id="last_name" name="last_name" placeholder="Enter last name" class="form-input" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter email" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" placeholder="Choose username" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="age" class="form-label">Age</label>
                <input type="number" id="age" name="age" placeholder="Enter age" min="18" max="100" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="role" class="form-label">Role</label>
                <select id="role" name="role" class="form-select" required>
                    <option value="" disabled selected>Select role</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" placeholder="Create password" class="form-input">
                <small class="form-hint">Leave blank to keep current password when editing</small>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-cancel" onclick="document.getElementById('userModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-submit">Save User</button>
            </div>
        </form>
    </div>
</div>

    <script>
        // Get the modal
        var modal = document.getElementById("userModal");
        
        // Get the button that opens the modal
        var btn = document.getElementById("openModal");
        
        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];
        
        // When the user clicks the button, open the modal 
        btn.onclick = function() {
            document.getElementById("modalTitle").textContent = "Add New User";
            document.getElementById("user_id").value = "";
            document.querySelector("form").reset();
            modal.style.display = "block";
        }
        
        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }
        
        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
        
        // Edit button functionality
        document.querySelectorAll(".edit-btn").forEach(button => {
            button.addEventListener("click", function() {
                let user = JSON.parse(this.dataset.user);
                document.getElementById("modalTitle").textContent = "Edit User";
                document.getElementById("user_id").value = user.id;
                document.getElementById("first_name").value = user.first_name;
                document.getElementById("last_name").value = user.last_name;
                document.getElementById("email").value = user.email;
                document.getElementById("username").value = user.username;
                document.getElementById("age").value = user.age;
                document.getElementById("role").value = user.role;
                document.getElementById("password").required = false; // Make password optional for edits
                modal.style.display = "block";
            });
        });
        // Delete button functionality with confirmation
document.querySelectorAll(".delete-btn").forEach(button => {
    button.addEventListener("click", function() {
        if (confirm("Are you sure you want to delete this user?")) {
            window.location.href = 'delete.php?id=' + this.dataset.id;
        }
    });
});
    </script>
</body>
</html>