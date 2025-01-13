<?php 
  session_start();
  include('config/connect.php');
  $error = "";
  
  // Initialize attempt tracking
  if (!isset($_SESSION['attempt_count'])) {
    $_SESSION['attempt_count'] = 0;
    $_SESSION['lockout_time'] = 0;
  }

  // Check if lockout period is active
  if ($_SESSION['attempt_count'] >= 3 && time() < $_SESSION['lockout_time']) {
    $error = '* Too many failed attempts. Try again in 3 minutes.';
  } elseif ($_SESSION['attempt_count'] >= 3 && time() >= $_SESSION['lockout_time']) {
    $_SESSION['attempt_count'] = 0;
    $_SESSION['lockout_time'] = 0;
  }

  // Handle staff login
  if (isset($_POST['login']) && $_SESSION['attempt_count'] < 3) {
    $user = $_REQUEST['uname'];
    $pass = $_REQUEST['pass'];

    // Sanitize input
    $user = mysqli_real_escape_string($conn, $user);

    if (!empty($user) && !empty($pass)) {
        // Rest of your existing login logic
        $query = "SELECT staff_email, staff_password FROM rpos_staff WHERE staff_email=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $user);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $row = mysqli_fetch_array($result);

        if ($row && $pass === $row['staff_password']) {
          $_SESSION['staff_email'] = $row['staff_email'];
          $_SESSION['attempt_count'] = 0;
          header("Location: dashboard.php");
          exit();
        } else {
          echo "<script>
              document.addEventListener('DOMContentLoaded', function() {
                  Swal.fire({
                      icon: 'error',
                      title: 'Login Failed',
                      text: 'Invalid Email or Password',
                      confirmButtonColor: '#1572e8'
                  });
              });
          </script>";
          $_SESSION['attempt_count']++;
        }
    }
    
    // Lockout after 3 failed attempts
    if ($_SESSION['attempt_count'] >= 3) {
        $_SESSION['lockout_time'] = time() + (3 * 60);
    }
  }

  // Handle staff account creation
  if (isset($_POST['create_account'])) {
    $staff_name = $_POST['name'];
    $staff_last_name = $_POST['last_name'];
    $staff_email = $_POST['email'];
    $staff_password = $_POST['password'];
    $staff_gender = $_POST['gender'];

    if (!empty($staff_name) && !empty($staff_last_name) && !empty($staff_email) && !empty($staff_password) && !empty($staff_gender)) {
      // Prepare statement for account creation
      $query = "INSERT INTO rpos_staff (staff_name, staff_last_name, staff_email, staff_password, staff_gender, date_created) 
                VALUES (?, ?, ?, ?, ?, NOW())";
      $stmt = mysqli_prepare($conn, $query);
      mysqli_stmt_bind_param($stmt, "sssss", $staff_name, $staff_last_name, $staff_email, $staff_password, $staff_gender);
      $result = mysqli_stmt_execute($stmt);

      if ($result) {
        echo "<script>alert('Account created successfully! Please login.');</script>";
      } else {
        echo "<script>alert('Error creating account.');</script>";
      }
    } else {
      $error = '* Please fill all the fields!';
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Rio Management System</title>
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/a.jpg">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Add Font Awesome CSS if not included -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Add reCAPTCHA v3 API -->
    <script src="https://www.google.com/recaptcha/api.js?render=6LcXBZQqAAAAAOHJGRgXUsIXpoe44YNomw8bjD5o"></script>

    <style type="text/css">
   /* Apply the fullscreen background color */
   body {
        background-color: #2a2f5b;
        color: white;
        margin: 0; /* Remove default margin */
        padding: 0; /* Remove default padding */
        height: 100%; /* Ensure the body covers the full screen */
    }

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
        <form method="post">
          <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
            <div class="d-flex align-items-center mb-3 pb-1">
              <span class="h1 fw-bold mb-0" style="color: #FEA116;">Staff Login</span>
            </div>
          </div>
          
          <div class="form-outline mb-4">
            <label class="form-label" for="user">Email</label>
            <input type="text" name="uname" id="user" class="form-control form-control-lg" placeholder="Enter email" />
          </div>
          <div class="form-outline mb-3">
            <label class="form-label" for="pass">Password</label>
            <input type="password" name="pass" id="psw" class="form-control form-control-lg" placeholder="Enter password" />
            <input class="p-2" type="checkbox" onclick="myFunction()" style="margin-left: 10px; margin-top: 13px;"> <span style="margin-left: 5px;">Show password</span>
          </div>

          <div class="d-flex justify-content-between align-items-center">
            <button type="submit" name="login" class="btn btn-warning btn-lg enter" style="background-color: #1572e8; color: white; padding-left: 2.5rem; padding-right: 2.5rem;" disabled>Login</button>
            <span id="countdown-timer" style="margin-right: 20px; font-weight: bold; color: #ff0000;"></span> 
          </div>

        </form>
      </div>
    </div>
  </div>
</section>

<script>
    // Generate reCAPTCHA token before submitting the form
    document.getElementById('login-form').addEventListener('submit', function(event) {
        event.preventDefault();
        grecaptcha.ready(function() {
            grecaptcha.execute('6LcXBZQqAAAAAOHJGRgXUsIXpoe44YNomw8bjD5o', { action: 'login' }).then(function(token) {
                document.getElementById('g-recaptcha-response').value = token;
                document.getElementById('login-form').submit();
            });
        });
    });
</script>

<!-- jQuery -->
<script src="assets/js/jquery-3.2.1.min.js"></script>
<!-- Bootstrap Core JS -->
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<!-- Custom JS -->
<script src="assets/js/script.js"></script>
<script type="text/javascript">
  function myFunction() {
    var x = document.getElementById("psw");
    if (x.type === "password") {
      x.type = "text";
    } else {
      x.type = "password";
    }
  }

  const attemptCount = <?php echo $_SESSION['attempt_count']; ?>;
  const lockoutTimeRemaining = <?php echo max(0, $_SESSION['lockout_time'] - time()); ?>; // Remaining lockout time in seconds

  const loginButton = document.querySelector('button[name="login"]');
  const countdownTimer = document.getElementById('countdown-timer');

  if (attemptCount >= 3 && lockoutTimeRemaining > 0) {
    let remainingTime = lockoutTimeRemaining;

    const updateTimer = () => {
      const minutes = Math.floor(remainingTime / 60);
      const seconds = remainingTime % 60;
      countdownTimer.textContent = `${minutes}:${seconds.toString().padStart(2, '0')} remaining`;

      if (remainingTime > 0) {
        remainingTime--;
      } else {
        // Enable the login button once the timer ends
        loginButton.disabled = false;
        countdownTimer.textContent = '';
        clearInterval(timerInterval);
      }
    };

    // Disable login button initially
    loginButton.disabled = true;

    // Start the countdown
    const timerInterval = setInterval(updateTimer, 1000);
    updateTimer();
  } else {
    // Enable the login button if no lockout
    loginButton.disabled = false;
  }
</script>
</body>
</html>
