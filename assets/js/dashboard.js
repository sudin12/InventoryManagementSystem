document.addEventListener('DOMContentLoaded', () => {
    fetchDashboardData();
});

async function fetchDashboardData() {
    try {
        // Fix the path to dashboard.php
        const response = await fetch('../pages/dashboard.php');
        const data = await response.json();
        console.log('Dashboard data:', data);

        if (data.error) {
            console.error('Server error:', data.error);
            return;
        }

        // Update Sales Overview
        updateElement('totalSales', formatCurrency(data.sales_overview.sales));
        updateElement('totalRevenue', formatCurrency(data.sales_overview.revenue));
        updateElement('totalProfit', formatCurrency(data.sales_overview.profit));
        updateElement('totalCostValue', formatCurrency(data.sales_overview.cost));

        // Update Purchase Overview
        updateElement('totalPurchase', data.purchase_overview.total_purchase);
        updateElement('totalCost', formatCurrency(data.purchase_overview.total_cost));

        // Update Summary Data
        if (data.summary) {
            console.log('Summary data:', data.summary);
            updateElement('totalQuantity', data.summary.quantity_in_hand);
            updateElement('supplierCount', data.summary.supplier_count);
            updateElement('categoryCount', data.summary.category_count);
        } else {
            console.error('Summary data missing from response');
        }

        // Update Tables
        updateSalesOrders(data.sales_orders);
        updateTopSelling(data.top_selling);
        updateLowStock(data.low_stock);

    } catch (error) {
        console.error('Error:', error);
    }
}

// Helper functions
function updateElement(id, value) {
    const element = document.getElementById(id);
    if (element) {
        element.textContent = value;
    }
}

function formatCurrency(value) {
    return 'Rs ' + formatNumber(value);
}

function updateSalesOrders(orders) {
    const tbody = document.getElementById('salesOrderBody');
    if (!tbody || !Array.isArray(orders)) return;

    tbody.innerHTML = orders.map(order => `
        <tr>
            <td>${order.channel || 'N/A'}</td>
            <td>${order.draft || 0}</td>
            <td>${order.confirmed || 0}</td>
            <td>${order.packed || 0}</td>
            <td>${order.shipped || 0}</td>
            <td>${order.invoiced || 0}</td>
        </tr>
    `).join('');
}

function updateTopSelling(items) {
    const tbody = document.getElementById('topSellingBody');
    if (!tbody || !Array.isArray(items)) return;

    tbody.innerHTML = items.map(item => `
        <tr>
            <td>${item.name || 'N/A'}</td>
            <td>${item.sold_quantity || 0}</td>
            <td>${item.remaining_quantity || 0}</td>
            <td>${formatCurrency(item.price || 0)}</td>
        </tr>
    `).join('');
}

function updateLowStock(items) {
    const container = document.getElementById('lowStockBody');
    if (!container || !Array.isArray(items)) return;

    container.innerHTML = items.map(item => `
        <div class="stock-item">
            <div class="stock-info">
                <span class="stock-name">${item.name || 'N/A'}</span>
                <span class="stock-quantity">Remaining: ${item.remaining_quantity || 0} Pieces</span>
            </div>
            <span class="low-stock-badge">Low</span>
        </div>
    `).join('');
}

function formatNumber(number) {
    if (number >= 1_000_000) {
        // For millions
        return (number / 1_000_000).toFixed(1).replace(/\.0/, '') + 'm';
    } else if (number >= 1_000) {
        // For thousands
        return (number / 1_000).toFixed(1).replace(/\.0/, '') + 'k';
    } else {
        // For numbers less than a thousand
        return number.toString();
    }
}