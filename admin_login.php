<?php
require_once 'config.php';
require_once 'authentication.php';

// Check if already logged in as admin
if (isAdmin()) {
    header("Location: admin_dashboard.php");
    exit();
}

$message = '';
$message_type = '';

// Handle admin login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['admin_login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Validate input
    if (empty($username) || empty($password)) {
        $message = "Please fill in all fields";
        $message_type = "error";
    } else {
        $result = loginAdmin($username, $password);
        
        if ($result['success']) {
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $message = $result['message'];
            $message_type = "error";
        }
    }
}

// Create default admin if none exists
$stmt = $conn->query("SELECT COUNT(*) as count FROM admins");
$admin_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

if ($admin_count == 0) {
    // Create a default admin with username 'admin' and password 'admin123'
    registerAdmin('Administrator', 'admin', 'admin@hostel.com', 'admin123');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Hostel Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-login-container {
            max-width: 500px;
            margin: 50px auto;
            background-color: #2c2c2c;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }
        
        .admin-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .admin-header h1 {
            color: #8b5cf6;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .admin-icon {
            font-size: 48px;
            color: #8b5cf6;
            margin-bottom: 20px;
        }
        
        .admin-form .form-group {
            margin-bottom: 20px;
        }
        
        .admin-note {
            text-align: center;
            font-size: 14px;
            color: #aaa;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include 'partials/navbar.php'; ?>
    
    <section class="auth-section">
        <div class="container">
            <div class="admin-login-container">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $message_type; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <div class="admin-header">
                    <div class="admin-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h1>Administrator Login</h1>
                    <p>Enter your credentials to access the admin dashboard</p>
                </div>
                
                <form action="admin_login.php" method="POST" class="admin-form">
                    <div class="form-group">
                        <label for="username"><i class="fas fa-user"></i> Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="admin-password"><i class="fas fa-lock"></i> Password</label>
                        <input type="password" id="admin-password" name="password" required>
                    </div>
                    <button type="submit" name="admin_login" class="btn btn-block">Log In</button>
                </form>
                
                <div class="admin-note">
                    
                    
                </div>
            </div>
        </div>
    </section>
    
    
    
    <script src="js/script.js"></script>
    <!-- Add this BEFORE the navbar.php include -->
<script>
// Directly inject the fix when the page loads
window.addEventListener('DOMContentLoaded', function() {
    // Ensure the menu toggle button exists and has the right structure
    setTimeout(function() {
        var menuToggle = document.querySelector('.menu-toggle');
        var navMenu = document.querySelector('.nav-menu');
        
        if (!menuToggle || !navMenu) {
            console.log('Creating/fixing menu elements');
            
            // First try to find elements by other classes or IDs
            var navMenu = document.querySelector('.main-nav ul') || document.querySelector('nav ul');
            var navbar = document.querySelector('.main-header') || document.querySelector('header');
            
            if (navMenu) {
                // Add the right class to the menu
                navMenu.classList.add('nav-menu');
                navMenu.id = 'nav-menu';
                
                // Create toggle button if it doesn't exist
                if (!menuToggle && navbar) {
                    menuToggle = document.createElement('button');
                    menuToggle.className = 'menu-toggle';
                    menuToggle.id = 'menu-toggle';
                    menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
                    
                    // Insert before the nav element
                    var nav = document.querySelector('nav') || navMenu.parentNode;
                    if (nav && nav.parentNode) {
                        nav.parentNode.insertBefore(menuToggle, nav);
                    }
                }
            }
        }
        
        // Now add the event listener
        if (menuToggle && navMenu) {
            menuToggle.addEventListener('click', function(e) {
                e.preventDefault();
                navMenu.classList.toggle('active');
                console.log('Menu toggled via injected handler');
            });
        }
    }, 100); // Short delay to ensure DOM is loaded
});
</script>


</body>
</html>