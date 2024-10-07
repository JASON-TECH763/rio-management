<?php 
  session_start();
  include('config/connect.php');

  // Anti-Brute Force: Track login attempts
  if(!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
  }
  
  // Define maximum login attempts and block duration
  $max_attempts = 3;
  $block_duration = 15 * 60; // 15 minutes
  
  $error = "";

  if(isset($_POST['login'])) {
    if($_SESSION['login_attempts'] >= $max_attempts && time() - $_SESSION['last_attempt_time'] < $block_duration) {
      $error = "* Too many login attempts. Please try again after 15 minutes.";
    } else {
      // CSRF token validation
      if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "* Invalid CSRF token.";
      } else {
        $user = $_REQUEST['uname'];
        $pass = $_REQUEST['pass'];
        // $pass = sha1($pass); // Consider hashing passwords

        // SQL Injection Prevention: Prepared statements
        if (!empty($user) && !empty($pass)) {
          $query = $conn->prepare("SELECT uname, password FROM admin WHERE uname = ? AND password = ?");
          $query->bind_param("ss", $user, $pass);
          $query->execute();
          $result = $query->get_result();
          
          if($result->num_rows == 1) {
            $_SESSION['uname'] = $user;
            $_SESSION['login_attempts'] = 0; // Reset attempts on successful login
            header("Location: dashboard.php");
          } else {
            $_SESSION['login_attempts']++; // Increment failed login attempts
            $_SESSION['last_attempt_time'] = time(); // Set the last attempt time
            $error = "* Invalid Username or Password";
          }
        } else {
          $error = "* Please fill all fields!";
        }
      }
    }
  }

  // Generate CSRF token
  if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Rio Management System</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
      /* Additional styles */
    </style>
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
          <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
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
<script>
  function myFunction() {
    var x = document.getElementById("psw");
    if (x.type === "password") {
      x.type = "text";
    } else {
      x.type = "password";
    }
  }

  // Disable right-click and developer tools
  document.addEventListener('contextmenu', function (e) {
    e.preventDefault();
  });

  document.onkeydown = function (e) {
    if (
      e.key === 'F12' ||
      (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J')) ||
      (e.ctrlKey && e.key === 'U')
    ) {
      e.preventDefault();
    }
  };
</script>
</body>
</html>
