<?php
session_start();
include('config/connect.php');

// Security Configuration
$error = "";
$max_attempts = 3;       // Maximum number of login attempts
$lockout_time = 180;     // Lockout time in seconds (3 minutes)

// reCAPTCHA secret key
$recaptcha_secret = 'YOUR_RECAPTCHA_SECRET_KEY';

// Initialize session variables for tracking attempts
if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

// Prevent direct script access
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    die('Access denied');
}

// Check if the user is locked out
if ($_SESSION['attempts'] >= $max_attempts) {
    $remaining_lockout_time = $lockout_time - (time() - $_SESSION['last_attempt_time']);
    if ($remaining_lockout_time > 0) {
        echo json_encode([
            'success' => false,
            'error' => "Too many login attempts. Please try again in " . ceil($remaining_lockout_time / 60) . " minutes.",
            'disable' => true,
            'time_remaining' => $remaining_lockout_time
        ]);
        exit();
    } else {
        $_SESSION['attempts'] = 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode([
            'success' => false, 
            'error' => 'Invalid CSRF token. Please refresh the page and try again.'
        ]);
        exit();
    }

    // Always verify reCAPTCHA first
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    
    if (empty($recaptcha_response)) {
        echo json_encode([
            'success' => false, 
            'error' => 'Please complete the reCAPTCHA verification'
        ]);
        exit();
    }
    
    // Verify reCAPTCHA response
    $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
    $verify_data = [
        'secret' => $recaptcha_secret,
        'response' => $recaptcha_response
    ];

    $verify_options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query($verify_data)
        ]
    ];
    
    $verify_context = stream_context_create($verify_options);
    $verify_response = @file_get_contents($verify_url, false, $verify_context);
    
    if ($verify_response === false) {
        echo json_encode([
            'success' => false, 
            'error' => 'Unable to verify reCAPTCHA. Please try again.'
        ]);
        exit();
    }
    
    $response_data = json_decode($verify_response);
    
    if (!$response_data->success) {
        echo json_encode([
            'success' => false, 
            'error' => 'reCAPTCHA verification failed'
        ]);
        exit();
    }

    // Sanitize and validate input
    $user = filter_var(trim($_POST['uname']), FILTER_SANITIZE_STRING);
    $pass = trim($_POST['pass']);

    // Validate input
    if (empty($user) || empty($pass)) {
        echo json_encode([
            'success' => false, 
            'error' => 'Please provide both username and password'
        ]);
        exit();
    }

    // Prevent brute-force by adding a small delay
    usleep(250000); // 250 milliseconds

    try {
        // Prepare statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, uname, password FROM admin WHERE uname = ?");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            // Verify the password
            if (password_verify($pass, $row['password'])) {
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                
                // Set session variables
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['uname'] = $row['uname'];
                $_SESSION['last_login'] = time();
                
                // Reset login attempts
                $_SESSION['attempts'] = 0;

                // Log successful login (recommended)
                error_log("Successful login for user: {$row['uname']} at " . date('Y-m-d H:i:s'));

                echo json_encode([
                    'success' => true, 
                    'redirect' => 'dashboard.php'
                ]);
                exit();
            } else {
                $error = 'Invalid Username or Password';
            }
        } else {
            $error = 'Invalid Username or Password';
        }

        // Increment attempts and update last attempt time
        $_SESSION['attempts']++;
        $_SESSION['last_attempt_time'] = time();

        echo json_encode([
            'success' => false, 
            'error' => $error,
            'disable' => $_SESSION['attempts'] >= $max_attempts,
            'time_remaining' => $_SESSION['attempts'] >= $max_attempts ? $lockout_time : null
        ]);
    } catch (Exception $e) {
        // Log the error securely
        error_log("Login error: " . $e->getMessage());
        
        echo json_encode([
            'success' => false, 
            'error' => 'An unexpected error occurred. Please try again.'
        ]);
    }
    exit();
}
?>