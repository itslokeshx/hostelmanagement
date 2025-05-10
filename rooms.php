<?php
require_once 'config.php';

// Get all rooms (available and unavailable)
$query = "SELECT * FROM rooms ORDER BY room_number";
$result = $conn->query($query);
$all_rooms = [];

if ($result) {
    $all_rooms = $result->fetchAll(PDO::FETCH_ASSOC);
}

// Get single room if ID is provided
$single_room = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $room_id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM rooms WHERE room_id = ?");
    $stmt->execute([$room_id]);
    $single_room = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Handle room booking request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_room']) && isLoggedIn()) {
    $room_id = (int)$_POST['room_id'];
    
    // Check if the user is a student, not an admin
    if (!isset($_SESSION['student_id']) || empty($_SESSION['student_id'])) {
        redirectWithMessage('rooms.php', 'Only students can book rooms.', 'error');
        exit();
    }
    
    $student_id = $_SESSION['student_id'];
    
    // Check if student already has a room
    $checkStmt = $conn->prepare("SELECT room_id FROM students WHERE student_id = ?");
    $checkStmt->execute([$student_id]);
    $studentData = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($studentData && $studentData['room_id']) {
        redirectWithMessage('profile.php', 'You already have a room booked.', 'error');
        exit();
    }

    // Check if the room is available
    $stmt = $conn->prepare("SELECT * FROM rooms WHERE room_id = ? AND occupied < capacity");
    $stmt->execute([$room_id]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($room) {
        // Start transaction to ensure data consistency
        $conn->beginTransaction();
        
        try {
            // Update student record with assigned room
            $updateStmt = $conn->prepare("UPDATE students SET room_id = ? WHERE student_id = ?");
            $updateStmt->execute([$room_id, $student_id]);
            
            // Update room occupancy
            $stmt = $conn->prepare("UPDATE rooms SET occupied = occupied + 1 WHERE room_id = ?");
            $stmt->execute([$room_id]);
            
            // Commit transaction
            $conn->commit();
            
            // Redirect or show success message
            redirectWithMessage('profile.php', 'Room booking successful!', 'success');
        } catch (Exception $e) {
            // Rollback in case of error
            $conn->rollBack();
            redirectWithMessage('rooms.php', 'An error occurred during booking: ' . $e->getMessage(), 'error');
        }
    } else {
        redirectWithMessage('rooms.php', 'Room is not available for booking.', 'error');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $single_room ? 'Room ' . htmlspecialchars($single_room['room_number']) : 'Rooms'; ?> - Hostel Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'partials/navbar.php'; ?>
    
    <section class="rooms-page-section">
        <div class="container">
            <?php if ($single_room): ?>
                <!-- Single Room View -->
                <div class="room-details-container">
                    <div class="room-details-header">
                        <h1>Room <?php echo htmlspecialchars($single_room['room_number']); ?></h1>
                        <div class="room-details-actions">
                            <?php
                                $available_spots = intval($single_room['capacity']) - intval($single_room['occupied']);
                                
                                if ($available_spots == 0) {
                                    $status_class = 'status-full';
                                    $status_text = 'Full';
                                } elseif ($available_spots < 3) {
                                    $status_class = 'status-limited';
                                    $status_text = 'Limited';
                                } else {
                                    $status_class = 'status-available';
                                    $status_text = 'Available';
                                }
                            ?>
                            <span class="room-status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                            <a href="rooms.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Back to Rooms</a>
                        </div>
                    </div>
                    
                    <div class="room-details-content">
                        <div class="room-image">
                            <img src="<?php echo htmlspecialchars($single_room['image_url']); ?>" alt="Room <?php echo htmlspecialchars($single_room['room_number']); ?>">
                        </div>
                        
                        <div class="room-info-card">
                            <div class="room-info-header">
                                <h2>Room Information</h2>
                            </div>
                            <div class="room-info-body">
                                <div class="info-item">
                                    <span class="label">Room Number:</span>
                                    <span class="value"><?php echo htmlspecialchars($single_room['room_number']); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Capacity:</span>
                                    <span class="value"><?php echo intval($single_room['capacity']); ?> students</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Available Space:</span>
                                    <span class="value"><?php echo intval($single_room['capacity']) - intval($single_room['occupied']); ?> beds</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Description:</span>
                                    <span class="value"><?php echo htmlspecialchars($single_room['description'] ?? 'Standard student accommodation'); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Price:</span>
                                    <span class="value">₹<?php echo isset($single_room['price']) ? number_format($single_room['price'], 0) : '0'; ?>/month</span>
                                </div>
                            </div>
                            
                            <?php if (isLoggedIn() && isset($_SESSION['student_id']) && $available_spots > 0): ?>
                                <?php
                                    // Check if student already has a room
                                    $student_id = $_SESSION['student_id'];
                                    $checkStmt = $conn->prepare("SELECT room_id FROM students WHERE student_id = ?");
                                    $checkStmt->execute([$student_id]);
                                    $studentData = $checkStmt->fetch(PDO::FETCH_ASSOC);
                                    $already_booked = ($studentData && $studentData['room_id']);
                                ?>
                                
                                <?php if (!$already_booked): ?>
                                    <div class="booking-section">
                                        <h3>Book This Room</h3>
                                        <p>Reserve your spot in this room now.</p>
                                        <form action="rooms.php" method="POST">
                                            <input type="hidden" name="room_id" value="<?php echo intval($single_room['room_id']); ?>">
                                            <button type="submit" name="book_room" class="btn btn-book"><i class="fas fa-check-circle"></i> Book Room</button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <div class="booking-section">
                                        <p class="booking-notice"><i class="fas fa-info-circle"></i> You already have a room booked. Check your profile for details.</p>
                                        <a href="profile.php" class="btn">View Your Profile</a>
                                    </div>
                                <?php endif; ?>
                            <?php elseif (!isLoggedIn()): ?>
                                <div class="booking-section">
                                    <p class="booking-notice"><i class="fas fa-info-circle"></i> Please login to book a room.</p>
                                    <a href="login.php" class="btn">Login Now</a>
                                </div>
                            <?php elseif (!isset($_SESSION['student_id'])): ?>
                                <div class="booking-section">
                                    <p class="booking-notice"><i class="fas fa-info-circle"></i> Only students can book rooms.</p>
                                </div>
                            <?php elseif ($available_spots <= 0): ?>
                                <div class="booking-section">
                                    <p class="booking-notice error"><i class="fas fa-exclamation-circle"></i> This room is currently full.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="room-amenities">
                        <h2>Room Amenities</h2>
                        <div class="amenities-grid">
                            <div class="amenity-item">
                                <i class="fas fa-wifi"></i>
                                <span>Free WiFi</span>
                            </div>
                            <div class="amenity-item">
                                <i class="fas fa-bed"></i>
                                <span>Comfortable Beds</span>
                            </div>
                            <div class="amenity-item">
                                <i class="fas fa-desk"></i>
                                <span>Study Table</span>
                            </div>
                            <div class="amenity-item">
                                <i class="fas fa-chair"></i>
                                <span>Chair</span>
                            </div>
                            <div class="amenity-item">
                                <i class="fas fa-bolt"></i>
                                <span>Power Outlets</span>
                            </div>
                            <div class="amenity-item">
                                <i class="fas fa-box"></i>
                                <span>Storage Cabinet</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- All Rooms View -->
                <h1>Available Rooms</h1>
                
                <!-- Room Filters -->
                <div class="room-filters">
                    <div class="filter-controls">
                        <div class="filter-group">
                            <label for="capacity-filter">Capacity:</label>
                            <select id="capacity-filter" class="filter-select">
                                <option value="all">All</option>
                                <option value="1">Single (1)</option>
                                <option value="2">Double (2)</option>
                                <option value="3">Triple (3)</option>
                                <option value="4">Quad (4+)</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="search-rooms">Search:</label>
                            <input type="text" id="search-rooms" class="search-input" placeholder="Search rooms...">
                        </div>
                        <div class="filter-group availability-filter">
                            <label>Availability:</label>
                            <div class="checkbox-group">
                                <input type="checkbox" id="available-filter" checked>
                                <label for="available-filter">Available</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($all_rooms)): ?>
                    <div class="rooms-grid">
                        <?php foreach($all_rooms as $room): ?>
                            <div class="room-card" 
                                data-capacity="<?php echo intval($room['capacity']); ?>"
                                data-availability="<?php echo (intval($room['capacity']) - intval($room['occupied']) > 0) ? 'available' : 'full'; ?>"
                                data-keywords="<?php echo htmlspecialchars($room['room_number'] . ' ' . ($room['description'] ?? '')); ?>">
                                <?php 
                                    $available = intval($room['capacity']) - intval($room['occupied']);
                                ?>
                                <div class="room-image">
                                    <img src="<?php echo htmlspecialchars($room['image_url']); ?>" alt="Room <?php echo htmlspecialchars($room['room_number']); ?>">
                                </div>
                                <div class="room-details">
                                    <h3>Room <?php echo htmlspecialchars($room['room_number']); ?></h3>
                                    <p><?php echo htmlspecialchars($room['description'] ?? 'Standard student accommodation'); ?></p>
                                    <div class="room-info">
                                        <span><i class="fas fa-bed"></i> Capacity: <?php echo intval($room['capacity']); ?></span>
                                        <span><i class="fas fa-user"></i> Available: <?php echo $available; ?></span>
                                        <span><i class="fas fa-rupee-sign"></i> ₹<?php echo isset($room['price']) ? number_format($room['price'], 0) : '0'; ?>/month</span>
                                    </div>
                                    <div class="room-actions">
                                        <a href="rooms.php?id=<?php echo intval($room['room_id']); ?>" class="btn">View Details</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-rooms">
                        <p>No rooms available at the moment. Please check back later.</p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>
    
    <?php include 'partials/footer.php'; ?>
    
    <!-- Fix for hamburger menu --><!-- Add this BEFORE the navbar.php include -->
<script>
// Directly inject the fix when the page loads
window.addEventListener('DOMContentLoaded', function() {
    // Ensure the menu toggle button exists and has the right structure
    setTimeout(function() {
        var menuToggle = document.querySelector('.menu-toggle');
        var navMenu = document.querySelector('.nav-menu');
        
        if (!menuToggle || !navMenu) {
            console.log('Creating/fixing menu elements');
            
            // First try to find elements by other classes or IDs
            var navMenu = document.querySelector('.main-nav ul') || document.querySelector('nav ul');
            var navbar = document.querySelector('.main-header') || document.querySelector('header');
            
            if (navMenu) {
                // Add the right class to the menu
                navMenu.classList.add('nav-menu');
                navMenu.id = 'nav-menu';
                
                // Create toggle button if it doesn't exist
                if (!menuToggle && navbar) {
                    menuToggle = document.createElement('button');
                    menuToggle.className = 'menu-toggle';
                    menuToggle.id = 'menu-toggle';
                    menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
                    
                    // Insert before the nav element
                    var nav = document.querySelector('nav') || navMenu.parentNode;
                    if (nav && nav.parentNode) {
                        nav.parentNode.insertBefore(menuToggle, nav);
                    }
                }
            }
        }
        
        // Now add the event listener
        if (menuToggle && navMenu) {
            menuToggle.addEventListener('click', function(e) {
                e.preventDefault();
                navMenu.classList.toggle('active');
                console.log('Menu toggled via injected handler');
            });
        }
    }, 100); // Short delay to ensure DOM is loaded
});
</script>

<?php include 'partials/navbar.php'; ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.getElementById('menu-toggle');
        const navMenu = document.getElementById('nav-menu');
        
        if (menuToggle && navMenu) {
            menuToggle.addEventListener('click', function() {
                navMenu.classList.toggle('active');
                menuToggle.classList.toggle('active');
                console.log('Menu toggled');
            });
        }};
    document.addEventListener('DOMContentLoaded', function() {
        // Room filtering functionality
        const capacityFilter = document.getElementById('capacity-filter');
        const searchInput = document.getElementById('search-rooms');
        const availabilityFilter = document.getElementById('available-filter');
        
        // Check if we're on the rooms page with filters
        if (capacityFilter || searchInput || availabilityFilter) {
            // Add event listeners to all filter controls
            if (capacityFilter) {
                capacityFilter.addEventListener('change', filterRooms);
            }
            
            if (searchInput) {
                searchInput.addEventListener('input', filterRooms);
                
                // Clear search on escape key
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        searchInput.value = '';
                        filterRooms();
                    }
                });
            }
            
            if (availabilityFilter) {
                availabilityFilter.addEventListener('change', filterRooms);
            }
            
            // Combined filtering function
            function filterRooms() {
                const roomCards = document.querySelectorAll('.room-card');
                const selectedCapacity = capacityFilter ? capacityFilter.value : 'all';
                const searchQuery = searchInput ? searchInput.value.trim().toLowerCase() : '';
                const showOnlyAvailable = availabilityFilter ? availabilityFilter.checked : false;
                
                roomCards.forEach(card => {
                    const capacity = parseInt(card.dataset.capacity);
                    const keywords = card.dataset.keywords.toLowerCase();
                    const availability = card.dataset.availability;
                    
                    // Start with the assumption that card should be visible
                    let shouldShow = true;
                    
                    // Apply capacity filter
                    if (selectedCapacity !== 'all') {
                        if (selectedCapacity === '4') {
                            // For 4+, show rooms with capacity greater than or equal to 4
                            shouldShow = shouldShow && (capacity >= 4);
                        } else {
                            // For specific capacity, require exact match
                            shouldShow = shouldShow && (capacity == parseInt(selectedCapacity));
                        }
                    }
                    
                    // Apply search filter
                    if (searchQuery) {
                        shouldShow = shouldShow && keywords.includes(searchQuery);
                    }
                    
                    // Apply availability filter
                    if (showOnlyAvailable) {
                        shouldShow = shouldShow && (availability === 'available');
                    }
                    
                    // Apply the combined filters
                    card.style.display = shouldShow ? 'block' : 'none';
                });
                
                // Check if no rooms are visible after filtering
                const visibleRooms = Array.from(roomCards).filter(card => card.style.display !== 'none');
                const noRoomsMessage = document.querySelector('.no-filtered-rooms');
                const roomsGrid = document.querySelector('.rooms-grid');
                
                if (visibleRooms.length === 0) {
                    if (!noRoomsMessage && roomsGrid) {
                        const message = document.createElement('div');
                        message.className = 'no-rooms no-filtered-rooms';
                        message.innerHTML = '<p>No rooms match your selected filters. Please try different criteria.</p>';
                        
                        roomsGrid.parentNode.insertBefore(message, roomsGrid.nextSibling);
                    }
                } else if (noRoomsMessage) {
                    noRoomsMessage.remove();
                }
            }
        }
        
        // Fix for hamburger menu toggle
        const menuToggle = document.getElementById('menu-toggle');
        const navMenu = document.getElementById('nav-menu');
        
        if (menuToggle && navMenu) {
            // Direct click event assignment (not using addEventListener)
            menuToggle.onclick = function(e) {
                e.preventDefault();
                navMenu.classList.toggle('active');
                menuToggle.classList.toggle('active');
                console.log('Menu toggled');
                return false; // Prevent default and stop propagation
            };
        }
    });




     
   
    </script>
</body>
</html>