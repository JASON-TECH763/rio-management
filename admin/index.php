<?php
session_start();

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Set security headers
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net https://www.google.com/recaptcha/ 'unsafe-inline'; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; img-src 'self' data:; font-src 'self' https://fonts.googleapis.com https://cdn.jsdelivr.net; frame-ancestors 'none'; form-action 'self'; base-uri 'self';");
header("X-XSS-Protection: 1; mode=block");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Rio Management System</title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/a.jpg">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Add Font Awesome CSS if not included -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- reCAPTCHA Script -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

<style type="text/css">
    .divider:after,
    .divider:before {
        content: "";
        flex: 1;
        height: 1px;
        background: #eee;
    }
    .h-custom {
        height: calc(100% - 73px);
    }
    @media (max-width: 450px) {
        .h-custom {
            height: 100%;
        }
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
    .back-button i {
        font-size: 1rem;
        margin-right: 5px;
    }

    /* Adjust position and size on mobile devices */
    @media (max-width: 450px) {
        .back-button {
            top: 10px;
            left: 10px;
            padding: 6px 10px; /* Slightly smaller padding */
        }
        .back-button i {
            font-size: 0.9rem; /* Slightly smaller icon size */
        }
    }
    .recaptcha-container {
        display: none;
        margin-bottom: 15px;
    }
</style>
</head>

<body>
<a href="https://rio-lawis.com/" class="btn btn-light back-button">
    <i class="fas fa-arrow-left"></i>
</a>

<section class="vh-100" style="background-color: #2a2f5b; color: white;">
<br><br>

  <div class="container-fluid h-custom">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-md-9 col-lg-6 col-xl-5 position-relative">
        <img src="assets/img/1bg.jpg" class="img-fluid" alt="Sample image">
      </div>
      <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
        <form id="loginForm">
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
            <input type="password" name="pass" id="psw" class="form-control form-control-lg" placeholder="Enter password" required autocomplete="current-password">
            <input class="p-2" type="checkbox" onclick="togglePassword()" style="margin-left: 10px; margin-top: 13px;"> <span style="margin-left: 5px;">Show password</span>
          </div>
          
          <!-- reCAPTCHA Container -->
          <div id="recaptchaContainer" class="recaptcha-container mb-3">
            <div class="g-recaptcha" data-sitekey="6LdO2IUqAAAAAI_flMld0C2tf7C9zVwIfk63QUfO"></div>
          </div>

          <div class="d-flex justify-content-between align-items-center">
            <button type="submit" name="login" class="btn btn-warning btn-lg enter" style="background-color: #1572e8; color: white; padding-left: 2.5rem; padding-right: 2.5rem;">Login</button>
            <a href="forgot_password.php" class="">Forgot password?</a>
          </div>
          <div class="text-center text-lg-start mt-4 pt-2"></div>
        </form>
      </div>
    </div>
  </div>
</section>
<script src="assets/js/jquery-3.2.1.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/script.js"></script>

<script>
  function togglePassword() {
    var x = document.getElementById("psw");
    x.type = x.type === "password" ? "text" : "password";
  }

  document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
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
        const recaptchaContainer = document.getElementById('recaptchaContainer');

        if (data.success) {
            window.location.href = data.redirect;
        } else {
            // Show SweetAlert for error messages
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: data.error,
                confirmButtonText: 'Try Again',
                timer: 5000
            });

            // Show reCAPTCHA after 3 failed attempts
            if (data.show_recaptcha) {
                recaptchaContainer.style.display = 'block';
            }

            if (data.disable) {
                loginButton.disabled = true;
                startTimer(data.time_remaining, loginButton);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});

function startTimer(duration, button) {
    const timerDisplay = document.createElement('span');
    timerDisplay.style.marginLeft = '10px';
    timerDisplay.style.color = 'red'; // Set color to red
    timerDisplay.style.fontWeight = 'bold'; // Optional: Make it bold
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

</script>

</body>
</html>