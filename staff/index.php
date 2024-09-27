<?php 
  session_start();
  include('config/connect.php');
  $error = "";

  // Handle staff login
  if (isset($_POST['login'])) {
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
        // Login successful, store session information
        $_SESSION['staff_email'] = $row['staff_email'];
        header("Location: dashboard.php"); // Redirect to staff dashboard
        exit(); // Always call exit after a header redirect
      } else {
        $error = '* Invalid Email or Password';
      }
    } else {
      $error = '* Please fill all the fields!';
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
            <div class="d-flex align-items e-center mb-3 pb-1">
              <span class="h1 fw-bold mb-0" style="color: #FEA116;">Staff Login</span>
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
          <div class="d-flex justify-content-between align-items-center">
            <button type="submit" name="login" class="btn btn-warning btn-lg enter" style="background-color: #1572e8; color: white; padding-left: 2.5rem; padding-right: 2.5rem;">Login</button>
        
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

        // Disable developer tools
        function disableDevTools() {
            if (window.devtools.isOpen) {
                window.location.href = "about:blank";
            }
        }

        // Check for developer tools every 100ms
        setInterval(disableDevTools, 100);

        // Disable selecting text
        document.onselectstart = function (e) {
            e.preventDefault();
        };
</script>
</body>
</html>
