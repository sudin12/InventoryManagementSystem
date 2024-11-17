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

// Handle form submission
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
  <title>Inventory Management</title>
</head>
<body>
  <?php include '../includes/sidebar.php'; ?>


  <main class="main-content">
  <!-- Stats Overview -->
  <div class="stats-grid">
    <div class="stat-card">
    <h3>Categories</h3>
    <p class="stat-value" id="categories-count"></p>
    <p class="stat-subtext">Last 7 days</p>
    </div>
    <div class="stat-card">
    <h3>Total Products</h3>
    <p class="stat-value" id="total-products-count"></p>
    <p class="stat-subtext">Last 7 days</p>
    </div>
    <div class="stat-card">
    <h3>Top Selling</h3>
    <p class="stat-value" id="top-selling"></p>
    <p class="stat-subtext">Revenue</p>
    </div>
    <div class="stat-card">
    <h3>Cost</h3>
    <p class="stat-value" id="cost"></p>
    <p class="stat-subtext">Last 7 days</p>
    </div>
  </div>

  <div class="product-section">
    <div class="product-header">
    <h2>Product List</h2>
    <div class="product-actions">
      <button class="btn btn-secondary" onclick="initializeButton()">
      <i data-lucide="filter"></i> Filters
      </button>
      <button class="btn btn-secondary">
      <i data-lucide="download"></i> Download all
      </button>
      <a href="AddProduct.php">
      <button class="btn btn-primary" onclick="showAddProductForm()">
       Add Product
      </button>
      </a>
    </div>
    </div>
\
    <div class="table-container">
    <table class="product-table">
      <thead>
      <tr>
        <th>Name</th>
        <th>Code</th>
        <th>Type</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Image</th>
        <th></th>
      </tr>
      </thead>
      <tbody id="product-table-body">
      </tbody>
    </table>
    </div>
  </div>
  </main>
<div id="add-product-form" class="form-popup">
  <form action="inventory.php" method="post" class="form-container" enctype="multipart/form-data">
    <h2>Add Product</h2>

    <label for="name"><b>Name</b></label>
    <input type="text" placeholder="Enter Product Name" name="name" required>

    <label for="category"><b>Category</b></label>
    <select name="category_id" required>
      <option value="">Select Category</option>
      <?php
      // Fetch categories from the database
      $query = "SELECT * FROM categories";
      $result = mysqli_query($conn, $query);
      while ($row = mysqli_fetch_assoc($result)) {
        echo "<option value='" . $row['category_id'] . "'>" . $row['name'] . "</option>";
      }
      ?>
    </select>

    <label for="price"><b>Price</b></label>
    <input type="number" placeholder="Enter Price" name="price" step="0.01" required>

    <label for="quantity"><b>Quantity</b></label>
    <input type="number" placeholder="Enter Quantity" name="quantity" required>

    <label for="image"><b>Product Image</b></label>
    <input type="file" name="image">

    <button type="submit" name="add_product" class="btn">Add Product</button>
    <button type="button" class="btn cancel" onclick="closeForm()">Close</button>
  </form>
</div>

  
</body>
</html>
