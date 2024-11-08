<?php
session_start();
include('config/connect.php'); // Database connection

require 'phpmailer/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = "";
$otpCode = rand(100000, 999999); // Generate OTP

if (isset($_POST['forgot_password'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if the email exists and is verified
    $query = "SELECT * FROM customer WHERE email='$email' AND verified=1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['otp'] = $otpCode; // Store OTP in session for verification
        $_SESSION['reset_email'] = $email;

        // Send OTP email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'riomanagement123@gmail.com';
            $mail->Password = 'vilenbrazfimbkbl';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->setFrom('riomanagement123@gmail.com', '3J-E');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Your Password Reset OTP Code';
            $mail->Body = "<p>Your OTP code is: <strong>$otpCode</strong></p>";

            $mail->send();
            $_SESSION['success'] = true; // Trigger SweetAlert success message
        } catch (Exception $e) {
            $error = "Failed to send OTP. Please try again.";
        }
    } else {
        $error = "Invalid email or unverified account.";
    }
}
?>
<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>

    <!-- SweetAlert CSS and JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- CSS Styles -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group, .form-floating {
            margin-bottom: 15px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: #80bdff;
            outline: none;
        }

        .btn {
            padding: 10px;
            width: 100%;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>

    <!-- Back Button with Left Arrow Icon -->
    <div class="container">
        <a href="index.php" class="btn btn-light back-button" style="background-color: #1572e8; color: white;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        
        <h2>Forgot Password?</h2>
        <p class="error"><?php echo $error; ?></p>
        <form method="POST">
            <div class="form-group">             
                <label for="email">Enter your registered email:</label><br><br>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" name="forgot_password" class="btn btn-primary">Send OTP</button>
        </form>
    </div>

    <!-- Font Awesome Script for Icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <script>
        // SweetAlert Success Message
        <?php if (isset($_SESSION['success']) && $_SESSION['success'] == true) { ?>
            Swal.fire({
                icon: 'success',
                title: 'OTP sent!',
                text: 'Check your email to reset your password.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = "verify_reset_code.php"; // Redirect to verification page
            });
            <?php unset($_SESSION['success']); ?>
        <?php } ?>
    </script>

</body>
</html>
