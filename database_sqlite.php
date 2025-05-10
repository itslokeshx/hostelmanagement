<?php
require_once 'config.php';

// Create Rooms Table
$conn->exec("CREATE TABLE IF NOT EXISTS rooms (
    room_id INTEGER PRIMARY KEY AUTOINCREMENT,
    room_number TEXT NOT NULL UNIQUE,
    capacity INTEGER NOT NULL,
    occupied INTEGER NOT NULL DEFAULT 0,
    image_url TEXT NOT NULL,
    description TEXT,
    price REAL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Create Students Table
$conn->exec("CREATE TABLE IF NOT EXISTS students (
    student_id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    room_id INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(room_id)
)");

// Create Complaints Table
$conn->exec("CREATE TABLE IF NOT EXISTS complaints (
    complaint_id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id INTEGER,
    title TEXT NOT NULL,
    description TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT 'Open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id)
)");

// Create Admin Table
$conn->exec("CREATE TABLE IF NOT EXISTS admins (
    admin_id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    name TEXT NOT NULL,
    email TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Insert Sample Rooms Data
$roomsData = [
    ['A101', 4, 2, 'images/room_a102.svg', 'Spacious 4-bed room with attached bathroom and study area', 15000],
    ['A102', 2, 0, 'images/room_a102.svg', 'Premium Single with Study Area', 12500],
    ['B101', 3, 1, 'images/room_b101.svg', '3-bed room with balcony and great view', 14000],
    ['B102', 2, 1, 'images/room_b102.svg', 'Deluxe Double with Study', 18000],
    ['C103', 4, 1, 'images/room_c103.svg', 'Economy 4-bed room with shared facilities', 10000],
    ['D102', 2, 0, 'images/room_d102.svg', 'Executive Suite with AC', 22500]
];

// Check if price column exists in rooms table
try {
    $checkPriceColumn = $conn->query("SELECT price FROM rooms LIMIT 1");
} catch(PDOException $e) {
    // If price column doesn't exist, add it
    $conn->exec("ALTER TABLE rooms ADD COLUMN price REAL DEFAULT 0");
    echo "Added price column to rooms table. ";
}

$checkRooms = $conn->query("SELECT COUNT(*) as count FROM rooms");
$roomCount = $checkRooms->fetch(PDO::FETCH_ASSOC)['count'];

if ($roomCount == 0) {
    $insertRoom = $conn->prepare("INSERT INTO rooms (room_number, capacity, occupied, image_url, description, price) VALUES (?, ?, ?, ?, ?, ?)");
    
    foreach ($roomsData as $room) {
        $insertRoom->execute($room);
    }
    
    echo "Sample data inserted successfully.";
} else {
    // Let's update existing rooms with prices and new image URLs
    $updateRoom = $conn->prepare("UPDATE rooms SET price = ?, image_url = ? WHERE room_number = ?");
    
    foreach ($roomsData as $room) {
        $updateRoom->execute([$room[5], $room[3], $room[0]]);
    }
    
    echo "Existing room data updated with prices.";
}

echo "Database tables created successfully.";
?>