<?php
session_start();

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Rio Management System - Login</title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/a.jpg">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        body {
            background-color: #2a2f5b;
            color: white;
            margin: 0;
            padding: 0;
            height: 100%;
        }

        .vh-100 {
            height: 100vh;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #1572e8;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            text-decoration: none;
        }

        .recaptcha-container {
            margin-bottom: 15px;
            display: flex;
            justify-content: center;
        }
    </style>
</head>

<body>
    <a href="https://rio-lawis.com/" class="back-button">
        <i class="fas fa-arrow-left"></i>
    </a>

    <section class="vh-100">
        <div class="container-fluid h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-9 col-lg-6 col-xl-5">
                    <img src="assets/img/1bg.jpg" class="img-fluid" alt="Sample image">
                </div>
                <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                    <form id="loginForm">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        
                        <div class="text-center mb-4">
                            <span class="h1 fw-bold" style="color: #FEA116;">RMS Login</span>
                            <i class="fa fa-heart fa-2x ms-3"></i>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="user">Username</label>
                            <input type="text" name="uname" id="user" class="form-control form-control-lg" 
                                   placeholder="Enter username" required autocomplete="username">
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="pass">Password</label>
                            <div class="input-group">
                                <input type="password" name="pass" id="psw" 
                                       class="form-control form-control-lg" 
                                       placeholder="Enter password" 
                                       minlength="8" 
                                       pattern="(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}" 
                                       title="Password must contain at least one uppercase letter, one number, and one special character" 
                                       required 
                                       autocomplete="current-password">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="recaptcha-container">
                            <div class="g-recaptcha" data-sitekey="6LcGl4kqAAAAAB6yVfa6va0KJEnZ5nBZjW9G9was"></div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" name="login" class="btn btn-primary btn-lg">Login</button>
                            <a href="forgot_password.php" class="text-primary">Forgot password?</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
    function togglePassword() {
        var passwordField = document.getElementById("psw");
        passwordField.type = passwordField.type === "password" ? "text" : "password";
    }

    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        var recaptchaResponse = grecaptcha.getResponse();
        if (recaptchaResponse.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'reCAPTCHA Required',
                text: 'Please complete the reCAPTCHA verification',
                confirmButtonText: 'OK'
            });
            return;
        }

        var formData = new FormData(this);
        formData.append('login', '1');

        fetch('login.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const loginButton = document.querySelector('button[name="login"]');

            if (data.success) {
                window.location.href = data.redirect;
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Login Failed',
                    text: data.error,
                    confirmButtonText: 'Try Again'
                });

                grecaptcha.reset();

                if (data.disable) {
                    loginButton.disabled = true;
                    startTimer(data.time_remaining, loginButton);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An unexpected error occurred. Please try again.'
            });
        });
    });

    function startTimer(duration, button) {
        const timerDisplay = document.createElement('span');
        timerDisplay.style.marginLeft = '10px';
        timerDisplay.style.color = 'red';
        button.parentNode.appendChild(timerDisplay);

        let remaining = duration;
        const interval = setInterval(() => {
            if (remaining <= 0) {
                clearInterval(interval);
                button.disabled = false;
                timerDisplay.remove();
            } else {
                remaining--;
                const minutes = Math.floor(remaining / 60);
                const seconds = remaining % 60 < 10 ? `0${remaining % 60}` : remaining % 60;
                timerDisplay.textContent = ` (Try again in ${minutes}:${seconds})`;
            }
        }, 1000);
    }
    </script>
</body>
</html>