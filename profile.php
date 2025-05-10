<?php
require_once 'config.php';
require_once 'authentication.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Get student information
$student_id = $_SESSION['student_id'];
$student = getStudentInfo($student_id);

// Get student complaints
$complaints_query = "SELECT * FROM complaints WHERE student_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($complaints_query);
$stmt->execute([$student_id]);
$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Hostel Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'partials/navbar.php'; ?>
    
    <section class="dashboard-section">
        <div class="container">
            <h1>Student Profile</h1>
            
            <div class="dashboard-grid">
                <div class="dashboard-card profile-card">
                    <div class="card-header">
                        <h2>Profile Information</h2>
                    </div>
                    <div class="card-body">
                        <div class="profile-info">
                            <div class="info-group">
                                <span class="label">Name:</span>
                                <span class="value"><?php echo htmlspecialchars($student['name']); ?></span>
                            </div>
                            <div class="info-group">
                                <span class="label">Email:</span>
                                <span class="value"><?php echo htmlspecialchars($student['email']); ?></span>
                            </div>
                            <?php if (!empty($student['room_number'])): ?>
                                <div class="info-group">
                                    <span class="label">Room Number:</span>
                                    <span class="value"><?php echo htmlspecialchars($student['room_number']); ?></span>
                                </div>
                            <?php else: ?>
                                <div class="info-group">
                                    <span class="label">Room:</span>
                                    <span class="value">Not assigned yet</span>
                                </div>
                                <a href="rooms.php" class="btn">Book a Room</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="dashboard-card complaints-card">
                    <div class="card-header">
                        <h2>My Complaints</h2>
                        <a href="complaints.php" class="btn btn-sm">New Complaint</a>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($complaints)): ?>
                            <div class="complaints-list">
                                <?php foreach($complaints as $complaint): ?>
                                    <div class="complaint-item">
                                        <div class="complaint-header">
                                            <h3><?php echo htmlspecialchars($complaint['title']); ?></h3>
                                            <span class="status status-<?php echo strtolower(str_replace(' ', '-', $complaint['status'])); ?>">
                                                <?php echo htmlspecialchars($complaint['status']); ?>
                                            </span>
                                        </div>
                                        <p class="complaint-desc"><?php echo htmlspecialchars($complaint['description']); ?></p>
                                        <div class="complaint-meta">
                                            <span class="date">
                                                <i class="fas fa-calendar"></i> 
                                                <?php echo date('M d, Y', strtotime($complaint['created_at'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="no-complaints">
                                <p>You haven't submitted any complaints yet.</p>
                                <a href="complaints.php" class="btn">Submit a Complaint</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <?php include 'partials/footer.php'; ?>
    
    <script src="js/script.js"></script>
</body>
</html>