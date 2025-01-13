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
    <title>Rio Management System</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
    <br><br>
   
    <div class="container mt-5">
        <center>
            <!-- Box container around the form -->
            <div class="form-box" style="background-color: #3b4272; padding: 30px; border-radius: 10px; width: 400px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); position: relative;">
                <!-- Back button positioned at top-left of form -->
                <a href="create_account.php" class="btn btn-link" style="position: absolute; left: 20px; top: 20px; color: white;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                
                <!-- Logo image -->
                <img src="assets/img/1bg.jpg" alt="Logo" class="img-fluid mb-4" style="width: 100px; border-radius: 50%;">
                
                <form method="post">
                    <div class="d-flex flex-row align-items-center justify-content-center" style="color:#FEA116; font-size: 1.7rem;">
                        <h1 class="mb-5">Verify OTP</h1>
                    </div>
                    <span style="color: white; font-size: 1.1rem; margin-bottom: 20px;">Enter OTP number to verify your account:</span>
                    
                    <div class="form-outline mb-3">
                        <br>
                        <input type="text" name="otp" id="otp" class="form-control form-control-lg" placeholder="Enter OTP" required />
                        <br> 
                    </div>
                    <br><br>
                    <div class="d-flex justify-content-center">
                        <button type="submit" name="verify_otp" class="btn btn-warning btn-lg" style="background-color: #1572e8; color: white; padding-left: 2.5rem; padding-right: 2.5rem;">Verify OTP</button>
                    </div>
                </form>
            </div>
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