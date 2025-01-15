// script.js
document.addEventListener('DOMContentLoaded', function () {
    fetchInventoryData(); // Fetch initial data
});

async function fetchInventoryData() {
    try {
        const response = await fetch('../pages/api/InventoryApi.php');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();

        if (data.error) {
            console.error('Server error:', data.error);
            return;
        }

        updateChart(data.chartData); // Update the chart with fetched data
        updateStats(data.stats);

    } catch (error) {
        console.error('Error fetching inventory data:', error);
    }
}


function updateChart(chartData) {
    const ctx = document.getElementById('stockChart').getContext('2d');

    // Destroy existing chart if it exists to avoid conflicts
    if (window.myChart) {
        window.myChart.destroy();
    }

    window.myChart = new Chart(ctx, { // Store chart instance in window
        type: 'bar',
        data: {
            labels: chartData.labels, // Use fetched labels
            datasets: [{
                label: 'Sales',
                data: chartData.data,     // Use fetched data
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 5 // Adjust step size as needed for better visualization
                    }
                }
            },
            responsive: true, // Make chart responsive
            maintainAspectRatio: false // Allow chart to resize freely
        }
    });
}

function updateStats(stats) {
    document.getElementById('totalProducts').textContent = stats.total_products;
    document.getElementById('lowStock').textContent = stats.low_stock;
    document.getElementById('outOfStock').textContent = stats.out_of_stock;
    document.getElementById('suppliers').textContent = stats.suppliers;
    document.getElementById('stockValue').textContent = 'Rs' + formatCurrency(stats.stock_value);
    document.getElementById('unfulfilled').textContent = stats.unfulfilled;
    document.getElementById('received').textContent = stats.received;
}

function formatCurrency(number) {
    if (number >= 1_000_000) {
        return (number / 1_000_000).toFixed(1).replace(/\.0/, '') + 'm';
    } else if (number >= 1_000) {
        return (number / 1_000).toFixed(1).replace(/\.0/, '') + 'k';
    } else {
        return number.toString();
    }
}