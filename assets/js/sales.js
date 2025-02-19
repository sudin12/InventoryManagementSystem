document.getElementById('addProduct').addEventListener('click', () => {
    const container = document.getElementById('productsContainer');
    const newRow = document.querySelector('.productRow').cloneNode(true);
    container.appendChild(newRow);
});

document.getElementById('salesForm').addEventListener('submit', async (event) => {
    event.preventDefault();

    const customer_id = document.getElementById('customer_id').value;
    const channel = document.getElementById('channel').value;
    const productRows = document.querySelectorAll('.productRow');

    // Add validation
    let isValid = true;
    const products = Array.from(productRows).map(row => {
        const quantity = row.querySelector('.productQuantity').value;
        const price = row.querySelector('.productSelect option:checked').dataset.price;
    
        if (!quantity || quantity <= 0) {
            alert('Quantity must be a positive number');
            isValid = false;
        }
        
        // Validate price
        if (!price || parseFloat(price) <= 0) {
            alert('Price must be a positive number');
            isValid = false;
        }

        return {
            product_id: row.querySelector('.productSelect').value,
            quantity: quantity,
            price: price
        };
    });

    if (!isValid) return;

    const response = await fetch('../pages/api/salesApi.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ customer_id, channel, products })
    });

    const result = await response.json();
    alert(result.message);
});
