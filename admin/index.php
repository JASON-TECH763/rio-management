<?php  
session_start();
include('config/connect.php');
$error = "";
$max_attempts = 5; // Maximum login attempts
$lockout_time = 30; // Lockout time in seconds

// Initialize attempts if not set
if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

// Check if the user is locked out
if ($_SESSION['attempts'] >= $max_attempts) {
    if ((time() - $_SESSION['last_attempt_time']) < $lockout_time) {
        $error = "Too many login attempts. Please try again later.";
    } else {
        // Reset attempts after lockout time
        $_SESSION['attempts'] = 0;
    }
}

if (isset($_POST['login']) && $_SESSION['attempts'] < $max_attempts) {
    // Sanitize inputs
    $user = trim($_POST['uname']);
    $pass = trim($_POST['pass']);

    // Basic input validation
    if (!empty($user) && !empty($pass)) {
        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("SELECT uname, password FROM admin WHERE uname = ?");
        $stmt->bind_param("s", $user); // Bind parameter safely
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            // Verify the hashed password
            if (password_verify($pass, $row['password'])) {
                $_SESSION['uname'] = $user;
                $_SESSION['attempts'] = 0; // Reset attempts on successful login
                header("Location: dashboard.php");
                exit(); // Stop further script execution
            } else {
                $_SESSION['attempts']++;
                $error = '* Invalid Username or Password';
            }
        } else {
            $_SESSION['attempts']++;
            $error = '* Invalid Username or Password';
        }

        $_SESSION['last_attempt_time'] = time(); // Update last attempt time
    } else {
        $error = "* Please Fill all the Fields!";
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
      }
    </style>
    <script>
        // Protect against XSS
        function sanitize(str) {
            return str.replace(/</g, "&lt;").replace(/>/g, "&gt;");
        }
    </script>
</head>
<a href="https://rio-lawis.com/" class="btn btn-light back-button" 
style="background-color: #1572e8; color: white; padding-left: 5px; padding-right: 5px;">Back to Site</a>
<body>
<section class="vh-100" style="background-color: #2a2f5b; color: white;">
  <div class="container-fluid h-custom">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-md-9 col-lg-6 col-xl-5 position-relative">
        <img src="assets/img/1bg.jpg" class="img-fluid" alt="Sample image">
      </div>
      <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
        <form method="post">
          <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
            <div class="d-flex align-items-center mb-3 pb-1">
              <span class="h1 fw-bold mb-0" style="color: #FEA116;">RMS Login</span>
              <i class="fa fa-heart fa-2x me-3"></i>
            </div>
          </div>
          <p style="color:red;"><?php echo $error; ?></p>
          <div class="form-outline mb-4">
            <label class="form-label" name="uname" for="user">Username</label>
            <input type="text" name="uname" id="user" class="form-control form-control-lg" placeholder="Enter username" required />
          </div>
          <div class="form-outline mb-3">
            <label class="form-label" for="pass">Password</label>
            <input type="password" name="pass" id="psw" class="form-control form-control-lg" placeholder="Enter password" required />
            <input class="p-2" type="checkbox" onclick="myFunction()" style="margin-left: 10px; margin-top: 13px;"> <span style="margin-left: 5px;">Show password</span>
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

<script>
// Disable right-click
document.addEventListener('contextmenu', function (e) {
    e.preventDefault();
});

// Disable F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
document.onkeydown = function (e) {
    if (
        e.key === 'F12' ||
        (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J')) ||
        (e.ctrlKey && e.key === 'U')
    ) {
        e.preventDefault();
    }
};

// Disable selecting text
document.onselectstart = function (e) {
    e.preventDefault();
};
</script>
</body>
</html>
