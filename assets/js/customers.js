document.addEventListener('DOMContentLoaded', function() {
    loadCustomers();
});

function loadCustomers() {
    fetch('../pages/api/CustomersApi.php')
        .then(response => response.json())
        .then(customers => {
            const tableBody = document.getElementById('customer-table-body');
            tableBody.innerHTML = '';

            customers.forEach(customer => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${customer.name}</td>
                    <td>${customer.contact_info}</td>
                    <td class="actions-cell">
                        <button class="btn-icon view" title="View" onclick="viewCustomer(${customer.customer_id})">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#2196F3" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 5C5 5 1 12 1 12s4 7 11 7 11-7 11-7-4-7-11-7z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                        <button class="btn-icon edit" title="Edit" onclick="editCustomer(${customer.customer_id})">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </button>
                        <button class="btn-icon delete" title="Delete" onclick="deleteCustomer(${customer.customer_id})">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#F44336" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M3 6h18"/>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/>
                                <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                <line x1="10" y1="11" x2="10" y2="17"/>
                                <line x1="14" y1="11" x2="14" y2="17"/>
                            </svg>
                        </button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Error loading customers:', error);
            showMessage('Error loading customers', 'error');
        });
}

function viewCustomer(customerId) {
    fetch(`../pages/api/CustomersApi.php?action=view&id=${customerId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                const customer = data.data;
                const modal = document.createElement('div');
                modal.className = 'modal';
                modal.innerHTML = `
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2>Customer Details</h2>
                            <span class="close">&times;</span>
                        </div>
                        <div class="modal-body">
                            <div class="detail-row">
                                <span class="label">Name:</span>
                                <span class="value">${customer.name}</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Contact:</span>
                                <span class="value">${customer.contact_info}</span>
                            </div>
                            <div class="form-actions">
                                <button class="btn-primary" onclick="editCustomer(${customer.customer_id})">Edit</button>
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
            showNotification('Error fetching customer details', 'error');
        });
}

function editCustomer(customerId) {
    fetch(`../pages/api/CustomersApi.php?action=view&id=${customerId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                const customer = data.data;
                const modal = document.createElement('div');
                modal.className = 'modal';
                modal.innerHTML = `
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2>Edit Customer</h2>
                            <span class="close">&times;</span>
                        </div>
                        <div class="modal-body">
                            <form id="editCustomerForm">
                                <div class="form-group">
                                    <label for="name">Name:</label>
                                    <input type="text" id="name" value="${customer.name}" required>
                                </div>
                                <div class="form-group">
                                    <label for="contact">Contact Info:</label>
                                    <input type="text" id="contact" value="${customer.contact_info}" required>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="btn-save">Save Changes</button>
                                    <button type="button" class="btn-cancel">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                `;

                document.body.appendChild(modal);
                setupModalHandlers(modal);
                setupEditForm(modal, customer.customer_id);
            }
        });
}

function deleteCustomer(customerId) {
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h2>Delete Customer</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this customer?</p>
                <div class="form-actions">
                    <button class="btn-delete">Delete</button>
                    <button class="btn-cancel">Cancel</button>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);
    setupModalHandlers(modal);
    setupDeleteHandler(modal, customerId);
}

// Add helper functions
function setupModalHandlers(modal) {
    const closeBtn = modal.querySelector('.close');
    const cancelBtn = modal.querySelector('.btn-cancel');

    [closeBtn, cancelBtn].forEach(btn => {
        if (btn) btn.onclick = () => modal.remove();
    });

    window.onclick = (event) => {
        if (event.target === modal) {
            modal.remove();
        }
    };
}

function setupEditForm(modal, customerId) {
    const form = modal.querySelector('form');
    form.onsubmit = (e) => {
        e.preventDefault();
        const formData = new FormData();
        formData.append('action', 'edit');
        formData.append('id', customerId);
        formData.append('name', form.name.value);
        formData.append('contact_info', form.contact.value);

        fetch('../pages/api/CustomersApi.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showMessage('Customer updated successfully', 'success');
                loadCustomers();
                modal.remove();
            } else {
                showMessage('Failed to update customer', 'error');
            }
        })
        .catch(error => {
            console.error('Error updating customer:', error);
            showMessage('Error updating customer', 'error');
        });
    };
}

function setupDeleteHandler(modal, customerId) {
    const deleteBtn = modal.querySelector('.btn-delete');
    if (deleteBtn) {
        deleteBtn.onclick = () => {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', customerId);

            fetch('../pages/api/CustomersApi.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showMessage('Customer deleted successfully', 'success');
                    loadCustomers();
                    modal.remove();
                } else {
                    showMessage('Failed to delete customer: ' + (result.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                console.error('Error deleting customer:', error);
                showMessage('Error deleting customer: ' + error.message, 'error');
            });
        };
    }
}

// Add the same styles as categories.js
const style = document.createElement('style');
style.textContent = `
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal-content {
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        width: 90%;
        max-width: 500px;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #ddd;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .modal-header h2 {
        margin: 0;
    }

    .modal-header .close {
        cursor: pointer;
        font-size: 1.5em;
    }

    .modal-body {
        padding: 10px 0;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
    }

    .form-group input {
        width: 100%;
        padding: 8px;
        box-sizing: border-box;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .btn-primary, .btn-save, .btn-delete {
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 10px 20px;
        cursor: pointer;
        border-radius: 5px;
    }

    .btn-cancel {
        background-color: #6c757d;
        color: #fff;
        border: none;
        padding: 10px 20px;
        cursor: pointer;
        border-radius: 5px;
    }

    .btn-primary:hover, .btn-save:hover, .btn-delete:hover, .btn-cancel:hover {
        opacity: 0.8;
    }
`;
document.head.appendChild(style);

function showMessage(message, type) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = message;
    document.body.appendChild(messageDiv);

    setTimeout(() => {
        messageDiv.classList.add('fade-out');
        setTimeout(() => messageDiv.remove(), 500);
    }, 3000);
}
