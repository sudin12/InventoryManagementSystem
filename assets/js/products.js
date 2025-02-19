document.getElementById('editProductForm').addEventListener('submit', async (event) => {
    event.preventDefault();

    // Get form values
    const product_id = document.getElementById('edit_product_id').value;
    const product_name = document.getElementById('edit_product_name').value;
    const price = document.getElementById('edit_price').value;
    const quantity = document.getElementById('edit_quantity').value;
    const category_id = document.getElementById('edit_category_id').value;

    // Add validation
    if (!price || parseFloat(price) <= 0) {
        alert('Price must be a positive number');
        return;
    }

    if (!quantity || parseInt(quantity) < 0) {
        alert('Quantity cannot be negative');
        return;
    }

    const response = await fetch('../pages/api/productsApi.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            product_id,
            product_name,
            price,
            quantity,
            category_id
        })
    });

    const result = await response.json();
    alert(result.message);
    if (result.status === 'success') {
        location.reload();
    }
});
