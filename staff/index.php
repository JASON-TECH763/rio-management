<?php
session_start();
include('config/connect.php');

$error = "";
$max_attempts = 3; // Limit to 3 attempts
$lockout_time = 180; // Lockout for 3 minutes (180 seconds)

// Initialize session variables if not set
if (!isset($_SESSION['staff_attempts'])) {
    $_SESSION['staff_attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

// Check if the user is locked out
if ($_SESSION['staff_attempts'] >= $max_attempts) {
    $remaining_lockout_time = $lockout_time - (time() - $_SESSION['last_attempt_time']);
    if ($remaining_lockout_time > 0) {
        $error = "Too many login attempts. Please try again in " . ceil($remaining_lockout_time / 60) . " minutes.";
    } else {
        // Reset attempts after lockout period
        $_SESSION['staff_attempts'] = 0;
    }
}

// Handle staff login
if (isset($_POST['login']) && empty($error)) {
    $user = trim($_POST['uname']);
    $pass = trim($_POST['pass']);

    // Sanitize input
    $user = mysqli_real_escape_string($conn, $user);

    if (!empty($user) && !empty($pass)) {
        // Prepare statement for login
        $query = "SELECT staff_email, staff_password FROM rpos_staff WHERE staff_email=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $user);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $row = mysqli_fetch_array($result);

        if ($row && password_verify($pass, $row['staff_password'])) {
            // Successful login, reset attempts and store session information
            $_SESSION['staff_email'] = $row['staff_email'];
            $_SESSION['staff_attempts'] = 0; // Reset attempts on success
            header("Location: dashboard.php"); // Redirect to staff dashboard
            exit(); // Always call exit after a header redirect
        } else {
            $error = '* Invalid Email or Password';
            $_SESSION['staff_attempts']++;
            $_SESSION['last_attempt_time'] = time();
        }
    } else {
        $error = '* Please fill all the fields!';
    }
}

// Handle staff account creation (unchanged from your original code)
if (isset($_POST['create_account'])) {
    $staff_name = $_POST['name'];
    $staff_last_name = $_POST['last_name'];
    $staff_email = $_POST['email'];
    $staff_password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $staff_gender = $_POST['gender'];

    if (!empty($staff_name) && !empty($staff_last_name) && !empty($staff_email) && !empty($staff_password) && !empty($staff_gender)) {
        // Prepare statement for account creation
        $query = "INSERT INTO rpos_staff (staff_name, staff_last_name, staff_email, staff_password, staff_gender, date_created) 
                  VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssss", $staff_name, $staff_last_name, $staff_email, $staff_password, $staff_gender);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            echo "<script>alert('Account created successfully! Please login.');</script>";
        } else {
            echo "<script>alert('Error creating account.');</script>";
        }
    } else {
        $error = '* Please fill all the fields!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Rio Management System</title>
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/a.jpg">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    .back-button {
        position: absolute;
        top: 20px;
        left: 20px;
        background-color: #1572e8;
        color: white;
        padding: 8px 12px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
    }
    .back-button i {
        font-size: 1rem;
        margin-right: 5px;
    }
</style>
</head>
<body>
<a href="https://rio-lawis.com/" class="btn btn-light back-button">
    <i class="fas fa-arrow-left"></i>
</a>
<section class="vh-100" style="background-color: #2a2f5b; color: white;">
    <div class="container-fluid h-custom">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-md-9 col-lg-6 col-xl-5">
                <img src="assets/img/1bg.jpg" class="img-fluid" alt="Sample image">
            </div>
            <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                <form id="loginForm" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <div class="d-flex flex-row align-items-center justify-content-center">
                        <span class="h1 fw-bold mb-0" style="color: #FEA116;">Staff Login</span>
                    </div>
                    <p style="color:red;" id="error-message"><?php echo $error; ?></p>
                    <div class="form-outline mb-4">
                        <label class="form-label" for="user">Email</label>
                        <input type="email" name="uname" id="user" class="form-control form-control-lg" placeholder="Enter email" required>
                    </div>
                    <div class="form-outline mb-3">
                        <label class="form-label" for="pass">Password</label>
                        <input type="password" name="pass" id="psw" class="form-control form-control-lg" placeholder="Enter password" required>
                        <input class="p-2" type="checkbox" onclick="togglePassword()" style="margin-left: 10px; margin-top: 13px;"> <span style="margin-left: 5px;">Show password</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="submit" name="login" class="btn btn-warning btn-lg enter" style="background-color: #1572e8; color: white; padding-left: 2.5rem; padding-right: 2.5rem;">Login</button>
                        <a href="forgot_password.php" class="text-light">Forgot password?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script src="assets/js/jquery-3.2.1.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script>
function togglePassword() {
    var x = document.getElementById("psw");
    x.type = x.type === "password" ? "text" : "password";
}

document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append('login', '1');

    fetch('login.php', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        const loginButton = document.querySelector('button[name="login"]');
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            document.getElementById('error-message').textContent = data.error;

            if (data.disable) {
                loginButton.disabled = true;
                startTimer(data.time_remaining, loginButton);
            }
        }
    })
    .catch(error => console.error('Error:', error));
});

function startTimer(duration, button) {
    let remaining = duration;
    const timerDisplay = document.createElement('span');
    timerDisplay.style.marginLeft = '10px';
    button.parentNode.appendChild(timerDisplay);

    const interval = setInterval(() => {
        if (remaining <= 0) {
            clearInterval(interval);
            button.disabled = false;
            timerDisplay.remove();
        } else {
            remaining--;
            const minutes = Math.floor(remaining / 60);
            const seconds = remaining % 60;
            timerDisplay.textContent = ` (Try again in ${minutes}m ${seconds}s)`;
        }
    }, 1000);
}
</script>
</body>
</html>