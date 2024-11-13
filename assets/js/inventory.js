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
        statValues[2].textContent = 'Rs' + formatCurrency(stats.revenue);
        
        // Cost
        statValues[3].textContent = 'Rs' + formatCurrency(stats.total_cost);
    }
    
    function updateProductTable(products) {
        const tbody = document.querySelector('.product-table tbody');
        tbody.innerHTML = products.map(product => `
            <tr>
                <td>${product.name}</td>
                <td>${product.code}</td>
                <td>${product.type}</td>
                <td>RS${formatCurrency(product.price)}</td>
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
    
    
        // Download button
        function showAddProductForm() {
            document.getElementById("add-product-form").style.display = "block";
          }
      
          function closeForm() {
            document.getElementById("add-product-form").style.display = "none";
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

    function editProduct(id) {
        // Implement edit functionality
        console.log('Edit product:', id);
    }
    
    function deleteProduct(id) {
        // Implement delete functionality
        console.log('Delete product:', id);
    }
    function showAddProductForm() {
        document.getElementById("add-product-form").style.display = "block";
      }
      
      function closeForm() {
        document.getElementById("add-product-form").style.display = "none";
      }