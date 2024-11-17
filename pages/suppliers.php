
<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Supplier List</title>
        <link rel="stylesheet" href="../assets/css/suppliers.css">
        <script src="../assets/js/suppliers.js" defer></script> 
</head>
<body>
        <?php include '../includes/sidebar.php'; ?>
        <div class="container">
                <header>
                        <h1>Supplier List</h1>
                        <div class="header-actions">
                                <button class="primary-btn">Add Supplier</button>
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
                                                <th>Supplier Name</th>
                                                
                                                <th>Contact No.</th>
                                                <th>Actions</th>
                                        </tr>
                                </thead>
                                <tbody id="supplier-table-body">
                                        
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