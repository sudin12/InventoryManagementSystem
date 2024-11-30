<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config.php';

if (!$conn) {
    die(json_encode(["error" => "Connection failed: " . mysqli_connect_error()]));
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';
$product_id = $data['product_id'] ?? null;

// Debug logging
error_log("Received request - Action: " . $action . ", Product ID: " . $product_id);

try {
    if (empty($action)) {
        throw new Exception("Action is required");
    }

    switch ($action) {
        case 'view':
            if (!$product_id) throw new Exception("Product ID is required");

            $query = "SELECT p.*, c.name as type 
                      FROM products p 
                      LEFT JOIN categories c ON p.category_id = c.category_id 
                      WHERE p.product_id = ?";

            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $product_id); // Product ID is integer
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $product = mysqli_fetch_assoc($result);

            if ($product) {
                echo json_encode(['success' => true, 'data' => $product]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Product not found']);
            }
            break;

        case 'delete':
            if (!$product_id) throw new Exception("Product ID is required");

            $query = "DELETE FROM products WHERE product_id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $product_id); // Product ID is integer
            $success = mysqli_stmt_execute($stmt);

            if ($success) {
                echo json_encode(['success' => true]);
            } else {
                throw new Exception('Failed to delete product');
            }
            break;

        case 'edit':
            $product_id = $data['product_id'] ?? null;
            $name = $data['name'] ?? '';
            $price = $data['price'] ?? '';
            $quantity = $data['quantity'] ?? '';

            if (!$product_id || empty($name) || empty($price) || empty($quantity)) {
                throw new Exception('Required fields are missing');
            }

            // Validate input data
            if (!is_numeric($price) || !is_numeric($quantity)) {
                throw new Exception('Invalid input data');
            }

            // Sanitize input data
            $name = mysqli_real_escape_string($conn, $name);
            $description = mysqli_real_escape_string($conn, $description);

            // Update the product in the database
            $query = "UPDATE products SET name = ?, description = ?, price = ?, quantity_in_hand = ? WHERE product_id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssdii", $name, $description, $price, $quantity, $product_id);
            $success = mysqli_stmt_execute($stmt);

            if ($success) {
                echo json_encode(['success' => true]);
            } else {
                throw new Exception('Failed to update product');
            }
            break;

        default:
            throw new Exception("Invalid action type: " . $action);
    }
} catch (Exception $e) {
    error_log("Error in actions.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

mysqli_close($conn);
?>