<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirectWithMessage('login.php', 'Please login to book a room', 'error');
}

// Handle room booking
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_room'])) {
    $room_id = (int)$_POST['room_id'];
    $student_id = $_SESSION['student_id'];
    
    // Check if the student already has a room
    $check_student = $conn->prepare("SELECT room_id FROM students WHERE student_id = ?");
    $check_student->execute([$student_id]);
    $student_data = $check_student->fetch(PDO::FETCH_ASSOC);
    
    if ($student_data && $student_data['room_id']) {
        redirectWithMessage('profile.php', 'You already have a room assigned', 'error');
    }
    
    // Check if the room has available space
    $check_room = $conn->prepare("SELECT capacity, occupied FROM rooms WHERE room_id = ?");
    $check_room->execute([$room_id]);
    $room_data = $check_room->fetch(PDO::FETCH_ASSOC);
    
    if (!$room_data) {
        redirectWithMessage('rooms.php', 'Room not found', 'error');
    }
    
    if ($room_data['occupied'] >= $room_data['capacity']) {
        redirectWithMessage('rooms.php', 'This room is already full', 'error');
    }
    
    // Start a transaction
    $conn->beginTransaction();
    
    try {
        // Update student with room_id
        $update_student = $conn->prepare("UPDATE students SET room_id = ? WHERE student_id = ?");
        $update_student->execute([$room_id, $student_id]);
        
        // Increment room occupied count
        $update_room = $conn->prepare("UPDATE rooms SET occupied = occupied + 1 WHERE room_id = ?");
        $update_room->execute([$room_id]);
        
        // Commit transaction
        $conn->commit();
        
        redirectWithMessage('profile.php', 'Room booked successfully', 'success');
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollBack();
        redirectWithMessage('rooms.php', 'An error occurred: ' . $e->getMessage(), 'error');
    }
} else {
    // Redirect if accessed directly
    header("Location: rooms.php");
    exit();
}
?>
