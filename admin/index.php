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
    
    <!-- reCAPTCHA and SweetAlert scripts -->
    <script src="https://www.google.com/recaptcha/api.js?render=explicit" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
    body {
        background-color: #2a2f5b;
        color: white;
        margin: 0;
        padding: 0;
        height: 100%;
    }

    html {
        height: 100%;
    }

    .vh-100 {
        height: 100vh;
    }

    .recaptcha-container {
        margin-bottom: 15px;
        display: flex;
        justify-content: center;
    }

    .back-button {
        position: absolute;
        top: 20px;
        left: 20px;
        background-color: #1572e8;
        color: white;
        padding: 8px 12px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
    }
    </style>
</head>

<body>
<a href="https://rio-lawis.com/" class="btn btn-light back-button">
    <i class="fas fa-arrow-left"></i>
</a>

<section class="vh-100" style="background-color: #2a2f5b; color: white;">
    <div class="container-fluid h-custom">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-md-9 col-lg-6 col-xl-5 position-relative">
                <img src="assets/img/1bg.jpg" class="img-fluid" alt="Sample image">
            </div>
            <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                <form id="loginForm" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    
                    <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                        <div class="d-flex align-items-center mb-3 pb-1">
                            <span class="h1 fw-bold mb-0" style="color: #FEA116;">RMS Login</span>
                            <i class="fa fa-heart fa-2x me-3"></i>
                        </div>
                    </div>

                    <div class="form-outline mb-4">
                        <label class="form-label" for="user">Username</label>
                        <input type="text" name="uname" id="user" class="form-control form-control-lg" placeholder="Enter username" required autocomplete="username">
                    </div>

                    <div class="form-outline mb-3">
                        <label class="form-label" for="pass">Password</label>
                        <input  
                            type="password" 
                            name="pass" 
                            id="psw" 
                            class="form-control form-control-lg" 
                            placeholder="Enter password" 
                            minlength="8" 
                            pattern="(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}" 
                            title="Password must contain at least one uppercase letter, one number, and one special character" 
                            required 
                            autocomplete="current-password"
                        >
                        <input class="p-2" type="checkbox" onclick="togglePassword()" style="margin-left: 10px; margin-top: 13px;">
                        <span style="margin-left: 5px;">Show password</span>
                    </div>

                    <div class="recaptcha-container mb-3">
                        <div id="g-recaptcha" class="g-recaptcha" 
                             data-sitekey="6LcGl4kqAAAAAB6yVfa6va0KJEnZ5nBZjW9G9was" 
                             data-callback="onSubmit"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <button type="submit" name="login" class="btn btn-warning btn-lg enter" style="background-color: #1572e8; color: white; padding-left: 2.5rem; padding-right: 2.5rem;">Login</button>
                        <a href="forgot_password.php" class="">Forgot password?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
let recaptchaWidgetId = null;

function togglePassword() {
    var x = document.getElementById("psw");
    x.type = x.type === "password" ? "text" : "password";
}

function onSubmit(token) {
    document.getElementById('loginForm').submit();
}

function renderRecaptcha() {
    recaptchaWidgetId = grecaptcha.render('g-recaptcha', {
        'sitekey': '6LcGl4kqAAAAAB6yVfa6va0KJEnZ5nBZjW9G9was',
        'callback': onSubmit
    });
}

document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Check if reCAPTCHA is verified
    var recaptchaResponse = grecaptcha.getResponse(recaptchaWidgetId);
    if (recaptchaResponse.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Verification Failed',
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
    .then(response => response.json())
    .then(data => {
        const loginButton = document.querySelector('button[name="login"]');

        if (data.success) {
            window.location.href = data.redirect;
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: data.error,
                confirmButtonText: 'Try Again',
                timer: 5000
            });

            // Reset reCAPTCHA on failure
            grecaptcha.reset(recaptchaWidgetId);

            if (data.disable) {
                loginButton.disabled = true;
                startTimer(data.time_remaining, loginButton);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Reset reCAPTCHA on error
        grecaptcha.reset(recaptchaWidgetId);
    });
});

function startTimer(duration, button) {
    const timerDisplay = document.createElement('span');
    timerDisplay.style.marginLeft = '5px';
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
            timerDisplay.textContent = ` (Try again in ${minutes}:${seconds} mins)`;
        }
    }, 1000);
}

// Ensure reCAPTCHA is rendered
window.onload = renderRecaptcha;
</script>
</body>
</html>