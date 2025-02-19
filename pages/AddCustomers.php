<?php
include_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $contact_info = $_POST['contact_info'];

    if (!empty($name) && !empty($contact_info)) {
        $sql = "INSERT INTO customers (name, contact_info) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ss", $name, $contact_info);
            if ($stmt->execute()) {
                echo "New customer added successfully.";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Customer</title>
    <link rel="stylesheet" href="../assets/css/addcustomers.css">
</head>
<body>
    <div class="container">
        <form class="customer-form" method="POST" action="">
            <h1>New Customer</h1>
            
            <div class="form-group">
                <label for="name">Customer Name</label>
                <input type="text" id="name" name="name" placeholder="Enter customer name">
            </div>

            <div class="form-group">
                <label for="contact_info">Contact Number</label>
                <input type="tel" id="contact_info" name="contact_info" placeholder="Enter customer contact number">
            </div>

            <div class="form-actions">
                <button type="button" class="btn-secondary">Discard</button>
                <button type="submit" class="btn-primary">Add Customer</button>
            </div>
        </form>
    </div>
</body>
</html>
