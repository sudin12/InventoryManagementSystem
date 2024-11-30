document.addEventListener('DOMContentLoaded', () => {
    fetchSuppliers();
});

let suppliersData = []; // Global variable to store supplier data

function fetchSuppliers() {
    fetch('../pages/api/SuppliersApi.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            suppliersData = data;
            // Convert supplier_id to number for consistent comparison
            suppliersData = data.map(supplier => ({
                ...supplier,
                supplier_id: parseInt(supplier.supplier_id)
            }));
            populateTable(suppliersData);
        })
        .catch(error => {
            console.error('Error fetching suppliers:', error);
            showNotification('Error fetching suppliers', 'error');
        });
}

function populateTable(data) {
    const tableBody = document.getElementById('supplier-table-body');
    tableBody.innerHTML = '';

    data.forEach(supplier => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="supplier-cell">${supplier.name}</td>
            <td>${supplier.contact_info}</td>
            <td class="actions-cell">
                <button class="btn-icon view" title="View" onclick="viewSupplier(${supplier.supplier_id})">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#2196F3" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 5C5 5 1 12 1 12s4 7 11 7 11-7 11-7-4-7-11-7z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
                <button class="btn-icon edit" title="Edit" onclick="editSupplier(${supplier.supplier_id})">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </button>
                <button class="btn-icon delete" title="Delete" onclick="deleteSupplier(${supplier.supplier_id})">
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
}

function viewSupplier(supplierId) {
    // Convert supplierId to number for comparison
    supplierId = parseInt(supplierId);
    const supplier = suppliersData.find(s => parseInt(s.supplier_id) === supplierId);

    if (!supplier) {
        showNotification('Supplier not found', 'error');
        return;
    }

    const modalHTML = `
        <div class="modal" id="viewSupplierModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Supplier Details</h2>
                    <span class="close" onclick="closeModal('viewSupplierModal')">&times;</span>
                </div>
                <div class="modal-body">
                    <div class="supplier-details">
                        <p><strong>Name:</strong> ${supplier.name}</p>
                        <p><strong>Contact Info:</strong> ${supplier.contact_info}</p>
                        <p><strong>Created At:</strong> ${supplier.created_at ? new Date(supplier.created_at).toLocaleDateString() : 'N/A'}</p>
                        <p><strong>Updated At:</strong> ${supplier.updated_at ? new Date(supplier.updated_at).toLocaleDateString() : 'N/A'}</p>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHTML);
    document.getElementById('viewSupplierModal').style.display = 'block';
}

function editSupplier(supplierId) {
    // Convert supplierId to number for comparison
    supplierId = parseInt(supplierId);
    const supplier = suppliersData.find(s => parseInt(s.supplier_id) === supplierId);

    if (!supplier) {
        showNotification('Supplier not found', 'error');
        return;
    }

    const modalHTML = `
        <div class="modal" id="editSupplierModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Edit Supplier</h2>
                    <span class="close" onclick="closeModal('editSupplierModal')">&times;</span>
                </div>
                <div class="modal-body">
                    <form id="editSupplierForm">
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input type="text" id="name" name="name" value="${supplier.name}" required>
                        </div>
                        <div class="form-group">
                            <label for="contact_info">Contact Info:</label>
                            <input type="text" id="contact_info" name="contact_info" value="${supplier.contact_info}" required>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Save Changes</button>
                            <button type="button" class="btn-secondary" onclick="closeModal('editSupplierModal')">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHTML);
    const modal = document.getElementById('editSupplierModal');
    modal.style.display = 'block';

    document.getElementById('editSupplierForm').addEventListener('submit', function(e) {
        e.preventDefault();
        handleEditSubmit(e, supplierId);
    });
}

async function handleEditSubmit(event, supplierId) {
    event.preventDefault();

    try {
        const formData = new FormData();
        formData.append('action', 'edit');
        formData.append('id', supplierId);
        formData.append('name', event.target.name.value.trim());
        formData.append('contact_info', event.target.contact_info.value.trim());

        const response = await fetch('../pages/api/SuppliersApi.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        console.log('Result:', result);
    

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        if (result.success) {
            closeModal('editSupplierModal');
            fetchSuppliers();
            showNotification('Supplier updated successfully', 'success');
        } else {
            showNotification(result.message || 'Failed to update supplier', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error updating supplier', 'error');
    }
}

function deleteSupplier(supplierId) {
    if (confirm('Are you sure you want to delete this supplier?')) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', supplierId);

        fetch('../pages/api/SuppliersApi.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(result => {
            if (result.success) {
                showNotification('Supplier deleted successfully', 'success');
                fetchSuppliers();
            } else {
                showNotification(result.message || 'Failed to delete supplier', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error deleting supplier', 'error');
        });
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.remove();
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
        opacity: 1;
    }
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
}

.modal-content {
    background-color: #fff;
    margin: 10% auto;
    padding: 20px;
    border-radius: 5px;
    width: 80%;
    max-width: 500px;
    position: relative;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.close {
    font-size: 24px;
    cursor: pointer;
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
    border: 1px solid #ddd;
    border-radius: 4px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}

.btn-primary {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
}
`;

// Add styles to the document
const styleSheet = document.createElement('style');
styleSheet.textContent = styles;
document.head.appendChild(styleSheet);