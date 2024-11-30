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

function getSuppliers($conn) {
    try {
        $sql = "SELECT * FROM suppliers";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    } catch (Exception $e) {
        return [];
    }
}

function getSupplierById($conn, $supplier_id) {
    try {
        $stmt = $conn->prepare("SELECT * FROM suppliers WHERE supplier_id = ?");
        $stmt->bind_param("i", $supplier_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $supplier = $result->fetch_assoc();
        $stmt->close();
        return $supplier;
    } catch (Exception $e) {
        return null;
    }
}

function editSupplier($conn, $supplier_id, $name, $contact_info) {
    try {
        // Validate inputs
        if (empty($name) || empty($contact_info)) {
            return false;
        }

        $stmt = $conn->prepare("UPDATE suppliers SET name = ?, contact_info = ? WHERE supplier_id = ?");
        $stmt->bind_param("ssi", $name, $contact_info, $supplier_id);
        $result = $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        return $affected > 0;
    } catch (Exception $e) {
        return false;
    }
}

function deleteSupplier($conn, $supplier_id) {
    try {
        // Check if supplier exists
        $checkStmt = $conn->prepare("SELECT supplier_id FROM suppliers WHERE supplier_id = ?");
        $checkStmt->bind_param("i", $supplier_id);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows === 0) {
            $checkStmt->close();
            return false;
        }
        $checkStmt->close();

        // Proceed with deletion
        $stmt = $conn->prepare("DELETE FROM suppliers WHERE supplier_id = ?");
        $stmt->bind_param("i", $supplier_id);
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
            $supplier = getSupplierById($conn, intval($_GET['id']));
            if ($supplier) {
                echo json_encode(['success' => true, 'data' => $supplier]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Supplier not found']);
            }
        } else {
            $suppliers = getSuppliers($conn);
            echo json_encode($suppliers);
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

                $supplier_id = intval($_POST['id']);
                $name = trim($_POST['name']);
                $contact_info = trim($_POST['contact_info']);

                $result = editSupplier($conn, $supplier_id, $name, $contact_info);
                if ($result) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update supplier']);
                }
                break;

            case 'delete':
                if (!isset($_POST['id'])) {
                    echo json_encode(['success' => false, 'message' => 'Missing supplier ID']);
                    exit;
                }

                $supplier_id = intval($_POST['id']);
                $result = deleteSupplier($conn, $supplier_id);

                if ($result) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete supplier']);
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