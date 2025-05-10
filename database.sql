-- Create Database
CREATE DATABASE IF NOT EXISTS hostel_management;
USE hostel_management;

-- Create Rooms Table
CREATE TABLE IF NOT EXISTS rooms (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(10) NOT NULL UNIQUE,
    capacity INT NOT NULL,
    occupied INT NOT NULL DEFAULT 0,
    image_url VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Students Table
CREATE TABLE IF NOT EXISTS students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    room_id INT,
    FOREIGN KEY (room_id) REFERENCES rooms(room_id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Complaints Table
CREATE TABLE IF NOT EXISTS complaints (
    complaint_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('Open', 'In Progress', 'Resolved') NOT NULL DEFAULT 'Open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id)
);

-- Insert Sample Rooms Data
INSERT INTO rooms (room_number, capacity, occupied, image_url, description) VALUES 
('A101', 4, 2, 'https://cdn.pixabay.com/photo/2016/11/18/17/20/couch-1835923_960_720.jpg', 'Spacious 4-bed room with attached bathroom and study area'),
('A102', 2, 0, 'https://cdn.pixabay.com/photo/2018/07/14/17/46/bedroom-3538932_960_720.jpg', 'Cozy 2-bed room with air conditioning'),
('B201', 3, 1, 'https://cdn.pixabay.com/photo/2017/07/09/03/19/home-2486092_960_720.jpg', '3-bed room with balcony and great view'),
('B202', 2, 2, 'https://cdn.pixabay.com/photo/2016/11/18/17/20/living-room-1835923_960_720.jpg', 'Premium 2-bed room with private bathroom'),
('C301', 4, 1, 'https://cdn.pixabay.com/photo/2016/11/18/17/20/bed-1835923_960_720.jpg', 'Economy 4-bed room with shared facilities');
