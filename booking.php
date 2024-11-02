<?php 
session_start();
include('config/connect.php');

// Anti-HTTP Secure Headers
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: no-referrer-when-downgrade");
header("Permissions-Policy: geolocation=(self), microphone=()");
header("Expect-CT: max-age=86400, enforce");
header("Clear-Site-Data: \"cache\", \"cookies\", \"storage\", \"executionContexts\"");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = mt_rand(10000000, 99999999);
    $checkin_date = htmlspecialchars($_POST['checkin_date'], ENT_QUOTES, 'UTF-8');
    $checkout_date = htmlspecialchars($_POST['checkout_date'], ENT_QUOTES, 'UTF-8');
    $r_name = htmlspecialchars($_POST['r_name'], ENT_QUOTES, 'UTF-8'); // Room name from the fetched data
    $amount = htmlspecialchars($_POST['amount'], ENT_QUOTES, 'UTF-8');  // Room price from the fetched data
    $first_name = htmlspecialchars($_POST['first_name'], ENT_QUOTES, 'UTF-8');
    $last_name = htmlspecialchars($_POST['last_name'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8');
    $payment = htmlspecialchars($_POST['payment'], ENT_QUOTES, 'UTF-8');

    $sql = "INSERT INTO reservations (booking_id, checkin_date, checkout_date, r_name, amount, first_name, last_name, email, phone, payment, status)
            VALUES ('$booking_id', '$checkin_date', '$checkout_date', '$r_name', '$amount', '$first_name', '$last_name', '$email', '$phone', '$payment', 'Pending')";

if ($conn->query($sql) === TRUE) {
    echo '<script>
            window.onload = function() {
                Swal.fire({
                    title: "Success!",
                    html: `Your booking was successful. Booking number is 
                           <span id="bookingNumber" style="font-weight: bold;">' . $booking_id . '</span> 
                           <span id="copyIcon" onclick="copyBookingNumber()" style="cursor: pointer; font-size: 18px; margin-left: 5px;">
                               📋
                           </span>`,
                    icon: "success"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "check_status.php";
                    }
                });
            };

            function copyBookingNumber() {
                const bookingNumberElement = document.getElementById("bookingNumber");
                const bookingNumber = bookingNumberElement.innerText;

                navigator.clipboard.writeText(bookingNumber).then(() => {
                    bookingNumberElement.style.backgroundColor = "#d4edda"; // Highlight the booking number
                    Swal.fire("Copied!", "Booking number copied to clipboard.", "success").then(() => {
                        window.location.href = "check_status.php"; // Redirect after "Copied!" alert
                    });
                }).catch(err => {
                    Swal.fire("Error!", "Failed to copy booking number.", "error");
                });
            }
          </script>';
} else {
    echo '<script>
            window.onload = function() {
                Swal.fire({
                    title: "Error!",
                    text: "Failed to book.",
                    icon: "error"
                });
            };
          </script>';
}


}
?>


<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
    <title>Rio Management System</title>
    <meta content="width=device-wi dth, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="assets/img/a.jpg" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">  

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">

 
    <script>
        // JavaScript function to prevent script tags and allow certain symbols
        function validateInput() {
            var nameField = document.getElementById('fname');
            var value = nameField.value;

            // Regular expression to allow letters, hyphens, apostrophes, and spaces, but no < or > (to prevent script tags)
            var regex = /^[A-Za-z\s'-]+$/;

            if (!regex.test(value)) {
                nameField.setCustomValidity("Please enter a valid name (letters, hyphens, apostrophes, and spaces allowed). No < or > symbols.");
            } else {
                nameField.setCustomValidity("");
            }
        }

        function validateInputs() {
            var nameField = document.getElementById('lname');
            var value = nameField.value;

            // Regular expression to allow letters, hyphens, apostrophes, and spaces, but no < or > (to prevent script tags)
            var regex = /^[A-Za-z\s'-]+$/;

            if (!regex.test(value)) {
                nameField.setCustomValidity("Please enter a valid lastname (letters, hyphens, apostrophes, and spaces allowed). No < or > symbols.");
            } else {
                nameField.setCustomValidity("");
            }
        }
    </script>
    
</head>

<body>
    <div class="container-xxl bg-white p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <!-- Header Start -->
        <?php include("header.php");?>
        <!-- Header End -->


       


       
        


            <!-- Booking Start -->
            <div class="container-xxl py-5">
                <div class="container">
               
                
                    <div class="container-xxl py-5">
            <div class="container">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    
                    <h1 class="mb-5"><span class="text-primary text-uppercase">Book</span> Now!</h1>
                </div>
                <div class="row g-12">
                    <!-- Centered Row for Room Details and Form -->
        <div class="row justify-content-center">
                <?php
// Check if a room_id is provided in the URL
if (isset($_GET['room_id'])) {
    $room_id = $_GET['room_id'];
    
    // Prepare the query to fetch the specific room
    $sql = "SELECT * FROM room WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if the room exists
    if ($result->num_rows > 0) {
        $room = $result->fetch_assoc(); // Fetch room data
?>

        <!-- Display the room details -->
        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
            <div class="room-item shadow rounded overflow-hidden">
                <div class="position-relative">
                   <!-- Display the room image with consistent sizing and inline CSS -->
<img src="admin/uploads/<?php echo htmlspecialchars($room['r_img']); ?>" 
     alt="<?php echo htmlspecialchars($room['r_name']); ?>" 
     class="img-fluid" 
     style="width: 100%; height: 200px; object-fit: cover;">



<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


                    <!-- Display the room price -->
                    <small class="position-absolute start-0 top-100 translate-middle-y bg-primary text-white rounded py-1 px-3 ms-4">₱<?php echo $room['price']; ?>/Night</small>
                </div>

                <div class="p-2 mt-2">
                    <div class="d-flex justify-content-between mb-3">
                        <!-- Display the room name dynamically -->
                        <h5 class="mb-0"><?php echo htmlspecialchars($room['r_name']); ?></h5>
                        <div class="ps-2">
                            <!-- Display 5 stars -->
                            <small class="fa fa-star text-primary"></small>
                            <small class="fa fa-star text-primary"></small>
                            <small class="fa fa-star text-primary"></small>
                            <small class="fa fa-star text-primary"></small>
                            <small class="fa fa-star text-primary"></small>
                        </div>
                    </div>

                    <!-- Display room availability -->
                    <h6 class="mb-0"><i class="fa fa-home text-primary me-2"></i><?php echo htmlspecialchars($room['available']); ?></h6><br>

                    <div class="d-flex mb-3">
                        <!-- Display bed and bath information dynamically -->
                        <small class="border-end me-3 pe-3"><i class="fa fa-bed text-primary me-2"></i><?php echo htmlspecialchars($room['bed']); ?></small>
                        <small class="border-end me-3 pe-3"><i class="fa fa-bath text-primary me-2"></i><?php echo htmlspecialchars($room['bath']); ?></small>
                        <small><i class="fa fa-snowflake text-primary me-2"></i>Aircon</small>
                    </div>

                    <!-- Guest selection dropdown -->
                    <div class="d-flex justify-content-between">
                    <div class="col-md-12">
                        <div class="form-floating">
                            <select id="guest-count-<?php echo $row['id']; ?>" class="form-control" name="guest-count-<?php echo $row['id']; ?>" onchange="updateRoomName('<?php echo $room['r_name']; ?>'); calculateAmount(<?php echo $room['price']; ?>, this.value)">
                                <option value="0">--Select--</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <!-- Add more options as needed -->
                            </select>
                            <label for="guest-count-<?php echo $row['id']; ?>">Select Guest</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php
    } else {
        // Room not found
        echo "<p class='alert alert-danger'>Room not found.</p>";
    }
} else {
    // No room_id provided
    echo "<p class='alert alert-warning'>No room selected. Please provide a room_id.</p>";
}
?>

                    <div class="col-lg-4">
                        <div class="wow fadeInUp" data-wow-delay="0.2s">
                            <form method="POST">

                            
                            <?php
// Function to limit input size early to avoid large payloads being processed
function limit_input_size($input, $max_length = 100) {
    if (strlen($input) > $max_length) {
        return false; // Return false if input exceeds the allowed size
    }
    return true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Limit input size and exit early if too large
    if (!limit_input_size($_POST['first_name']) || !limit_input_size($_POST['last_name'])) {
        echo '<div class="alert alert-danger">Invalid input: Name too long. Maximum 100 characters allowed.</div>';
        exit; // Stop further execution to save resources
    }

    // Efficient sanitization using trim and filter_input
    $first_name = trim(filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING));
    $last_name = trim(filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING));

    // Sanitize to block any HTML or script tags
    $first_name_sanitized = htmlspecialchars($first_name, ENT_QUOTES, 'UTF-8');
    $last_name_sanitized = htmlspecialchars($last_name, ENT_QUOTES, 'UTF-8');

    // Efficient regex processing: Validates the input in one step
    $name_pattern = "/^[A-Za-z\s'-]+$/";

    // Validate names and avoid unnecessary computations
    if (!preg_match($name_pattern, $first_name) || $first_name !== $first_name_sanitized) {
        echo '<div class="alert alert-danger">Invalid input: Please enter a valid first name (letters, hyphens, apostrophes, and spaces only).</div>';
    } elseif (!preg_match($name_pattern, $last_name) || $last_name !== $last_name_sanitized) {
        echo '<div class="alert alert-danger">Invalid input: Please enter a valid last name (letters, hyphens, apostrophes, and spaces only).</div>';
    } else {
        // Proceed with valid input and reduce unnecessary operations
        echo '<div class="alert alert-success">Input is valid. Form submitted successfully!</div>';

        // Efficiently handle data here (e.g., store in the database) without redundant file access or memory usage
        // Make sure to batch operations if interacting with the database or external resources
    }
}
?>

<div class="row g-3">
        <div class="form-floating">
            <input type="date" name="checkin_date" class="form-control" id="checkin_date" placeholder="Check-in Date" value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>" required>
            <label for="checkin_date">Check-in Date</label>
        </div>
    
        <div class="form-floating">
            <input type="date" name="checkout_date" class="form-control" id="checkout_date" placeholder="Check-out Date" required>
            <label for="checkout_date">Check-out Date</label>
        </div>
                                        <div class="col-md-12">
    <div class="form-floating">
        <input id="r_name" name="r_name" class="form-control" readonly placeholder="Selected Room" required>
        <label for="r_name">Selected Room</label>
    </div>
</div>

                                   
                                    
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                        <input type="text" class="form-control" id="fname" name="first_name" placeholder="Enter Firstname" required oninput="validateInput()" pattern="[A-Za-z\s'-]+">
                                        <label for="first_name">First Name</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                         <div class="form-floating">
                                         <input type="text" class="form-control" id="lname" name="last_name" placeholder="Enter Lastname" required oninput="validateInputs()" pattern="[A-Za-z\s'-]+">
                                         <label for="last_name">Last Name</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                           <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                            <label for="email">Email</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
    <div class="form-floating">
        <input 
            type="tel" 
            class="form-control" 
            id="phone" 
            name="phone" 
            placeholder="Phone" 
            pattern="\d{11}" 
            maxlength="11" 
            required 
            title="Please enter an 11-digit phone number without letters."
            oninput="this.value = this.value.replace(/[^0-9]/g, '');"
        >
        <label for="phone">Phone</label>
    </div>
</div>

                                    
<div class="col-md-12">
    <div class="form-floating">
        <input id="amount" name="amount" class="form-control" readonly placeholder="Total Amount" required>
        <label for="amount">Amount</label>
    </div>
</div>
                                    <div class="col-md-12">
                                        <div class="form-floating">
                                           <input type="text" class="form-control" id="payment" name="payment" placeholder="Payment Method" value="Only accept cash as payment upon arrival" readonly required>
                                            <label for="payment">Payment Method</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-primary w-100 py-3" name="submit" type="submit">Book Now</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

             
                    
                    
              
             
                </div>
            </div>
        </div>
    </div>
                    <br><br><br><br>
        <!-- Booking End -->


       
        

        <!-- Footer Start -->
        <?php include("footer.php");?>
        <!-- Footer End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>

    <script>
function updateRoomName(roomName) {
    // Set the value of the room name input field based on the selected room
    document.getElementById('r_name').value = roomName;
}

function calculateAmount(price, guestCount) {
    // Get the check-in and check-out dates
    const checkInDate = new Date(document.getElementById('checkin_date').value);
    const checkOutDate = new Date(document.getElementById('checkout_date').value);

    // Calculate the number of nights (difference between check-in and check-out)
    let nights = 0;
    if (checkInDate && checkOutDate && checkOutDate > checkInDate) {
        nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24)); // Convert milliseconds to days
    }

    // Calculate the total amount based on room price, number of guests, and number of nights
    const amountField = document.getElementById('amount');
    const guests = parseInt(guestCount);
    const totalAmount = (nights > 0 && guests > 0) ? price * nights * guests : 0; // Calculate if valid data

    // Update the total amount field
    amountField.value = totalAmount > 0 ? '₱' + totalAmount : '₱0'; // Display the total amount
}
</script>


    <!-- SweetAlert JS -->
    <script src="js/sweetalert.js"></script>

    <script>
    // Function to set the minimum checkout date
    function setMinCheckoutDate() {
        var checkinInput = document.getElementById('checkin_date');
        var checkoutInput = document.getElementById('checkout_date');
        var checkinDate = new Date(checkinInput.value);
        checkinDate.setDate(checkinDate.getDate() + 1); // Set checkout to at least one day after check-in
        var minCheckoutDate = checkinDate.toISOString().split('T')[0]; // Format to 'YYYY-MM-DD'

        checkoutInput.min = minCheckoutDate;
        // If the current value is less than the minimum, reset it
        if (checkoutInput.value < minCheckoutDate) {
            checkoutInput.value = minCheckoutDate;
        }
    }

    // Set the minimum checkout date on page load
    window.onload = function() {
        setMinCheckoutDate();
    };

    // Add event listener for check-in date change
    document.getElementById('checkin_date').addEventListener('change', setMinCheckoutDate);
</script>


    <script>
// Disable right-click
        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
        });

        // Disable F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
        document.onkeydown = function (e) {
            if (
                e.key === 'F12' ||
                (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J')) ||
                (e.ctrlKey && e.key === 'U')
            ) {
                e.preventDefault();
            }
        };

        // Disable developer tools
        function disableDevTools() {
            if (window.devtools.isOpen) {
                window.location.href = "about:blank";
            }
        }

        // Check for developer tools every 100ms
        setInterval(disableDevTools, 100);

        // Disable selecting text
        document.onselectstart = function (e) {
            e.preventDefault();
        };
</script>
</body>

</html>