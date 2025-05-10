<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirectWithMessage('login.php', 'Please login to submit a complaint', 'error');
}

$student_id = $_SESSION['student_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaints - Hostel Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'partials/navbar.php'; ?>
    
    <section class="complaints-section">
        <div class="container">
            <h1>Submit a Complaint</h1>
            
            <div class="complaint-form-container">
                <form action="handle_complaint.php" method="POST" class="complaint-form">
                    <div class="form-group">
                        <label for="complaint-title">Complaint Title</label>
                        <input type="text" id="complaint-title" name="title" required placeholder="Brief description of the issue">
                    </div>
                    
                    <div class="form-group">
                        <label for="complaint-desc">Detailed Description</label>
                        <textarea id="complaint-desc" name="description" rows="6" required placeholder="Please provide details about your complaint..."></textarea>
                    </div>
                    
                    <button type="submit" name="submit_complaint" class="btn btn-submit">Submit Complaint</button>
                </form>
            </div>
            
            <div class="complaints-guidelines">
                <h2>Guidelines for Submitting Complaints</h2>
                <ul>
                    <li>Be specific about the issue you're experiencing</li>
                    <li>Include relevant details such as dates, times, and locations</li>
                    <li>Describe any steps you've already taken to resolve the issue</li>
                    <li>Maintain a respectful tone and language</li>
                    <li>All complaints are reviewed by hostel management within 48 hours</li>
                </ul>
            </div>
        </div>
    </section>
    
    <?php include 'partials/footer.php'; ?>
    
    <script src="js/script.js"></script>
</body>
</html>
