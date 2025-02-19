<?php
require_once '../config.php';

function addProduct($conn, $data) {
  $sql = "INSERT INTO products (name, category, price, quantity) 
      VALUES (?, ?, ?, ?)";
  
  $stmt = $conn->prepare($sql);
  return $stmt->execute([
    $data['name'],
    $data['category_id'],
    $data['price'],
    $data['quantity']
  ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $result = addProduct($conn, $_POST);
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
  <script src="../assets/js/inventory.js" defer></script>

<script src="/InventoryManagementSystem/assets/js/shared.js"></script>
  <title>Inventory Management</title>
</head>
<body>
  <?php include '../includes/sidebar.php'; ?>

  <main class="main-content">
    <div class="stats-grid">
      <div class="stat-card">
        <h3>Categories</h3>
        <p class="stat-value">0</p>
        <p class="stat-subtext">Last 7 days</p>
      </div>
      <div class="stat-card">
        <h3>Total Products</h3>
        <p class="stat-value">0</p>
        <p class="stat-subtext">Last 7 days</p>
      </div>
      <div class="stat-card">
        <h3>Top Selling</h3>
        <p class="stat-value">Rs0.00</p>
        <p class="stat-subtext">Revenue</p>
      </div>
      <div class="stat-card">
        <h3>Cost</h3>
        <p class="stat-value">Rs0.00</p>
        <p class="stat-subtext">Last 7 days</p>
      </div>
    </div>

    <div class="product-section">
      <div class="product-header">
        <h2>Product List</h2>
        <div class="product-actions">
          <button class="btn btn-secondary" onclick="showFilters()">
            <i data-lucide="filter"></i> Filters
          </button>
          <button class="btn btn-secondary" onclick="downloadProducts()">
            <i data-lucide="download"></i> Download all
          </button>
          <button class="btn btn-primary" onclick="showAddProductForm()">
            Add Product
          </button>
        </div>
      </div>
      
      <div class="table-container">
        <table class="product-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Code</th>
              <th>Type</th>
              <th>Price</th>
              <th>Quantity</th>
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
