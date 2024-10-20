<?php
session_start();
include('config/connect.php');

// Enhance Content Security Policy (CSP)
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; img-src 'self' data:; font-src 'self' https://fonts.googleapis.com https://cdn.jsdelivr.net; frame-ancestors 'none'; form-action 'self'; base-uri 'self';");

// Error message
$error = "";
$max_attempts = 5; // Maximum login attempts
$lockout_time = 30; // Lockout time in seconds

// Initialize attempts if not set
if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate 32-byte random token
}

// Check if the user is locked out
if ($_SESSION['attempts'] >= $max_attempts) {
    if ((time() - $_SESSION['last_attempt_time']) < $lockout_time) {
        $error = "Too many login attempts. Please try again later.";
    } else {
        // Reset attempts after lockout time
        $_SESSION['attempts'] = 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login']) && $_SESSION['attempts'] < $max_attempts) {
    // CSRF token validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid CSRF token!";
    } else {
        // Sanitize inputs
        $user = trim($_POST['uname']);
        $pass = trim($_POST['pass']);

        // Basic input validation
        if (!empty($user) && !empty($pass)) {
            // Use prepared statements to prevent SQL injection
            $stmt = $conn->prepare("SELECT uname, password FROM admin WHERE uname = ?");
            $stmt->bind_param("s", $user); // Bind parameter safely
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();

                // Verify the hashed password
                if (password_verify($pass, $row['password'])) {
                    session_regenerate_id(true); // Regenerate session ID upon login to prevent session fixation
                    $_SESSION['uname'] = $user;
                    $_SESSION['attempts'] = 0; // Reset attempts on successful login
                    echo json_encode(['success' => true, 'redirect' => 'dashboard.php']);
                    exit();
                } else {
                    $_SESSION['attempts']++;
                    $error = '* Invalid Username or Password';
                }
            } else {
                $_SESSION['attempts']++;
                $error = '* Invalid Username or Password';
            }

            $_SESSION['last_attempt_time'] = time(); // Update last attempt time
        } else {
            $error = "* Please Fill all the Fields!";
        }
    }
}

echo json_encode(['success' => false, 'error' => $error]);