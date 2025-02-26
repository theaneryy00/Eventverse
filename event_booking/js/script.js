document.addEventListener("DOMContentLoaded", function () {
    
    
    
    
    // Determine the current page's filename
    const currentPage = window.location.pathname.split("/").pop();
    // List of pages where we DON'T want session expiration
    const noExpirationPages = ["index.php", "login.php", "signup.php"];

    // Only add the auto-logout timer if the current page is NOT in the noExpirationPages list.
    if (!noExpirationPages.includes(currentPage)) {
        // ================================
        // Client-Side Inactivity Timer for Auto-Logout
        // ================================
        // Set inactivity timeout duration in milliseconds (30 seconds for testing; use 1800000 for 30 minutes)
        const inactivityTimeout = 30000;
        let inactivityTimer;

        // Function to trigger auto logout
        function autoLogout() {
            alert("Your session has expired due to inactivity. Please log in again.");
            window.location.href = "logout.php"; // Redirect to logout or login page
        }

        // Start the inactivity timer
        function startInactivityTimer() {
            inactivityTimer = setTimeout(autoLogout, inactivityTimeout);
        }
        
        // Reset the inactivity timer on user activity
        function resetInactivityTimer() {
            clearTimeout(inactivityTimer);
            startInactivityTimer();
        }

        // Start the timer initially
        startInactivityTimer();

        // List of events that count as activity
        const activityEvents = ["mousemove", "mousedown", "keypress", "scroll", "touchstart"];
        activityEvents.forEach(eventType => {
            document.addEventListener(eventType, resetInactivityTimer, false);
        });
    }


    



    // ✅ Check for .success-message and show alert
    let successMessage = document.querySelector(".success-message");
    if (successMessage) {
        alert(successMessage.textContent);
        window.location.href = "dashboard.php"; // Redirect after clicking "OK"
    }

    // ✅ Check for #notification message stored in session
    let notification = document.getElementById("notification");
    if (notification) {
        let message = notification.dataset.message;
        let messageType = notification.dataset.type;

        if (message) {
            alert(message); // Show alert message
            if (messageType === "success") {
                window.location.href = "manage_events.php"; // Redirect after success message
            }
        }
    }

    // ✅ Ticket price update functionality
    let ticketCategory = document.getElementById("ticket_category");
    let priceDisplay = document.getElementById("ticket_price");

    function updatePrice() {
        priceDisplay.textContent = ticketCategory.value === "VIP" ? "₱500.00" : "₱250.00";
    }

    if (ticketCategory) {
        ticketCategory.addEventListener("change", updatePrice);
    }

    // ✅ Event edit modal handling
    let editModal = document.getElementById("editModal");
    let editEventId = document.getElementById("edit_event_id");
    let editName = document.getElementById("edit_name");
    let editDate = document.getElementById("edit_date");

    document.querySelectorAll(".edit-btn").forEach(button => {
        button.addEventListener("click", function () {
            editEventId.value = this.dataset.id;
            editName.value = this.dataset.name;
            editDate.value = this.dataset.date;
            editModal.style.display = "block"; // Show modal
        });
    });

    // ✅ Close modal
    document.querySelector(".close").addEventListener("click", function () {
        editModal.style.display = "none"; // Hide modal
    });

    // ✅ Close modal when clicking outside
    window.addEventListener("click", function (event) {
        if (event.target === editModal) {
            editModal.style.display = "none"; // Hide modal
        }
    });

    // ✅ Login Form Validation
    let loginForm = document.getElementById("login-form");
    if (loginForm) {
        loginForm.addEventListener("submit", function (event) {
            let emailInput = document.getElementById("email");
            let passwordInput = document.getElementById("password");
            let errors = [];

            if (!emailInput.value.match(/^[^@]+@[^@]+\.[a-zA-Z]{2,}$/)) {
                errors.push("Please enter a valid email.");
            }

            if (passwordInput.value.length < 8 || passwordInput.value.length > 72) {
                errors.push("Password must be between 8 and 72 characters.");
            }

            if (errors.length > 0) {
                event.preventDefault();
                alert(errors.join("\n"));
            }
        });
    }

    // Sign up client side input
    let signupForm = document.getElementById("signup-form");
    if (signupForm) {
        signupForm.addEventListener("submit", function (event) {
            let nameInput = document.getElementById("name");
            let emailInput = document.getElementById("email");
            let passwordInput = document.getElementById("password");
            let errors = [];

            // Check required fields
            if (nameInput.value.trim() === "") {
                errors.push("Name is required.");
            }
            if (emailInput.value.trim() === "") {
                errors.push("Email is required.");
            }
            if (passwordInput.value.trim() === "") {
                errors.push("Password is required.");
            }

            // Validate Full Name length and allowed characters
            if (nameInput.value.trim().length > 100) {
                errors.push("Full Name must not exceed 100 characters.");
            }
            if (!/^[a-zA-Z\s'-]+$/.test(nameInput.value.trim())) {
                errors.push("Full Name must not contain special characters.");
            }

            // Validate email format and length
            if (!/^[^@]+@[^@]+\.[a-zA-Z]{2,}$/.test(emailInput.value.trim())) {
                errors.push("Please enter a valid email.");
            }
            if (emailInput.value.trim().length > 100) {
                errors.push("Email exceeds the allowed length.");
            }

            // Validate password length and complexity
            if (passwordInput.value.length < 8 || passwordInput.value.length > 72) {
                errors.push("Password must be between 8 and 72 characters.");
            }
            if (!/(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])/.test(passwordInput.value)) {
                errors.push("Password must include at least one letter, one number, and one special character.");
            }

            if (errors.length > 0) {
                event.preventDefault();
                alert(errors.join("\n"));
            }
        });
    }

    // ✅ Ticket Booking Validation (if applicable)
    let ticketForm = document.getElementById("ticket-form");
    if (ticketForm) {
        ticketForm.addEventListener("submit", function (event) {
            let category = ticketCategory.value;
            if (!category) {
                event.preventDefault();
                alert("Please select a ticket category.");
            }
        });
    }

    // ✅ Real-time Input Validation
    document.querySelectorAll("input").forEach(input => {
        input.addEventListener("input", function () {
            // ✅ Apply 'valid' class if input meets the validation rules
            if (input.checkValidity()) {
                input.classList.remove("invalid");
                input.classList.add("valid");
            } else {
                // ✅ Apply 'invalid' class if input does not meet validation rules
                input.classList.remove("valid");
                input.classList.add("invalid");
            }
        });
    });

    // ✅ Additional Constraint Validation for Inputs
    let allInputs = document.querySelectorAll("input[required], select[required]");
    allInputs.forEach(input => {
        input.addEventListener("blur", function () {
            if (!input.checkValidity()) {
                input.classList.add("invalid");
                input.classList.remove("valid");
            } else {
                input.classList.add("valid");
                input.classList.remove("invalid");
            }
        });
    });


    
});
