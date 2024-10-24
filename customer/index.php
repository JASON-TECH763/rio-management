<?php 
session_start();
include('config/connect.php');
$error = "";

if (isset($_POST['login'])) {
  // Retrieve and sanitize input
  $user = trim($_POST['uname']);
  $pass = trim($_POST['pass']);

  // Use prepared statements to prevent SQL injection
  if (!empty($user) && !empty($pass)) {
      // Prepare a parameterized query to prevent SQL injection
      $query = "SELECT email, password, verified FROM customer WHERE email=?";
      $stmt = mysqli_prepare($conn, $query);

      if ($stmt) {
          // Bind the email (user input) as a parameter to the prepared statement
          mysqli_stmt_bind_param($stmt, "s", $user);
          mysqli_stmt_execute($stmt);
          $result = mysqli_stmt_get_result($stmt);

          // Fetch the user data
          $row = mysqli_fetch_array($result);

          if ($row) {
              // Verify the hashed password
              if (password_verify($pass, $row['password'])) {
                  // Check if the account is verified
                  if ($row['verified'] == 1) {
                      // Login successful
                      $_SESSION['email'] = $row['email'];
                      $_SESSION['verified'] = $row['verified'];  // Set session variable for verified status
                      header("Location: order.php");
                      exit(); // Stop further script execution after redirect
                  } else {
                      $_SESSION['status'] = "error";
                      $_SESSION['message'] = "Your account is not verified. Please use a verified email account.";
                      header("Location: index.php");
                      exit();
                  }
              } else {
                  $error = '* Invalid Email or Password';
              }
          } else {
              $error = '* Invalid Email or Password';
          }
      } else {
          $error = '* Failed to prepare the SQL statement';
      }
  } else {
      $error = '* Please fill all the fields!';
  }
}

// Handle account creation
if (isset($_POST['create_account'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    if (!empty($name) && !empty($email) && !empty($password) && !empty($phone)) {
        // Check if email already exists
        $email_check_query = "SELECT email FROM customer WHERE email = ?";
        $stmt = mysqli_prepare($conn, $email_check_query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $email_check_result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($email_check_result) == 0) {
            // Prepare statement for account creation
            $query = "INSERT INTO customer (name, email, password, phone, date_created) 
                      VALUES (?, ?, ?, ?, NOW())";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $hashed_password, $phone);
            $result = mysqli_stmt_execute($stmt);

            if ($result) {
                echo "<script>alert('Account created successfully! Please login.');</script>";
            } else {
                echo "<script>alert('Error creating account. Please try again.');</script>";
            }
        } else {
            $error = '* Email already exists. Please use a different email.';
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
</head>

<body>
<a href="https://rio-lawis.com/" class="btn btn-light back-button" 
style="background-color: #1572e8; color: white; padding-left: 5px; padding-right: 5px;">Back to Site</a>


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

          <div class="d-flex justify-content-between align-items-center">
            <button type="submit" name="login" class="btn btn-warning btn-lg enter" style="background-color: #1572e8; color: white; padding-left: 2.5rem; padding-right: 2.5rem;">Login</button>
            <a href="create_account.php"  class="btn btn-warning btn-lg enter" style="background-color: #1572e8; color: white; padding-left: 2.5rem; padding-right: 2.5rem;">Create Account</a>
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

<!-- SweetAlert -->
<?php if (isset($_SESSION['status']) && $_SESSION['status'] != ""): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Swal.fire({
        title: '<?php echo ($_SESSION["status"] == "success") ? "Success!" : "Error!"; ?>',
        text: '<?php echo $_SESSION["message"]; ?>',
        icon: '<?php echo $_SESSION["status"]; ?>',
        confirmButtonText: 'OK'
    });
</script>
<?php
    unset($_SESSION['status']);
    unset($_SESSION['message']);
endif;
?>
</body>
</html>
