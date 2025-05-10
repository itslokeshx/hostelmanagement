<?php
require_once 'config.php';
require_once 'authentication.php';

// Check if logged in as admin
if (!isAdmin()) {
    header("Location: admin_login.php");
    exit();
}

// Get counts for overview
$stmt = $conn->query("SELECT COUNT(*) as total_students FROM students");
$total_students = $stmt->fetch(PDO::FETCH_ASSOC)['total_students'];

$stmt = $conn->query("SELECT COUNT(*) as total_rooms FROM rooms");
$total_rooms = $stmt->fetch(PDO::FETCH_ASSOC)['total_rooms'];

$stmt = $conn->query("SELECT SUM(capacity) as total_capacity FROM rooms");
$total_capacity = $stmt->fetch(PDO::FETCH_ASSOC)['total_capacity'];

$stmt = $conn->query("SELECT SUM(occupied) as total_occupied FROM rooms");
$total_occupied = $stmt->fetch(PDO::FETCH_ASSOC)['total_occupied'];

$occupancy_rate = ($total_capacity > 0) ? round(($total_occupied / $total_capacity) * 100) : 0;

$stmt = $conn->query("SELECT COUNT(*) as total_complaints FROM complaints");
$total_complaints = $stmt->fetch(PDO::FETCH_ASSOC)['total_complaints'];

$stmt = $conn->query("SELECT COUNT(*) as open_complaints FROM complaints WHERE status = 'Open'");
$open_complaints = $stmt->fetch(PDO::FETCH_ASSOC)['open_complaints'];

// Get all complaints with student information
$stmt = $conn->query("
    SELECT c.*, s.name as student_name, s.email as student_email 
    FROM complaints c
    LEFT JOIN students s ON c.student_id = s.student_id
    ORDER BY c.created_at DESC
");
$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all rooms with occupancy information
$stmt = $conn->query("
    SELECT * FROM rooms
    ORDER BY room_number
");
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process status update if submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $complaint_id = $_POST['complaint_id'];
    $new_status = $_POST['new_status'];
    
    $stmt = $conn->prepare("UPDATE complaints SET status = ? WHERE complaint_id = ?");
    if ($stmt->execute([$new_status, $complaint_id])) {
        $message = "Complaint status updated successfully";
        $message_type = "success";
        
        // Refresh the complaints data
        $stmt = $conn->query("
            SELECT c.*, s.name as student_name, s.email as student_email 
            FROM complaints c
            LEFT JOIN students s ON c.student_id = s.student_id
            ORDER BY c.created_at DESC
        ");
        $complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $message = "Failed to update complaint status";
        $message_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Hostel Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-dashboard {
            padding: 30px 0;
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .admin-header h1 {
            color: #8b5cf6;
            margin: 0;
        }
        
        .admin-actions {
            display: flex;
            gap: 10px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background-color: #2c2c2c;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        
        .stat-card .icon {
            font-size: 36px;
            color: #8b5cf6;
            margin-bottom: 10px;
        }
        
        .stat-card .stat-value {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
            color: #f5f5f5;
        }
        
        .stat-card .stat-label {
            color: #aaa;
            font-size: 16px;
        }
        
        .admin-section {
            background-color: #2c2c2c;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        
        .admin-section h2 {
            color: #8b5cf6;
            border-bottom: 1px solid #444;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .admin-tabs {
            display: flex;
            border-bottom: 1px solid #444;
            margin-bottom: 20px;
        }
        
        .admin-tab {
            padding: 10px 20px;
            cursor: pointer;
            color: #aaa;
            border-bottom: 3px solid transparent;
        }
        
        .admin-tab.active {
            color: #8b5cf6;
            border-bottom-color: #8b5cf6;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .complaints-table, .rooms-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .complaints-table th, .complaints-table td,
        .rooms-table th, .rooms-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #444;
        }
        
        .complaints-table th, .rooms-table th {
            background-color: #333;
            color: #8b5cf6;
        }
        
        .complaints-table tr:hover, .rooms-table tr:hover {
            background-color: #333;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-Open {
            background-color: #dc2626;
            color: white;
        }
        
        .status-In-Progress {
            background-color: #eab308;
            color: black;
        }
        
        .status-Resolved {
            background-color: #22c55e;
            color: white;
        }
        
        .admin-room-card {
            border: 1px solid #444;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .admin-room-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .room-title {
            font-size: 18px;
            font-weight: bold;
            color: #8b5cf6;
        }
        
        .room-capacity {
            color: #aaa;
        }
        
        .progress-bar {
            height: 8px;
            background-color: #444;
            border-radius: 4px;
            margin: 10px 0;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background-color: #8b5cf6;
        }
        
        .room-details {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 14px;
            color: #ccc;
        }
        
        .update-status-form {
            display: inline-block;
        }
        
        .status-select {
            padding: 5px 10px;
            background-color: #333;
            color: #fff;
            border: 1px solid #444;
            border-radius: 4px;
        }
        
        .complaint-action {
            display: flex;
            gap: 10px;
        }
        
        .complaint-action .btn {
            padding: 5px 10px;
            font-size: 12px;
        }
        
        .logout-btn {
            background-color: #dc2626;
            color: white;
        }
        
        .logout-btn:hover {
            background-color: #b91c1c;
        }
        
        .admin-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .admin-avatar {
            width: 40px;
            height: 40px;
            background-color: #8b5cf6;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">
                    <i class="fas fa-hotel"></i> Hostel Management
                </a>
                <div class="admin-profile">
                    <div class="admin-avatar">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div>
                        <span class="admin-name"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                        <span class="admin-role">Administrator</span>
                    </div>
                    <a href="logout.php" class="btn logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </header>
    
    <section class="admin-dashboard">
        <div class="container">
            <div class="admin-header">
                <h1>Admin Dashboard</h1>
                <div class="admin-actions">
                    <a href="index.php" class="btn">
                        <i class="fas fa-home"></i> Visit Website
                    </a>
                </div>
            </div>
            
            <?php if (isset($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="icon"><i class="fas fa-users"></i></div>
                    <div class="stat-value"><?php echo $total_students; ?></div>
                    <div class="stat-label">Total Students</div>
                </div>
                <div class="stat-card">
                    <div class="icon"><i class="fas fa-door-open"></i></div>
                    <div class="stat-value"><?php echo $total_rooms; ?></div>
                    <div class="stat-label">Total Rooms</div>
                </div>
                <div class="stat-card">
                    <div class="icon"><i class="fas fa-bed"></i></div>
                    <div class="stat-value"><?php echo $occupancy_rate; ?>%</div>
                    <div class="stat-label">Occupancy Rate</div>
                </div>
                <div class="stat-card">
                    <div class="icon"><i class="fas fa-exclamation-circle"></i></div>
                    <div class="stat-value"><?php echo $open_complaints; ?></div>
                    <div class="stat-label">Open Complaints</div>
                </div>
            </div>
            
            <div class="admin-section">
                <div class="admin-tabs">
                    <div class="admin-tab active" data-tab="complaints">
                        <i class="fas fa-exclamation-circle"></i> Complaints Management
                    </div>
                    <div class="admin-tab" data-tab="rooms">
                        <i class="fas fa-door-open"></i> Rooms Overview
                    </div>
                </div>
                
                <div class="tab-content active" id="complaints">
                    <h2>Student Complaints</h2>
                    <?php if (!empty($complaints)): ?>
                        <table class="complaints-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Student</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($complaints as $complaint): ?>
                                    <tr>
                                        <td><?php echo $complaint['complaint_id']; ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($complaint['student_name']); ?><br>
                                            <small><?php echo htmlspecialchars($complaint['student_email']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($complaint['title']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($complaint['description'], 0, 100)) . (strlen($complaint['description']) > 100 ? '...' : ''); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo str_replace(' ', '-', $complaint['status']); ?>">
                                                <?php echo $complaint['status']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($complaint['created_at'])); ?></td>
                                        <td>
                                            <div class="complaint-action">
                                                <form action="admin_dashboard.php" method="POST" class="update-status-form">
                                                    <input type="hidden" name="complaint_id" value="<?php echo $complaint['complaint_id']; ?>">
                                                    <select name="new_status" class="status-select">
                                                        <option value="Open" <?php echo ($complaint['status'] == 'Open') ? 'selected' : ''; ?>>Open</option>
                                                        <option value="In Progress" <?php echo ($complaint['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                                                        <option value="Resolved" <?php echo ($complaint['status'] == 'Resolved') ? 'selected' : ''; ?>>Resolved</option>
                                                    </select>
                                                    <button type="submit" name="update_status" class="btn">Update</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No complaints have been submitted yet.</p>
                    <?php endif; ?>
                </div>
                
                <div class="tab-content" id="rooms">
                    <h2>Room Management</h2>
                    <div class="rooms-grid">
                        <?php foreach ($rooms as $room): ?>
                            <?php 
                                $occupancy_percent = ($room['capacity'] > 0) ? ($room['occupied'] / $room['capacity']) * 100 : 0;
                                $available = $room['capacity'] - $room['occupied'];
                            ?>
                            <div class="admin-room-card">
                                <div class="admin-room-header">
                                    <div class="room-title">Room <?php echo htmlspecialchars($room['room_number']); ?></div>
                                    <div class="room-capacity"><?php echo $room['occupied']; ?> / <?php echo $room['capacity']; ?> Occupied</div>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo $occupancy_percent; ?>%"></div>
                                </div>
                                <div class="room-description">
                                    <?php echo htmlspecialchars($room['description']); ?>
                                </div>
                                <div class="room-details">
                                    <span><i class="fas fa-rupee-sign"></i> ₹<?php echo number_format($room['price'], 0); ?>/month</span>
                                    <span><i class="fas fa-user-plus"></i> <?php echo $available; ?> spots available</span>
                                    <span><i class="fas fa-calendar-alt"></i> Updated <?php echo date('M d, Y', strtotime($room['created_at'])); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <?php include 'partials/footer.php'; ?>
    
    <script src="js/script.js"></script>
    <script>
        // Tab switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.admin-tab');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove('active'));
                    
                    // Add active class to clicked tab
                    this.classList.add('active');
                    
                    // Hide all tab content
                    document.querySelectorAll('.tab-content').forEach(content => {
                        content.classList.remove('active');
                    });
                    
                    // Show the corresponding tab content
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });
        });
    </script>
</body>
</html>