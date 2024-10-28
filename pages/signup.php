<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Track - Sign Up</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1><span class="inventory">Inventory</span> <span class="track">Track</span></h1>
        <h2>Sign up</h2>
        <p>Please enter your details.</p>
        <form>
            <div class="row">
                <div class="field">
                    <label for="first-name">First Name</label>
                    <input type="text" id="first-name" placeholder="John" required>
                </div>
                <div class="field">
                    <label for="last-name">Last Name</label>
                    <input type="text" id="last-name" placeholder="ex: Doe" required>
                </div>
            </div>

            <label for="address">Address</label>
            <input type="text" id="address" placeholder="Enter your specific address" required>

            <div class="row">
                <div class="field">
                    <label for="state">State</label>
                    <input type="text" id="state" placeholder="ex: Bagmati" required>
                </div>
                <div class="field">
                    <label for="postal-code">Postal Code</label>
                    <input type="text" id="postal-code" placeholder="ex: 44600" required>
                </div>
            </div>

            <div class="row">
                <div class="field">
                    <label for="phone">Phone Number</label>
                    <input type="date" id="phone" placeholder="yyyy-mm-dd" required>
                </div>
                <div class="field">
                    <label for="company-size">Company Size</label>
                    <input type="text" id="company-size" placeholder="ex: 1234" required>
                </div>
            </div>

            <label for="company-name">Company Name</label>
            <input type="text" id="company-name" placeholder="Enter your Company Name" required>

            <label for="email">Email</label>
            <input type="email" id="email" placeholder="Enter your email" required>

            <label for="password">Password</label>
            <input type="password" id="password" placeholder="Enter your password" required>

            <button type="submit">Sign up</button>
        </form>
        <p class="login-text">Don't have an account? <a href="#">Login</a></p>
    </div>
</body>
</html>
