<?php 
session_start();
include('config/connect.php');

// Initialize session variables if not set
if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
    $_SESSION['last_failed_attempt'] = time();
}

// SweetAlert error variable
$sweetalert_error = "";

// Check if login button should be disabled
if ($_SESSION['attempts'] >= 3 && (time() - $_SESSION['last_failed_attempt']) < 180) {
    $sweetalert_error = 'You have reached the maximum login attempts. Please try again after 3 minutes.';
} else {
    if (isset($_POST['login'])) {
        $user = $_REQUEST['uname'];
        $pass = $_REQUEST['pass'];

        // Sanitize input
        $user = mysqli_real_escape_string($conn, $user);

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
                    $_SESSION['last_failed_attempt'] = time();

                    // Check if account is verified
                    if ($row['verified'] == 1) {
                        $_SESSION['email'] = $row['email'];
                        $_SESSION['verified'] = $row['verified'];
                        header("Location: order.php");
                        exit();
                    } else {
                        $_SESSION['status'] = "error";
                        $_SESSION['message'] = "Your account is not verified. Please use a verified email account.";
                        header("Location: index.php");
                        exit();
                    }
                } else {
                    $_SESSION['attempts']++;
                    $_SESSION['last_failed_attempt'] = time();
                    $sweetalert_error = '* Invalid Email or Password';
                }
            } else {
                $_SESSION['attempts']++;
                $_SESSION['last_failed_attempt'] = time();
                $sweetalert_error = '* Invalid Email or Password';
            }
        } else {
            $sweetalert_error = '* Please fill all the fields!';
        }
    }
}
?>

<!-- Include SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
<?php if (!empty($sweetalert_error)): ?>
    Swal.fire({
        icon: 'error',
        title: 'Login Failed',
        text: '<?php echo $sweetalert_error; ?>',
    });
<?php endif; ?>
</script>


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

    <div class="container-fluid h-custom">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-md-9 col-lg-6 col-xl-5 position-relative">
                <img src="assets/img/1bg.jpg" class="img-fluid" alt="Sample image">
            </div>
            <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                <form method="post">
                    <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                        <div class="d-flex align-items e-center mb-3 pb-1">
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
                        <input class="p-2" type="checkbox" onclick="myFunction()" style="margin-left: 10px; margin-top: 13px;"> <span style="margin-left: 5px;">Show password</span>
                    </div>
                      <!-- Forgot Password Link -->
          <div class="d-flex justify-content-between align-items-center mb-4">
              <a href="forgot_password.php" style="color: #FEA116;">Forgot Password?</a>
              <span id="countdown-timer" style="margin-right: 20px; font-weight: bold; color: #ff0000;"></span> 
              
          </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="submit" name="login" class="btn btn-warning btn-lg enter" id="login-btn" style="background-color: #1572e8; color: white; padding-left: 2.5rem; padding-right: 2.5rem;" <?php echo ($_SESSION['attempts'] >= 3 && (time() - $_SESSION['last_failed_attempt']) < 180) ? 'disabled' : ''; ?>>Login</button>
                        <a href="create_account.php" class="btn btn-warning btn-lg enter" style="background-color: #1572e8; color: white; padding-left: 2.5rem; padding-right: 2.5rem;">Sign Up</a>
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

  const attemptCount = <?php echo $_SESSION['attempts']; ?>;
    const lockoutTimeRemaining = <?php echo max(0, 180 - (time() - $_SESSION['last_failed_attempt'])); ?>; // Remaining lockout time in seconds
    const loginButton = document.getElementById('login-btn');
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
                loginButton.disabled = false;
                countdownTimer.textContent = '';
                clearInterval(timerInterval);
            }
        };

        loginButton.disabled = true;
        const timerInterval = setInterval(updateTimer, 1000);
        updateTimer();
    }
</script>

</body>
</html>
