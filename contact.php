<?php
require_once 'config.php';

$message = '';
$message_type = '';

// Handle contact form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_contact'])) {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $message_content = sanitize($_POST['message']);
    
    // Validate input
    if (empty($name) || empty($email) || empty($subject) || empty($message_content)) {
        $message = "Please fill in all fields";
        $message_type = "error";
    } else {
        // In a real application, you would send an email here
        // For this demo, we'll just display a success message
        $message = "Thank you for your message! We will get back to you soon.";
        $message_type = "success";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Hostel Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
.contact-container{

    display: grid;
     grid-template-columns: 4fr 1fr; 
    gap: 30px;}
</style>
</head>
<body>
    <?php include 'partials/navbar.php'; ?>
    
    <section class="contact-section">
        <div class="container">
            <h1>Contact Us</h1>
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <div class="contact-container">
                <div class="contact-info">
                    <h2>Get in Touch</h2>
                    <p>Have questions or need assistance? Feel free to reach out to us using any of the following methods:</p>
                    
                    <div class="contact-methods">
                        <div class="contact-method">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <h3>Address</h3>
                                <p>123 University Street, City, State, ZIP</p>
                            </div>
                        </div>
                        
                        <div class="contact-method">
                            <i class="fas fa-phone"></i>
                            <div>
                                <h3>Phone</h3>
                                <p>+123 456 7890</p>
                                <p>+123 456 7891</p>
                            </div>
                        </div>
                        
                        <div class="contact-method">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <h3>Email</h3>
                                <p>info@hostelmanagement.com</p>
                                <p>support@hostelmanagement.com</p>
                            </div>
                        </div>
                        
                        <div class="contact-method">
                            <i class="fas fa-clock"></i>
                            <div>
                                <h3>Office Hours</h3>
                                <p>Monday - Friday: 9:00 AM - 5:00 PM</p>
                                <p>Saturday: 10:00 AM - 2:00 PM</p>
                            </div>
                        </div>
                   
                
                
           
        </div>
    </section>
    
    <?php include 'partials/footer.php'; ?>
    
    <script src="js/script.js"></script>
</body>
</html>