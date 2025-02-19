<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
try {
    include_once '../../config.php';
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database configuration error']);
    exit;
}

function getCustomers($conn) {
    try {
        $sql = "SELECT * FROM customers";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    } catch (Exception $e) {
        return [];
    }
}

function getCustomerById($conn, $customer_id) {
    try {
        $stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = ?");
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $customer = $result->fetch_assoc();
        $stmt->close();
        return $customer;
    } catch (Exception $e) {
        return null;
    }
}

function editCustomer($conn, $customer_id, $name, $contact_info) {
    try {
        if (empty($name) || empty($contact_info)) {
            return false;
        }

        $stmt = $conn->prepare("UPDATE customers SET name = ?, contact_info = ? WHERE customer_id = ?");
        $stmt->bind_param("ssi", $name, $contact_info, $customer_id);
        $result = $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        return $affected > 0;
    } catch (Exception $e) {
        return false;
    }
}

function deleteCustomer($conn, $customer_id) {
    try {
        $checkStmt = $conn->prepare("SELECT customer_id FROM customers WHERE customer_id = ?");
        $checkStmt->bind_param("i", $customer_id);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows === 0) {
            $checkStmt->close();
            return false;
        }
        $checkStmt->close();

        $stmt = $conn->prepare("DELETE FROM customers WHERE customer_id = ?");
        $stmt->bind_param("i", $customer_id);
        $result = $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        return $affected > 0;
    } catch (Exception $e) {
        return false;
    }
}

// Handle API requests
try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['id'])) {
            $customer = getCustomerById($conn, intval($_GET['id']));
            if ($customer) {
                echo json_encode(['success' => true, 'data' => $customer]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Customer not found']);
            }
        } else {
            $customers = getCustomers($conn);
            echo json_encode($customers);
        }
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'edit':
                if (!isset($_POST['id']) || !isset($_POST['name']) || !isset($_POST['contact_info'])) {
                    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                    exit;
                }

                $customer_id = intval($_POST['id']);
                $name = trim($_POST['name']);
                $contact_info = trim($_POST['contact_info']);

                $result = editCustomer($conn, $customer_id, $name, $contact_info);
                if ($result) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update customer']);
                }
                break;

            case 'delete':
                if (!isset($_POST['id'])) {
                    echo json_encode(['success' => false, 'message' => 'Missing customer ID']);
                    exit;
                }

                $customer_id = intval($_POST['id']);
                $result = deleteCustomer($conn, $customer_id);

                if ($result) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete customer']);
                }
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
                break;
        }
        exit;
    }
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error']);
    exit;
}
?>
