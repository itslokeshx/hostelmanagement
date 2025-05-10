<?php
require_once 'config.php';

// Get available rooms from database
$query = "SELECT * FROM rooms WHERE occupied < capacity ORDER BY room_number";
$result = $conn->query($query);
$available_rooms = [];

if ($result) {
    $available_rooms = $result->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostel Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'partials/navbar.php'; ?>
    
    <header class="hero">
        <div class="hero-content">
            <h1>Welcome to Student Hostel Management</h1>
            <p>Find your perfect room and enjoy comfortable student living with modern amenities and a supportive community environment</p>
            <div class="hero-buttons">
                <a href="#available-rooms" class="btn"><i class="fas fa-search"></i> View Available Rooms</a>
            </div>
        </div>
    </header>
    
    <section id="available-rooms" class="rooms-section">
        <div class="container">
            <h2>Available Rooms</h2>
            <p class="section-subtitle">Browse our selection of comfortable and affordable rooms with modern amenities</p>
            
            <?php if (!empty($available_rooms)): ?>
                <div class="rooms-grid">
                    <?php foreach($available_rooms as $room): ?>
                        <div class="room-card <?php echo ($room['room_number'] == 'A102') ? 'new' : ''; ?>">
                            <?php 
                                $available_spots = intval($room['capacity']) - intval($room['occupied']);
                                if ($available_spots >= intval($room['capacity']) * 0.6) {
                                    $status_class = 'status-available';
                                    $status_text = 'Available';
                                } elseif ($available_spots > 0) {
                                    $status_class = 'status-limited';
                                    $status_text = 'Limited';
                                } else {
                                    $status_class = 'status-full';
                                    $status_text = 'Full';
                                }
                            ?>
                            <div class="room-image">
                                <img src="<?php echo isset($room['image_url']) ? htmlspecialchars($room['image_url']) : 'images/room_' . strtolower($room['room_number']) . '.svg'; ?>" alt="Room <?php echo htmlspecialchars($room['room_number']); ?>">
                            </div>
                            <div class="room-details">
                                <h3>Room <?php echo htmlspecialchars($room['room_number']); ?></h3>
                                <p><?php echo isset($room['description']) ? htmlspecialchars($room['description']) : 'Comfortable student room'; ?></p>
                                <div class="room-info">
                                    <span><i class="fas fa-bed"></i> Capacity: <?php echo intval($room['capacity']); ?></span>
                                    <span><i class="fas fa-user"></i> Available: <?php echo $available_spots; ?></span>
                                    <span><i class="fas fa-rupee-sign"></i> ₹<?php echo isset($room['price']) ? number_format($room['price'], 0) : '0'; ?>/month</span>
                                </div>
                                <div class="room-actions">
                                    <a href="rooms.php?id=<?php echo intval($room['room_id']); ?>" class="btn btn-sm"><i class="fas fa-info-circle"></i> View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-rooms">
                    <p>No available rooms at the moment. Please check back later.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
    
    <section class="features-section">
        <div class="container">
            <h2>Our Facilities</h2>
            <p class="section-subtitle">Enjoy premium amenities designed to enhance your student living experience</p>
            <div class="features-grid">
                <div class="feature">
                    <i class="fas fa-wifi"></i>
                    <h3>Free Wi-Fi</h3>
                    <p>High-speed internet access throughout the hostel</p>
                </div>
                <div class="feature">
                    <i class="fas fa-utensils"></i>
                    <h3>Dining Facilities</h3>
                    <p>Modern kitchen and dining area for students</p>
                </div>
                <div class="feature">
                    <i class="fas fa-book"></i>
                    <h3>Study Rooms</h3>
                    <p>Quiet areas dedicated for focused studying</p>
                </div>
                <div class="feature">
                    <i class="fas fa-dumbbell"></i>
                    <h3>Fitness Center</h3>
                    <p>Well-equipped gym for staying active</p>
                </div>
            </div>
        </div>
    </section>
    
    <?php include 'partials/footer.php'; ?>
    
    <div class="scroll-to-top" id="scrollToTop">
        <i class="fas fa-arrow-up"></i>
    </div>
    
    <script src="js/script.js"></script>
    <script>
        // Scroll to top functionality
        const scrollToTopBtn = document.getElementById("scrollToTop");
        
        window.addEventListener("scroll", () => {
            if (window.pageYOffset > 300) {
                scrollToTopBtn.classList.add("visible");
            } else {
                scrollToTopBtn.classList.remove("visible");
            }
        });
        
        scrollToTopBtn.addEventListener("click", () => {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });
    </script>
</body>
</html>
