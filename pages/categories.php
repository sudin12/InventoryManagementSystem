<?php
require_once '../config.php';

function addCategory($conn, $data) {
    $sql = "INSERT INTO categories (name) VALUES (?)";
    
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$data['name']]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $result = addCategory($conn, $_POST);
        echo json_encode(['success' => $result]);
        exit;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/inventory.css">
    
<script src="/InventoryManagementSystem/assets/js/shared.js"></script>
    <script src="../assets/js/categories.js" defer></script>
    <title>Category Management</title>
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>

    <main class="main-content">
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Categories</h3>
                <p class="stat-value">0</p>
                <p class="stat-subtext">Active</p>
            </div>
            <div class="stat-card">
                <h3>Products</h3>
                <p class="stat-value">0</p>
                <p class="stat-subtext">Across Categories</p>
            </div>
            <div class="stat-card">
                <h3>Top Category</h3>
                <p class="stat-value">None</p>
                <p class="stat-subtext">By Products</p>
            </div>
            <div class="stat-card">
                <h3>Inactive</h3>
                <p class="stat-value">0</p>
                <p class="stat-subtext">Categories</p>
            </div>
        </div>

        <div class="product-section">
            <div class="product-header">
                <h2>Categories List</h2>
                <div class="product-actions">
                    <button class="btn btn-secondary" onclick="showFilters()">
                        <i data-lucide="filter"></i> Filters
                    </button>
                    <button class="btn btn-secondary" onclick="downloadCategories()">
                        <i data-lucide="download"></i> Download all
                    </button>
                    <button class="btn btn-primary" onclick="showAddCategoryForm()">
                        Add Category
                    </button>
                </div>
            </div>
            
            <div class="table-container">
                <table class="product-table">
                    <thead>
                        <tr>
                            <th>Category ID</th>
                            <th>Name</th>
                            <th>Quick Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            
            <div id="pagination" class="pagination-container"></div>
        </div>
    </main>
</body>
</html>
