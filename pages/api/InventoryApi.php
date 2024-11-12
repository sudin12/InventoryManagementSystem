<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config.php';


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

try {
    $stats_query = "SELECT 
        (SELECT COUNT(*) FROM categories) as total_categories,
        (SELECT COUNT(*) FROM products) as total_products,
        (SELECT COALESCE(SUM(oi.quantity * oi.price), 0) 
         FROM order_items oi 
         JOIN orders o ON oi.order_id = o.order_id 
         WHERE o.order_status = 'Invoiced') as revenue,
        (SELECT COALESCE(SUM(cost), 0) 
         FROM inventory_transactions 
         WHERE transaction_type = 'Purchase') as total_cost
    FROM dual";

    $stats_result = mysqli_query($conn, $stats_query);
    if (!$stats_result) {
        throw new Exception(mysqli_error($conn));
    }
    $stats = mysqli_fetch_assoc($stats_result);

    $products_query = "SELECT 
        p.name,
        p.product_id as code,
        c.name as type,
        p.price,
        p.quantity_in_hand as quantity
        
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    ORDER BY p.product_id";

    $products_result = mysqli_query($conn, $products_query);
    if (!$products_result) {
        throw new Exception(mysqli_error($conn));
    }

    $products = [];
    while($row = mysqli_fetch_assoc($products_result)) {
        $products[] = $row;
    }

    $response = [
        "stats" => [
            "categories" => $stats['total_categories'],
            "total_products" => $stats['total_products'],
            "revenue" => $stats['revenue'],
            "total_cost" => $stats['total_cost']
        ],
        "products" => $products
    ];

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}

mysqli_close($conn);
?>