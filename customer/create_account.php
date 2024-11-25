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

    // Validate terms agreement
    if (!isset($_POST['terms_agreement'])) {
        $error = "You must agree to the Terms and Conditions.";
    }

    // Hash the password (recommended for security)
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    if (empty($error) && !empty($name) && !empty($email) && !empty($password) && !empty($phone)) {
        // Check if the email already exists
        $query = "SELECT * FROM customer WHERE email='$email'";
        $result = mysqli_query($conn, $query);
        $num_rows = mysqli_num_rows($result);

        if ($num_rows > 0) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'An account with this email already exists.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                });
            </script>";
        } else {
            // Continue with the OTP email sending process
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        .form-control {
            width: 100%;
            padding: 10px;
            padding-right: 40px; /* Space for the icon */
            box-sizing: border-box;
        }
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
    </style>
</head>

<body>
    <section class="vh-100" style="background-color: #2a2f5b; color: white;">
        <div class="container-fluid h-custom">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                    <!-- Box container around the form -->
                    <div class="form-box text-center position-relative" style="background-color: #3b4272; padding: 30px; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);">
                        
                        <!-- Icon button for back -->
                        <a href="https://rio-lawis.com/customer/" class="btn btn-light position-absolute" style="top: 10px; left: 10px; background-color: transparent; color:  #1572e8;">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        
                        <!-- Logo image at the top -->
                        <img src="assets/img/1bg.jpg" alt="Logo" class="img-fluid mb-4" style="max-width: 100px; border-radius: 50%;">
                        
                        <form method="post">
                            <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                                <span class="h1 fw-bold mb-0" style="color: #FEA116; text-align: center;">Create Customer Account</span>
                            </div>
                            <p style="color:red;"><?php echo $error; ?></p>
                            <div class="form-outline mb-4">
                                <div class="form-group">
    <label for="name"></label>
    <input type="text" name="name" id="name" class="form-control" placeholder="Enter your name" required 
    pattern="[A-Za-zÀ-ž' -]+" title="Name can contain only letters, hyphens, apostrophes, and spaces." oninput="validateName()">
</div>

<script>
    function validateName() {
        var nameField = document.getElementById('name');
        var value = nameField.value;

        // Regular expression to allow multiple capitalized words with letters, hyphens, apostrophes, and spaces
        var regex = /^([A-Z][a-zÀ-ž'-]+\s?)+$/;

        if (!regex.test(value)) {
            nameField.setCustomValidity("");
        } else {
            nameField.setCustomValidity(""); // Clear the message if valid
        }
    }
</script>

<?php
// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];

    // Sanitize input to remove any HTML or script tags
    $name_sanitized = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

    // Validate the input format with multiple capitalized words
    if (!preg_match("/^([A-Z][a-zÀ-ž'-]+\s?)+$/", $name)) {
        echo '<div class="alert alert-danger">Invalid input: Please enter a valid name</div>';
    } else if ($name !== $name_sanitized) {
        echo '<div class="alert alert-danger">Invalid input: HTML or script tags are not allowed.</div>';
    } 
}
?>

                                <br>
                                <div class="form-group">
                                    <label for="email"></label>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
                                </div>
                                <br>
                                <div class="form-group position-relative">
    <label for="password"></label>
    <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" minlength="8" pattern="(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}" title="Password must contain at least one uppercase letter, one number, and one special character" required>
    <span class="password-toggle" style="cursor: pointer; position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
        <i class="fas fa-eye" id="togglePassword" onclick="togglePasswordVisibility()"></i>
    </span>
</div>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById("password");
        const toggleIcon = document.getElementById("togglePassword");
        
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            toggleIcon.classList.remove("fa-eye-slash");
            toggleIcon.classList.add("fa-eye");
        } else {
            passwordInput.type = "password";
            toggleIcon.classList.remove("fa-eye");
            toggleIcon.classList.add("fa-eye-slash");
        }
    }
</script>

                                <br>
                                <div class="form-group">
                                    <label for="phone"></label>
                                    <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter your phone number" pattern="09\d{9}" maxlength="11" title="Phone number must start with '09' and be exactly 11 digits" required>
                                </div>
                                <br>
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="termsCheckbox" name="terms_agreement" required>
                                    <label class="form-check-label" for="termsCheckbox">
                                        I agree to the <a href="#" data-toggle="modal" data-target="#termsModal" style="color: #1572e8;">Terms and Conditions</a>
                                    </label>
                                </div>
                                <button type="submit" name="create_account" class="btn btn-warning btn-lg" style="background-color: #1572e8; color: white;">Send verification code</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Terms and Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h6>Room Reservation and Restaurant Management System Terms of Service</h6>
                    <ol>
                        <li><strong>Account Usage</strong>
                            <ul>
                                <li>You must provide accurate and current information during registration.</li>
                                <li>You are responsible for maintaining the confidentiality of your account credentials.</li>
                            </ul>
                        </li>
                        <li><strong>Reservation Policies</strong>
                            <ul>
                                <li>Reservations are subject to availability and confirmation.</li>
                                <li>Cancellations must be made at least 24 hours in advance.</li>
                                <li>Late cancellations may incur a cancellation fee.</li>
                            </ul>
                        </li>
                        <li><strong>Privacy and Data Protection</strong>
                            <ul>
                                <li>Personal information will be handled in accordance with our privacy policy.</li>
                                <li>We do not share personal data with third parties without consent.</li>
                            </ul>
                        </li>
                    </ol>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('form').on('submit', function(e) {
                if (!$('#termsCheckbox').is(':checked')) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terms and Conditions',
                        text: 'Please agree to the Terms and Conditions before proceeding.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                    e.preventDefault();
                }
            });
        });
    </script>

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