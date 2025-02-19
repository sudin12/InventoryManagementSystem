<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Management</title>
    <style>
     
      
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        select, input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        button {
            background: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #45a049;
        }
        .product-row {
            display: grid;
            grid-template-columns: 3fr 2fr 2fr 1fr;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }
        .summary {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .recent-sales {
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f4f4f4;
        }
        .status {
            padding: 5px 10px;
            border-radius: 3px;
            color: white;
            font-size: 0.9em;
        }
        .status-confirmed { background: #2196F3; }
        .status-packed { background: #FF9800; }
        .status-shipped { background: #9C27B0; }
        .status-invoiced { background: #4CAF50; }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .alert-success { background: #dff0d8; color: #3c763d; }
        .alert-danger { background: #f2dede; color: #a94442; }
        .btn-danger {
            background: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 800px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        .bill-action-btn {
            margin: 0 5px;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
        }

        .bill-action-btn:hover {
            background-color: #45a049;
        }

        #billContent {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }

        .bill-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .bill-details {
            margin-bottom: 20px;
        }

        .bill-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .bill-table th, .bill-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .bill-total {
            text-align: right;
            font-weight: bold;
        }
        
        /* Add styles for bill template */
        #billContent {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }
        .bill-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .company-info {
            margin: 20px 0;
        }
        .bill-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .customer-info, .order-info {
            flex: 1;
        }
        .bill-items {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .bill-items th, .bill-items td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .bill-items th {
            background: #f4f4f4;
        }
        .bill-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
    <?php include '../includes/sidebar.php'; ?>
        <h1>Sales Management</h1>
        
        <form id="salesForm">
            <div class="form-group">
                <label for="customer_id">Customer</label>
                <select id="customer_id" name="customer_id" required>
                    <option value="">Select Customer...</option>
                </select>
            </div>

            <div class="form-group">
                <label for="sales_channel">Sales Channel</label>
                <select id="sales_channel" name="sales_channel" required>
                    <option value="Direct Sales">Direct Sales</option>
                    <option value="Wholesale">Wholesale</option>
                    <option value="Retail">Retail</option>
                </select>
            </div>

            <div id="productsContainer">
                <div class="product-row">
                    <select class="product-select" required>
                        <option value="">Select Product...</option>
                    </select>
                    <input type="number" class="product-quantity" placeholder="Quantity" min="1" required>
                    <input type="text" class="product-price" readonly>
                    <button type="button" class="btn-danger remove-product">×</button>
                </div>
            </div>

            <button type="button" id="addProduct">Add Product</button>

            <div class="summary">
                <div class="summary-row">
                    <span>Total Amount:</span>
                    <span id="total">NRs. 0.00</span>
                </div>
                <button type="submit">Submit Sale</button>
            </div>
        </form>

        <div class="recent-sales">
            <h2>Recent Sales</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Channel</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="recentSales"></tbody>
            </table>
        </div>

        <!-- Bill Modal -->
        <div id="billModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <div id="billContent">
                    <div class="bill-header">
                        <h2>INVOICE</h2>
                        <div class="company-info">
                            <h3>Sudin and Samir</h3>
                            <p>Ekantakuna-14, Lalitpur</p>
                            <p>Phone: +977 9745245678</p>
                        </div>
                    </div>
                    <div class="bill-details">
                        <div class="customer-info">
                            <h4>Bill To:</h4>
                            <p id="billCustomerName"></p>
                            <p id="billCustomerPhone"></p>
                        </div>
                        <div class="order-info">
                            <p>Order ID: <span id="billOrderId"></span></p>
                            <p>Date: <span id="billOrderDate"></span></p>
                            <p>Status: <span id="billOrderStatus"></span></p>
                        </div>
                    </div>
                    <table class="bill-items">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="billItems">
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                                <td id="billTotal"></td>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="bill-footer">
                        <p>Thank you for your business!</p>
                    </div>
                </div>
                <button id="downloadBill" class="bill-action-btn">Download Bill</button>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        let products = [];
        
        document.addEventListener('DOMContentLoaded', () => {
            loadCustomers();
            loadProducts();
            loadRecentSales();
            setupEventListeners();
        });

        function setupEventListeners() {
            document.getElementById('addProduct').addEventListener('click', addProductRow);
            document.getElementById('salesForm').addEventListener('submit', submitSale);
            
            // Add validation to initial quantity input
            const initialQuantityInput = document.querySelector('.product-quantity');
            initialQuantityInput.addEventListener('input', validateQuantity);
            
            // Event delegation for dynamic elements
            document.getElementById('productsContainer').addEventListener('change', (e) => {
                if (e.target.classList.contains('product-select') || 
                    e.target.classList.contains('product-quantity')) {
                    updateTotals();
                }
            });
            
            document.getElementById('productsContainer').addEventListener('click', (e) => {
                if (e.target.classList.contains('remove-product')) {
                    removeProductRow(e);
                }
            });
        }

        async function loadCustomers() {
            try {
                const response = await axios.get('api/salesApi.php?action=fetch_customers');
                console.log('Customer response:', response.data); // Debug log
                const customerSelect = document.getElementById('customer_id');
                customerSelect.innerHTML = '<option value="">Select Customer...</option>';

                if (response.data.success && response.data.customers) {
                    response.data.customers.forEach(customer => {
                        const option = document.createElement('option');
                        option.value = customer.id;
                        option.textContent = customer.name;
                        customerSelect.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error loading customers:', error);
                showAlert('Error loading customers', 'danger');
            }
        }

        async function loadProducts() {
            try {
                const response = await axios.get('api/salesApi.php?action=fetch_products');
                console.log('Products response:', response.data); // Debug log
                if (response.data.success && response.data.products) {
                    products = response.data.products;
                    updateProductSelects();
                }
            } catch (error) {
                console.error('Error loading products:', error);
                showAlert('Error loading products', 'danger');
            }
        }

        function updateProductSelects() {
            document.querySelectorAll('.product-select').forEach(select => {
                const currentValue = select.value;
                select.innerHTML = '<option value="">Select Product...</option>';
                
                products.forEach(product => {
                    const option = document.createElement('option');
                    option.value = product.id;
                    option.textContent = `${product.name} (Stock: ${product.quantity_in_hand})`;
                    option.dataset.price = product.price;
                    option.dataset.stock = product.quantity_in_hand;
                    select.appendChild(option);
                });

                if (currentValue) select.value = currentValue;
            });
        }

        function addProductRow() {
            const container = document.getElementById('productsContainer');
            const newRow = container.querySelector('.product-row').cloneNode(true);
            newRow.querySelector('.product-quantity').value = '';
            newRow.querySelector('.product-price').value = '';
            // Add input validation for quantity
            const quantityInput = newRow.querySelector('.product-quantity');
            quantityInput.addEventListener('input', validateQuantity);
            container.appendChild(newRow);
            updateProductSelects();
        }

        // Add this new function for quantity validation
        function validateQuantity(e) {
            const input = e.target;
            let value = parseInt(input.value);
            
            if (isNaN(value) || value <= 0) {
                showAlert('Quantity must be a positive number greater than zero', 'danger');
                input.value = '';
                updateTotals();
                return false;
            }
            return true;
        }

        function removeProductRow(event) {
            const row = event.target.closest('.product-row');
            if (document.querySelectorAll('.product-row').length > 1) {
                row.remove();
                updateTotals();
            }
        }

        // Modify the updateTotals function to include validation
        function updateTotals() {
            let total = 0;
            
            document.querySelectorAll('.product-row').forEach(row => {
                const select = row.querySelector('.product-select');
                const quantityInput = row.querySelector('.product-quantity');
                const quantity = parseInt(quantityInput.value) || 0;
                const priceInput = row.querySelector('.product-price');
                
                if (select.value && quantity) {
                    if (quantity <= 0) {
                        showAlert('Quantity must be greater than zero', 'danger');
                        quantityInput.value = '';
                        return;
                    }
                    
                    const product = products.find(p => p.id === select.value);
                    if (product) {
                        // Check if quantity exceeds available stock
                        if (quantity > product.quantity_in_hand) {
                            showAlert(`Error: Only ${product.quantity_in_hand} units available for ${product.name}`, 'danger');
                            quantityInput.value = product.quantity_in_hand;
                            return updateTotals();
                        }
                        const itemTotal = product.price * quantity;
                        priceInput.value = `NRs. ${itemTotal.toFixed(2)}`;
                        total += itemTotal;
                    }
                }
            });

            document.getElementById('total').textContent = `NRs. ${total.toFixed(2)}`;
        }

        async function submitSale(e) {
            e.preventDefault();

            const saleData = {
                customer_id: document.getElementById('customer_id').value,
                sales_channel: document.getElementById('sales_channel').value,
                products: []
            };

            let isValid = true;

            document.querySelectorAll('.product-row').forEach(row => {
                const select = row.querySelector('.product-select');
                const quantity = parseInt(row.querySelector('.product-quantity').value);
                
                if (select.value && quantity) {
                    const product = products.find(p => p.id === select.value);
                    if (quantity > product.quantity_in_hand) {
                        showAlert(`Insufficient stock for ${product.name}`, 'danger');
                        isValid = false;
                        return;
                    }
                    
                    saleData.products.push({
                        product_id: select.value,
                        quantity: quantity
                    });
                }
            });

            if (!isValid) return;

            try {
                const response = await axios.post('api/salesApi.php?action=submit_sale', saleData);
                console.log('Submit sale response:', response.data); // Debug log
                if (response.data.success) {
                    showAlert('Sale submitted successfully', 'success');
                    document.getElementById('salesForm').reset();
                    loadProducts();
                    loadRecentSales();
                } else {
                    showAlert(response.data.message, 'danger');
                }
            } catch (error) {
                console.error('Error submitting sale:', error);
                showAlert('Error submitting sale', 'danger');
            }
        }

        async function loadRecentSales() {
            try {
                const response = await axios.get('api/salesApi.php?action=fetch_recent_sales');
                const tbody = document.getElementById('recentSales');
                tbody.innerHTML = '';

                if (response.data.success && response.data.sales) {
                    response.data.sales.forEach(sale => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${sale.order_id}</td>
                            <td>${sale.customer_name}</td>
                            <td>${sale.channel || 'N/A'}</td>
                            <td>${sale.order_date}</td>
                            <td>
                                <select class="status-select" data-order-id="${sale.order_id}">
                                    <option value="Confirmed" ${sale.order_status === 'Confirmed' ? 'selected' : ''}>Confirmed</option>
                                    <option value="Packed" ${sale.order_status === 'Packed' ? 'selected' : ''}>Packed</option>
                                    <option value="Shipped" ${sale.order_status === 'Shipped' ? 'selected' : ''}>Shipped</option>
                                    <option value="Invoiced" ${sale.order_status === 'Invoiced' ? 'selected' : ''}>Invoiced</option>
                                </select>
                            </td>
                            <td>NRs. ${parseFloat(sale.total).toFixed(2)}</td>
                            <td>
                                <button class="update-status-btn bill-action-btn" data-order-id="${sale.order_id}">Update Status</button>
                                <button class="view-bill-btn bill-action-btn" data-order-id="${sale.order_id}">View Bill</button>
                                <button class="download-bill-btn bill-action-btn" data-order-id="${sale.order_id}">Download Bill</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });

                    // Add event listeners for status updates and bill viewing
                    setupEventListenersForRecentSales();
                }
            } catch (error) {
                console.error('Error loading recent sales:', error);
                showAlert('Error loading recent sales', 'danger');
            }
        }

        async function viewBill(orderId) {
            try {
                const response = await axios.get(`api/salesApi.php?action=get_bill_details&order_id=${orderId}`);
                if (response.data.success) {
                    const bill = response.data.bill;
         
                    document.getElementById('billCustomerName').textContent = bill.customer_name;
                    document.getElementById('billCustomerPhone').textContent = bill.contact_info;
                    document.getElementById('billOrderId').textContent = bill.order_id;
                    document.getElementById('billOrderDate').textContent = new Date(bill.order_date).toLocaleDateString();
                    document.getElementById('billOrderStatus').textContent = bill.order_status;
    
                    const itemsHtml = bill.items.map(item => `
                        <tr>
                            <td>${item.product_name}</td>
                            <td>${item.quantity}</td>
                            <td>NRs. ${parseFloat(item.price).toFixed(2)}</td>
                            <td>NRs. ${(item.quantity * item.price).toFixed(2)}</td>
                        </tr>
                    `).join('');
                    document.getElementById('billItems').innerHTML = itemsHtml;
                    document.getElementById('billTotal').textContent = `NRs. ${parseFloat(bill.total).toFixed(2)}`;
                    
                    const modal = document.getElementById('billModal');
                    const billContent = document.getElementById('billContent');
                    billContent.style.display = 'block';
                    modal.style.display = 'block';

                    void billContent.offsetHeight;
                }
            } catch (error) {
                console.error('Error fetching bill details:', error);
                showAlert('Error fetching bill details', 'danger');
            }
        }

        async function downloadBill(orderId) {
            try {
                const response = await axios.get(`api/salesApi.php?action=get_bill_details&order_id=${orderId}`);
                if (response.data.success) {
                    const bill = response.data.bill;
     
                    await viewBill(orderId);
        
                    await new Promise(resolve => setTimeout(resolve, 1000));
                    
                    const billElement = document.getElementById('billContent');

                    const clonedBill = billElement.cloneNode(true);
                    clonedBill.style.display = 'block';
                    document.body.appendChild(clonedBill);
                    
                    const date = new Date().toISOString().split('T')[0];
                    const customerName = bill.customer_name.replace(/[^a-zA-Z0-9]/g, '_');
                    const filename = `Invoice_${orderId}_${customerName}_${date}.pdf`;
                    
                    const opt = {
                        margin: 10,
                        filename: filename,
                        image: { type: 'jpeg', quality: 1 },
                        html2canvas: { 
                            scale: 2,
                            useCORS: true,
                            logging: false,
                            removeContainer: true,
                            windowWidth: 800
                        },
                        jsPDF: { 
                            unit: 'mm', 
                            format: 'a4', 
                            orientation: 'landscape'
                        }
                    };

                    try {
                        await html2pdf()
                            .from(clonedBill)
                            .set(opt)
                            .save();
                    } finally {
                        document.body.removeChild(clonedBill);
                    }
                    
                } else {
                    showAlert('Error getting bill details', 'danger');
                }
            } catch (error) {
                console.error('Error downloading bill:', error);
                showAlert('Error downloading bill', 'danger');
            }
        }

        function setupEventListenersForRecentSales() {
            document.querySelectorAll('.update-status-btn').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    const orderId = e.target.dataset.orderId;
                    const statusSelect = document.querySelector(`.status-select[data-order-id="${orderId}"]`);
                    const newStatus = statusSelect.value;

                    try {
                        const response = await axios.post('api/salesApi.php?action=update_status', {
                            order_id: orderId,
                            status: newStatus
                        });

                        if (response.data.success) {
                            showAlert('Order status updated successfully', 'success');
                        } else {
                            showAlert('Failed to update order status', 'danger');
                        }
                    } catch (error) {
                        console.error('Error updating status:', error);
                        showAlert('Error updating order status', 'danger');
                    }
                });
            });

            document.querySelectorAll('.view-bill-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const orderId = e.target.dataset.orderId;
                    viewBill(orderId);
                });
            });

            document.querySelectorAll('.download-bill-btn').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    const orderId = e.target.dataset.orderId;
                    await downloadBill(orderId);
                });
            });

      
            document.querySelector('.close').addEventListener('click', () => {
                document.getElementById('billModal').style.display = "none";
                document.getElementById('billContent').style.display = "block";
            });

     
            document.getElementById('downloadBill').addEventListener('click', async () => {
                const orderId = document.getElementById('billOrderId').textContent;
                if (orderId) {
                    await downloadBill(orderId);
                }
            });
        }

        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;
            alertDiv.textContent = message;
            document.querySelector('.container').insertBefore(alertDiv, document.querySelector('form'));
            setTimeout(() => alertDiv.remove(), 5000);
        }
    </script>
</body>
</html>
