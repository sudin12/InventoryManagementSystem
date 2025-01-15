<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management Dashboard</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
            background-color: #f4f4f4; /* Light gray background */
        }

        .dashboard-container {
            background-color: #001f3f; /* Dark blue background */
            color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            max-width: 900px; /* Set a maximum width for the dashboard */
            margin: 0 auto; /* Center the dashboard */
        }

        .top-metrics {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }

        .metric-box {
            background-color: #0074D9; /* Blue metric boxes */
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            width: 20%; /* Adjust width as needed */
        }

        .metric-box h2 {
            margin-top: 0;
            margin-bottom: 5px;
        }

        .metric-box p {
            font-size: 1.2em;
            font-weight: bold;
        }

        .value-stock{
            background-color: #001f3f;
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 30%;
            float: left;
        }
        .warehouse-stock {
            background-color: white;
            color: black;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 65%;
            float: right;
        }
        .clear-fix{
            clear: both;
        }
        /* Styles for the bar chart (placeholder) */
        .chart {
            height: 200px; /* Adjust height as needed */
            background-color: #eee; /* Light gray background for the chart area */
            border-radius: 5px;
            margin-top: 20px;
        }
        .stock-purchases{
            margin-top: 20px;
            clear:both;
        }

    </style>
</head>
<body>

    <div class="dashboard-container">
        <h1>Inventory Management</h1>
        <div class="top-metrics">
            <div class="metric-box">
                <h2>Total Products</h2>
                <p>9</p>
            </div>
            <div class="metric-box">
                <h2>Low Stock</h2>
                <p>4</p>
            </div>
            <div class="metric-box">
                <h2>Out of Stock</h2>
                <p>1</p>
            </div>
            <div class="metric-box">
                <h2>Suppliers</h2>
                <p>5</p>
            </div>
        </div>

        <div class="value-stock">
            <h2>Value of stock</h2>
            <p>$3,186</p>
            <div class="stock-purchases">
                <h2>Stock Purchases</h2>
                <p>Unfulfilled 4</p>
                <p>Received 1</p>
            </div>
        </div>
        <div class="warehouse-stock">
            <h2>Warehouse Stock</h2>
            <div class="chart">
                <p>Bar chart would go here (using a library like Chart.js)</p>
            </div>
        </div>
        <div class="clear-fix"></div>
    </div>

</body>
</html>