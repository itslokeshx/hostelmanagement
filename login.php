<?php
require_once 'config.php';
require_once 'authentication.php';

// Check if already logged in
if (isLoggedIn()) {
    header("Location: profile.php");
    exit();
}

$message = '';
$message_type = '';

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Validate input
    if (empty($email) || empty($password)) {
        $message = "Please fill in all fields";
        $message_type = "error";
    } else {
        $result = loginStudent($email, $password);
        
        if ($result['success']) {
            header("Location: profile.php");
            exit();
        } else {
            $message = $result['message'];
            $message_type = "error";
        }
    }
}

// Handle registration form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate input
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = "Please fill in all fields";
        $message_type = "error";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match";
        $message_type = "error";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters long";
        $message_type = "error";
    } else {
        $result = registerStudent($name, $email, $password);
        
        if ($result['success']) {
            $message = $result['message'] . ". Please login.";
            $message_type = "success";
        } else {
            $message = $result['message'];
            $message_type = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hostel Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'partials/navbar.php'; ?>
    
    <section class="auth-section">
        <div class="container">
            <div class="auth-container">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $message_type; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <div class="tabs">
                    <button class="tab-btn active" data-target="login">Login</button>
                    <button class="tab-btn" data-target="register">Register</button>
                </div>
                
                <div class="tab-content">
                    <div class="tab-pane active" id="login">
                        <h2>Login to Your Account</h2>
                        <form action="login.php" method="POST" class="auth-form">
                            <div class="form-group">
                                <label for="login-email">Email</label>
                                <input type="email" id="login-email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="login-password">Password</label>
                                <input type="password" id="login-password" name="password" required>
                            </div>
                            <button type="submit" name="login" class="btn btn-block">Login</button>
                        </form>
                    </div>
                    
                    <div class="tab-pane" id="register">
                        <h2>Create an Account</h2>
                        <form action="login.php" method="POST" class="auth-form">
                            <div class="form-group">
                                <label for="register-name">Full Name</label>
                                <input type="text" id="register-name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="register-email">Email</label>
                                <input type="email" id="register-email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="register-password">Password</label>
                                <input type="password" id="register-password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm-password">Confirm Password</label>
                                <input type="password" id="confirm-password" name="confirm_password" required>
                            </div>
                            <button type="submit" name="register" class="btn btn-block">Register</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <?php include 'partials/footer.php'; ?>
    
    <script src="js/script.js"></script>
</body>
</html>
