<?php 
  session_start();
  include('config/connect.php');
  $error = "";

  // Initialize login attempts session variable
  if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['lockout_time'] = null;
  }

  // Check lockout status
  if (isset($_SESSION['lockout_time'])) {
    $current_time = time();
    if ($current_time - $_SESSION['lockout_time'] >= 180) { // 3 minutes
      $_SESSION['login_attempts'] = 0; // Reset attempts after lockout period
      $_SESSION['lockout_time'] = null;
    } else {
      $remaining_time = 180 - ($current_time - $_SESSION['lockout_time']);
    }
  }

  // Handle staff login
  if (isset($_POST['login']) && !isset($_SESSION['lockout_time'])) {
    $user = $_REQUEST['uname'];
    $pass = $_REQUEST['pass'];

    // Sanitize input
    $user = mysqli_real_escape_string($conn, $user);

    if (!empty($user) && !empty($pass)) {
      // Prepare statement for login
      $query = "SELECT staff_email, staff_password FROM rpos_staff WHERE staff_email=?";
      $stmt = mysqli_prepare($conn, $query);
      mysqli_stmt_bind_param($stmt, "s", $user);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);

      $row = mysqli_fetch_array($result);

      if ($row && $pass === $row['staff_password']) {
        // Login successful, reset attempts
        $_SESSION['login_attempts'] = 0;
        $_SESSION['staff_email'] = $row['staff_email'];
        header("Location: dashboard.php"); // Redirect to staff dashboard
        exit();
      } else {
        $_SESSION['login_attempts']++;
        if ($_SESSION['login_attempts'] >= 3) {
          $_SESSION['lockout_time'] = time(); // Start lockout timer
          $error = 'Too many failed attempts. Please try again after 3 minutes.';
          $remaining_time = 180; // Start countdown at 3 minutes
        } else {
          $error = '* Invalid Email or Password';
        }
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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

        @media (max-width: 450px) {
            .back-button {
                top: 10px;
                left: 10px;
                padding: 6px 10px;
            }
            .back-button i {
                font-size: 0.9rem;
            }
        }
        #timer {
            color: red;
            font-size: 18px;
            font-weight: bold;
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
            <input type="text" name="uname" id="user" class="form-control form-control-lg" placeholder="Enter email" 
            <?php echo isset($_SESSION['lockout_time']) ? 'disabled' : ''; ?> />
          </div>
          <div class="form-outline mb-3">
            <label class="form-label" for="pass">Password</label>
            <input type="password" name="pass" id="psw" class="form-control form-control-lg" placeholder="Enter password" 
            <?php echo isset($_SESSION['lockout_time']) ? 'disabled' : ''; ?> />
            <input class="p-2" type="checkbox" onclick="myFunction()" style="margin-left: 10px; margin-top: 13px;"> <span style="margin-left: 5px;">Show password</span>
          </div>
          <div class="d-flex justify-content-between align-items-center">
            <button type="submit" name="login" class="btn btn-warning btn-lg enter" 
              style="background-color: #1572e8; color: white; padding-left: 2.5rem; padding-right: 2.5rem;" 
              <?php echo isset($_SESSION['lockout_time']) ? 'disabled' : ''; ?>>
              Login
            </button>
            <p style="color:red;"><?php echo $error; ?></p>
          <?php if (isset($remaining_time)): ?>
            <div id="timer"></div>
          <?php endif; ?>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

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

  // Countdown timer logic
  <?php if (isset($remaining_time)): ?>
    let countdown = <?php echo $remaining_time; ?>;
    const timerElement = document.getElementById('timer');

    function updateTimer() {
      const minutes = Math.floor(countdown / 60);
      const seconds = countdown % 60;
      timerElement.innerHTML = `Try again in ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
      if (countdown > 0) {
        countdown--;
        setTimeout(updateTimer, 1000);
      } else {
        location.reload();
      }
    }
    updateTimer();
  <?php endif; ?>
</script>
</body>
</html>
