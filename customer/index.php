<?php
session_start();
include('config/connect.php');
$error = "";

// Initialize login attempts and timeout if not set
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

if ($_SESSION['login_attempts'] >= 3) {
    // Calculate time elapsed since last attempt
    $time_elapsed = time() - $_SESSION['last_attempt_time'];

    // If 3 minutes have passed, reset login attempts
    if ($time_elapsed > 180) {
        $_SESSION['login_attempts'] = 0;  // Reset attempts
    } else {
        $remaining_time = 180 - $time_elapsed;  // Calculate remaining time for lock
        $error = "Too many login attempts. Please try again after " . ceil($remaining_time / 60) . " minute(s).";
    }
}

if (isset($_POST['login'])) {
    // Reset error message if attempts are less than 3
    $error = '';

    if ($_SESSION['login_attempts'] < 3) {
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
                    // Check if account is verified
                    if ($row['verified'] == 1) {
                        // Login successful, store email and verified status in session
                        $_SESSION['email'] = $row['email'];
                        $_SESSION['verified'] = $row['verified'];  // Ensure this is set
                        $_SESSION['login_attempts'] = 0; // Reset attempts on successful login
                        header("Location: order.php"); // Redirect to order page
                        exit();
                    } else {
                        $_SESSION['status'] = "error";
                        $_SESSION['message'] = "Your account is not verified. Please use a verified email account.";
                        header("Location: index.php");
                        exit();
                    }
                } else {
                    // Increment login attempts on failed login
                    $_SESSION['login_attempts']++;
                    $_SESSION['last_attempt_time'] = time(); // Update last attempt time
                    $error = '* Invalid Email or Password';
                }
            } else {
                $_SESSION['login_attempts']++;
                $_SESSION['last_attempt_time'] = time(); // Update last attempt time
                $error = '* Invalid Email or Password';
            }
        } else {
            $error = '* Please fill all the fields!';
        }
    }
}
?>

<!-- HTML Form -->
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

    <script>
        // Disable the login button if there are more than 2 attempts and show countdown
        window.onload = function() {
            var loginAttempts = <?php echo $_SESSION['login_attempts']; ?>;
            if (loginAttempts >= 3) {
                var remainingTime = <?php echo $remaining_time ?? 0; ?>;
                var loginButton = document.getElementById('login-button');
                loginButton.disabled = true;
                if (remainingTime > 0) {
                    setTimeout(function() {
                        location.reload(); // Reload page after countdown
                    }, remainingTime * 1000);
                }
            }
        };
    </script>

</head>
<body>
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="forgot_password.php" style="color: #FEA116;">Forgot Password?</a>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <button type="submit" name="login" id="login-button" class="btn btn-warning btn-lg enter" style="background-color: #1572e8; color: white; padding-left: 2.5rem; padding-right: 2.5rem;" <?php if ($_SESSION['login_attempts'] >= 3) echo 'disabled'; ?>>Login</button>
                    <a href="create_account.php" class="btn btn-warning btn-lg enter" style="background-color: #1572e8; color: white; padding-left: 2.5rem; padding-right: 2.5rem;">Sign Up</a>
                </div>
            </form>
        </div>
    </div>
</section>
<script src="assets/js/jquery-3.2.1.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
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
</script>
</body>
</html>
