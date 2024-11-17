<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sidebar</title>
        <link rel="stylesheet" href="../assets/css/sidebar.css">
</head>
<style>
      :root {
        --blue-500: #3b82f6;
        --purple-500: #8b5cf6;
        --orange-500: #f97316;
        --green-500: #22c55e;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
    }
    
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        background-color: var(--gray-50);
        color: var(--gray-700);
    }
    
    .layout {
        display: flex;
        min-height: 100vh;
    }
    
    /* Sidebar */
    .sidebar {
        width: 256px;
        background: white;
        border-right: 1px solid var(--gray-200);
        display: flex;
        flex-direction: column;
    }
    
    .logo {
        padding: 1.5rem;
    }
    
    .logo h1 {
        color: var(--blue-500);
        font-size: 1.25rem;
        font-weight: 600;
    }
    
    .nav-menu {
        flex: 1;
    }
    
    .nav-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 1.5rem;
        color: var(--gray-600);
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .nav-item:hover {
        background-color: var(--gray-50);
        color: var(--blue-500);
    }
    
    .nav-item.active {
        background-color: var(--gray-50);
        color: var(--blue-500);
    }
    
    .nav-item i {
        width: 20px;
        height: 20px;
        margin-right: 0.75rem;
    }
    
    .sidebar-footer {
        padding: 1.5rem;
        border-top: 1px solid var(--gray-200);
    }
    
    /* Main Content */
    .main-content {
        flex: 1;
        padding: 2rem;
    }
    
    /* Header */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .search-box {
        position: relative;
    }
    
    .search-box i {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-400);
    }
    
    .search-box input {
        padding: 0.5rem 1rem 0.5rem 2.5rem;
        width: 384px;
        border: 1px solid var(--gray-200);
        border-radius: 0.5rem;
        font-size: 0.875rem;
    }
    
    .header-right {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .notification-btn {
        padding: 0.5rem;
        border: none;
        background: none;
        border-radius: 9999px;
        cursor: pointer;
        color: var(--gray-600);
    }
    
    .notification-btn:hover {
        background-color: var(--gray-100);
    }
    
    .avatar {
        width: 2rem;
        height: 2rem;
        border-radius: 9999px;
        background-color: var(--gray-200);
    }
  
 </style>
<body>
<div class="layout">
      <aside class="sidebar">
        <div class="logo">
          <h1>Inventory Track</h1>
        </div>
        <nav class="nav-menu">
          <a href="#" class="nav-item active">
            <img src="../assets/img/Home.svg" alt="" />
            <span style="margin-left: 12px">Dashboard</span>
          </a>
          <a href="#" class="nav-item">
            <img src="../assets/img/inventory.svg" alt="" />
            <span style="margin-left: 12px">Inventory</span>
          </a>
          <a href="#" class="nav-item">
            <img src="../assets/img/Report.svg" alt="" />
            <span style="margin-left: 12px">Reports</span>
          </a>
          <a href="../pages/suppliers.php" class="nav-item">
            <img src="../assets/img/Suppliers.svg" alt="" />
            <span style="margin-left: 12px">Suppliers</span>
          </a>
          <a href="#" class="nav-item">
            <img src="../assets/img/Order.svg" alt="" />
            <span style="margin-left: 12px">Orders</span>
          </a>
          <a href="#" class="nav-item">
            <img src="../assets/img/Manage Store.svg" alt="" />
            <span style="margin-left: 12px">Manage Store</span>
          </a>
        </nav>
        <div class="sidebar-footer">
          <a href="#" class="nav-item">
            <i data-lucide="settings"></i>
            <span style="margin-left: 12px">Settings</span>
          </a>
          <a href="#" class="nav-item">
            <i data-lucide="log-out"></i>
            <span style="margin-left: 12px">Log Out</span>
          </a>
        </div>
      </aside>

      <!-- Main Content -->
      <main class="main-content">
        <!-- Header -->
        <header class="header">
          <div class="search-box">
            <i data-lucide="search"></i>
            <input type="text" placeholder="Search product, supplier, order" />
          </div>
          <div class="header-right">
            <button class="notification-btn">
              <i data-lucide="bell"></i>
            </button>
            <div class="avatar"></div>
          </div>
        </header>

</body>
</html>