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
        max-width: 400px;
        margin: auto;
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-bottom: 20px;
        position: relative;
    }

    .form-control {
        width: 100%;
        padding: 12px 40px 12px 10px; /* Extra space on the right for the icon */
        font-size: 16px;
        border: 1px solid #ced4da;
        border-radius: 5px;
        box-sizing: border-box; /* Ensures padding and border are within the width */
    }

    .password-toggle {
        cursor: pointer;
        position: absolute;
        right: 15px; /* Space for the icon */
        top: 65%;
        transform: translateY(-50%);
        font-size: 20px; /* Adjust size of the icon */
    }

    .btn {
        padding: 10px;
        width: 100%;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        background-color: #007bff;
        color: white;
        cursor: pointer;
    }

    .btn:hover {
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

    
    <p class="success"><?php echo $success; ?></p>

    <?php if (!$success): ?>
        <form method="post">
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" name="new_password" id="new_password" class="form-control" 
                       placeholder="" minlength="8" required>
                <span class="password-toggle" onclick="togglePasswordVisibility('new_password', 'toggleNewPassword')">
                    <i class="fas fa-eye-slash" id="toggleNewPassword"></i>
                </span>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" 
                       placeholder="" minlength="8" required>
                <span class="password-toggle" onclick="togglePasswordVisibility('confirm_password', 'toggleConfirmPassword')">
                    <i class="fas fa-eye-slash" id="toggleConfirmPassword"></i>
                </span>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit" name="reset_password" class="btn">Reset Password</button>
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
    }).then(() => {
        window.location = 'index.php';
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
