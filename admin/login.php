<?php
session_start();
include('config/connect.php');

// Content Security Policy (CSP) and security headers
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net https://www.google.com/recaptcha/ 'unsafe-inline'; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; img-src 'self' data:; font-src 'self' https://fonts.googleapis.com https://cdn.jsdelivr.net; frame-ancestors 'none'; form-action 'self'; base-uri 'self';");
header("X-XSS-Protection: 1; mode=block");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

$error = "";
$max_attempts = 3;       // Maximum number of login attempts
$lockout_time = 180;     // Lockout time in seconds (3 minutes)

// reCAPTCHA secret key (replace with your actual secret key)
$recaptcha_secret = '6Le_xYUqAAAAAIs1Ful4tgqjUmuScMwxU47admb6';

// Initialize session variables for tracking attempts
if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

// Check if the user is locked out
if ($_SESSION['attempts'] >= $max_attempts) {
    $remaining_lockout_time = $lockout_time - (time() - $_SESSION['last_attempt_time']);
    if ($remaining_lockout_time > 0) {
        // User is still locked out
        echo json_encode([
            'success' => false,
            'error' => "Too many login attempts. Please try again in " . ceil($remaining_lockout_time / 60) . " minutes.",
            'disable' => true,
            'time_remaining' => $remaining_lockout_time
        ]);
        exit();
    } else {
        // Reset attempts after lockout time has passed
        $_SESSION['attempts'] = 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid CSRF token!";
        echo json_encode(['success' => false, 'error' => $error]);
        exit();
    }

    $user = trim($_POST['uname']);
    $pass = trim($_POST['pass']);

    // Check if reCAPTCHA is required
    $require_recaptcha = $_SESSION['attempts'] >= $max_attempts;
    
    if ($require_recaptcha) {
        // Verify reCAPTCHA
        $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
        
        // Verify reCAPTCHA response
        $verify_response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
        $response_data = json_decode($verify_response);
        
        if (!$response_data->success) {
            echo json_encode([
                'success' => false, 
                'error' => 'Please complete the reCAPTCHA', 
                'show_recaptcha' => true
            ]);
            exit();
        }
    }

    if (!empty($user) && !empty($pass)) {
        // Query the database for the provided username
        $stmt = $conn->prepare("SELECT uname, password FROM admin WHERE uname = ?");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            // Verify the password
            if (password_verify($pass, $row['password'])) {
                session_regenerate_id(true); // Regenerate session ID
                $_SESSION['uname'] = $user;
                $_SESSION['attempts'] = 0; // Reset attempts on successful login
                echo json_encode(['success' => true, 'redirect' => 'dashboard.php']);
                exit();
            } else {
                $error = '* Invalid Username or Password';
            }
        } else {
            $error = '* Invalid Username or Password';
        }

        // Increment attempts and update last attempt time
        $_SESSION['attempts']++;
        $_SESSION['last_attempt_time'] = time();
    } else {
        $error = "* Please Fill all the Fields!";
    }

    // Return error as JSON response
    echo json_encode([
        'success' => false, 
        'error' => $error, 
        // Show reCAPTCHA if attempts exceed max_attempts
        'show_recaptcha' => $_SESSION['attempts'] >= $max_attempts
    ]);
}
?>