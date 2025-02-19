document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
    initializeLucideIcons();
});

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

function showAddCategoryForm() {
    const name = prompt("Enter category name:");
    if (name) {
        fetch('/InventoryManagementSystem/pages/api/CategoriesApi.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name: name })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Category added successfully');
                loadCategories();
            } else {
                showNotification('Failed to add category', 'error');
            }
        })
        .catch(error => showNotification('Error adding category', 'error'));
    }
}

function viewCategory(id) {
    const row = document.querySelector(`tr[data-id="${id}"]`);
    const category = {
        id: id,
        name: row.querySelector('td:nth-child(2)').textContent,
        productCount: row.querySelector('td:nth-child(3)').textContent
    };
    
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h2>Category Details</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <div class="detail-row">
                    <span class="label">ID:</span>
                    <span class="value">${category.id}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Name:</span>
                    <span class="value">${category.name}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Products:</span>
                    <span class="value">${category.productCount}</span>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    
    const closeBtn = modal.querySelector('.close');
    closeBtn.onclick = () => modal.remove();
    
    window.onclick = (event) => {
        if (event.target === modal) {
            modal.remove();
        }
    };
}

function editCategory(id, currentName) {
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Category</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="editCategoryForm">
                    <div class="form-group">
                        <label for="categoryName">Category Name:</label>
                        <input type="text" id="categoryName" value="${currentName}" required>
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

    const form = modal.querySelector('form');
    const closeBtn = modal.querySelector('.close');
    const cancelBtn = modal.querySelector('.btn-cancel');

    form.onsubmit = (e) => {
        e.preventDefault();
        const newName = modal.querySelector('#categoryName').value;
        if (newName && newName !== currentName) {
            fetch('/InventoryManagementSystem/pages/api/CategoriesApi.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id, name: newName })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Category updated successfully');
                    loadCategories();
                    modal.remove();
                } else {
                    showNotification('Failed to update category', 'error');
                }
            })
            .catch(error => showNotification('Error updating category', 'error'));
        }
    };

    [closeBtn, cancelBtn].forEach(btn => {
        btn.onclick = () => modal.remove();
    });

    window.onclick = (event) => {
        if (event.target === modal) {
            modal.remove();
        }
    };
}

function deleteCategory(id) {
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h2>Delete Category</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this category?</p>
                <div class="form-actions">
                    <button class="btn-delete">Delete</button>
                    <button class="btn-cancel">Cancel</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);

    const closeBtn = modal.querySelector('.close');
    const deleteBtn = modal.querySelector('.btn-delete');
    const cancelBtn = modal.querySelector('.btn-cancel');

    deleteBtn.onclick = () => {
        fetch(`/InventoryManagementSystem/pages/api/CategoriesApi.php?id=${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Category deleted successfully');
                loadCategories();
                modal.remove();
            } else {
                showNotification('Failed to delete category', 'error');
            }
        })
        .catch(error => showNotification('Error deleting category', 'error'));
    };

    [closeBtn, cancelBtn].forEach(btn => {
        btn.onclick = () => modal.remove();
    });

    window.onclick = (event) => {
        if (event.target === modal) {
            modal.remove();
        }
    };
}

function loadCategories(page = 1) {
    const tbody = document.querySelector('.product-table tbody');
    tbody.innerHTML = '<tr><td colspan="3">Loading...</td></tr>';

    // Fix: Use absolute path and add timestamp to prevent caching
    fetch(`/InventoryManagementSystem/pages/api/CategoriesApi.php?page=${page}&t=${Date.now()}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('JSON parse error:', text);
                throw new Error('Server returned invalid JSON');
            }
        });
    })
    .then(data => {
        tbody.innerHTML = '';
        
        if (!data.categories || data.categories.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3">No categories found</td></tr>';
            return;
        }
        
        data.categories.forEach(category => {
            tbody.innerHTML += `
                <tr data-id="${category.category_id}">
                    <td>${category.category_id}</td>
                    <td>${category.name}</td>
                    <td>${category.product_count || 0}</td>
                    <td class="actions-cell">
                        <button class="btn-icon view" title="View" onclick="viewCategory(${category.category_id})">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#2196F3" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 5C5 5 1 12 1 12s4 7 11 7 11-7 11-7-4-7-11-7z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                        <button class="btn-icon edit" title="Edit" onclick="editCategory(${category.category_id})">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </button>
                        <button class="btn-icon delete" title="Delete" onclick="deleteCategory(${category.category_id})">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#F44336" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M3 6h18"/>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/>
                                <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                <line x1="10" y1="11" x2="10" y2="17"/>
                                <line x1="14" y1="11" x2="14" y2="17"/>
                            </svg>
                        </button>
                    </td>
                </tr>
            `;
        });

        if (data.total) {
            updatePagination(data.total);
        }
        initializeLucideIcons();
    })
    .catch(error => {
        console.error('Fetch error:', error);
        tbody.innerHTML = `<tr><td colspan="3">Error loading categories: ${error.message}</td></tr>`;
        showNotification('Failed to load categories', 'error');
    });
}

function downloadCategories() {
    fetch('/InventoryManagementSystem/pages/api/CategoriesApi.php')
        .then(response => response.json())
        .then(data => {
            const csv = convertToCSV(data.categories);
            downloadCSV(csv, 'categories.csv');
        });
}

function convertToCSV(data) {
    const headers = ['Category ID', 'Name', 'Product Count'];
    const rows = data.map(item => [
        item.category_id,
        item.name,
        item.product_count || 0
    ]);
    return [headers, ...rows].map(row => row.join(',')).join('\n');
}

function downloadCSV(csv, filename) {
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.setAttribute('hidden', '');
    a.setAttribute('href', url);
    a.setAttribute('download', filename);
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

function updatePagination(total) {
    const pages = Math.ceil(total / 10);
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';
    
    for (let i = 1; i <= pages; i++) {
        const button = document.createElement('button');
        button.textContent = i;
        button.onclick = () => loadCategories(i);
        pagination.appendChild(button);
    }
}

function showFilters() {

    alert('Filter functionality coming soon!');
}

function initializeLucideIcons() {
    if (window.lucide) {
        lucide.createIcons();
    }
}

const style = document.createElement('style');
style.textContent = `
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

    .btn-icon {
        background: none;
        border: none;
        padding: 5px;
        cursor: pointer;
        transition: transform 0.2s;
        border-radius: 50%;
    }

    .btn-icon:hover {
        transform: scale(1.2);
    }

    .btn-icon svg {
        width: 20px;
        height: 20px;
        stroke-width: 2px;
    }

    .view svg { stroke: #2196F3; }
    .edit svg { stroke: #4CAF50; }
    .delete svg { stroke: #F44336; }

    .btn-icon:hover.view { background-color: rgba(33, 150, 243, 0.1); }
    .btn-icon:hover.edit { background-color: rgba(76, 175, 80, 0.1); }
    .btn-icon:hover.delete { background-color: rgba(244, 67, 54, 0.1); }

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
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
        color: #666;
    }

    .detail-row {
        display: flex;
        margin-bottom: 10px;
        padding: 8px;
        border-bottom: 1px solid #eee;
    }

    .label {
        font-weight: bold;
        width: 120px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
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

    .btn-save {
        background-color: #4CAF50;
        color: white;
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-delete {
        background-color: #F44336;
        color: white;
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-cancel {
        background-color: #9e9e9e;
        color: white;
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-save:hover { background-color: #45a049; }
    .btn-delete:hover { background-color: #da3c31; }
    .btn-cancel:hover { background-color: #8e8e8e; }
`;
document.head.appendChild(style);
