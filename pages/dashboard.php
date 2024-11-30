<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once '../config.php';

if (!$conn) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

try {
    // Sales Overview
    //COALESCE() function is used to handle NULL values in the query. If the value is NULL, it will be replaced with 0.
    $sales_query = "SELECT 
        COALESCE(SUM(oi.quantity * oi.price), 0) as total_sales, 
        COALESCE(SUM(oi.quantity * oi.price), 0) - 
            (SELECT COALESCE(SUM(cost), 0) FROM inventory_transactions WHERE transaction_type = 'Purchase') as revenue,
        (SELECT SUM(oi.quantity * oi.price) 
     FROM order_items oi 
     JOIN orders o ON oi.order_id = o.order_id 
     WHERE o.order_status = 'Invoiced') -
    (SELECT SUM(cost) 
     FROM inventory_transactions 
     WHERE transaction_type = 'Purchase') as profit,
        (SELECT COALESCE(SUM(cost), 0) FROM inventory_transactions WHERE transaction_type = 'Purchase') as cost
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.order_id
    WHERE o.order_status = 'Invoiced'";

    $sales_result = mysqli_query($conn, $sales_query);
    if (!$sales_result) {
        throw new Exception(mysqli_error($conn));
    }
    $sales_overview = mysqli_fetch_assoc($sales_result);

    // Purchase Overview
    $purchase_query = "SELECT 
        COUNT(*) as total_purchase,
        COALESCE(SUM(cost), 0) as total_cost,
        (SELECT COUNT(*) FROM returns) as cancel_count,
        (SELECT COALESCE(SUM(r.quantity * p.price), 0) FROM returns r 
         JOIN products p ON r.product_id = p.product_id) as return_amount
    FROM inventory_transactions 
    WHERE transaction_type = 'Purchase'";

    $purchase_result = mysqli_query($conn, $purchase_query);
    $purchase_overview = mysqli_fetch_assoc($purchase_result);

    // Sales Order
    $sales_order_query = "SELECT 
        channel,
        SUM(CASE WHEN order_status = 'Draft' THEN 1 ELSE 0 END) as draft,
        SUM(CASE WHEN order_status = 'Confirmed' THEN 1 ELSE 0 END) as confirmed,
        SUM(CASE WHEN order_status = 'Packed' THEN 1 ELSE 0 END) as packed,
        SUM(CASE WHEN order_status = 'Shipped' THEN 1 ELSE 0 END) as shipped,
        SUM(CASE WHEN order_status = 'Invoiced' THEN 1 ELSE 0 END) as invoiced
    FROM orders
    GROUP BY channel";

    $sales_order_result = mysqli_query($conn, $sales_order_query);
    $sales_orders = [];
    while($row = mysqli_fetch_assoc($sales_order_result)) {
        $sales_orders[] = $row;
    }

    // Top Selling Stock
    $top_selling_query = "SELECT 
        p.name,
        COALESCE(SUM(oi.quantity), 0) as sold_quantity,
        p.quantity_in_hand as remaining_quantity,
        p.price
    FROM products p
    LEFT JOIN order_items oi ON p.product_id = oi.product_id
    GROUP BY p.product_id
    ORDER BY sold_quantity DESC
    LIMIT 5";

    $top_selling_result = mysqli_query($conn, $top_selling_query);
    $top_selling = [];
    while($row = mysqli_fetch_assoc($top_selling_result)) {
        $top_selling[] = $row;
    }

    // Low Quantity Stock
    $low_stock_query = "SELECT 
        name,
        quantity_in_hand as remaining_quantity,
        low_quantity_alert
    FROM products
    WHERE quantity_in_hand <= 10
    LIMIT 5";

    $low_stock_result = mysqli_query($conn, $low_stock_query);
    $low_stock = [];
    while($row = mysqli_fetch_assoc($low_stock_result)) {
        $low_stock[] = $row;
    }

    // Inventory Summary
    $summary_query = "SELECT 
        SUM(quantity_in_hand) as quantity_in_hand,
        SUM(quantity_to_receive) as to_be_received,
        (SELECT COUNT(*) FROM suppliers) as supplier_count,
        (SELECT COUNT(*) FROM categories) as category_count
    FROM products";

    $summary_result = mysqli_query($conn, $summary_query);
    $summary = mysqli_fetch_assoc($summary_result);

    $response = [
        "sales_overview" => [
            "sales" => floatval($sales_overview['total_sales']),
            "revenue" => floatval($sales_overview['revenue']),
            "profit" => floatval($sales_overview['profit']),
            "cost" => floatval($sales_overview['cost'])
        ],
        "purchase_overview" => [
            "total_purchase" => intval($purchase_overview['total_purchase']),
            "total_cost" => floatval($purchase_overview['total_cost']),
            "cancel_count" => intval($purchase_overview['cancel_count']),
            "return_amount" => floatval($purchase_overview['return_amount'])
        ],
        "sales_orders" => $sales_orders,
        "top_selling" => $top_selling,
        "low_stock" => $low_stock,
        "summary" => [
            "quantity_in_hand" => intval($summary['quantity_in_hand']),
            "to_be_received" => intval($summary['to_be_received']),
            "supplier_count" => intval($summary['supplier_count']),
            "category_count" => intval($summary['category_count'])
        ]
    ];

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

mysqli_close($conn);
?>