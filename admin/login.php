<?php
session_start();
include('config/connect.php');

// Enhance Content Security Policy (CSP)
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; img-src 'self' data:; font-src 'self' https://fonts.googleapis.com https://cdn.jsdelivr.net; frame-ancestors 'none'; form-action 'self'; base-uri 'self';");

// Set additional security headers
header("X-XSS-Protection: 1; mode=block");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");

// Error message
$error = "";
$max_attempts = 5; // Maximum login attempts
$lockout_time = 1800; // Lockout time in seconds (30 minutes)

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
        echo json_encode(['success' => false, 'error' => $error]);
        exit();
    } else {
        // Reset attempts after lockout time
        $_SESSION['attempts'] = 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login']) && $_SESSION['attempts'] < $max_attempts) {
    // CSRF token validation
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = "Invalid CSRF token!";
    } else {
        // Sanitize inputs
        $user = filter_input(INPUT_POST, 'uname', FILTER_SANITIZE_STRING);
        $pass = filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_STRING);

        // Basic input validation
        if (!empty($user) && !empty($pass)) {
            // Use prepared statements to prevent SQL injection
            $stmt = $conn->prepare("SELECT id, uname, password FROM admin WHERE uname = ?");
            $stmt->bind_param("s", $user);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();

                // Verify the hashed password
                if (password_verify($pass, $row['password'])) {
                    // Regenerate session ID upon login to prevent session fixation
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['uname'] = $user;
                    $_SESSION['attempts'] = 0; // Reset attempts on successful login
                    $_SESSION['last_login'] = time();
                    
                    // Set a secure, HTTP-only cookie
                    $token = bin2hex(random_bytes(32));
                    $expiry = time() + 3600; // 1 hour expiry
                    setcookie('auth_token', $token, [
                        'expires' => $expiry,
                        'path' => '/',
                        'domain' => '',
                        'secure' => true,
                        'httponly' => true,
                        'samesite' => 'Strict'
                    ]);
                    
                    // Store the token in the database (you'll need to add a column for this)
                    $stmt = $conn->prepare("UPDATE admin SET auth_token = ?, token_expiry = ? WHERE id = ?");
                    $stmt->bind_param("ssi", $token, $expiry, $row['id']);
                    $stmt->execute();

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

// Log failed login attempts
if (!empty($error)) {
    error_log("Failed login attempt: " . $_SERVER['REMOTE_ADDR'] . " - " . date("Y-m-d H:i:s"));
}

echo json_encode(['success' => false, 'error' => $error]);