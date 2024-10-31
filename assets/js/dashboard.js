document.addEventListener('DOMContentLoaded', function() {
        fetchDashboardData();
});

async function fetchDashboardData() {
        try {
                const response = await fetch('../pages/dashboard.php');
                if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();

                if (data.error) {
                        console.error('Server error:', data.error);
                        return;
                }

                // Update Sales Overview
                document.getElementById('totalSales').textContent = 'Rs ' + formatNumber(data.sales_overview.sales);
                document.getElementById('totalRevenue').textContent = 'Rs ' + formatNumber(data.sales_overview.revenue);
                document.getElementById('totalProfit').textContent = 'Rs ' + formatNumber(data.sales_overview.profit);
                document.getElementById('totalCostValue').textContent = 'Rs ' + formatNumber(data.sales_overview.cost);

                // Update Purchase Overview
                document.getElementById('totalPurchase').textContent = data.purchase_overview.total_purchase;
                document.getElementById('totalCost').textContent = 'Rs ' + formatNumber(data.purchase_overview.total_cost);
                document.getElementById('totalCancel').textContent = data.purchase_overview.cancel_count;
                document.getElementById('totalReturn').textContent = 'Rs ' + formatNumber(data.purchase_overview.return_amount);

                // Update Sales Order Table
                const salesOrderBody = document.getElementById('salesOrderBody');
                salesOrderBody.innerHTML = '';
                data.sales_orders.forEach(order => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                                <td>${order.channel}</td>
                                <td>${order.draft}</td>
                                <td>${order.confirmed}</td>
                                <td>${order.packed}</td>
                                <td>${order.shipped}</td>
                                <td>${order.invoiced}</td>
                        `;
                        salesOrderBody.appendChild(row);
                });

                // Update Top Selling Stock
                const topSellingBody = document.getElementById('topSellingBody');
                topSellingBody.innerHTML = '';
                data.top_selling.forEach(item => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                                <td>${item.name}</td>
                                <td>${item.sold_quantity}</td>
                                <td>${item.remaining_quantity}</td>
                                <td>Rs ${formatNumber(item.price)}</td>
                        `;
                        topSellingBody.appendChild(row);
                });

                // Update Low Stock
                const lowStockBody = document.getElementById('lowStockBody');
                lowStockBody.innerHTML = '';
                data.low_stock.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'stock-item';
                        div.innerHTML = `
                                <div class="stock-info">
                                        <span class="stock-name">${item.name}</span>
                                        <span class="stock-quantity">Remaining: ${item.remaining_quantity} Pieces</span>
                                </div>
                                <span class="low-stock-badge">Low</span>
                        `;
                        lowStockBody.appendChild(div);
                });

        } catch (error) {
                console.error('Error fetching dashboard data:', error);
        }
}

function formatNumber(number) {
        return new Intl.NumberFormat('en-IN', {
                maximumFractionDigits: 2,
                minimumFractionDigits: 2
        }).format(number);
}