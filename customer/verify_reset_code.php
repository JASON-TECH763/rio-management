<?php
session_start();
include('config/connect.php');

if (!isset($_SESSION['otp']) || !isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

$error = "";
$otp_success = false;
$otp_error = "";

if (isset($_POST['verify_otp'])) {
    $otp = $_POST['otp'];
    if ($otp == $_SESSION['otp']) {
        $_SESSION['otp_verified'] = true;
        header("Location: reset_password.php"); // Redirect to reset_password.php
        exit();
    } else {
        $otp_error = "Invalid OTP. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #2a2f5b;
        }
        .form-box {
            background-color: #3b4272;
            padding: 30px;
            border-radius: 10px;
            width: 400px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-warning {
            transition: background-color 0.3s;
            font-size: 1rem;
            padding: 7px;
        }
        .btn-warning:hover {
            background-color: #0e5bb5;
            font-size: 1.2rem;
            padding: 10px;
        }
        .form-control {
            font-size: 1.2rem;
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <center>
            <div class="form-box">
                <a href="http://localhost/RIO" class="btn btn-light position-absolute" style="top: 10px; left: 10px; background-color: transparent; color: #1572e8;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <img src="assets/img/1bg.jpg" alt="Logo" class="img-fluid mb-4" style="max-width: 100px; border-radius: 50%;">
                
                <form method="post">
                    <h1 style="color: #FEA116; font-size: 1.7rem;" class="mb-5">Verify OTP</h1>
                    <span style="color: white; font-size: 1.1rem; margin-bottom: 20px;">Enter OTP number to verify your account:</span>
                    
                    <div class="form-outline mb-3">
                        <input type="text" name="otp" id="otp" class="form-control form-control-lg" placeholder="Enter OTP" required />
                    </div>
                    <br><br>
                    <div class="d-flex justify-content-center">
                        <button type="submit" name="verify_otp" class="btn btn-warning btn-lg" style="background-color: #1572e8; color: white; padding-left: 2.5rem; padding-right: 2.5rem;">Verify OTP</button>
                    </div>
                </form>
            </div>
        </center>
    </div>

    <script>
        <?php if (!empty($otp_error)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '<?php echo $otp_error; ?>'
            });
        <?php endif; ?>
    </script>
</body>
</html>
