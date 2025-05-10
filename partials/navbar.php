<?php
// Make sure authentication.php is included to access isAdmin()
require_once __DIR__ . '/../authentication.php';
?>
<nav class="navbar">
    <div class="container">
        <div class="logo">
            <a href="index.php">Hostel Management</a>
        </div>
        <div class="menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
        <ul class="nav-menu">
            <li><a href="index.php">Home</a></li>
            <li><a href="rooms.php">Rooms</a></li>
            <li><a href="complaints.php">Complaints</a></li>
            <li><a href="contact.php">Contact</a></li>
            <?php if (function_exists('isAdmin') && isAdmin()): ?>
                <li><a href="admin_dashboard.php">Admin Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php elseif (isLoggedIn()): ?>
                <li><a href="profile.php">My Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php" class="btn btn-login">Login</a></li>
                <li><a href="admin_login.php" class="btn btn-admin">Admin</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<?php if (isset($_SESSION['message'])): ?>
<div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
    <?php 
    echo $_SESSION['message']; 
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
    ?>
</div>
<?php endif; ?>
