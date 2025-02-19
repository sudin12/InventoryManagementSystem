<?php
header('Content-Type: application/json');

// Database connection
$conn = new mysqli('localhost', 'root', '', 'inventory_management_system');

if ($conn->connect_error) {
    die(json_encode(['error' => $conn->connect_error]));
}

// Fetch total sales and product details
$query = "
    SELECT 
        p.name AS product_name,
        p.quantity_in_hand,
        SUM(oi.quantity) AS total_sales,
        p.price
    FROM 
        products p
    LEFT JOIN 
        order_items oi ON p.product_id = oi.product_id
    GROUP BY 
        p.product_id
    ORDER BY 
        total_sales DESC";

$result = $conn->query($query);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'product_name' => $row['product_name'],
            'quantity_in_hand' => (int)$row['quantity_in_hand'],
            'total_sales' => (int)$row['total_sales'],
            'price' => (float)$row['price']
        ];
    }
}

echo json_encode($data);
$conn->close();
?>
