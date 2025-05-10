<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirectWithMessage('login.php', 'Please login to submit a complaint', 'error');
}

// Handle complaint submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_complaint'])) {
    $student_id = $_SESSION['student_id'];
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    
    // Validate input
    if (empty($title) || empty($description)) {
        redirectWithMessage('complaints.php', 'Please fill in all fields', 'error');
    }
    
    // Insert complaint into database
    $stmt = $conn->prepare("INSERT INTO complaints (student_id, title, description) VALUES (?, ?, ?)");
    
    try {
        $stmt->execute([$student_id, $title, $description]);
        redirectWithMessage('profile.php', 'Complaint submitted successfully', 'success');
    } catch (PDOException $e) {
        redirectWithMessage('complaints.php', 'Error submitting complaint: ' . $e->getMessage(), 'error');
    }
} else {
    // Redirect if accessed directly
    header("Location: complaints.php");
    exit();
}
?>
