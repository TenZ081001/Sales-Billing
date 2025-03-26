<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    background: linear-gradient(135deg, #2c3e50, #34495e); /* Gradient background */
    color: #333;
}

.sidebar {
    width: 250px;
    background-color: #2c3e50;
    color: white;
    padding: 15px;
    height: 100vh;
    box-sizing: border-box;
    position: fixed;
}

.sidebar h2 {
    text-align: center;
    margin-bottom: 20px;
}

.sidebar ul {
    list-style-type: none;
    padding: 0;
}

.sidebar ul li {
    margin: 15px 0;
}

.sidebar ul li a {
    color: white;
    text-decoration: none;
    display: block;
    padding: 10px;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.sidebar ul li a:hover {
    background-color: #34495e;
}

.content {
    flex-grow: 1;
    padding: 20px;
    margin-left: 250px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin: 20px;
    margin-left: 270px; /* Adjusted for sidebar width */
}

h1 {
    color: #2c3e50;
    margin-bottom: 20px;
}

.dashboard-widgets {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.widget {
    background-color: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.widget:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.widget h3 {
    margin-top: 0;
    color: #2c3e50;
    font-size: 18px;
}

.widget p {
    font-size: 24px;
    font-weight: bold;
    color: #34495e;
    margin: 10px 0;
}

.widget ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.widget ul li {
    padding: 8px 0;
    border-bottom: 1px solid #e0e0e0;
    color: #555;
}

.widget ul li:last-child {
    border-bottom: none;
}

.widget .icon {
    font-size: 24px;
    color: #34495e;
    margin-bottom: 10px;
}

/* Progress Bar */
.progress-bar {
    background-color: #e0e0e0;
    border-radius: 4px;
    overflow: hidden;
    height: 10px;
    margin: 10px 0;
}

.progress-bar .progress {
    background-color: #3498db;
    height: 100%;
    border-radius: 4px;
}

/* Buttons */
.button {
    display: inline-block;
    padding: 10px 20px;
    background-color: #3498db;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.button:hover {
    background-color: #2980b9;
}

/* Charts */
.chart {
    width: 100%;
    height: 150px;
    background-color: #f4f4f9;
    border-radius: 8px;
    margin: 10px 0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .content {
        margin-left: 20px;
    }

    .dashboard-widgets {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
}
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="content">
        <h1>Dashboard</h1>
        <div class="dashboard-widgets">
            <div class="widget">
                <div class="icon"><i class="fas fa-chart-line"></i></div>
                <h3>Total Sales</h3>
                <p>$25,000</p>
                <div class="progress-bar">
                    <div class="progress" style="width: 75%;"></div>
                </div>
            </div>
            <div class="widget">
                <div class="icon"><i class="fas fa-tasks"></i></div>
                <h3>Active Job Orders</h3>
                <p>15</p>
                <a href="#" class="button">View Details</a>
            </div>
            <div class="widget">
                <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                <h3>Pending Collections</h3>
                <p>$5,000</p>
                <div class="progress-bar">
                    <div class="progress" style="width: 50%;"></div>
                </div>
            </div>
            <div class="widget">
                <div class="icon"><i class="fas fa-bell"></i></div>
                <h3>Recent Activities</h3>
                <ul>
                    <li>New job order created</li>
                    <li>Invoice #123 paid</li>
                    <li>New customer registered</li>
                </ul>
            </div>
            <div class="widget">
                <div class="icon"><i class="fas fa-boxes"></i></div>
                <h3>Inventory Status</h3>
                <p>Supplies: 85%</p>
                <p>Services: 90%</p>
                <div class="progress-bar">
                    <div class="progress" style="width: 85%;"></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>