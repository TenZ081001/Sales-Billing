<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <title>Billing</title>
</head>
<body>
    <div class="sidebar">
        <h2>SALES AND BILLING MANAGEMENT SYSTEM</h2>
        <ul>
            <li><a href="index.php" onclick="showSection('dashboard')" class="active"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
            <li><a href="admin.php" onclick="showSection('admin')"><i class="fas fa-user-cog"></i>ADMIN</a></li>
            <li class="dropdown">
                <a href="#" onclick="toggleDropdown('recordsDropdown')"><i class="fas fa-folder"></i>RECORDS</a>
                <div class="dropdown-content" id="recordsDropdown">
                    <a href="record-personnel.php" onclick="showSection('personnel')"><i class="fas fa-users"></i>Personnel</a>
                    <a href="record-services.php" onclick="showSection('services')"><i class="fas fa-cogs"></i>Services</a>
                    <a href="record-supplies.php" onclick="showSection('supplies')"><i class="fas fa-boxes"></i>Supplies</a>
                    <a href="record-customer.php" onclick="showSection('customer')"><i class="fas fa-user-tie"></i>Customer</a>
                </div>
            </li>
            <li><a href="job-order.php" onclick="showSection('job-order')"><i class="fas fa-clipboard-list"></i>JOB ORDER</a></li>
            <li class="dropdown">
                <a href="#" onclick="toggleDropdown('billingDropdown')"><i class="fas fa-file-invoice-dollar"></i>BILLING</a>
                <div class="dropdown-content" id="billingDropdown">
                    <a href="billing-SOA.php" onclick="showSection('soa')"><i class="fas fa-file-alt"></i>Statement of Account</a>
                    <a href="billing-ledger.php" onclick="showSection('ledger')"><i class="fas fa-book"></i>Account Ledger</a>
                </div>
            </li>
            <li><a href="cashiering.php" onclick="showSection('casheiring')"><i class="fas fa-cash-register"></i>CASHIERING</a></li>
            <li><a href="#reports" onclick="showSection('reports')"><i class="fas fa-chart-bar"></i>REPORTS</a></li>
            <!-- Logout Button -->
            <li>
                <a href="logout.php" onclick="return confirm('Are you sure you want to logout?');">
                    <i class="fas fa-sign-out-alt"></i>Logout
                </a>
            </li>
        </ul>
    </div>

    <script>
        function toggleDropdown(id) {
            var dropdown = document.getElementById(id);
            if (dropdown.style.display === "block") {
                dropdown.style.display = "none";
            } else {
                // Close all other dropdowns
                var dropdowns = document.querySelectorAll('.dropdown-content');
                dropdowns.forEach(function(dropdown) {
                    dropdown.style.display = "none";
                });
                // Open the clicked dropdown
                dropdown.style.display = "block";
            }
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            var dropdowns = document.querySelectorAll('.dropdown-content');
            var isClickInside = false;

            dropdowns.forEach(function(dropdown) {
                if (dropdown.contains(event.target) || dropdown.previousElementSibling.contains(event.target)) {
                    isClickInside = true;
                }
            });

            if (!isClickInside) {
                dropdowns.forEach(function(dropdown) {
                    dropdown.style.display = "none";
                });
            }
        });

        function showSection(sectionId) {
            // Hide all sections
            var sections = document.querySelectorAll('.section');
            sections.forEach(function(section) {
                section.style.display = "none";
            });

            // Show the selected section
            var selectedSection = document.getElementById(sectionId);
            if (selectedSection) {
                selectedSection.style.display = "block";
            }

            // Close all dropdowns
            var dropdowns = document.querySelectorAll('.dropdown-content');
            dropdowns.forEach(function(dropdown) {
                dropdown.style.display = "none";
            });

            // Set active state for sidebar items
            var links = document.querySelectorAll('.sidebar ul li a');
            links.forEach(function(link) {
                link.classList.remove('active');
            });
            var activeLink = document.querySelector(`.sidebar ul li a[href="${sectionId}"]`);
            if (activeLink) {
                activeLink.classList.add('active');
            }
        }
    </script>
</body>
</html>