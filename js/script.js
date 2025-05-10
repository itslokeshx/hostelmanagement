document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if(menuToggle) {
        menuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    }
    
    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        if (navMenu && navMenu.classList.contains('active') && !event.target.closest('.navbar')) {
            navMenu.classList.remove('active');
        }
    });
    
    // Room filtering functionality
    const capacityFilter = document.getElementById('capacity-filter');
    const searchInput = document.getElementById('search-rooms');
    const availabilityFilter = document.getElementById('available-filter');
    
    // Check if we're on the rooms page
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
                        // For 4+, hide rooms with capacity less than 4
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
                card.classList.toggle('hidden', !shouldShow);
            });
            
            // Check if no rooms are visible after filtering
            const visibleRooms = document.querySelectorAll('.room-card:not(.hidden)');
            const noRoomsMessage = document.querySelector('.no-filtered-rooms');
            
            if (visibleRooms.length === 0) {
                if (!noRoomsMessage) {
                    const message = document.createElement('div');
                    message.className = 'no-rooms no-filtered-rooms';
                    message.innerHTML = '<p>No rooms match your selected filters. Please try different criteria.</p>';
                    
                    const roomsGrid = document.querySelector('.rooms-grid');
                    roomsGrid.parentNode.insertBefore(message, roomsGrid.nextSibling);
                }
            } else if (noRoomsMessage) {
                noRoomsMessage.remove();
            }
        }
        
        // Run initial filtering
        filterRooms();
    }
    
    // Auto hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    if(alerts.length > 0) {
        setTimeout(function() {
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 500);
            });
        }, 5000);
    }
    
    // Login/Register tabs
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    if(tabBtns.length > 0) {
        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const target = this.dataset.target;
                
                // Remove active class from all buttons and panes
                tabBtns.forEach(btn => btn.classList.remove('active'));
                tabPanes.forEach(pane => pane.classList.remove('active'));
                
                // Add active class to clicked button and corresponding pane
                this.classList.add('active');
                document.getElementById(target).classList.add('active');
            });
        });
    }
    
    // Form validation
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let hasError = false;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    hasError = true;
                    field.classList.add('error');
                    
                    // Create error message if doesn't exist
                    let errorMsg = field.parentElement.querySelector('.error-message');
                    if (!errorMsg) {
                        errorMsg = document.createElement('div');
                        errorMsg.className = 'error-message';
                        errorMsg.textContent = 'This field is required';
                        field.parentElement.appendChild(errorMsg);
                    }
                } else {
                    field.classList.remove('error');
                    const errorMsg = field.parentElement.querySelector('.error-message');
                    if (errorMsg) {
                        errorMsg.remove();
                    }
                }
            });
            
            // Email validation
            const emailFields = form.querySelectorAll('[type="email"]');
            emailFields.forEach(field => {
                if (field.value.trim() && !isValidEmail(field.value)) {
                    hasError = true;
                    field.classList.add('error');
                    
                    // Create error message if doesn't exist
                    let errorMsg = field.parentElement.querySelector('.error-message');
                    if (!errorMsg) {
                        errorMsg = document.createElement('div');
                        errorMsg.className = 'error-message';
                        errorMsg.textContent = 'Please enter a valid email address';
                        field.parentElement.appendChild(errorMsg);
                    } else {
                        errorMsg.textContent = 'Please enter a valid email address';
                    }
                }
            });
            
            // Password confirmation validation
            const passwordField = form.querySelector('[name="password"]');
            const confirmPasswordField = form.querySelector('[name="confirm_password"]');
            
            if (passwordField && confirmPasswordField) {
                if (passwordField.value !== confirmPasswordField.value) {
                    hasError = true;
                    confirmPasswordField.classList.add('error');
                    
                    // Create error message if doesn't exist
                    let errorMsg = confirmPasswordField.parentElement.querySelector('.error-message');
                    if (!errorMsg) {
                        errorMsg = document.createElement('div');
                        errorMsg.className = 'error-message';
                        errorMsg.textContent = 'Passwords do not match';
                        confirmPasswordField.parentElement.appendChild(errorMsg);
                    } else {
                        errorMsg.textContent = 'Passwords do not match';
                    }
                }
            }
            
            if (hasError) {
                e.preventDefault();
            }
        });
        
        // Clear error styling on input
        const formInputs = form.querySelectorAll('input, textarea');
        formInputs.forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('error');
                const errorMsg = this.parentElement.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }
            });
        });
    });
    
    // Helper function to validate email format
    function isValidEmail(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }
    
    // Handle image loading errors
    const roomImages = document.querySelectorAll('.room-image img');
    roomImages.forEach(img => {
        img.addEventListener('error', function() {
            this.classList.add('error');
            // Optional: Set a data attribute to indicate this image failed to load
            this.dataset.loadFailed = true;
        });
    });
});
// script.js
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const menuToggle = document.getElementById('menu-toggle');
    const navMenu = document.getElementById('nav-menu');
    
    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            menuToggle.classList.toggle('active');
            console.log('Menu toggled');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (navMenu.classList.contains('active') && 
                !event.target.closest('.nav-menu') && 
                !event.target.closest('#menu-toggle')) {
                navMenu.classList.remove('active');
                menuToggle.classList.remove('active');
            }
        });
    }
    
    // Room filtering (if on rooms page)
    const capacityFilter = document.getElementById('capacity-filter');
    if (capacityFilter) {
        // Room filtering code here (if needed)
    }
});