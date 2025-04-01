<?php
require 'db/dbconn.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $age = intval($_POST['age']);
    $role = trim($_POST['role']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
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
                alert('Registration Successful! Welcome to Gerry\\'s Refrigeration and Aircon Repair Shop');
                window.location.href = 'Login.php';
            </script>";
            exit();
        }
    } catch (PDOException $e) {
        echo "<script>
            alert('Registration Failed: " . addslashes($e->getMessage()) . "');
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
    <title>Registration Form</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .registration-container {
            background: linear-gradient(to bottom, white 0%, white 70%, #34495e 100%);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 500px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
        }

        .registration-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .registration-header h1 {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 10px;
            background: linear-gradient(to right, #3498db, #2980b9);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .registration-header p {
            color: #7f8c8d;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-size: 14px;
            font-weight: 500;
        }

        .form-group input, 
        .form-group select {
            width: 100%;
            padding: 14px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            background-color: rgba(255, 255, 255, 0.8);
        }

        .form-group input:focus, 
        .form-group select:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
            background-color: white;
        }

        .name-fields {
            display: flex;
            gap: 15px;
        }

        .name-fields .form-group {
            flex: 1;
        }

        .password-strength {
            height: 4px;
            background-color: #e0e0e0;
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
        }

        .strength-meter {
            height: 100%;
            width: 0%;
            background: linear-gradient(to right, #e74c3c, #f39c12, #2ecc71);
            transition: all 0.3s;
        }

        .register-button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(to right, #3498db, #2980b9);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .register-button:hover {
            background: linear-gradient(to right, #2980b9, #3498db);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            font-size: 15px;
            color: white;
            font-weight: 500;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        .login-link a {
            color: #4fc3f7;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .login-link a:hover {
            color: #0288d1;
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .name-fields {
                flex-direction: column;
                gap: 20px;
            }
            
            .registration-container {
                padding: 30px 20px;
                background: linear-gradient(to bottom, white 0%, white 65%, #34495e 100%);
            }
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <div class="registration-header">
            <h1>Create Your Account</h1>
            <p>Join Gerry's Refrigeration and Aircon Repair Shop</p>
        </div>

        <form method="POST" action="">
            <div class="name-fields">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Choose a username" required>
            </div>

            <div class="form-group">
                <label for="age">Age</label>
                <input type="number" id="age" name="age" placeholder="Enter your age" min="18" max="100" required>
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="" disabled selected>Select your role</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Create a password" required>
                <div class="password-strength">
                    <div class="strength-meter" id="strength-meter"></div>
                </div>
            </div>

            <button type="submit" class="register-button">Register</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="Login.php">Log in</a>
        </div>
    </div>

    <script>
        // Simple password strength indicator
        const passwordInput = document.getElementById('password');
        const strengthMeter = document.getElementById('strength-meter');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length > 0) strength += 20;
            if (password.length >= 8) strength += 30;
            if (/[A-Z]/.test(password)) strength += 20;
            if (/\d/.test(password)) strength += 20;
            if (/[^A-Za-z0-9]/.test(password)) strength += 10;
            
            strengthMeter.style.width = strength + '%';
        });
    </script>
</body>
</html>