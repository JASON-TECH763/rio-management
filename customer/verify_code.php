<?php
session_start();
include('config/connect.php');

$otp_error = "";
$otp_success = false; // Flag for OTP success

if (isset($_POST['verify_otp'])) {
    $entered_otp = $_POST['otp'];

    // Check if OTP matches the one in the session
    if ($entered_otp == $_SESSION['otp']) {
        $otp_success = true; // Set success flag

        // Get temporary account data from session
        $temp_account = $_SESSION['temp_account'];

        // Insert the new account into the database
        $query = "INSERT INTO customer (name, email, password, phone, date_created, verified) 
                  VALUES ('{$temp_account['name']}', '{$temp_account['email']}', '{$temp_account['password']}', '{$temp_account['phone']}', NOW(), 1)";
        if (mysqli_query($conn, $query)) {
            // Clear temporary data
            unset($_SESSION['temp_account']);
            unset($_SESSION['otp']);

            // Set success message
            $otp_success = true;
        } else {
            $otp_error = "There was an error creating your account.";
        }
    } else {
        $otp_error = "Invalid OTP code.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Verify OTP</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.onload = function() {
            <?php if ($otp_success): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Account Verified!',
                    text: 'Successfully created your account.',
                    confirmButtonText: 'OK'
                }).then(function() {
                    window.location = 'index.php'; // Redirect to login page
                });
            <?php elseif (!empty($otp_error)): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '<?php echo $otp_error; ?>'
                });
            <?php endif; ?>
        };
    </script>
</head>
<body>
    <div class="container mt-5">
        <center>
            <form method="post">
                <div class="d-flex flex-row align-items-center justify-content-center" style="color:#FEA116; font-size: 2.5rem;">
                    <h1 class="mb-5">Verify OTP</h1>
                </div>
                <div class="form-outline mb-3">
                    <input type="text" name="otp" id="otp" class="form-control form-control-lg" placeholder="Enter OTP" required />
                </div>
                <br><br>
                <div class="d-flex justify-content-center">
                    <button type="submit" name="verify_otp" class="btn btn-warning btn-lg" style="background-color: #1572e8; color: white; padding-left: 2.5rem; padding-right: 2.5rem;">Verify OTP</button>
                </div>
            </form>
        </center>
    </div>

    <style>
        body {
            background-color: #2a2f5b;
        }
        .form-label {
            font-weight: bold;
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
</body>
</html>