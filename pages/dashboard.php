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
    // Add debug logging
    error_log("Starting dashboard data fetch...");

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
    error_log("Sales query result: " . ($sales_result ? "Success" : mysqli_error($conn)));
    if (!$sales_result) {
        throw new Exception(mysqli_error($conn));
    }
    $sales_overview = mysqli_fetch_assoc($sales_result);

    // Purchase Overview
    $purchase_query = "SELECT 
        COUNT(*) as total_purchase,
        COALESCE(SUM(cost), 0) as total_cost
    FROM inventory_transactions 
    WHERE transaction_type = 'Purchase'";

    $purchase_result = mysqli_query($conn, $purchase_query);
    error_log("Purchase query result: " . ($purchase_result ? "Success" : mysqli_error($conn)));
    $purchase_overview = mysqli_fetch_assoc($purchase_result);

    // Sales Order with debug
    $sales_order_query = "SELECT 
        IFNULL(channel, 'Unknown') as channel,
        SUM(CASE WHEN order_status = 'Draft' THEN 1 ELSE 0 END) as draft,
        SUM(CASE WHEN order_status = 'Confirmed' THEN 1 ELSE 0 END) as confirmed,
        SUM(CASE WHEN order_status = 'Packed' THEN 1 ELSE 0 END) as packed,
        SUM(CASE WHEN order_status = 'Shipped' THEN 1 ELSE 0 END) as shipped,
        SUM(CASE WHEN order_status = 'Invoiced' THEN 1 ELSE 0 END) as invoiced
    FROM orders
    GROUP BY channel";

    $sales_order_result = mysqli_query($conn, $sales_order_query);
    if (!$sales_order_result) {
        error_log("Sales order query error: " . mysqli_error($conn));
    }
    $sales_orders = [];
    while($row = mysqli_fetch_assoc($sales_order_result)) {
        $sales_orders[] = $row;
    }
    error_log("Sales orders data: " . json_encode($sales_orders));

    // Top Selling Stock with debug
    $top_selling_query = "SELECT 
        p.name,
        COALESCE(SUM(CASE 
            WHEN o.order_status = 'Invoiced' THEN oi.quantity 
            ELSE 0 
        END), 0) as sold_quantity,
        p.quantity_in_hand as remaining_quantity,
        p.price
    FROM products p
    LEFT JOIN order_items oi ON p.product_id = oi.product_id
    LEFT JOIN orders o ON oi.order_id = o.order_id
    GROUP BY p.product_id, p.name, p.quantity_in_hand, p.price
    HAVING sold_quantity > 0
    ORDER BY sold_quantity DESC
    LIMIT 5";

    $top_selling_result = mysqli_query($conn, $top_selling_query);
    if (!$top_selling_result) {
        throw new Exception(mysqli_error($conn));
    }
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

    // Inventory Summary with debug
    $summary_query = "SELECT 
        SUM(quantity_in_hand) as quantity_in_hand,
        (SELECT COUNT(*) FROM suppliers) as supplier_count,
        (SELECT COUNT(*) FROM categories) as category_count
    FROM products";

    $summary_result = mysqli_query($conn, $summary_query);
    if (!$summary_result) {
        error_log("Summary query error: " . mysqli_error($conn));
    }
    $summary = mysqli_fetch_assoc($summary_result);
    error_log("Summary data: " . json_encode($summary));

    $response = [
        "sales_overview" => [
            "sales" => floatval($sales_overview['total_sales']),
            "revenue" => floatval($sales_overview['revenue']),
            "profit" => floatval($sales_overview['profit']),
            "cost" => floatval($sales_overview['cost'])
        ],
        "purchase_overview" => [
            "total_purchase" => intval($purchase_overview['total_purchase']),
            "total_cost" => floatval($purchase_overview['total_cost'])
        ],
        "sales_orders" => $sales_orders,
        "top_selling" => $top_selling,
        "low_stock" => $low_stock,
        "summary" => [
            "quantity_in_hand" => intval($summary['quantity_in_hand']),
            "supplier_count" => intval($summary['supplier_count']),
            "category_count" => intval($summary['category_count'])
        ]
    ];

    // Log the final response
    error_log("Final response: " . json_encode($response));

    echo json_encode($response);

} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
    echo json_encode(["error" => $e->getMessage()]);
}

mysqli_close($conn);
?>