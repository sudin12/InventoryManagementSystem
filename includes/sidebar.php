<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sidebar</title>
        <link rel="stylesheet" href="../assets/css/sidebar.css">
</head>
<body>
        
<div class="sidenav">
        <div class="inventory-track">
                Inventory Track
        </div>

        <div class="list-menu">
<div class="list-menu1">
<div class="list-menu2">
<div class="content">
<img class="home-icon" alt="" src="../assets/img/Home.svg">
<div class="text">Dashobard</div>
</div>
</div>
<div class="list-menu3" id="listMenuContainer1">
<div class="content">
<img class="home-icon" alt="" src="../assets/img/Inventory.svg">
<div class="text">Inventory</div>
</div>
</div>
<div class="list-menu4">
<div class="content">
<img class="home-icon" alt="" src="../assets/img/Report.svg">
<div class="text">Reports</div>
</div>
</div>
<div class="list-menu4">
<div class="content3" id="contentContainer">
<img class="home-icon" alt="" src="../assets/img/Suppliers.svg">
<div class="text">Suppliers</div>
</div>
</div>
<div class="list-menu4">
<div class="content3" id="contentContainer">
<img class="home-icon" alt="" src="../assets/img/Order.svg">
<div class="text">Orders</div>
</div>
</div>
<div class="list-menu4">
<div class="content3" id="contentContainer">
<img class="home-icon" alt="" src="../assets/img/Manage Store.svg">
<div class="text">Manage Store</div>
</div>
</div>
</div>
</div>

<script>
var listMenuContainer1 = document.getElementById("listMenuContainer1");
if(listMenuContainer1) {
listMenuContainer1.addEventListener("click", function (e) {

});
}
var contentContainer = document.getElementById("contentContainer");
if(contentContainer) {
contentContainer.addEventListener("click", function (e) {

});
}</script>

</div>
<div class="search-bar-parent">
<div class="search-bar">
<img class="search-icon" alt="" src="../assets/img/Search.svg">
<div class="search-product-supplier">Search product, supplier, order</div>
</div>
<div class="notification-parent">
<div class="notification">
<img class="notification-icon" alt="" src="Notification.svg">
</div>
<img class="image-2-icon" alt="" src="../assets/img/image 2.png">
</div>
</div>
</body>
</html>