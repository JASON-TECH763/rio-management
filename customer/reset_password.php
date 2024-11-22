<?php
session_start();
include('config/connect.php');

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

$error = "";
$success = "";

// CSRF token generation for extra security
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['reset_password'])) {
    // CSRF token validation
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = "Invalid CSRF token.";
    } else {
        $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
        $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $email = $_SESSION['reset_email'];

            $query = "UPDATE customer SET password='$hashed_password' WHERE email='$email'";
            if (mysqli_query($conn, $query)) {
                unset($_SESSION['reset_email'], $_SESSION['otp']);
                $success = "Your password has been successfully reset. You can now log in.";
            } else {
                $error = "Failed to reset password. Please try again.";
            }
        } else {
            $error = "Passwords do not match.";
        }
    }
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

        .success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <p class="error"><?php echo $error; ?></p>
        <p class="success"><?php echo $success; ?></p>

        <?php if (!$success): ?>
            <form method="post">
            <div class="form-group">
    <label for="new_password">New Password:</label>
    <input type="password" name="new_password" id="new_password" class="form-control" 
           placeholder="Enter your new password" minlength="8" 
           pattern="(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}" 
           title="Password must contain at least one uppercase letter, one number, and one special character" 
           required>
</div>
<div class="form-group">
    <label for="confirm_password">Confirm Password:</label>
    <input type="password" name="confirm_password" id="confirm_password" class="form-control" 
           placeholder="Confirm your new password" minlength="8" 
           pattern="(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}" 
           title="Password must contain at least one uppercase letter, one number, and one special character" 
           required>
</div>

        <?php endif; ?>
    </div>

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
