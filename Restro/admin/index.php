<?php
session_start();
include('config/config.php');

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['login'])) {
    $admin_email = $_POST['admin_email'];
    $admin_password = sha1(md5($_POST['admin_password'])); // double encrypt to increase security

    // Prepare SQL statement to log in user
    $stmt = $mysqli->prepare("SELECT admin_id, admin_email, admin_password FROM rpos_admin WHERE admin_email = ? AND admin_password = ?");
    if ($stmt === false) {
        die('MySQL prepare error: ' . $mysqli->error);
    }

    // Bind fetched parameters
    $stmt->bind_param('ss', $admin_email, $admin_password);
    $stmt->execute(); // Execute bind 

    // Bind result
    $stmt->bind_result($admin_id, $admin_email, $admin_password);
    $stmt->store_result();
    $rs = $stmt->fetch();
    
    // Check if the user exists in the database
    if ($stmt->num_rows > 0) {
        $_SESSION['admin_id'] = $admin_id;
        header("Location: dashboard.php");
        exit();
    } else {
        $err = "Incorrect Authentication Credentials";
    }

    // Close the statement
    $stmt->close();
}
require_once('partials/_head.php');
?>

<body class="bg-dark">
  <div class="main-content">
    <div class="header bg-gradient-primary py-7">
      <div class="container">
        <div class="header-body text-center mb-7">
          <div class="row justify-content-center">
            <div class="col-lg-5 col-md-6">
              <h1 class="text-white">Rio Admin</h1>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Page content -->
    <div class="container mt--8 pb-5">
      <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
          <div class="card bg-secondary shadow border-0">
            <div class="card-body px-lg-5 py-lg-5">
              <?php if (isset($err)) { echo "<div class='alert alert-danger'>$err</div>"; } ?>
              <form method="post" role="form">
                <div class="form-group mb-3">
                  <div class="input-group input-group-alternative">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                    </div>
                    <input class="form-control" required name="admin_email" placeholder="Email" type="email">
                  </div>
                </div>
                <div class="form-group">
                  <div class="input-group input-group-alternative">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                    </div>
                    <input class="form-control" required name="admin_password" placeholder="Password" type="password">
                  </div>
                </div>
                <div class="custom-control custom-control-alternative custom-checkbox">
                  <input class="custom-control-input" id=" customCheckLogin" type="checkbox">
                  <label class="custom-control-label" for=" customCheckLogin">
                    <span class="text-muted">Remember Me</span>
                  </label>
                </div>
                <div class="text-center">
                  <button type="submit" name="login" class="btn btn-primary my-4">Log In</button>
                </div>
              </form>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-6">
              <!-- <a href="forgot_pwd.php" class="text-light"><small>Forgot password?</small></a> -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Footer -->
  <?php require_once('partials/_footer.php'); ?>
  <!-- Argon Scripts -->
  <?php require_once('partials/_scripts.php'); ?>
</body>

</html>
