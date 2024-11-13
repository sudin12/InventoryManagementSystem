<?php
require_once '../config.php';

function addProduct($conn, $data) {
  $sql = "INSERT INTO products (name, category_id, price, quantity_in_hand) 
          VALUES (?, ?, ?, ?)";

  $stmt =  $conn->prepare($sql);
  $stmt->bind_param("sidi", $data['name'], $data['category_id'], $data['price'], $data['quantity_in_hand']);

  return $stmt->execute();
}

// Handle form submission
if (isset($_POST['add_product'])) {
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
  $price = mysqli_real_escape_string($conn, $_POST['price']);
  $quantity = mysqli_real_escape_string($conn, $_POST['quantity_in_hand']);

  // Handle image upload
 /* $new_image_name = null;
  if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_name = $_FILES['image']['name'];
    $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array(strtolower($image_ext), $allowed_ext)) {
      $new_image_name = uniqid('IMG-', true) . '.' . $image_ext;
      $image_upload_path = '../uploads/' . $new_image_name;
      move_uploaded_file($image_tmp_name, $image_upload_path);
    }
  }*/

  // Prepare data array
  $data = [
    'name' => $name,
    'category_id' => $category_id,
    'price' => $price,
    'quantity' => $quantity
  ];

  if (addProduct($conn, $data)) {
    // Successfully added
    header("Location: inventory.php");
    exit();
  } else {
    // Error occurred
    echo "Error: " . $conn->error;
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>

<div id="add-product-form" class="form-popup">
  <form action="AddProduct.php" method="post" class="form-container" enctype="multipart/form-data">
    <h2>Add Product</h2>

    <label for="name"><b>Name</b></label>
    <input type="text" placeholder="Enter Product Name" name="name" required>

    <label for="category"><b>Category</b></label>
    <select name="category_id" required>
      <option value="">Select Category</option>
      <?php
    
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
    <input type="number" placeholder="Enter Quantity" name="quantity_in_hand" required>



    <button type="submit" name="add_product" class="btn">Add Product</button>
    <button type="button" class="btn cancel" onclick="closeForm()">Close</button>
  </form>
</div>
</body>
</html>