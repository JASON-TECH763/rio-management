<?php
session_start();

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Set security headers
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; img-src 'self' data:; font-src 'self' https://fonts.googleapis.com https://cdn.jsdelivr.net; frame-ancestors 'none'; form-action 'self'; base-uri 'self';");
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
    <title>Rio Management System - Staff Login</title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/a.jpg">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        #timer { color: red; font-size: 18px; font-weight: bold; }
    </style>
</head>
<body>
<section class="vh-100" style="background-color: #2a2f5b; color: white;">
  <div class="container-fluid h-custom">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-md-9 col-lg-6 col-xl-5">
        <img src="assets/img/1bg.jpg" class="img-fluid" alt="Login Image">
      </div>
      <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
        <form id="loginForm">
          <h1 class="fw-bold mb-4" style="color: #FEA116;">Staff Login</h1>
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
          <div class="form-outline mb-4">
            <label class="form-label" for="user">Email</label>
            <input type="text" name="uname" id="user" class="form-control form-control-lg" placeholder="Enter email" required autocomplete="username">
          </div>
          <div class="form-outline mb-3">
            <label class="form-label" for="pass">Password</label>
            <input type="password" name="pass" id="psw" class="form-control form-control-lg" placeholder="Enter password" required autocomplete="current-password">
            <input class="mt-2" type="checkbox" onclick="togglePassword()"> Show password
          </div>
          <button type="submit" class="btn btn-warning btn-lg" style="background-color: #1572e8; color: white;">Login</button>
          <p id="error" style="color:red;" class="mt-2"></p>
          <div id="timer" class="mt-2"></div>
        </form>
      </div>
    </div>
  </div>
</section>

<script src="assets/js/jquery-3.2.1.min.js"></script>
<script>
  // Toggle password visibility
  function togglePassword() {
    const passwordField = document.getElementById('psw');
    passwordField.type = passwordField.type === 'password' ? 'text' : 'password';
  }

  // Handle form submission
  $('#loginForm').on('submit', function(e) {
    e.preventDefault();
    const formData = $(this).serialize();
    $.ajax({
      url: 'login.php',
      type: 'POST',
      data: formData,
      success: function(response) {
        const res = JSON.parse(response);
        if (res.success) {
          window.location.href = res.redirect;
        } else {
          $('#error').text(res.message);
          if (res.disable) {
            disableLogin(res.time_remaining);
          }
        }
      },
      error: function() {
        $('#error').text("An error occurred. Please try again.");
      }
    });
  });

  // Disable login temporarily
  function disableLogin(duration) {
    const loginButton = $('button[type="submit"]');
    loginButton.prop('disabled', true);

    const timerDisplay = $('#timer');
    let remaining = duration;

    const interval = setInterval(() => {
      if (remaining <= 0) {
        clearInterval(interval);
        loginButton.prop('disabled', false);
        timerDisplay.text('');
      } else {
        timerDisplay.text(`Try again in ${Math.ceil(remaining / 60)}m ${remaining % 60}s`);
        remaining--;
      }
    }, 1000);
  }
</script>
</body>
</html>
