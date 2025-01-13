<?php
session_start();
include('config/connect.php');

header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; img-src 'self' data:; font-src 'self' https://fonts.googleapis.com https://cdn.jsdelivr.net; frame-ancestors 'none'; form-action 'self'; base-uri 'self';");

require 'phpmailer/vendor/autoload.php'; // Include PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if the email is riomanagement123@gmail.com
    if ($email === 'riomanagement123@gmail.com') {
        // Generate a unique reset token
        $token = bin2hex(random_bytes(50));

        // Set token expiration (1 hour)
        $update_query = "UPDATE admin SET reset_token='$token', token_expire=DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email='$email'";
        if (mysqli_query($conn, $update_query)) {
            // Initialize PHPMailer and configure settings
            $mail = new PHPMailer(true);
            try {
                // SMTP Server configuration
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';           // Set the SMTP server
                $mail->SMTPAuth   = true;
                $mail->Username   = 'riomanagement123@gmail.com';  // Your SMTP username
                $mail->Password   = 'vilenbrazfimbkbl';         // Your SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Enable SSL encryption
                $mail->Port       = 465;                        // TCP port

                // Sender and recipient settings
                $mail->setFrom('riomanagement123@gmail.com', 'Rio Management System');
                $mail->addAddress($email, 'Admin'); // Add recipient email

                // Email content
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                $mail->Body    = "
                    <p>Hi Admin,</p>
                    <p>You have requested to reset your password. Click the link below to reset your password:</p>
                    <a href='http://rio-lawis.com/admin/reset_password.php?token=$token'>Reset Password</a>
                    <p>This link will expire in 1 hour.</p>
                ";

                // Send the email
                $mail->send();
                $_SESSION['success'] = 'Password reset link sent! Check your email to reset your password.';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Mailer Error: ' . $mail->ErrorInfo;
            }
        } else {
            $_SESSION['error'] = 'Failed to update the reset token in the database.';
        }
    } else {
        // If email is not riomanagement123@gmail.com
        $_SESSION['error'] = 'Invalid email. Only the admin can reset the password.';
    }

    // Redirect to avoid form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
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

        .form-group {
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
    </style>
</head>
<body>

<div class="container">
    <a href="index.php" class="btn btn-light back-button" style="background-color: #1572e8; color: white;">
        <i class="fas fa-arrow-left"></i> Back
    </a>
    <h2>Forgot Password ?</h2>
    <form method="post">
        <div class="form-group">             
            <label for="email">Enter your registered email:</label><br><br>
            <input type="email" name="email" class="form-control" required>
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Send Reset Link</button>
    </form>
</div>

<script>
    <?php if (isset($_SESSION['success'])) { ?>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '<?php echo $_SESSION['success']; ?>',
            confirmButtonText: 'OK'
        });
        <?php unset($_SESSION['success']); ?>
    <?php } ?>

    <?php if (isset($_SESSION['error'])) { ?>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?php echo $_SESSION['error']; ?>',
            confirmButtonText: 'OK'
        });
        <?php unset($_SESSION['error']); ?>
    <?php } ?>
</script>

</body>
</html>
