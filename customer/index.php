<?php 
session_start();
include('config/connect.php');
$error = "";

// Initialize session variables if not set
if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
    $_SESSION['last_failed_attempt'] = time();
}

if (isset($_POST['login'])) {
    $user = $_REQUEST['uname'];
    $pass = $_REQUEST['pass'];

    // Sanitize input
    $user = mysqli_real_escape_string($conn, $user);

    // Check if the login button should be disabled
    if ($_SESSION['attempts'] >= 3 && (time() - $_SESSION['last_failed_attempt']) < 180) {
        $error = 'You have reached the maximum login attempts. Please try again after 3 minutes.';
    } else {
        if (!empty($user) && !empty($pass)) {
            // Prepare statement for login
            $query = "SELECT email, password, verified FROM customer WHERE email=?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "s", $user);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $row = mysqli_fetch_array($result);

            if ($row) {
                // Check if password is correct
                if (password_verify($pass, $row['password'])) {
                    // Reset attempts on successful login
                    $_SESSION['attempts'] = 0;
                    $_SESSION['last_failed_attempt'] = time(); // Reset failed attempt timer

                    // Check if account is verified
                    if ($row['verified'] == 1) {
                        // Login successful, store email and verified status in session
                        $_SESSION['email'] = $row['email'];
                        $_SESSION['verified'] = $row['verified'];
                        header("Location: order.php"); // Redirect to order page
                        exit(); // Always call exit after a header redirect
                    } else {
                        $_SESSION['status'] = "error";
                        $_SESSION['message'] = "Your account is not verified. Please use a verified email account.";
                        header("Location: index.php");
                        exit();
                    }
                } else {
                    // Increment attempts on failed login
                    $_SESSION['attempts']++;
                    $_SESSION['last_failed_attempt'] = time();
                    $error = '* Invalid Email or Password';
                }
            } else {
                // Increment attempts on failed login
                $_SESSION['attempts']++;
                $_SESSION['last_failed_attempt'] = time();
                $error = '* Invalid Email or Password';
            }
        } else {
            $error = '* Please fill all the fields!';
        }
    }
}
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

    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-md-9 col-lg-6 col-xl-5 position-relative">
        <img src="assets/img/1bg.jpg" class="img-fluid" alt="Sample image">
      </div>
      <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
        <form method="post">
          <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
            <div class="d-flex align-items-center mb-3 pb-1">
              <span class="h1 fw-bold mb-0" style="color: #FEA116;">Customer Login</span>
            </div>
          </div>

          <p style="color:red;"><?php echo $error; ?></p>

          <div class="form-outline mb-4">
            <label class="form-label" for="user">Email</label>
            <input type="text" name="uname" id="user" class="form-control form-control-lg" placeholder="Enter email" />
          </div>

          <div class="form-outline mb-3">
            <label class="form-label" for="pass">Password</label>
            <input type="password" name="pass" id="psw" class="form-control form-control-lg" placeholder="Enter password" />
            <input class="p-2" type="checkbox" onclick="myFunction()" style="margin-left: 10px; margin-top: 13px;"> 
            <span style="margin-left: 5px;">Show password</span>
          </div>

          <!-- Forgot Password Link -->
          <div class="d-flex justify-content-between align-items-center mb-4">
              <a href="forgot_password.php" style="color: #FEA116;">Forgot Password?</a>
          </div>

          <!-- Countdown Timer (Above Sign Up Button) -->
          <div id="countdown-timer" style="color: #FEA116; text-align: center; margin-bottom: 10px;"></div>

          <div class="d-flex justify-content-between align-items-center">
            <button type="submit" name="login" class="btn btn-warning btn-lg enter" style="background-color: #1572e8; color: white; padding-left: 2.5rem; padding-right: 2.5rem;">Login</button>
            <a href="create_account.php" class="btn btn-warning btn-lg enter" style="background-color: #1572e8; color: white; padding-left: 2.5rem; padding-right: 2.5rem;">Sign Up</a>
          </div>
          
        </form>
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

  $(document).ready(function() {
    var countdownTimer = $('#countdown-timer');
    var lastFailedAttempt = <?php echo $_SESSION['last_failed_attempt']; ?>; // Timestamp of the last failed login
    var attempts = <?php echo $_SESSION['attempts']; ?>; // Number of failed attempts

    // If the user has exceeded the limit of failed attempts
    if (attempts >= 3 && (Math.floor(Date.now() / 1000) - lastFailedAttempt) < 180) {
        // Disable the login button and start the countdown
        $("button[name='login']").prop('disabled', true);
        
        // Calculate the remaining time for the countdown
        var remainingTime = 180 - (Math.floor(Date.now() / 1000) - lastFailedAttempt);
        
        // Function to update the countdown
        function updateCountdown() {
            var minutes = Math.floor(remainingTime / 60);
            var seconds = remainingTime % 60;
            if (seconds < 10) seconds = '0' + seconds;

            countdownTimer.text('Please wait: ' + minutes + ':' + seconds);

            remainingTime--;

            if (remainingTime < 0) {
                // Enable the login button when countdown finishes
                $("button[name='login']").prop('disabled', false);
                countdownTimer.text('');
            }
        }

        setInterval(updateCountdown, 1000); // Update countdown every second
    }
  });
</script>

</body>
</html>
