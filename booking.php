<?php
session_start();
include('config/connect.php');

// Validate terms agreement
if (!isset($_POST['terms_agreement'])) {
    $error = "You must agree to the Terms and Conditions.";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = mt_rand(10000000, 99999999);
    $checkin_date = $_POST['checkin_date'];
    $checkout_date = $_POST['checkout_date'];
    $r_name = $_POST['r_name']; // Room name from the fetched data
    $amount = $_POST['amount'];  // Room price from the fetched data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $payment = $_POST['payment'];

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

    <style>
         .terms-link {
            color: #1572e8;
            text-decoration: underline;
            cursor: pointer;
        }
        .modal-content {
    background-color: #2a2f5b;
    color: white;
}

.modal-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    justify-content: center;  /* This centers the title */
    text-align: center;
}

.modal-title {
    text-align: center;
    width: 100%;  /* This ensures the title takes full width */
}

/* Adjust the close button position */
.modal-header .close {
    color: white;
    position: absolute;
    right: 1rem;
    padding: 1rem;
    margin: -1rem -1rem -1rem auto;
}
    </style>
 
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
    
    // Fetch the specific room details
    $sql = "SELECT * FROM room WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $roomResult = $stmt->get_result();

    // Check if the room exists
    if ($roomResult->num_rows > 0) {
        $room = $roomResult->fetch_assoc();

        // Fetch all images for this room
        $sqlImages = "SELECT image_path FROM room_images WHERE room_id = ?";
        $stmtImages = $conn->prepare($sqlImages);
        $stmtImages->bind_param("i", $room_id);
        $stmtImages->execute();
        $imagesResult = $stmtImages->get_result();
        $images = [];
        
        // Collect image paths
        while ($imgRow = $imagesResult->fetch_assoc()) {
            $images[] = $imgRow['image_path'];
        }
?> 

<!-- Display the room details -->
<div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
            <div class="room-item shadow rounded overflow-hidden">
                <div class="position-relative">
                    <!-- Carousel for room images -->
                    <div id="carousel-<?php echo $room['id']; ?>" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php
                            if (!empty($images)) {
                                foreach ($images as $index => $image) {
                                    echo '<div class="carousel-item ' . ($index === 0 ? 'active' : '') . '">';
                                    echo '<img src="admin/uploads/' . htmlspecialchars($image) . '" class="d-block w-100" alt="Room Image" style="width: 100p%; height: 300px; object-fit: cover;">';
                                    echo '</div>';
                                }
                            } else {
                                // Default image if no images found
                                echo '<div class="carousel-item active"><img src="admin/uploads/default.jpg" class="d-block w-100" alt="No Image" style="width: 100p%; height: 300px; object-fit: cover;"></div>';
                            }
                            ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carousel-<?php echo $room['id']; ?>" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carousel-<?php echo $room['id']; ?>" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                    <small class="position-absolute start-0 top-100 translate-middle-y bg-primary text-white rounded py-1 px-3 ms-4">₱<?php echo htmlspecialchars($room['price']); ?>/Night</small>
                </div>
                <div class="p-2 mt-2">
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="mb-0"><?php echo htmlspecialchars($room['r_name']); ?></h5>
                        <div class="ps-2">
                            <small class="fa fa-star text-primary"></small>
                            <small class="fa fa-star text-primary"></small>
                            <small class="fa fa-star text-primary"></small>
                            <small class="fa fa-star text-primary"></small>
                            <small class="fa fa-star text-primary"></small>
                        </div>
                    </div>
                    <h6 class="mb-0"><i class="fa fa-home text-primary me-2"></i><?php echo htmlspecialchars($room['available']); ?></h6><br>
                    <div class="d-flex mb-3">
                        <small class="border-end me-3 pe-3"><i class="fa fa-bed text-primary me-2"></i><?php echo htmlspecialchars($room['bed']); ?></small>
                        <small class="border-end me-3 pe-3"><i class="fa fa-bath text-primary me-2"></i><?php echo htmlspecialchars($room['bath']); ?></small>
                        <small><i class="fa fa-snowflake text-primary me-2"></i>Aircon</small>
                    </div>

                    <!-- Guest selection dropdown -->
                    <div class="d-flex justify-content-between">
                        <div class="col-md-12">
                            <div class="form-floating">
                                <select id="guest-count-<?php echo $room['id']; ?>" class="form-control" name="guest-count-<?php echo $room['id']; ?>" onchange="updateRoomName('<?php echo $room['r_name']; ?>'); calculateAmount(<?php echo $room['price']; ?>, this.value)">
                                    <option value="0">--Select--</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <!-- Add more options as needed -->
                                </select>
                                <label for="guest-count-<?php echo $room['id']; ?>">Select Guest</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php
    } else {
        echo "<p>Room not found.</p>";
    }
} else {
    echo "<p>No room ID provided.</p>";
}
?>

                    <div class="col-lg-4">
                        <div class="wow fadeInUp" data-wow-delay="0.2s">
                            <form method="POST">

                            


 <?php
        // Check if the form was submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $first_name = $_POST['first_name'];

            // Sanitize input to remove any HTML or script tags
            $first_name_sanitized = htmlspecialchars($first_name, ENT_QUOTES, 'UTF-8');

            // Validate the input: allow letters, hyphens, apostrophes, and spaces, but block < or >
            if (!preg_match("/^[A-Za-z\s'-]+$/", $first_name)) {
                echo '<div class="alert alert-danger">Invalid input: Please enter a valid name (letters, hyphens, apostrophes, and spaces only).</div>';
            } else if ($first_name !== $first_name_sanitized) {
                echo '<div class="alert alert-danger">Invalid input: HTML or script tags are not allowed.</div>';
            } else {
                // If valid, display success message
                echo '<div class="alert alert-success">Input is valid. Form submitted successfully!</div>';
                // Here, you can proceed with storing or processing the sanitized input.
            }

            $last_name = $_POST['last_name'];

            // Sanitize input to remove any HTML or script tags
            $last_name_sanitized = htmlspecialchars($last_name, ENT_QUOTES, 'UTF-8');

            // Validate the input: allow letters, hyphens, apostrophes, and spaces, but block < or >
            if (!preg_match("/^[A-Za-z\s'-]+$/", $last_name)) {
                echo '<div class="alert alert-danger">Invalid input: Please enter a valid name (letters, hyphens, apostrophes, and spaces only).</div>';
            } else if ($last_name !== $last_name_sanitized) {
                echo '<div class="alert alert-danger">Invalid input: HTML or script tags are not allowed.</div>';
            } else {
                // If valid, display success message
                echo '<div class="alert alert-success">Input is valid. Form submitted successfully!</div>';
                // Here, you can proceed with storing or processing the sanitized input.
            }
        }
        ?>
        
        <div class="row g-3">
    <div class="form-floating">
        <input type="date" name="checkin_date" class="form-control" id="checkin_date" placeholder="Check-in Date" 
               value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>" required>
        <label for="checkin_date">Check-in Date</label>
    </div>

    <div class="form-floating">
        <input type="date" name="checkout_date" class="form-control" id="checkout_date" placeholder="Check-out Date" 
               min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
        <label for="checkout_date">Check-out Date</label>
    </div>


<script>
    document.getElementById('checkin_date').addEventListener('change', function() {
        var checkinDate = new Date(this.value);
        checkinDate.setDate(checkinDate.getDate() + 1); // Ensure checkout is after check-in
        document.getElementById('checkout_date').min = checkinDate.toISOString().split("T")[0];
    });
</script>


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

                                   <!-- Terms Checkbox and Link -->
<div class="form-group">
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" id="termsCheckbox" name="terms_agreement" required>
        <label class="custom-control-label" for="termsCheckbox">
            I agree to the <span class="terms-link" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</span>
        </label>
    </div>
</div>
                                    <div class="col-12">
                                        <button class="btn btn-primary w-100 py-3" name="submit" type="submit">Book Now</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

             <!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content" style="background-color: #2a2f5b; color: white;">
            <div class="modal-header" style="border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                <h5 class="modal-title" id="termsModalLabel" style="color: white;">Terms and Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="color: white;"></button>
            </div>
            <div class="modal-body" style="color: white;">
    <h6 style="color: white;">Room Reservation Terms and Conditions</h6>
    <ol>
        <li><strong>Reservation Policies</strong>
            <ul>
                <li>Reservations are subject to availability and confirmation.</li>
                <li>Only the admin and staff can cancel a room reservation within 24 hours of the booking time.</li>
                <li>Room reservations cannot be edited or modified once booked.</li>
            </ul>
        </li>
        <li><strong>Payment Policies</strong>
            <ul>
                <li>Payment for room reservations is applicable in our company and will be accepted upon arrival.</li>
                <li>Failure to complete payment at check-in may result in the cancellation of the reservation.</li>
            </ul>
        </li>
        <li><strong>Privacy and Data Protection</strong>
            <ul>
                <li>Personal information will be handled in accordance with our privacy policy.</li>
                <li>We do not share personal data with third parties without consent.</li>
            </ul>
        </li>
        <li><strong>Liability Disclaimer</strong>
            <ul>
                <li>We are not liable for any inconvenience caused by circumstances beyond our control.</li>
                <li>Customers are responsible for their personal belongings.</li>
            </ul>
        </li>
    </ol>
</div>

            <div class="modal-footer" style="border-top: 1px solid rgba(255, 255, 255, 0.1);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
       document.addEventListener('DOMContentLoaded', () => {
    // Automatically check the checkbox when terms modal is triggered
    document.querySelector('.terms-link').addEventListener('click', () => {
        document.querySelector('#termsCheckbox').checked = true;
    });

    // Validation on form submission
    document.querySelector('form').addEventListener('submit', (event) => {
        if (!document.querySelector('#termsCheckbox').checked) {
            event.preventDefault(); // Prevent form submission
            Swal.fire({
                icon: 'error',
                title: 'Terms and Conditions',
                text: 'Please agree to the Terms and Conditions before proceeding.',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        }
    });
});

    </script>
</body>

</html>