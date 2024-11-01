document.addEventListener('DOMContentLoaded', function() {
        fetchInventoryData();
        initializeButtons();
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
    
            // Update Stats
            updateStats(data.stats);
    
            // Update Product Table
            updateProductTable(data.products);
    
        } catch (error) {
            console.error('Error fetching inventory data:', error);
        }
    }
    
    function updateStats(stats) {
        const statValues = document.querySelectorAll('.stat-value');
        
        // Categories
        statValues[0].textContent = stats.categories;
        
        // Total Products
        statValues[1].textContent = stats.total_products;
        
        // Revenue (Top Selling)
        statValues[2].textContent = '₹' + formatCurrency(stats.revenue);
        
        // Cost
        statValues[3].textContent = '₹' + formatCurrency(stats.total_cost);
    }
    
    function updateProductTable(products) {
        const tbody = document.querySelector('.product-table tbody');
        tbody.innerHTML = products.map(product => `
            <tr>
                <td>${product.name}</td>
                <td>${product.code}</td>
                <td>${product.type}</td>
                <td>₹${formatCurrency(product.price)}</td>
                <td>${product.quantity}</td>
                <td>
                    <img src="${product.image_url || '../assets/img/products/default.png'}" 
                         alt="${product.name}" 
                         class="product-image">
                </td>
                <td>
                    <button class="btn-icon" onclick="editProduct(${product.id})">
                        <i data-lucide="edit"></i>
                    </button>
                    <button class="btn-icon" onclick="deleteProduct(${product.id})">
                        <i data-lucide="trash-2"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    
        // Reinitialize Lucide icons
        lucide.createIcons();
    }
    
    function initializeButtons() {
        // Filter button
        document.querySelector('button i[data-lucide="filter"]').parentElement
            .addEventListener('click', showFilters);

        function showFilters() {
            const filterContainer = document.querySelector('.filter-container');
            filterContainer.classList.toggle('hidden');
            
            const categoryFilter = document.querySelector('#category-filter');
            categoryFilter.addEventListener('change', function() {
                const selectedCategory = this.value;
                filterProductsByCategory(selectedCategory);
            });
        }

        function filterProductsByCategory(category) {
            const rows = document.querySelectorAll('.product-table tbody tr');
            rows.forEach(row => {
                const productCategory = row.querySelector('td:nth-child(3)').textContent;
                if (category === 'all' || productCategory === category) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    
        // Download button
        document.querySelector('button i[data-lucide="download"]').parentElement
            .addEventListener('click', downloadProducts);
    
        // Add Product button
        document.querySelector('button i[data-lucide="plus"]').parentElement
            .addEventListener('click', showAddProductForm);
    }
    
    function formatCurrency(value) {
        const amount = parseFloat(value);
        if (amount >= 10000000) { // 1 crore = 10000000
            return (amount / 10000000).toFixed(2) + 'cr';
        } else if (amount >= 100000) { // 1 lakh = 100000
            return (amount / 100000).toFixed(2) + 'L';
        }
        return new Intl.NumberFormat('en-IN').format(amount);
    }
    
    // Placeholder functions for buttons
    function showFilters() {
        // Implement filter functionality
        console.log('Show filters');
    }
    
    function downloadProducts() {
        // Implement download functionality
        console.log('Download products');
    }
    
    function showAddProductForm() {
        // Implement add product functionality
        console.log('Show add product form');
    }
    
    function editProduct(id) {
        // Implement edit functionality
        console.log('Edit product:', id);
    }
    
    function deleteProduct(id) {
        // Implement delete functionality
        console.log('Delete product:', id);
    }