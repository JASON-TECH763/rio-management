<?php 
session_start();
include('config/connect.php');

// Enhance Content Security Policy (CSP) headers
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; img-src 'self' data:; font-src 'self' https://fonts.googleapis.com https://cdn.jsdelivr.net; frame-ancestors 'none'; form-action 'self'; base-uri 'self';");
header("X-XSS-Protection: 1; mode=block");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

$response = ['status' => false, 'message' => ''];

// Lockout settings
$max_attempts = 3;
$lockout_time = 180; // 3 minutes

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['lockout_time'] = null;
}

// Check lockout status
if (isset($_SESSION['lockout_time'])) {
    $current_time = time();
    if ($current_time - $_SESSION['lockout_time'] < $lockout_time) {
        $remaining_time = $lockout_time - ($current_time - $_SESSION['lockout_time']);
        $response['message'] = "Too many failed attempts. Try again in {$remaining_time} seconds.";
        echo json_encode($response);
        exit;
    } else {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['lockout_time'] = null;
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    $user = trim(filter_var($_POST['uname'], FILTER_SANITIZE_EMAIL));
    $pass = trim(filter_var($_POST['pass'], FILTER_SANITIZE_STRING));

    // CSRF token validation
    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        $response['message'] = 'Invalid CSRF token!';
        echo json_encode($response);
        exit;
    }

    if (!empty($user) && !empty($pass)) {
        $query = "SELECT staff_email, staff_password FROM rpos_staff WHERE staff_email = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("s", $user);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                if (password_verify($pass, $row['staff_password'])) {
                    session_regenerate_id(true);
                    $_SESSION['staff_email'] = $row['staff_email'];
                    $_SESSION['login_attempts'] = 0;
                    $response['status'] = true;
                    $response['message'] = 'Login successful';
                    $response['redirect'] = 'dashboard.php'; // Redirect to dashboard or any appropriate page
                } else {
                    $_SESSION['login_attempts']++;
                    $response['message'] = 'Invalid Email or Password';
                }
            } else {
                $_SESSION['login_attempts']++;
                $response['message'] = 'Invalid Email or Password';
            }

            // Handle lockout
            if ($_SESSION['login_attempts'] >= $max_attempts) {
                $_SESSION['lockout_time'] = time();
                $response['message'] = 'Too many failed attempts. Please try again after 3 minutes.';
            }
        } else {
            $response['message'] = 'Database error. Please try again later.';
        }
    } else {
        $response['message'] = 'Please fill in all the fields!';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
