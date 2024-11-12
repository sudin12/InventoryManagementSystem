<?php
include('../config.php');
?>

<div class="container-fluid">
        <div class="card shadow mb-4">
                <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Add New Product</h6>
                </div>
                <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                        <label>Product Name</label>
                                        <input type="text" class="form-control" name="product_name" required>
                                </div>
                                <div class="form-group">
                                        <label>Description</label>
                                        <textarea class="form-control" name="description" rows="3"></textarea>
                                </div>
                                <div class="form-row">
                                        <div class="form-group col-md-6">
                                                <label>Quantity</label>
                                                <input type="number" class="form-control" name="quantity" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                                <label>Price</label>
                                                <input type="number" step="0.01" class="form-control" name="price" required>
                                        </div>
                                </div>
                                <div class="form-group">
                                        <label>Category</label>
                                        <select class="form-control" name="category" required>
                                                <option value="">Select Category</option>
                                                <?php
                                                $query = "SELECT * FROM categories";
                                                $result = mysqli_query($conn, $query);
                                                while($row = mysqli_fetch_assoc($result)) {
                                                        echo "<option value='".$row['id']."'>".$row['category_name']."</option>";
                                                }
                                                ?>
                                        </select>
                                </div>
                                <div class="form-group">
                                        <label>Product Image</label>
                                        <input type="file" class="form-control-file" name="product_image">
                                </div>
                                <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
                                
                        </form>
                </div>
        </div>
</div>
