<?php
header("Content-Type: application/json");


// Include database connection
include_once '../../config.php';
// Check if API request method is valid


// Switch based on the requested API endpoint
switch ($method) {
    case 'GET':
        // If GET method, fetch the data
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'totalProducts':
                    getTotalProducts();
                    break;
                case 'lowStock':
                    getLowStock();
                    break;
                case 'outOfStock':
                    getOutOfStock();
                    break;
                case 'suppliers':
                    getSuppliers();
                    break;
                case 'stockValue':
                    getStockValue();
                    break;
                default:
                    echo json_encode(["message" => "Action not found"]);
                    break;
            }
        } else {
            echo json_encode(["message" => "Action required"]);
        }
        break;
    default:
        echo json_encode(["message" => "Method not allowed"]);
        break;
}

// Function to get total products
function getTotalProducts() {
    global $conn;
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM products");
    $data = mysqli_fetch_assoc($result);
    echo json_encode(["totalProducts" => $data['total']]);
}

// Function to get low stock products
function getLowStock() {
    global $conn;
    $result = mysqli_query($conn, "SELECT COUNT(*) AS lowStock FROM products WHERE quantity <= 5");
    $data = mysqli_fetch_assoc($result);
    echo json_encode(["lowStock" => $data['lowStock']]);
}

// Function to get out of stock products
function getOutOfStock() {
    global $conn;
    $result = mysqli_query($conn, "SELECT COUNT(*) AS outOfStock FROM products WHERE quantity = 0");
    $data = mysqli_fetch_assoc($result);
    echo json_encode(["outOfStock" => $data['outOfStock']]);
}

// Function to get total suppliers
function getSuppliers() {
    global $conn;
    $result = mysqli_query($conn, "SELECT COUNT(*) AS suppliers FROM suppliers");
    $data = mysqli_fetch_assoc($result);
    echo json_encode(["suppliers" => $data['suppliers']]);
}

// Function to calculate the value of stock
function getStockValue() {
    global $conn;
    $result = mysqli_query($conn, "SELECT SUM(quantity * price) AS stockValue FROM products");
    $data = mysqli_fetch_assoc($result);
    echo json_encode(["stockValue" => $data['stockValue']]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error']);
    exit;
}
?>