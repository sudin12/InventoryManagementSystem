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
  <!-- Sidebar -->
  <?php include '../includes/sidebar.php'; ?>

  <!-- Main Content -->
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

    <!-- Product List Section -->
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
          <button class="btn btn-primary">
            <i data-lucide="plus"></i> Add Product
          </button>
        </div>
      </div>

      <!-- Product Table -->
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
            <!-- Data will be populated here by JavaScript -->
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Icons Script -->
  <script src="https://unpkg.com/lucide@latest"></script>
  
</body>
</html>
