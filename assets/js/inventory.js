document.addEventListener('DOMContentLoaded', function () {
    loadInventory();
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

        updateStats(data.stats);
        updateProductTable(data.products);

    } catch (error) {
        console.error('Error fetching inventory data:', error);
    }
}

function updateStats(stats) {
    const statValues = document.querySelectorAll('.stat-value');

    statValues[0].textContent = stats.categories;
    statValues[1].textContent = stats.total_products;
    statValues[2].textContent = 'Rs' + formatCurrency(stats.revenue);
    statValues[3].textContent = 'Rs' + formatCurrency(stats.total_cost);
}

function updateProductTable(products) {
    const tbody = document.querySelector('.product-table tbody');
    tbody.innerHTML = products.map(product => `
        <tr>
            <td>${product.name}</td>
            <td>${product.product_id}</td>
            <td>${product.type}</td>
            <td>Rs${formatCurrency(product.price)}</td>
            <td>${product.quantity_in_hand}</td>
            <td class="actions-cell">
                <button class="btn-icon view" onclick="viewProduct('${product.product_id}')" title="View">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
    <path d="M12 5C5 5 1 12 1 12s4 7 11 7 11-7 11-7-4-7-11-7z"/>
    <circle cx="12" cy="12" r="3"/>
</svg>
                </button>
                <button class="btn-icon edit" onclick="editProduct('${product.product_id}')" title="Edit">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
</svg>
                </button>
                <button class="btn-icon delete" onclick="deleteProduct('${product.product_id}')" title="Delete">
                   <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
    <path d="M3 6h18"/>
    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/>
    <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
    <line x1="10" y1="11" x2="10" y2="17"/>
    <line x1="14" y1="11" x2="14" y2="17"/>
</svg>
                </button>
            </td>
        </tr>
    `).join('');
}

function viewProduct(productId) {
    fetch(`../pages/api/ProductsApi.php?action=view&id=${productId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                const product = data.data;
                const modal = document.createElement('div');
                modal.className = 'modal';
                modal.innerHTML = `
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2>Product Details</h2>
                            <span class="close">&times;</span>
                        </div>
                        <div class="modal-body">
                            <div class="detail-row">
                                <span class="label">Name:</span>
                                <span class="value">${product.name}</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Price:</span>
                                <span class="value">Rs${formatCurrency(product.price)}</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Quantity:</span>
                                <span class="value">${product.quantity_in_hand}</span>
                            </div>
                            <div class="form-actions">
                                <button class="btn-primary" onclick="editProduct(${product.product_id})">Edit</button>
                                <button class="btn-cancel">Close</button>
                            </div>
                        </div>
                    </div>
                `;

                document.body.appendChild(modal);
                setupModalHandlers(modal);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error fetching product details', 'error');
        });
}

async function editProduct(productId) {
    try {
        const response = await fetch('../pages/api/actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'view', product_id: productId })
        });

        const data = await response.json();

        if (data.success) {
            showEditProductModal(data.data);
        } else {
            alert(data.error || 'Error loading product details');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error loading product details');
    }
}

function showEditProductModal(product) {
    // Remove existing modal if any
    const existingModal = document.getElementById('editProductModal');
    if (existingModal) existingModal.remove();

    const modalHTML = `
        <div class="modal" id="editProductModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Edit Product</h2>
                    <span class="close" onclick="closeModal('editProductModal')">&times;</span>
                </div>
                <div class="modal-body">
                    <form id="editProductForm">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="product_id" value="${product.product_id}">
                        <div class="form-group">
                            <label>Name:</label>
                            <input type="text" name="name" value="${product.name}" required 
                                onchange="validateField(this, 'Name cannot be empty')">
                        </div>
                        <div class="form-group">
                            <label>Price:</label>
                            <input type="number" name="price" value="${product.price}" step="0.01" required 
                                onchange="validatePrice(this)"
                                oninput="validatePrice(this)">
                            <span class="error-message"></span>
                        </div>
                        <div class="form-group">
                            <label>Quantity:</label>
                            <input type="number" name="quantity" value="${product.quantity_in_hand}" required 
                                onchange="validateQuantity(this)"
                                oninput="validateQuantity(this)">
                            <span class="error-message"></span>
                        </div>
                        <button type="submit" class="btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHTML);
    const modal = document.getElementById('editProductModal');
    modal.style.display = 'block';

    // Add these new validation functions
    window.validateField = function(input, message) {
        const isValid = input.value.trim() !== '';
        input.setCustomValidity(isValid ? '' : message);
        showFieldError(input, isValid ? '' : message);
        return isValid;
    };

    window.validatePrice = function(input) {
        const price = parseFloat(input.value);
        const isValid = !isNaN(price) && price > 0;
        const message = !isNaN(price) ? (price <= 0 ? 'Price must be greater than 0' : '') : 'Please enter a valid number';
        input.setCustomValidity(isValid ? '' : message);
        showFieldError(input, isValid ? '' : message);
        return isValid;
    };

    window.validateQuantity = function(input) {
        const quantity = parseInt(input.value);
        const isValid = !isNaN(quantity) && quantity >= 0;
        const message = !isNaN(quantity) ? (quantity < 0 ? 'Quantity cannot be negative' : '') : 'Please enter a valid number';
        input.setCustomValidity(isValid ? '' : message);
        showFieldError(input, isValid ? '' : message);
        return isValid;
    };

    window.showFieldError = function(input, message) {
        const errorSpan = input.nextElementSibling;
        if (errorSpan && errorSpan.className === 'error-message') {
            errorSpan.textContent = message;
            errorSpan.style.display = message ? 'block' : 'none';
        }
    };

    // Modified form submission handler with improved validation
    document.getElementById('editProductForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const name = e.target.name.value.trim();
        const priceStr = e.target.price.value;
        const quantityStr = e.target.quantity.value;
        const price = parseFloat(priceStr);
        const quantity = parseInt(quantityStr);

        // Clear previous error messages
        document.querySelectorAll('.error-message').forEach(el => {
            el.style.display = 'none';
            el.textContent = '';
        });

        // Validate each field
        if (!name) {
            showNotification('Product name cannot be empty', 'error');
            return;
        }

        if (!priceStr || isNaN(price) || price <= 0) {
            showNotification('Price must be greater than 0', 'error');
            return;
        }

        if (!quantityStr || isNaN(quantity) || quantity < 0) {
            showNotification('Quantity cannot be negative', 'error');
            return;
        }

        // Create validated data object
        const formData = {
            action: 'edit',
            product_id: e.target.product_id.value,
            name: name,
            price: price,
            quantity: quantity
        };

        try {
            const response = await fetch('../pages/api/actions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });

            const result = await response.json();
            
            if (!result.success) {
                showNotification(result.error || 'Failed to update product', 'error');
                return;
            }

            showNotification('Product updated successfully', 'success');
            closeModal('editProductModal');
            fetchInventoryData();
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error updating product', 'error');
        }
    });
}

function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product?')) {
        // Send delete request to the server
        fetch('../pages/api/actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'delete', product_id: productId })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product deleted successfully');
                    showNotification('Product deleted successfully', 'success');
                    fetchInventoryData(); // Refresh the data
                } else {
                    alert(data.error || 'Failed to delete product');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting product');
            });
    }
}

// Helper function to close modal
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.remove();
    }
}

function formatCurrency(number) {
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

function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    // Create new notification
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);

    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

const styles = `
 .notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 25px;
    border-radius: 4px;
    color: white;
    z-index: 1000;
    animation: slideIn 0.5s ease-out;
}

.notification.success {
    background-color: #4CAF50;
}

.notification.error {
    background-color: #F44336;
}

.notification.info {
    background-color: #2196F3;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1);
    }
}

.error-message {
    color: #F44336;
    font-size: 12px;
    margin-top: 4px;
    display: none;
}

.form-group {
    margin-bottom: 15px;
}
`;
