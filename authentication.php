<?php
require_once 'config.php';

// Register a new student
function registerStudent($name, $email, $password) {
    global $conn;
    
    // Sanitize inputs
    $name = sanitize($name);
    $email = sanitize($email);
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM students WHERE email = ?");
    $stmt->execute([$email]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        return ['success' => false, 'message' => 'Email already exists'];
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new student
    $stmt = $conn->prepare("INSERT INTO students (name, email, password) VALUES (?, ?, ?)");
    
    try {
        $stmt->execute([$name, $email, $hashed_password]);
        return ['success' => true, 'message' => 'Registration successful'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
    }
}

// Login a student
function loginStudent($email, $password) {
    global $conn;
    
    // Sanitize input
    $email = sanitize($email);
    
    // Get student by email
    $stmt = $conn->prepare("SELECT student_id, name, email, password FROM students WHERE email = ?");
    $stmt->execute([$email]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($student) {
        // Verify password
        if (password_verify($password, $student['password'])) {
            // Set session variables
            $_SESSION['student_id'] = $student['student_id'];
            $_SESSION['student_name'] = $student['name'];
            $_SESSION['student_email'] = $student['email'];
            $_SESSION['user_type'] = 'student';
            
            return ['success' => true, 'message' => 'Login successful'];
        }
    }
    
    return ['success' => false, 'message' => 'Invalid email or password'];
}

// Login an admin
function loginAdmin($username, $password) {
    global $conn;
    
    // Sanitize input
    $username = sanitize($username);
    
    // Get admin by username
    $stmt = $conn->prepare("SELECT admin_id, name, username, email, password FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        // Verify password
        if (password_verify($password, $admin['password'])) {
            // Set session variables
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['user_type'] = 'admin';
            
            return ['success' => true, 'message' => 'Admin login successful'];
        }
    }
    
    return ['success' => false, 'message' => 'Invalid username or password'];
}

// Register an admin (for system setup)
function registerAdmin($name, $username, $email, $password) {
    global $conn;
    
    // Check if username already exists
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        return ['success' => false, 'message' => 'Username already exists'];
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new admin
    $stmt = $conn->prepare("INSERT INTO admins (username, name, email, password) VALUES (?, ?, ?, ?)");
    
    try {
        $stmt->execute([$username, $name, $email, $hashed_password]);
        return ['success' => true, 'message' => 'Admin registration successful'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Admin registration failed: ' . $e->getMessage()];
    }
}

// Get student information
function getStudentInfo($student_id) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT s.*, r.room_number, r.capacity, r.occupied 
        FROM students s
        LEFT JOIN rooms r ON s.room_id = r.room_id
        WHERE s.student_id = ?
    ");
    $stmt->execute([$student_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        return $result;
    }
    
    return null;
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']) && $_SESSION['user_type'] === 'admin';
}
?>
