<?php
session_start();
include('config/connect.php');

$error = "";
$max_attempts = 3;       // Maximum number of login attempts
$lockout_time = 180;     // Lockout time in seconds (3 minutes)

// reCAPTCHA secret key
$recaptcha_secret = '6LcGl4kqAAAAAMDe4J1_HVSJ1xpMETM4cwxWIpG-';

// Initialize session variables for tracking attempts
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
        $_SESSION['attempts'] = 0;
    }
}

// Ensure this is a POST request with login parameter
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['login'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit();
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit();
}

// Verify reCAPTCHA
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
$verify_response = file_get_contents($verify_url, false, $verify_context);
$response_data = json_decode($verify_response);

if (!$response_data->success) {
    echo json_encode([
        'success' => false, 
        'error' => 'reCAPTCHA verification failed'
    ]);
    exit();
}

// Process login
$user = trim($_POST['uname']);
$pass = trim($_POST['pass']);

if (empty($user) || empty($pass)) {
    echo json_encode([
        'success' => false, 
        'error' => 'Please fill all fields'
    ]);
    exit();
}

// Query database for user
$stmt = $conn->prepare("SELECT uname, password FROM admin WHERE uname = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();

    // Verify password
    if (password_verify($pass, $row['password'])) {
        session_regenerate_id(true);
        $_SESSION['uname'] = $user;
        $_SESSION['attempts'] = 0;
        
        echo json_encode([
            'success' => true, 
            'redirect' => 'dashboard.php'
        ]);
        exit();
    }
}

// Login failed
$_SESSION['attempts']++;
$_SESSION['last_attempt_time'] = time();

echo json_encode([
    'success' => false, 
    'error' => 'Invalid username or password',
    'disable' => $_SESSION['attempts'] >= $max_attempts,
    'time_remaining' => $_SESSION['attempts'] >= $max_attempts ? $lockout_time : null
]);
exit();
?>