<?php
session_start();
include('config/connect.php');

// Enhance Content Security Policy (CSP)
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; img-src 'self' data:; font-src 'self' https://fonts.googleapis.com https://cdn.jsdelivr.net; frame-ancestors 'none'; form-action 'self'; base-uri 'self';");
header("X-XSS-Protection: 1; mode=block");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

$error = "";
$max_attempts = 3; // Limit to 3 attempts
$lockout_time = 180; // Lockout for 3 minutes (180 seconds)

// Initialize attempts if not set
if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
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
        // Reset attempts after lockout time
        $_SESSION['attempts'] = 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // CSRF token validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid CSRF token!";
    } else {
        $user = trim($_POST['uname']);
        $pass = trim($_POST['pass']);

        
        if (!empty($user) && !empty($pass)) {
            $stmt = $conn->prepare("SELECT uname, password FROM admin WHERE uname = ?");
            $stmt->bind_param("s", $user);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                if (password_verify($pass, $row['password'])) {
                    session_regenerate_id(true);
                    $_SESSION['uname'] = $user;
                    $_SESSION['attempts'] = 0; // Reset on success
                    echo json_encode(['success' => true, 'redirect' => 'dashboard.php']);
                    exit();
                } else {
                    $error = '* Invalid Username or Password';
                }
            } else {
                $error = '* Invalid Username or Password';
            }

            $_SESSION['attempts']++;
            $_SESSION['last_attempt_time'] = time();
        } else {
            $error = "* Please Fill all the Fields!";
        }
    }

    echo json_encode(['success' => false, 'error' => $error]);
}
?>
