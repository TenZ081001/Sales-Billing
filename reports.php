<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        
        .content {
            margin-left: 220px; /* Adjust based on sidebar width */
            padding: 20px;
        }
        
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        
        h2 {
            color: #444;
            margin-top: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .sidebar h2 {
        border-bottom: none !important;
        margin-bottom: 0 !important;
         padding-bottom: 0 !important;
        }
        
        .module-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .report-controls {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .search-box {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 200px;
        }
        
        .select-box {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
        }
        
        .records-filter {
            margin: 15px 0;
            font-size: 14px;
            color: #666;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .action-icons {
            display: flex;
            gap: 10px;
        }
        
        .action-icons span {
            cursor: pointer;
            font-size: 18px;
        }
        
        .pagination {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
            gap: 5px;
        }
        
        .pagination button {
            padding: 5px 10px;
            border: 1px solid #ddd;
            background-color: white;
            cursor: pointer;
            border-radius: 3px;
        }
        
        .pagination button:hover {
            background-color: #f2f2f2;
        }
        
        .pagination button.active {
            background-color: #4CAF50;
            color: white;
            border-color: #4CAF50;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="content">
        <h1>Reports</h1>
        <div class="module-content">
            <div class="report-controls">
                <input type="text" class="search-box" placeholder="Search...">
                <select class="select-box">
                    <option>Select Report</option>
                    <option>Collections</option>
                    <option>Back Jobs</option>
                    <option>Account Receivables</option>
                </select>
                <select class="select-box">
                    <option>Select Time Period</option>
                    <option>Today</option>
                    <option>This Week</option>
                    <option>This Month</option>
                    <option>This Year</option>
                </select>
            </div>
            
            <div class="records-filter">
                Showing: All records
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>WAWEX</td>
                        <td>1050</td>
                        <td>2025-03-26</td>
                        <td class="action-icons">
                            <span>🖨️ Print</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <div class="pagination">
                <button class="active">1</button>
                <button>2</button>
                <button>Next</button>
            </div>
        </div>
    </div>
</body>
</html>