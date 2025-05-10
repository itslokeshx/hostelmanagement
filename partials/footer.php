<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section about">
                <h3>About Us</h3>
                <p>We provide quality accommodation for students with modern amenities and a comfortable environment for study and leisure.</p>
                <div class="contact">
                    <p><i class="fas fa-phone"></i> +123 456 7890</p>
                    <p><i class="fas fa-envelope"></i> info@hostelmanagement.com</p>
                    <p><i class="fas fa-map-marker-alt"></i> 123 University Street, City</p>
                </div>
            </div>
            <div class="footer-section links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="rooms.php">Rooms</a></li>
                    <li><a href="complaints.php">Complaints</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="profile.php">My Profile</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="footer-section social">
                <h3>Follow Us</h3>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2023 Hostel Management System. All rights reserved.</p>
        </div>
    </div>
</footer>
