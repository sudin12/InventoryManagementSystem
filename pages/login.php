<?php
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            header("Location: dashboard.html");
            exit();


        } else {
            echo "Invalid password.";
        }
    } else {
        echo "Login failed";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Track - Login</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <div class="container">
        <h1><span class="inventory">Inventory</span> <span class="track">Track</span></h1>
        <h2>Log in</h2>
        <p>Welcome back! Please enter your details.</p>
        <form method="POST">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <button type="submit">Login</button>
        </form>
        <p class="signup-text">Don't have an account? <a href="signup.php">Sign up</a></p>
    </div>
</body>
</html>
