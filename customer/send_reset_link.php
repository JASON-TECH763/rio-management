<?php
session_start();
include('config/connect.php');
require 'phpmailer/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $otpCode = rand(100000, 999999); // Generate a random OTP code
    $expiry = date("Y-m-d H:i:s", strtotime("+1 hour")); // Token expiry time set to 1 hour

    // Update the database with the OTP code and expiry time
    $query = "UPDATE customer SET reset_token=?, token_expiry=? WHERE email=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sss", $otpCode, $expiry, $email);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        // Send OTP email
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->SMTPDebug = 0; // Disable debug output
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'riomanagement123@gmail.com'; // Your email
            $mail->Password   = 'vilenbrazfimbkbl'; // Your password or app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            // Recipients
            $mail->setFrom('riomanagement123@gmail.com', '3J-E');
            $mail->addAddress($email); // Add recipient

            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'Your Password Reset OTP';
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
                        <h2>Password Reset Request</h2>
                        <p>Your OTP code for password reset is:</p>
                        <p class='otp'>$otpCode</p>
                        <p>Please use this code to verify your request.</p>
                    </div>
                </body>
            </html>";

            $mail->send();
            $_SESSION['otp'] = $otpCode; // Store OTP in session for verification
            $_SESSION['reset_email'] = $email; // Store email in session for later use

            // Redirect to OTP verification page
            header("Location: verify_reset_code.php");
            exit();
        } catch (Exception $e) {
            $_SESSION['status'] = "error";
            $_SESSION['message'] = "Failed to send OTP. Mailer Error: {$mail->ErrorInfo}";
            header("Location: forgot_password.php");
            exit();
        }
    } else {
        echo "No account found with that email.";
    }
}
?>
