<?php
session_start();
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; img-src 'self' data:; font-src 'self' https://fonts.googleapis.com https://cdn.jsdelivr.net; frame-ancestors 'none'; form-action 'self'; base-uri 'self';");
header('Referrer-Policy: strict-origin-when-cross-origin');

// Database Connection
function dbConnect() {
    $host = 'localhost';
    $dbname = 'your_database';
    $username = 'your_username';
    $password = 'your_password';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database Connection Error: " . $e->getMessage());
        exit("Database connection failed");
    }
}

// Prevent Brute Force Attack
class LoginSecurity {
    private $pdo;
    private $maxAttempts = 3;
    private $lockoutDuration = 300; // 5 minutes

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function logLoginAttempt($username, $success) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt = $this->pdo->prepare("INSERT INTO login_attempts (username, ip_address, success, attempt_time) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$username, $ip, $success ? 1 : 0]);
    }

    public function checkLoginAttempts($username) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as attempts 
            FROM login_attempts 
            WHERE username = ? 
            AND ip_address = ? 
            AND success = 0 
            AND attempt_time > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
        ");
        $stmt->execute([$username, $ip]);
        $result = $stmt->fetch();

        return $result['attempts'] >= $this->maxAttempts;
    }

    public function authenticateUser($username, $password) {
        // Check if login attempts exceeded
        if ($this->checkLoginAttempts($username)) {
            return ['success' => false, 'error' => 'Too many failed attempts. Please try again later.'];
        }

        // Validate username and password
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Clear previous failed attempts
            $this->clearLoginAttempts($username);
            
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);
            
            return [
                'success' => true, 
                'redirect' => 'dashboard.php',
                'user_id' => $user['id']
            ];
        } else {
            // Log failed attempt
            $this->logLoginAttempt($username, false);
            return ['success' => false, 'error' => 'Invalid username or password'];
        }
    }

    private function clearLoginAttempts($username) {
        $stmt = $this->pdo->prepare("DELETE FROM login_attempts WHERE username = ?");
        $stmt->execute([$username]);
    }
}

// CSRF Protection
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Handle Login Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // CSRF Token Validation
    if (!validateCSRFToken($_POST['csrf_token'])) {
        echo json_encode([
            'success' => false, 
            'error' => 'CSRF token validation failed'
        ]);
        exit;
    }

    // Sanitize Input
    $username = filter_input(INPUT_POST, 'uname', FILTER_SANITIZE_STRING);
    $password = $_POST['pass'] ?? '';

    // Validate Input
    if (empty($username) || empty($password)) {
        echo json_encode([
            'success' => false, 
            'error' => 'Username and password are required'
        ]);
        exit;
    }

    // Authenticate User
    $pdo = dbConnect();
    $loginSecurity = new LoginSecurity($pdo);
    $result = $loginSecurity->authenticateUser($username, $password);

    echo json_encode($result);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Secure Login System</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { background-color: #2a2f5b; color: white; }
        .login-container { max-width: 500px; margin: auto; padding: 20px; }
        .error-message { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
<div class="container login-container">
    <form id="loginForm" class="mt-5">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
        
        <div class="text-center mb-4">
            <h2 class="text-warning">Secure Login</h2>
        </div>
        
        <div id="error-message" class="error-message text-center"></div>
        
        <div class="form-group mb-3">
            <label for="user">Username</label>
            <input type="text" name="uname" id="user" 
                   class="form-control" 
                   placeholder="Enter username" 
                   required 
                   autocomplete="username">
        </div>
        
        <div class="form-group mb-3">
            <label for="pass">Password</label>
            <div class="input-group">
                <input type="password" 
                       name="pass" 
                       id="psw" 
                       class="form-control" 
                       placeholder="Enter password" 
                       required 
                       autocomplete="current-password">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" 
                            type="button" 
                            onclick="togglePassword()">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <button type="submit" 
                name="login" 
                id="loginButton" 
                class="btn btn-warning btn-block w-100">
            Login
        </button>
        
        <div class="text-center mt-3">
            <a href="forgot_password.php" class="text-light">Forgot password?</a>
        </div>
    </form>
</div>

<script>
// Login Attempt Tracking
const MAX_ATTEMPTS = 3;
const LOCKOUT_TIME = 3 * 60 * 1000; // 3 minutes in milliseconds

function initLoginAttemptTracking() {
    const loginAttempts = JSON.parse(localStorage.getItem('loginAttempts') || '{}');
    const currentTime = new Date().getTime();

    if (loginAttempts.lockUntil && currentTime < loginAttempts.lockUntil) {
        const remainingTime = Math.ceil((loginAttempts.lockUntil - currentTime) / 1000);
        disableLoginButton(remainingTime);
        return false;
    }

    return true;
}

function updateLoginAttempts(success) {
    const loginAttempts = JSON.parse(localStorage.getItem('loginAttempts') || '{}');
    const currentTime = new Date().getTime();

    if (success) {
        localStorage.removeItem('loginAttempts');
        enableLoginButton();
        return;
    }

    loginAttempts.attempts = (loginAttempts.attempts || 0) + 1;

    if (loginAttempts.attempts >= MAX_ATTEMPTS) {
        loginAttempts.lockUntil = currentTime + LOCKOUT_TIME;
        disableLoginButton(LOCKOUT_TIME / 1000);
    }

    localStorage.setItem('loginAttempts', JSON.stringify(loginAttempts));
}

function disableLoginButton(seconds) {
    const loginButton = document.getElementById('loginButton');
    loginButton.disabled = true;
    loginButton.style.opacity = '0.5';
    
    const originalText = loginButton.textContent;
    
    function updateCountdown() {
        if (seconds > 0) {
            loginButton.textContent = `Try again in ${seconds} seconds`;
            seconds--;
            setTimeout(updateCountdown, 1000);
        } else {
            loginButton.disabled = false;
            loginButton.style.opacity = '1';
            loginButton.textContent = originalText;
            
            const loginAttempts = JSON.parse(localStorage.getItem('loginAttempts') || '{}');
            delete loginAttempts.attempts;
            delete loginAttempts.lockUntil;
            localStorage.setItem('loginAttempts', JSON.stringify(loginAttempts));
        }
    }

    updateCountdown();
}

function enableLoginButton() {
    const loginButton = document.getElementById('loginButton');
    loginButton.disabled = false;
    loginButton.style.opacity = '1';
}

function togglePassword() {
    const passwordInput = document.getElementById('psw');
    passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
}

document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();

    if (!initLoginAttemptTracking()) {
        return;
    }

    const formData = new FormData(this);
    formData.append('login', '1');

    fetch('login.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateLoginAttempts(true);
            window.location.href = data.redirect;
        } else {
            updateLoginAttempts(false);
            document.getElementById('error-message').textContent = data.error;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        updateLoginAttempts(false);
    });
});

// Prevent dev tools and right-click
document.addEventListener('contextmenu', function(e) {
    if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
        e.preventDefault();
    }
});

document.onkeydown = function(e) {
    if (e.ctrlKey && (e.keyCode === 85 || e.keyCode === 83)) {
        return false;
    }
};

// Initialize login attempt tracking on page load
document.addEventListener('DOMContentLoaded', initLoginAttemptTracking);
</script>
</body>
</html>