<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Customer List</title>
        <link rel="stylesheet" href="../assets/css/customers.css">
        
<script src="/InventoryManagementSystem/assets/js/shared.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <script src="../assets/js/customers.js" defer></script> 
</head>
<body>
        <?php include '../includes/sidebar.php'; ?>
        <div class="container">
                <header>
                        <h1>Customer List</h1>
                        <div class="header-actions">
                                <a href="AddCustomers.php">
                                <button class="primary-btn">Add Customer</button>
                                </a>
                                <button class="filter-btn">
                                        <span>Filters</span>
                                </button>
                                <button class="secondary-btn">Download all</button>
                        </div>
                </header>

                <main>
                        <table>
                                <thead>
                                        <tr>
                                                <th>Customer Name</th>
                                                <th>Contact No.</th>
                                                <th>Quick Actions</th>
                                        </tr>
                                </thead>
                                <tbody id="customer-table-body">
                                        
                                </tbody>
                        </table>
                </main>

                <footer>
                        <div class="pagination">
                                <button class="page-btn" disabled>Previous</button>
                                <span>Page 1 of 10</span>
                                <button class="page-btn">Next</button>
                        </div>
                </footer>
        </div>
</body>
</html>
