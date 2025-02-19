<?php
session_start();
require_once '../../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        case 'fetch_customers':
            fetchCustomers($conn);
            break;
        case 'fetch_products':
            fetchProducts($conn);
            break;
        case 'submit_sale':
            submitSale($conn);
            break;
        case 'fetch_recent_sales':
            fetchRecentSales($conn);
            break;
        case 'update_status':
            updateOrderStatus($conn);
            break;
        case 'get_bill_details':
            getBillDetails($conn);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
}

function fetchCustomers($conn) {
    try {
        $query = "SELECT customer_id as id, name FROM customers ORDER BY name ASC";
        $result = $conn->query($query);

        if (!$result) {
            throw new Exception($conn->error);
        }

        $customers = [];
        while ($row = $result->fetch_assoc()) {
            $customers[] = [
                'id' => $row['id'],
                'name' => $row['name']
            ];
        }

        echo json_encode(['success' => true, 'customers' => $customers]);
    } catch (Exception $e) {
        error_log("Error in fetchCustomers: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error fetching customers: ' . $e->getMessage()]);
    }
}

function fetchProducts($conn) {
    try {
        $query = "SELECT 
                    p.product_id as id, 
                    p.name, 
                    p.price, 
                    p.quantity_in_hand,
                    c.name as category 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.category_id 
                WHERE p.quantity_in_hand > 0 
                ORDER BY p.name ASC";
        
        $result = $conn->query($query);

        if (!$result) {
            throw new Exception($conn->error);
        }

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'price' => (float)$row['price'],
                'quantity_in_hand' => (int)$row['quantity_in_hand'],
                'category' => $row['category']
            ];
        }

        echo json_encode(['success' => true, 'products' => $products]);
    } catch (Exception $e) {
        error_log("Error in fetchProducts: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error fetching products: ' . $e->getMessage()]);
    }
}

function submitSale($conn) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['customer_id']) || !isset($data['sales_channel']) || empty($data['products'])) {
            throw new Exception('Invalid input data');
        }

        $customer_id = $conn->real_escape_string($data['customer_id']);
        $sales_channel = $conn->real_escape_string($data['sales_channel']);

        $conn->begin_transaction();

        // Create new order
        $query = "INSERT INTO orders (order_date, order_status, channel, customer_id) 
                VALUES (CURRENT_DATE(), 'Confirmed', ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si', $sales_channel, $customer_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Error creating order: " . $stmt->error);
        }
        
        $order_id = $conn->insert_id;

        // Insert order items and update inventory
        $total_amount = 0;
        foreach ($data['products'] as $product) {
            $product_id = $conn->real_escape_string($product['product_id']);
            $quantity = (int)$product['quantity'];

            // Check stock availability
            $stock_query = "SELECT quantity_in_hand, price FROM products WHERE product_id = ?";
            $stmt = $conn->prepare($stock_query);
            $stmt->bind_param('i', $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $product_data = $result->fetch_assoc();

            if (!$product_data) {
                throw new Exception("Product not found: $product_id");
            }

            if ($product_data['quantity_in_hand'] < $quantity) {
                throw new Exception("Insufficient stock for product ID: $product_id");
            }

            // Insert order item
            $price = $product_data['price'];
            $query = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('iiid', $order_id, $product_id, $quantity, $price);
            
            if (!$stmt->execute()) {
                throw new Exception("Error creating order item: " . $stmt->error);
            }

            // Update inventory
            $query = "UPDATE products SET quantity_in_hand = quantity_in_hand - ? WHERE product_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ii', $quantity, $product_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Error updating inventory: " . $stmt->error);
            }

            // Record inventory transaction
            $query = "INSERT INTO inventory_transactions (product_id, transaction_type, quantity, transaction_date, cost) 
                    VALUES (?, 'Sale', ?, CURRENT_DATE(), ?)";
            $total_cost = $quantity * $price;
            $stmt = $conn->prepare($query);
            $stmt->bind_param('iid', $product_id, $quantity, $total_cost);
            
            if (!$stmt->execute()) {
                throw new Exception("Error recording inventory transaction: " . $stmt->error);
            }

            $total_amount += $total_cost;
        }

        $conn->commit();
        echo json_encode([
            'success' => true, 
            'message' => 'Sale completed successfully',
            'order_id' => $order_id,
            'total_amount' => $total_amount
        ]);
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollback();
        }
        error_log("Error in submitSale: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function fetchRecentSales($conn) {
    try {
        $query = "SELECT 
                    o.order_id,
                    o.order_date,
                    o.order_status,
                    o.channel,
                    c.name as customer_name,
                    COALESCE(SUM(oi.quantity * oi.price), 0) as total
                FROM orders o
                JOIN customers c ON o.customer_id = c.customer_id
                LEFT JOIN order_items oi ON o.order_id = oi.order_id
                GROUP BY o.order_id, o.order_date, o.order_status, o.channel, c.name
                ORDER BY o.order_date DESC, o.order_id DESC
                LIMIT 10";

        $result = $conn->query($query);

        if (!$result) {
            throw new Exception($conn->error);
        }

        $sales = [];
        while ($row = $result->fetch_assoc()) {
            $sales[] = [
                'order_id' => (int)$row['order_id'],
                'order_date' => $row['order_date'],
                'order_status' => $row['order_status'],
                'channel' => $row['channel'],
                'customer_name' => $row['customer_name'],
                'total' => (float)$row['total']
            ];
        }

        echo json_encode(['success' => true, 'sales' => $sales]);
    } catch (Exception $e) {
        error_log("Error in fetchRecentSales: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error fetching recent sales: ' . $e->getMessage()]);
    }
}

function updateOrderStatus($conn) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['order_id']) || !isset($data['status'])) {
            throw new Exception('Invalid input data');
        }

        $order_id = $conn->real_escape_string($data['order_id']);
        $status = $conn->real_escape_string($data['status']);
        
        // Validate status
        $valid_statuses = ['Confirmed', 'Packed', 'Shipped', 'Invoiced'];
        if (!in_array($status, $valid_statuses)) {
            throw new Exception('Invalid status value');
        }

        $query = "UPDATE orders SET order_status = ? WHERE order_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $status, $order_id);
        
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        if ($stmt->affected_rows === 0) {
            throw new Exception('Order not found or status not changed');
        }

        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
    } catch (Exception $e) {
        error_log("Error in updateOrderStatus: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error updating status: ' . $e->getMessage()]);
    }
}

function getBillDetails($conn) {
    try {
        if (!isset($_GET['order_id'])) {
            throw new Exception('Order ID is required');
        }

        $order_id = $conn->real_escape_string($_GET['order_id']);
        
        // First query - get order details
        $orderQuery = "SELECT 
                        o.order_id,
                        o.order_date,
                        o.order_status,
                        o.channel,
                        c.name as customer_name,
                        c.contact_info
                    FROM orders o
                    JOIN customers c ON o.customer_id = c.customer_id
                    WHERE o.order_id = ?";

        $stmt = $conn->prepare($orderQuery);
        if (!$stmt) {
            throw new Exception('Failed to prepare order query: ' . $conn->error);
        }
        
        $stmt->bind_param('i', $order_id);
        if (!$stmt->execute()) {
            throw new Exception('Failed to execute order query: ' . $stmt->error);
        }
        
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            throw new Exception('Order not found');
        }

        $orderDetails = $result->fetch_assoc();
        $stmt->close();

        // Second query - get order items
        $itemsQuery = "SELECT 
                        oi.quantity,
                        oi.price,
                        p.name as product_name
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.product_id
                    WHERE oi.order_id = ?";

        $stmt = $conn->prepare($itemsQuery);
        if (!$stmt) {
            throw new Exception('Failed to prepare items query: ' . $conn->error);
        }
        
        $stmt->bind_param('i', $order_id);
        if (!$stmt->execute()) {
            throw new Exception('Failed to execute items query: ' . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $items = [];
        $total = 0;

        while ($row = $result->fetch_assoc()) {
            $itemTotal = $row['quantity'] * $row['price'];
            $items[] = [
                'product_name' => $row['product_name'],
                'quantity' => (int)$row['quantity'],
                'price' => (float)$row['price'],
                'total' => $itemTotal
            ];
            $total += $itemTotal;
        }

        $billData = [
            'order_id' => $orderDetails['order_id'],
            'order_date' => $orderDetails['order_date'],
            'order_status' => $orderDetails['order_status'],
            'channel' => $orderDetails['channel'],
            'customer_name' => $orderDetails['customer_name'],
            'contact_info' => $orderDetails['contact_info'],
            'items' => $items,
            'total' => $total
        ];

        echo json_encode(['success' => true, 'bill' => $billData]);
    } catch (Exception $e) {
        error_log("Error in getBillDetails: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Error getting bill details: ' . $e->getMessage(),
            'error' => $e->getMessage()
        ]);
    }
}
?>
