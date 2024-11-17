document.addEventListener('DOMContentLoaded', function() {
        fetchSuppliers();
});

function fetchSuppliers() {
        fetch('../pages/api/SuppliersApi.php')
                .then(response => response.json())
                .then(data => {
                        const tableBody = document.getElementById('supplier-table-body');
                        tableBody.innerHTML = ''; 

                        data.forEach(supplier => {
                                const row = document.createElement('tr');

                                const nameCell = document.createElement('td');
                                nameCell.classList.add('supplier-cell');
                                const logo = document.createElement('img');
                                logo.src = supplier.logoUrl;
                                logo.alt = supplier.name;
                                logo.classList.add('supplier-logo');
                                nameCell.appendChild(logo);
                                nameCell.appendChild(document.createTextNode(supplier.name));
                                row.appendChild(nameCell);

                                const contactCell = document.createElement('td');
                                contactCell.textContent = supplier.contact_info;
                                row.appendChild(contactCell);

                                const actionsCell = document.createElement('td');
                                const orderHistoryLink = document.createElement('a');
                                orderHistoryLink.href = '#';
                                orderHistoryLink.classList.add('link');
                                orderHistoryLink.textContent = 'Order History';
                                actionsCell.appendChild(orderHistoryLink);
                                row.appendChild(actionsCell);

                                tableBody.appendChild(row);
                        });
                })
                .catch(error => {
                        console.error('Error fetching suppliers:', error);
                });
}