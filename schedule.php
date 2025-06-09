<?php
require 'db/dbconn.php';

$personnel_stmt = $db->query("SELECT * FROM personnel ORDER BY name ASC");
$services_stmt = $db->query("SELECT * FROM services ORDER BY service_name ASC");

$error_message = '';
$edit_mode = false;
$edit_data = [];

// Handle delete
if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    $deleteStmt = $db->prepare("DELETE FROM bookings WHERE id = ?");
    $deleteStmt->execute([$deleteId]);
    header("Location: schedule.php");
    exit;
}

// Handle edit request (load data)
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $edit_id = $_GET['edit'];
    $stmt = $db->prepare("SELECT * FROM bookings WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer = $_POST['customer_name'];
    $service_id = $_POST['service_type'];
    $time = $_POST['schedule_time'];
    $tech = $_POST['technician'];
    $edit_id = $_POST['edit_id'] ?? null;

    $service_stmt = $db->prepare("SELECT service_name FROM services WHERE id = ?");
    $service_stmt->execute([$service_id]);
    $service_data = $service_stmt->fetch();
    $service_name = $service_data ? $service_data['service_name'] : '';

    $scheduled_time = strtotime($time);
    $start_range = date('Y-m-d H:i:s', $scheduled_time - 1800);
    $end_range = date('Y-m-d H:i:s', $scheduled_time + 1800);

    $stmt = $db->prepare("SELECT * FROM bookings 
                          WHERE technician = ? 
                          AND schedule_time BETWEEN ? AND ?" . ($edit_id ? " AND id != ?" : ""));
    $params = [$tech, $start_range, $end_range];
    if ($edit_id) $params[] = $edit_id;
    $stmt->execute($params);

    if ($stmt->rowCount() > 0) {
        $error_message = "⚠️ This technician is already scheduled for a time too close to this appointment. Please choose a time at least 30 minutes apart.";
    } else {
        if ($edit_id) {
            $sql = "UPDATE bookings SET customer_name = ?, service_type = ?, schedule_time = ?, technician = ? WHERE id = ?";
            $db->prepare($sql)->execute([$customer, $service_name, $time, $tech, $edit_id]);
        } else {
            $sql = "INSERT INTO bookings (customer_name, service_type, schedule_time, technician)
                    VALUES (?, ?, ?, ?)";
            $db->prepare($sql)->execute([$customer, $service_name, $time, $tech]);
        }
        header("Location: schedule.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Repair Shop Scheduler</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #4895ef;
            --secondary: #f0f0f0;
            --accent: #f72585;
            --success: #4cc9f0;
            --warning: #f8961e;
            --danger: #ef233c;
            --text: #2b2d42;
            --dark: #212121;
            --light: #f8f9fa;
            --gray: #adb5bd;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --card-shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: #f5f7fa;
            color: var(--text);
            line-height: 1.6;
        }

        .main-content {
            margin-left: 260px;
            padding: 2rem;
            transition: all 0.3s ease;
            max-width: 1400px;
            width: calc(100% - 260px);
        }

        .container {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            animation: fadeIn 0.5s ease-in-out;
            margin-bottom: 2rem;
            width: 100%;
            box-sizing: border-box;
        }

        h1, h2 {
            color: var(--primary);
            margin-bottom: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        h2 {
            color: var(--dark);
            font-size: 1.5rem;
            margin-top: 2rem;
        }

        .form-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            position: relative;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(0, 0, 0, 0.05);
            width: 100%;
            box-sizing: border-box;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.25rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.75rem;
            color: var(--dark);
            font-weight: 500;
            font-size: 0.95rem;
        }

        input[type="text"],
        input[type="datetime-local"],
        select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--gray);
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            background-color: white;
            box-sizing: border-box;
        }

        input[type="text"]:focus,
        input[type="datetime-local"]:focus,
        select:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }

        .btn {
            background-color: var(--primary);
            color: white;
            padding: 0.85rem 1.75rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            margin-right: 0.75rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .btn:hover {
            background-color: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: var(--card-shadow-hover);
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .btn-danger {
            background-color: var(--danger);
        }

        .btn-danger:hover {
            background-color: #d90429;
        }

        .btn-info {
            background-color: var(--primary-light);
        }

        .btn-info:hover {
            background-color: #3a86ff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-top: 1.5rem;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }

        table th {
            background-color: var(--primary);
            color: white;
            padding: 1.25rem;
            text-align: left;
            font-weight: 600;
        }

        table td {
            padding: 1.25rem;
            border-bottom: 1px solid #e9ecef;
        }

        table tr:last-child td {
            border-bottom: none;
        }

        table tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }

        .table-responsive {
            overflow-x: auto;
            border-radius: 8px;
            width: 100%;
        }

        .status-badge {
            padding: 0.4rem 0.85rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-primary {
            background-color: var(--primary);
            color: white;
        }

        .animate-fade {
            animation: fadeIn 0.4s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Alert System */
        .alert-container {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            width: 90%;
            max-width: 600px;
        }

        .alert {
            background-color: var(--danger);
            color: white;
            padding: 1.25rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            animation: slideDown 0.5s ease-out;
            margin-bottom: 1rem;
        }

        .alert i {
            margin-right: 0.85rem;
            font-size: 1.35rem;
        }

        .alert-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.35rem;
            cursor: pointer;
            padding: 0 0.5rem;
            opacity: 0.8;
            transition: opacity 0.2s ease;
        }

        .alert-close:hover {
            opacity: 1;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-50px);}
            to { opacity: 1; transform: translateY(0);}
        }

        .action-buttons {
            display: flex;
            gap: 0.75rem;
        }

        .form-actions {
            margin-top: 1.5rem;
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 1.5rem;
            }
            .form-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 1rem;
            }
            .action-buttons {
                flex-direction: column;
                gap: 0.5rem;
            }
            .form-actions {
                flex-direction: column;
            }
            .btn {
                width: 100%;
                justify-content: center;
                margin-right: 0;
                margin-bottom: 0.5rem;
            }
            table th, table td {
                padding: 1rem;
            }
            .form-card {
                padding: 1.25rem;
            }
            .form-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            input[type="text"],
            input[type="datetime-local"],
            select {
                padding: 0.65rem 0.9rem;
            }
        }

        /* Loading animation for form submission */
        .btn-loading {
            position: relative;
            pointer-events: none;
        }
        .btn-loading:after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg);}
        }
        @keyframes fadeOut {
            from { opacity: 1; transform: translateY(0);}
            to { opacity: 0; transform: translateY(-50px);}
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <?php if (!empty($error_message)): ?>
        <div class="alert-container">
            <div class="alert">
                <div>
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= htmlspecialchars($error_message) ?>
                </div>
                <button class="alert-close" onclick="this.parentElement.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    <?php endif; ?>

    <div class="container animate-fade">
        <h1><i class="fas fa-calendar-alt"></i> <?= $edit_mode ? 'Edit Appointment' : 'Schedule New Appointment' ?></h1>
        <div class="form-card">
            <form id="bookingForm" action="" method="post">
                <input type="hidden" name="edit_id" value="<?= $edit_mode ? $edit_data['id'] : '' ?>">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="customer_name"><i class="fas fa-user"></i> Customer Name</label>
                        <input type="text" id="customer_name" name="customer_name"
                               value="<?= $edit_mode ? htmlspecialchars($edit_data['customer_name']) : '' ?>"
                               placeholder="Enter customer name" required>
                    </div>
                    <div class="form-group">
                        <label for="service_type"><i class="fas fa-tools"></i> Service Type</label>
                        <select id="service_type" name="service_type" required>
                            <option value="" disabled <?= !$edit_mode ? 'selected' : '' ?>>Select Service</option>
                            <?php
                            $services_stmt = $db->query("SELECT * FROM services ORDER BY service_name ASC");
                            while ($service = $services_stmt->fetch(PDO::FETCH_ASSOC)):
                            ?>
                                <option value="<?= $service['id'] ?>" <?= $edit_mode && $service['service_name'] === $edit_data['service_type'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($service['service_name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="schedule_time"><i class="far fa-clock"></i> Schedule Time</label>
                        <input type="datetime-local" id="schedule_time" name="schedule_time"
                               value="<?= $edit_mode ? date('Y-m-d\TH:i', strtotime($edit_data['schedule_time'])) : '' ?>"
                               required>
                    </div>
                    <div class="form-group">
                        <label for="technician"><i class="fas fa-user-cog"></i> Technician</label>
                        <select id="technician" name="technician" required>
                            <option value="" disabled <?= !$edit_mode ? 'selected' : '' ?>>Select Technician</option>
                            <?php
                            $personnel_stmt = $db->query("SELECT * FROM personnel ORDER BY name ASC");
                            while ($tech = $personnel_stmt->fetch(PDO::FETCH_ASSOC)):
                            ?>
                                <option value="<?= htmlspecialchars($tech['name']) ?>" <?= $edit_mode && $tech['name'] === $edit_data['technician'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tech['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn" id="submitBtn">
                        <i class="fas fa-save"></i> <?= $edit_mode ? 'Update Appointment' : 'Book Appointment' ?>
                    </button>
                    <?php if ($edit_mode): ?>
                        <a href="schedule.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <h2><i class="fas fa-list"></i> Scheduled Appointments</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Time</th>
                        <th>Technician</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $db->query("SELECT * FROM bookings ORDER BY schedule_time ASC");
                    while ($row = $stmt->fetch()):
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['customer_name']) ?></td>
                            <td><span class="status-badge badge-primary"><?= htmlspecialchars($row['service_type']) ?></span></td>
                            <td><?= date('M j, Y g:i A', strtotime($row['schedule_time'])) ?></td>
                            <td><?= htmlspecialchars($row['technician']) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="schedule.php?edit=<?= $row['id'] ?>" class="btn btn-info">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="schedule.php?delete=<?= $row['id'] ?>" class="btn btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this appointment?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const datetimeInput = document.getElementById('schedule_time');
    if (!datetimeInput.value) {
        datetimeInput.style.color = '#6c757d';
    }
    datetimeInput.addEventListener('change', function() {
        this.style.color = '#212529';
    });

    // Auto-hide alert after 5 seconds
    const alert = document.querySelector('.alert');
    if (alert) {
        setTimeout(() => {
            alert.style.animation = 'fadeOut 0.5s ease-out';
            setTimeout(() => {
                alert.parentElement.style.display = 'none';
            }, 500);
        }, 5000);
    }

    // Form submission loading indicator
    const form = document.getElementById('bookingForm');
    const submitBtn = document.getElementById('submitBtn');
    if (form) {
        form.addEventListener('submit', function() {
            submitBtn.classList.add('btn-loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner"></i> Processing...';
        });
    }
});
</script>
</body>
</html>
