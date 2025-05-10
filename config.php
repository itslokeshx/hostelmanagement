<?php
// Database configuration for MySQL
$host = "sql112.infinityfree.com";
$dbname = "if0_38900559_hostelmanagement"; // Your MySQL database name
$username = "if0_38900559";            // Default XAMPP MySQL username
$password = "thelokesha7345";                // Default XAMPP password is blank

// Create MySQL database connection
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Function to sanitize user input
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to check if user is logged in
function isLoggedIn() {
    return (isset($_SESSION['student_id']) && !empty($_SESSION['student_id'])) || 
           (isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']));
}

// Function to redirect with a message
function redirectWithMessage($url, $message, $type = "success") {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
    header("Location: $url");
    exit;
}
?>


<?php
require_once 'config.php';

// Add 5 more rooms to the database
$additional_rooms = [
    ['D101', 3, 1, 'images/room_d101.svg', 'Triple room with balcony view and attached bathroom', 16500],
    ['E201', 2, 0, 'images/room_e201.svg', 'Double premium room with air conditioning and modern furniture', 19000],
    ['E202', 4, 2, 'images/room_e202.svg', 'Spacious quad room with separate study area and large windows', 15000],
    ['F101', 1, 0, 'images/room_f101.svg', 'Single deluxe room with private bathroom and workspace', 23000],
    ['F102', 3, 1, 'images/room_f102.svg', 'Triple room with high ceilings and extra storage space', 17500]
];

// Check if the rooms already exist
$stmt = $conn->prepare("SELECT COUNT(*) FROM rooms WHERE room_number = ?");

$count = 0;
foreach ($additional_rooms as $room) {
    $stmt->execute([$room[0]]);
    $exists = $stmt->fetchColumn();
    
    if ($exists == 0) {
        $insert = $conn->prepare("INSERT INTO rooms (room_number, capacity, occupied, image_url, description, price) VALUES (?, ?, ?, ?, ?, ?)");
        if ($insert->execute($room)) {
            $count++;
        }
    }
}


?>

