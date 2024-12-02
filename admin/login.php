<?php
session_start();
include('config/connect.php');

// Configurations
$max_attempts = 3;         // Maximum login attempts
$lockout_time = 180;       // Lockout time in seconds (3 minutes)
$recaptcha_secret = '6LcGl4kqAAAAAMDe4J1_HVSJ1xpMETM4cwxWIpG-';

// Initialize attempt tracking
if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

// Handle lockout
if ($_SESSION['attempts'] >= $max_attempts) {
    $remaining_lockout_time = $lockout_time - (time() - $_SESSION['last_attempt_time']);
    if ($remaining_lockout_time > 0) {
        echo json_encode([
            'success' => false,
            'error' => "Too many login attempts. Try again in " . ceil($remaining_lockout_time / 60) . " minutes.",
            'disable' => true,
            'time_remaining' => $remaining_lockout_time
        ]);
        exit();
    } else {
        $_SESSION['attempts'] = 0; // Reset attempts after lockout
    }
}

// Handle login request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // CSRF Token Validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'error' => "Invalid CSRF token!"]);
        exit();
    }

    // Verify reCAPTCHA
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    if (empty($recaptcha_response)) {
        echo json_encode(['success' => false, 'error' => 'Please complete the reCAPTCHA verification.']);
        exit();
    }

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
    $verify_response = file_get_contents($verify_url, false, $verify_context);
    $response_data = json_decode($verify_response);

    if (!$response_data->success) {
        echo json_encode(['success' => false, 'error' => 'reCAPTCHA verification failed.']);
        exit();
    }

    // Input Validation
    $user = trim($_POST['uname']);
    $pass = trim($_POST['pass']);

    if (empty($user) || empty($pass)) {
        echo json_encode(['success' => false, 'error' => 'Please fill all the fields!']);
        exit();
    }

    if (!preg_match('/^[a-zA-Z0-9_]+$/', $user)) {
        echo json_encode(['success' => false, 'error' => 'Invalid username format.']);
        exit();
    }

    // Authenticate user
    $stmt = $conn->prepare("SELECT uname, password FROM admin WHERE uname = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($pass, $row['password'])) {
            session_regenerate_id(true); // Regenerate session ID
            $_SESSION['uname'] = $user;
            $_SESSION['attempts'] = 0; // Reset attempts
            echo json_encode(['success' => true, 'redirect' => 'dashboard.php']);
            exit();
        }
    }

    // Increment attempts and return error
    $_SESSION['attempts']++;
    $_SESSION['last_attempt_time'] = time();
    echo json_encode(['success' => false, 'error' => 'Invalid username or password.']);
    exit();
}
?>
