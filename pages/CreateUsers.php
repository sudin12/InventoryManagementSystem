<?php
session_start();
require '../config.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $address = $conn->real_escape_string($_POST['address']);
    $phone_number = $conn->real_escape_string($_POST['phone_number']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $user_type = $conn->real_escape_string($_POST['user_type']);

    $check_email = "SELECT * FROM Users WHERE email = '$email'";
    $result = $conn->query($check_email);
    if ($result->num_rows > 0) {
        $error = "An account with this email already exists.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO Users (name, address, phone_number, email, password, user_type)
                VALUES ('$name', '$address', '$phone_number', '$email', '$password_hash', '$user_type')";

        if ($conn->query($sql) === TRUE) {
            header("Location: login.php"); 
            exit();
        } else {
            $error = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Track - Create User</title>
    <link rel="stylesheet" href="../assets/css/signup.css">
</head>
<body>
    <div class="container">
        <h1><span class="inventory">Inventory</span> <span class="track">Track</span></h1>
        <h2>Create User</h2>
        <p>Please enter user details.</p>
        <form method="POST">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" placeholder="Enter full name" required>

            <label for="address">Address</label>
            <input type="text" id="address" name="address" placeholder="Enter address" required>

            <label for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone_number" placeholder="Enter phone number" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter password" required>

            <label for="user-type">User Type</label>
            <select id="user-type" name="user_type" required>
                <option value="admin">Admin</option>
                <option value="store_keeper">Store Keeper</option>
            </select>

            <button type="submit">Create User</button>
        </form>
        <p class="login-text">Already have an account? <a href="login.php">Login</a></p>
    </div>

<script>
document.querySelector('form').addEventListener('submit', function(event) {
    var name = document.getElementById('name').value.trim();
    var address = document.getElementById('address').value.trim();
    var phoneNumber = document.getElementById('phone').value.trim();
    var email = document.getElementById('email').value.trim();
    var password = document.getElementById('password').value.trim();

    if (!name || !address || !phoneNumber || !email || !password) {
        alert('Please fill in all fields.');
        event.preventDefault();
        return;
    }

    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        alert('Please enter a valid email address.');
        event.preventDefault();
        return;
    }

    if (password.length < 6) {
        alert('Password must be at least 6 characters long.');
        event.preventDefault();
        return;
    }

    if (phoneNumber.length < 10) {
        alert('Phone number must be at least 10 digits long.');
        event.preventDefault();
        return;
    }
});
</script>
</body>
</html>