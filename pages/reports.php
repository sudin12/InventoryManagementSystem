<?php
include('../config.php');
?>
<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management Dashboard</title>
    <link rel="stylesheet" href="../assets/css/report.css"> 
    
</head>
<?php include '../includes/sidebar.php'; ?>

<body>
    <div class="dashboard">
        <h1>Inventory Management</h1>
        <div class="summary">
            <div class="card">
                <h2>Total Products</h2>
                <p id="totalProducts">9</p>
            </div>
            <div class="card">
                <h2>Low Stock</h2>
                <p id="lowStock">4</p>
            </div>
            <div class="card">
                <h2>Out of Stock</h2>
                <p id="outOfStock">1</p>
            </div>
            <div class="card">
                <h2>Suppliers</h2>
                <p id="suppliers">5</p>
            </div>
        </div>

        <div class="details">
            <div class="stock-value">
                <h2>Value of stock</h2>
                <p id="stockValue">$3,186</p>
            </div>
            <div class="chart-container">
                <h2>Warehouse Stock</h2>
                <canvas id="stockChart"></canvas>
            </div>
            <div class="stock-purchases">
                <h2>Stock Purchases</h2>
                <p>Unfulfilled: <span id="unfulfilled">4</span></p>
                <p>Received: <span id="received">1</span></p>
            </div>

        </div>
    </div>
    <script src="reports.js"></script>
</body>

</html>