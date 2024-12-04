<?php
// ==============================
// Security Headers
// ==============================

// Content Security Policy: Restricts sources for content, scripts, and frames
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://trusted-scripts.com; frame-ancestors 'none';");
header("Content-Security-Policy: script-src 'self'; object-src 'none';");

// Prevent clickjacking by disallowing framing
header("X-Frame-Options: DENY");

// Enforce HTTPS using Strict-Transport-Security
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");

// Prevent MIME type sniffing
header("X-Content-Type-Options: nosniff");

// Enable basic XSS protection for older browsers
header("X-XSS-Protection: 1; mode=block");

// Control referrer information sent with requests
header("Referrer-Policy: no-referrer-when-downgrade");

// Restrict usage of certain browser features and APIs
header("Permissions-Policy: geolocation=(), camera=(), microphone=(), payment=()");



// ==============================
// Redirect HTTP to HTTPS
// ==============================
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
}

// ==============================
// Secure Session Cookie Settings
// ==============================
ini_set('session.cookie_secure', '1');          // Enforces HTTPS-only session cookies
ini_set('session.cookie_httponly', '1');        // Prevents JavaScript access to session cookies
ini_set('session.cookie_samesite', 'Strict');   // Mitigates CSRF by limiting cross-site cookie usage

// Start a session securely
session_start();                                

// ==============================
// Anti-XXE: Secure XML Parsing
// ==============================

// Disable loading of external entities to prevent XXE attacks
libxml_disable_entity_loader(true);

// Suppress libxml errors to allow custom handling
libxml_use_internal_errors(true);

/**
 * Securely parses XML strings to prevent XXE vulnerabilities.
 *
 * @param string $xmlString The XML input as a string.
 * @return DOMDocument The parsed DOMDocument object.
 * @throws Exception If parsing fails.
 */
function parseXMLSecurely($xmlString) {
    $dom = new DOMDocument();

    // Load the XML string securely
    if (!$dom->loadXML($xmlString, LIBXML_NOENT | LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_NOCDATA)) {
        throw new Exception('Error loading XML');
    }

    return $dom;
}

// ==============================
// Example Usage
// ==============================
try {
    $xmlString = '<root><element>Sample</element></root>'; // Replace with actual XML input
    $dom = parseXMLSecurely($xmlString);

    // Continue processing $dom...
    echo " ";
} catch (Exception $e) {
    // Handle XML processing errors securely
    echo 'Error processing XML: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>

<script type="text/javascript">
    // Disable right-click with an alert
    document.addEventListener('contextmenu', function(event) {
        event.preventDefault();
        alert("Right-click is disabled on this page.");
    });

    // Disable F12 key and Inspect Element keyboard shortcuts with alerts
    document.onkeydown = function(e) {
        // F12
        if (e.key === "F12") {
            alert("F12 (DevTools) is disabled.");
            e.preventDefault(); // Prevent default action
            return false;
        }

        // Ctrl + Shift + I (Inspect)
        if (e.ctrlKey && e.shiftKey && e.key === "I") {
            alert("Inspect Element is disabled.");
            e.preventDefault();
            return false;
        }

        // Ctrl + Shift + J (Console)
        if (e.ctrlKey && e.shiftKey && e.key === "J") {
            alert("Console is disabled.");
            e.preventDefault();
            return false;
        }


         // Ctrl + U or Ctrl + u (View Source)
         if (e.ctrlKey && (e.key === "U" || e.key === "u" || e.keyCode === 85)) {
            alert("Viewing page source is disabled.");
            e.preventDefault();
            return false;
        }
    };
</script>

<script>
    (function() {
  const detectDevToolsAdvanced = () => {
    // Detect if the console is open by triggering a breakpoint
    const start = new Date();
    debugger; // This will trigger when dev tools are open
    const end = new Date();
    if (end - start > 100) {
      document.body.innerHTML = "<h1>Unauthorized Access</h1><p>Developer tools are not allowed on this page.</p>";
      document.body.style.textAlign = "center";
      document.body.style.paddingTop = "20%";
      document.body.style.backgroundColor = "#fff";
      document.body.style.color = "#000";
    }
  };

  setInterval(detectDevToolsAdvanced, 500); // Continuously monitor
})();


const blockedAgents = ["Cyberfox", "Kali"];
if (navigator.userAgent.includes(blockedAgents)) {
  document.body.innerHTML = "<h1>Access Denied</h1><p>Your browser is not supported.</p>";
}


if (window.__proto__.toString() !== "[object Window]") {
  alert("Unauthorized modification detected.");
  window.location.href = "https://www.bible-knowledge.com/wp-content/uploads/battle-verses-against-demonic-attacks.jpg";
}

</script>
<?php
$disallowedUserAgents = [
    "BurpSuite", 
    "Cyberfox", 
    "OWASP ZAP", 
    "PostmanRuntime"
];

if (preg_match("/(" . implode("|", $disallowedUserAgents) . ")/i", $_SERVER['HTTP_USER_AGENT'])) {
    http_response_code(403);
    exit("Unauthorized access");
}
?>

<style>
 @media (max-width: 576px) {
    .navbar-brand h1 {
        font-size: 1.30rem;
        padding: 1rem;
    }
}
</style>
 <div class="container-fluid bg-dark px-0">
            <div class="row gx-0">
                <div class="col-lg-6 bg-dark d-none d-lg-block">
                <a href="index.php" class="navbar-brand w-100 h-100 m-0 p-0 d-flex align-items-center justify-content-center">
        <h1 class="m-0 text-primary text-uppercase display-6 display-md-4">Rio Management System</h1>
               </a>

                </div>
                <div class="col-lg-6">
                 
                    <nav class="navbar navbar-expand-lg bg-dark navbar-dark p-3 p-lg-0">
                        <a href="index.php" class="navbar-brand d-block d-lg-none">
                            <h1 class="m-0 text-primary text-uppercase display-6 display-md-4">Rio Management System</h1>
                        </a>
                        <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                            <div class="navbar-nav mr-auto py-0">
                                <a href="https://rio-lawis.com/" class="nav-item nav-link active">Home</a>
                                <a href="about.php" class="nav-item nav-link">About</a>
                                <a href="service.php" class="nav-item nav-link">Services</a>
                                <a href="room.php" class="nav-item nav-link">Rooms</a>         
                                <a href="contact.php" class="nav-item nav-link">Contact</a>
                                <a href="check_status.php" class="nav-item nav-link">Status</a>
                                 
                                <style>
  .dropdown-menu {
  background-color: #14165b; /* Change background color */
}

.dropdown-item {
  color: #FEA116; /* Change text color to white for visibility */
}

.dropdown-item:hover {
  background-color: #FEA116; /* Optional: Lighter shade on hover */
  color: #fff; /* Ensure text stays white */
}
.profile-pic {
  color: #fff; /* Change login text to white */
}

</style>

<!-- Navbar container -->
<div class="container">
   
   <!-- Navbar items -->
   <ul class="navbar-nav ms-auto">
     <!-- Dropdown trigger -->
     <li class="nav-item dropdown">
       <a class="nav-link dropdown-toggle profile-pic" href="#" id="loginTrigger" role="button">
         <span class="fw-bold">Login</span>
       </a>

       <!-- Dropdown menu -->
       <ul class="dropdown-menu dropdown-user animated fadeIn" id="loginDropdown">
         <div class="dropdown-user-scroll scrollbar-outer">
           <div class="dropdown-divider"></div>
           <a class="dropdown-item" href="admin">
             <i class="fas fa-user-shield"></i> Admin
           </a>
           <div class="dropdown-divider"></div>
           <a class="dropdown-item" href="staff">
             <i class="fas fa-user-tie"></i> Staff
           </a> 
           <div class="dropdown-divider"></div>
           <a class="dropdown-item" href="customer">
             <i class="fas fa-users"></i> Customer
           </a>
         </div>
       </ul>
     </li>
   </ul>
 </div>
</nav>

<!-- JavaScript to toggle the dropdown -->
<script>
 document.addEventListener('DOMContentLoaded', function () {
   const loginTrigger = document.getElementById('loginTrigger');
   const loginDropdown = document.getElementById('loginDropdown');

   // Toggle the dropdown on click
   loginTrigger.addEventListener('click', function (e) {
     e.preventDefault();
     loginDropdown.style.display = loginDropdown.style.display === 'none' ? 'block' : 'none';
   });

   // Close dropdown if clicked outside
   document.addEventListener('click', function (e) {
     if (!loginTrigger.contains(e.target) && !loginDropdown.contains(e.target)) {
       loginDropdown.style.display = 'none';
     }
   });
 });
</script>
    
  
                        </div>
                           
                        </div>
                    </nav>
                </div>
            </div>
        </div>