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

    // Validate name format
    if (!preg_match("/^([A-Z][a-zÀ-ž'-]+\s?)+$/", $name)) {
        $error = "Invalid name format. Please use proper capitalization.";
    }
    
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
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #2a2f5b;
            color: white;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .form-container {
            background-color: #3b4272;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
        }
        .form-control {
            background-color: rgba(255,255,255,0.1);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
        }
        .form-control:focus {
            background-color: rgba(255,255,255,0.2);
            color: white;
            border-color: #1572e8;
            box-shadow: none;
        }
        .btn-primary {
            background-color: #1572e8;
            border-color: #1572e8;
        }
        .terms-link {
            color: #1572e8;
            text-decoration: underline;
            cursor: pointer;
        }
        .modal-content {
    background-color: #2a2f5b;
    color: white;
}

.modal-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    justify-content: center;  /* This centers the title */
    text-align: center;
}

.modal-title {
    text-align: center;
    width: 100%;  /* This ensures the title takes full width */
}

/* Adjust the close button position */
.modal-header .close {
    color: white;
    position: absolute;
    right: 1rem;
    padding: 1rem;
    margin: -1rem -1rem -1rem auto;
}
.form-control::placeholder {
    color: rgba(255, 255, 255, 0.7) !important;  /* Makes placeholder text white with some transparency */
}

.form-control {
    color: white !important;  /* Makes input text white */
}

.form-control:-webkit-autofill,
.form-control:-webkit-autofill:hover,
.form-control:-webkit-autofill:focus,
.form-control:-webkit-autofill:active {
    -webkit-text-fill-color: white !important;
    -webkit-box-shadow: 0 0 0 30px #3b4272 inset !important;  /* Matches your form background */
    transition: background-color 5000s ease-in-out 0s;
}

.custom-control-label {
    color: white;  /* Makes checkbox label text white */
}

.terms-link {
    color: #1572e8;  /* Keeps the Terms and Conditions link blue */
}

.password-toggle-icon i {
    color: white;  /* Makes the password toggle icon white */
}
    </style>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="form-container text-center">
                    <a href="index.php" class="btn btn-link position-absolute" style="top: 10px; left: 10px; color: white;">
                        <i class="fas fa-arrow-left"></i>
                    </a>

                    <img src="assets/img/1bg.jpg" alt="Logo" class="img-fluid mb-4 rounded-circle" style="max-width: 100px;">
                    
                    <h2 class="mb-4" style="color: #FEA116;">Create Customer Account</h2>

                    <form method="post" id="createAccountForm">
                    <div class="form-group">
    <input 
        type="text" 
        name="name" 
        class="form-control" 
        placeholder="Full Name" 
        pattern="[A-Za-zÀ-ž' -]+" 
        title="Name can contain only letters, hyphens, apostrophes, and spaces." 
        oninput="formatName(this)">
</div>

<script>
function formatName(input) {
    // Automatically capitalize the first letter of each word
    input.value = input.value
        .toLowerCase() // Convert all to lowercase
        .replace(/\b\w/g, char => char.toUpperCase()); // Capitalize first letter of each word
}
</script>


                        <div class="form-group">
                            <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                        </div>

                        <div class="form-group position-relative">
                            <input type="password" name="password" id="password" class="form-control" 
                                   placeholder="Password" 
                                   minlength="8" 
                                   pattern="(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}" 
                                   title="Password must contain at least one uppercase letter, one number, and one special character" 
                                   required>
                            <span class="password-toggle-icon" onclick="togglePasswordVisibility()">
                                <i class="fas fa-eye" id="passwordToggle" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                            </span>
                        </div>

                        <div class="form-group">
                            <input type="tel" name="phone" class="form-control" 
                                   placeholder="Phone Number" 
                                   pattern="09\d{9}" 
                                   maxlength="11" 
                                   title="Phone number must start with '09' and be exactly 11 digits" 
                                   required>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="termsCheckbox" name="terms_agreement" required>
                                <label class="custom-control-label" for="termsCheckbox">
                                    I agree to the <span class="terms-link" data-toggle="modal" data-target="#termsModal">Terms and Conditions</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" name="create_account" class="btn btn-primary btn-block">
                            Create Account
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms and Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Terms and Conditions</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
    <h6>Food and Drinks Reservation Terms of Service</h6>
    <ol>
    <li><strong>Reservation Policies</strong>
            <ul>
                <li>Reservations for food and drinks are subject to availability and confirmation.</li>
                <li>Cancellations can only be made by the admin and the staff within 24 hours of the reservation time.</li>
                <li>Customers are not allowed to cancel reservations once confirmed.</li>
            </ul>
        </li>
        <li><strong>No Order Changes</strong>
            <ul>
                <li>Order reservations cannot be changed once confirmed.</li>
                <li>We reserve the right to enforce the original reservation details as booked.</li>
            </ul>
        </li>
        <li><strong>Privacy and Data Protection</strong>
            <ul>
                <li>Personal information will be handled in accordance with our privacy policy.</li>
                <li>We do not share personal data with third parties without consent.</li>
            </ul>
        </li>
        <li><strong>Payment Policies</strong>
            <ul>
                <li>Payment for room reservations is applicable in our company and will be accepted upon arrival.</li>
                <li>Failure to complete payment may result in the cancellation of the reservation.</li>
            </ul>
        </li>
        <li><strong>Liability Disclaimer</strong>
            <ul>
                <li>We are not liable for any inconvenience caused by unforeseen circumstances.</li>
                <li>Customers are responsible for their personal belongings during the reservation.</li>
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

    <!-- jQuery, Popper.js, and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('passwordToggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
               
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            }
        }

        // Prevent form submission if terms are not checked
        $(document).ready(function() {
            $('#createAccountForm').on('submit', function(e) {
                if (!$('#termsCheckbox').is(':checked')) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Terms and Conditions',
                        text: 'Please agree to the Terms and Conditions before proceeding.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                }
            });

            // Make terms link in modal clickable to check checkbox
            $('.terms-link').on('click', function() {
                $('#termsCheckbox').prop('checked', true);
            });
        });
    </script>
</body>
</html>