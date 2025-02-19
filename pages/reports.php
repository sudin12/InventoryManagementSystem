<?php
require_once '../includes/session.php';
checkAdminOrStoreKeeper();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Page</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            width: 80%;
            margin: 20px auto;
        }
        canvas {
            background: #f9f9f9;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>
    <h1>Sales and Products Report</h1>
    <div class="chart-container">
        <canvas id="salesChart"></canvas>
    </div>
    <div class="chart-container">
        <canvas id="stockChart"></canvas>
    </div>

    <script>
        async function fetchData() {
            const response = await fetch('api/report.php');
            return await response.json();
        }

        async function createCharts() {
            const data = await fetchData();

            // Extract data for the charts
            const labels = data.map(item => item.product_name);
            const salesData = data.map(item => item.total_sales);
            const stockData = data.map(item => item.quantity_in_hand);

            // Sales Chart
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            new Chart(salesCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Sales',
                        data: salesData,
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Sales'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Products'
                            }
                        }
                    }
                }
            });

            // Stock Chart
            const stockCtx = document.getElementById('stockChart').getContext('2d');
            new Chart(stockCtx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Stock in Hand',
                        data: stockData,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.5)',
                            'rgba(54, 162, 235, 0.5)',
                            'rgba(255, 206, 86, 0.5)',
                            'rgba(75, 192, 192, 0.5)',
                            'rgba(153, 102, 255, 0.5)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true
                }
            });
        }

        // Create charts on page load
        createCharts();
    </script>
</body>
</html>
