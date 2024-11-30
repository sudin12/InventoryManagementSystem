<?php
session_start();

require '../config.php';  

if ($_SERVER["REQUEST_METHOD"] == "POST") {
 
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $address = $conn->real_escape_string($_POST['address']);
    $state = $conn->real_escape_string($_POST['state']);
    $postal_code = $conn->real_escape_string($_POST['postal_code']);
    $phone_number = $conn->real_escape_string($_POST['phone_number']);
    $company_size = $conn->real_escape_string($_POST['company_size']);
    $company_name = $conn->real_escape_string($_POST['company_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $check_email = "SELECT * FROM Users WHERE email = '$email'";
    $result = $conn->query($check_email);
    if ($result->num_rows > 0) {
        $error = "An account with this email already exists.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO Users (first_name, last_name, address, state, postal_code, phone_number,company_size, company_name, email, password_hash)
                VALUES ('$first_name', '$last_name', '$address', '$state', '$postal_code', '$phone_number', '$company_size', '$company_name', '$email', '$password_hash')";

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
    <title>Inventory Track - Sign Up</title>
    <link rel="stylesheet" href="../assets/css/signup.css">
</head>
<body>
    <div class="container">
        <h1><span class="inventory">Inventory</span> <span class="track">Track</span></h1>
        <h2>Sign up</h2>
        <p>Please enter your details.</p>
        <form method="POST">
            <div class="row">
                <div class="field">
                    <label for="first-name">First Name</label>
                    <input type="text" id="first-name" name="first_name" placeholder="Ram" required>
                </div>
                <div class="field">
                    <label for="last-name">Last Name</label>
                    <input type="text" id="last-name" name="last_name" placeholder="ex: Shrestha" required>
                </div>
            </div>

            <label for="address">Address</label>
            <input type="text" id="address" name="address" placeholder="Enter your specific address" required>

            <div class="row">
                <div class="field">
                    <label for="state">State</label>
                    <input type="text" id="state" name="state" placeholder="ex: Bagmati" required>
                </div>
                <div class="field">
                    <label for="postal-code">Postal Code</label>
                    <input type="text" id="postal-code" name="postal_code" placeholder="ex: 44600" required>
                </div>
            </div>

            <div class="row">
                <div class="field">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone_number" required>
                </div>
                <div class="field">
                    <label for="company-size">Company Size</label>
                    <input type="text" id="company-size" name="company_size" placeholder="ex: 1234" required>
                </div>
            </div>

            <label for="company-name">Company Name</label>
            <input type="text" id="company-name" name="company_name" placeholder="Enter your Company Name" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <button type="submit">Sign up</button>
        </form>
        <p class="login-text">Don't have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>
<script>
document.querySelector('form').addEventListener('submit', function(event) {
    var firstName = document.getElementById('first-name').value.trim();
    var lastName = document.getElementById('last-name').value.trim();
    var address = document.getElementById('address').value.trim();
    var state = document.getElementById('state').value.trim();
    var postalCode = document.getElementById('postal-code').value.trim();
    var phoneNumber = document.getElementById('phone').value.trim();
    var companySize = document.getElementById('company-size').value.trim();
    var companyName = document.getElementById('company-name').value.trim();
    var email = document.getElementById('email').value.trim();
    var password = document.getElementById('password').value.trim();

    if (!firstName || !lastName || !address || !state || !postalCode || !phoneNumber || !companySize || !companyName || !email || !password) {
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

    if (isNaN(companySize) || companySize <= 0) {
        alert('Company size must be a positive number.');
        event.preventDefault();
        return;
    }
});
</script>