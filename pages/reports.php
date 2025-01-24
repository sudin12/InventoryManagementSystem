<?php
include('../config.php');
include('../includes/sidebar.php');
include('../includes/navbar.php');

// Fetch summary data
$totalProducts = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];
$lowStock = $conn->query("SELECT COUNT(*) as total FROM products WHERE quantity_in_hand <= low_quantity_alert")->fetch_assoc()['total'];
$outOfStock = $conn->query("SELECT COUNT(*) as total FROM products WHERE quantity_in_hand = 0")->fetch_assoc()['total'];
$totalSuppliers = $conn->query("SELECT COUNT(*) as total FROM suppliers")->fetch_assoc()['total'];
$stockValue = $conn->query("SELECT SUM(quantity_in_hand * price) as total FROM products")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <style>
        .container {
            max-width: 1200px;
            margin: 20px;
            padding: 20px;
        }

        .search-bar {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 16px;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 28px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #1e88e5;
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: left;
        }

        .stat-card h3 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: normal;
        }

        .stat-card p {
            margin: 10px 0 0;
            font-size: 2rem;
            font-weight: bold;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
        }

        .chart-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
            border: 1px solid #e0e0e0;
        }

        .chart-card h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 20px;
        }

        #stockValue {
            font-size: 2.5rem;
            color: #2c3e50;
            font-weight: bold;
            margin: 0;
        }

        /* Modern flat design colors */
        .stat-card:nth-child(1) { background: #2196F3; }
        .stat-card:nth-child(2) { background: #FF9800; }
        .stat-card:nth-child(3) { background: #F44336; }
        .stat-card:nth-child(4) { background: #4CAF50; }
    </style>
</head>
<body>
   
        
        <h1>Inventory Management</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Products</h3>
                <p><?php echo $totalProducts; ?></p>
            </div>
            <div class="stat-card">
                <h3>Low Stock</h3>
                <p><?php echo $lowStock; ?></p>
            </div>
            <div class="stat-card">
                <h3>Out of Stock</h3>
                <p><?php echo $outOfStock; ?></p>
            </div>
            <div class="stat-card">
                <h3>Suppliers</h3>
                <p><?php echo $totalSuppliers; ?></p>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <h2>Value of stock</h2>
                <p id="stockValue">$<?php echo number_format($stockValue, 2); ?></p>
            </div>
            <div class="chart-card">
                <h2>Warehouse Stock</h2>
                <div id="warehouseStockChart"></div>
            </div>
        </div>
    </div>


    <script>
        // Fetch warehouse stock data and create chart
        async function fetchWarehouseStock() {
            try {
                const response = await fetch('<?php echo $baseUrl; ?>/api/warehouse-stock.php');
                const data = await response.json();
                
                const ctx = document.getElementById('warehouseStockChart');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.map(item => item.name),
                        datasets: [{
                            label: 'Quantity in Hand',
                            data: data.map(item => item.quantity_in_hand),
                            backgroundColor: '#2196F3',
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    display: false
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error fetching warehouse stock data:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', fetchWarehouseStock);
    </script>
</body>
</html>
