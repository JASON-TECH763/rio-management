<?php
session_start();
include('config/connect.php'); // Include your database connection file

require 'phpmailer/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = "";
$otpCode = rand(100000, 999999); // Generate a random OTP code

if (isset($_POST['create_account'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];

    // Sanitize input to prevent SQL injection
    $name = mysqli_real_escape_string($conn, $name);
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);
    $phone = mysqli_real_escape_string($conn, $phone);

    // Hash the password (recommended for security)
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    if (!empty($name) && !empty($email) && !empty($password) && !empty($phone)) {
        // Check if the email already exists
        $query = "SELECT * FROM customer WHERE email='$email'";
        $result = mysqli_query($conn, $query);
        $num_rows = mysqli_num_rows($result);

        if ($num_rows > 0) {
            $error = "An account with this email already exists.";
        } else {
            // Store user data temporarily in the session
            $_SESSION['temp_account'] = [
                'name' => $name,
                'email' => $email,
                'password' => $hashed_password,
                'phone' => $phone,
            ];

            // Send OTP email
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->SMTPDebug = 0; // Disable debug output
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'riomanagement123@gmail.com';
                $mail->Password   = 'vilenbrazfimbkbl';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;

                // Recipients
                $mail->setFrom('riomanagement123@gmail.com', '3J-E');
                $mail->addAddress($email, $name); // Add a recipient

                // Email content
                $mail->isHTML(true);
                $mail->Subject = 'Your OTP Code';
                $mail->Body = "<html>
                    <head>
                        <style>
                            body { font-family: Arial, sans-serif; }
                            .container { background-color: #f0f0f0; padding: 20px; }
                            .otp { font-size: 20px; font-weight: bold; color: #333; }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <h2>Hello, $name</h2>
                            <p>Your OTP code is:</p>
                            <p class='otp'>$otpCode</p>
                            <p>Please use this code to verify your account.</p>
                        </div>
                    </body>
                </html>";

                $mail->send();
                $_SESSION['otp'] = $otpCode; // Store OTP in session for verification

                // Redirect to verify page
                header("Location: verify_code.php");
                exit();
            } catch (Exception $e) {
                $_SESSION['status'] = "error";
                $_SESSION['message'] = "Failed to send OTP. Mailer Error: {$mail->ErrorInfo}";
                header("Location: create_account.php");
                exit();
            }
        }
    } else {
        $error = "Please fill all the fields.";
    }
}
?>

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
        left: 10px;
    }
</style>

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <a href="http://localhost/RIO" class="btn btn-light back-button" style="background-color: #1572e8; color: white;">Back to Site</a>
    <section class="vh-100" style="background-color: #2a2f5b; color: white;">
        <div class="container-fluid h-custom">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-9 col-lg-6 col-xl-5 position-relative">
                    <img src="assets/img/1bg.jpg" class="img-fluid" alt="Sample image">
                </div>
                <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                    <form method="post">
                        <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                            <span class="h1 fw-bold mb-0" style="color: #FEA116; text-align: center;">Create Customer Account</span>
                        </div>
                        <p style="color:red;"><?php echo $error; ?></p>
                        <div class="form-outline mb-4">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Enter your name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter your phone number" required>
                            </div>
                            <br>
                            <button type="submit" name="create_account" class="btn btn-warning btn-lg" style="background-color: #1572e8; color: white;">Send verification code</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>

    <?php if (isset($_SESSION['status']) && $_SESSION['status'] != ""): ?>
    <script>
        Swal.fire({
            title: '<?php echo ($_SESSION["status"] == "success") ? "Account Created!" : "Error!"; ?>',
            text: '<?php echo $_SESSION["message"]; ?>',
            icon: '<?php echo $_SESSION["status"]; ?>',
            confirmButtonText: 'OK'
        }).then(function() {
            window.location = 'verify_code.php';
        });
    </script>
    <?php
        unset($_SESSION['status']);
        unset($_SESSION['message']);
    endif;
    ?>
</body>
</html>
