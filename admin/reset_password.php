<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include('config/connect.php'); // Include database connection

header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; img-src 'self' data:; font-src 'self' https://fonts.googleapis.com https://cdn.jsdelivr.net; frame-ancestors 'none'; form-action 'self'; base-uri 'self';");



$error = "";
$success = "";

// CSRF token generation for extra security
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check if the token is set
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verify the token and its expiration
    $query = "SELECT * FROM admin WHERE reset_token='$token' AND token_expire > NOW()";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        // Token is valid, proceed with password reset
        if (isset($_POST['reset'])) {
            // CSRF token validation
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                $error = "Invalid CSRF token.";
            } else {
                // Validate the new password
                $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
                $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

                if ($new_password === $confirm_password) {
                    // Hash the new password before storing it
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    // Update the password in the database
                    $update_query = "UPDATE admin SET password='$hashed_password', reset_token=NULL, token_expire=NULL WHERE email='" . $user['email'] . "'";
                    if (mysqli_query($conn, $update_query)) {
                        $success = "Your password has been successfully reset. You can now log in.";
                    } else {
                        $error = "Failed to reset the password. Please try again.";
                    }
                } else {
                    $error = "Passwords do not match.";
                }
            }
        }
    } else {
        $error = "Invalid or expired token.";
    }
} else {
    $error = "No token provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>

    <!-- SweetAlert CSS and JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

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
            margin-bottom: 20px;
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 12px 40px 12px 10px;  /* Extra space on the right for the icon */
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: #80bdff;
            outline: none;
        }

        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 15px;  /* Space for the icon */
            top: 68%;
            transform: translateY(-50%);
            font-size: 20px;  /* Adjust size of the icon */
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

        .success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <div style="position: relative; text-align: center;">
            <img src="assets/img/a.jpg" alt="navbar brand" class="navbar-brand" height="70" style="position: absolute; left: 0; top: 50%; transform: translateY(-50%);">
            <h2>Reset Password</h2>
        </div>

        <p class="error"><?php echo $error; ?></p>
        <p class="success"><?php echo $success; ?></p>

        <?php if (isset($user) && !$success): ?>
            <form method="post">
    <div class="form-group"><br>
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" id="new_password" class="form-control" 
               placeholder="Enter your password" 
               minlength="8" 
               pattern="(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}" 
               title="Password must contain at least one uppercase letter, one number, and one special character" 
               required>
        <span class="password-toggle" onclick="togglePasswordVisibility('new_password', 'toggleNewPassword')">
            <i class="fas fa-eye-slash" id="toggleNewPassword"></i>
        </span>
    </div>
    <div class="form-group">
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" class="form-control" 
               placeholder="Confirm your password" 
               minlength="8" 
               pattern="(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}" 
               title="Password must contain at least one uppercase letter, one number, and one special character" 
               required>
        <span class="password-toggle" onclick="togglePasswordVisibility('confirm_password', 'toggleConfirmPassword')">
            <i class="fas fa-eye-slash" id="toggleConfirmPassword"></i>
        </span>
    </div>
    <!-- CSRF Token -->
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <button type="submit" name="reset" class="btn btn-primary">Reset Password</button>
</form>

        <?php endif; ?>
    </div>

    <script>
        function togglePasswordVisibility(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);

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

    <script>
        // SweetAlert Success Message
        <?php if (!empty($success)) { ?>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '<?php echo $success; ?>',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location = 'index.php';
                }
            });
        <?php } ?>

        // SweetAlert Error Message
        <?php if (!empty($error)) { ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo $error; ?>',
                confirmButtonText: 'OK'
            });
        <?php } ?>
    </script>
</body>
</html>
