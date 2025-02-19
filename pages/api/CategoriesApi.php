<?php
ob_start(); // Start output buffering
require_once '../../config.php';

// Clear any previous output
ob_clean();

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log errors to file
ini_set('log_errors', 1);
ini_set('error_log', '../logs/php-error.log');

class CategoriesApi {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getCategories($page = 1, $limit = 10) {
        try {
            $offset = ($page - 1) * $limit;
            $sql = "SELECT c.category_id, c.name, 
                    (SELECT COUNT(*) FROM products p WHERE p.category_id = c.category_id) as product_count 
                    FROM categories c 
                    ORDER BY c.category_id 
                    LIMIT ? OFFSET ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $limit, $offset);
            $stmt->execute();

            $result = $stmt->get_result();
            $categories = $result->fetch_all(MYSQLI_ASSOC);

            return $categories;
        } catch (Exception $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function getTotalCategories() {
        try {
            $result = $this->conn->query("SELECT COUNT(*) as count FROM categories");
            $row = $result->fetch_assoc();
            return $row['count'];
        } catch (Exception $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function addCategory($name) {
        $stmt = $this->conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        return $stmt->execute();
    }

    public function updateCategory($id, $name) {
        $stmt = $this->conn->prepare("UPDATE categories SET name = ? WHERE category_id = ?");
        $stmt->bind_param("si", $name, $id);
        return $stmt->execute();
    }

    public function deleteCategory($id) {
        $stmt = $this->conn->prepare("DELETE FROM categories WHERE category_id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

try {
    $api = new CategoriesApi($conn);

    $response = ['success' => false]; // Default response

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $categories = $api->getCategories($page);
            $total = $api->getTotalCategories();

            $response = [
                'success' => true,
                'categories' => $categories,
                'total' => $total
            ];
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $api->addCategory($data['name']);
            $response = ['success' => $result];
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $api->updateCategory($data['id'], $data['name']);
            $response = ['success' => $result];
            break;

        case 'DELETE':
            $id = $_GET['id'];
            $result = $api->deleteCategory($id);
            $response = ['success' => $result];
            break;
    }

    // Ensure clean JSON output
    ob_clean();
    if (json_encode($response) === false) {
        throw new Exception("Failed to encode JSON response");
    }
    echo json_encode($response);

} catch (Exception $e) {
    ob_clean();
    error_log("Categories API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

// Flush and close output buffer
ob_end_flush();
exit();
