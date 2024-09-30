<?php
session_start();
include('config/connect.php');

header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; style-src 'self' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; img-src 'self' data:;");

// At the start of the session
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// In the form
?>
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
<?php

// Before processing the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }
    // Proceed with booking processing
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = mt_rand(10000000,99999999);
    $checkin_date = htmlspecialchars($_POST['checkin_date'], ENT_QUOTES, 'UTF-8');
    $checkout_date = htmlspecialchars($_POST['checkout_date'], ENT_QUOTES, 'UTF-8');
    $r_name = htmlspecialchars($_POST['r_name'], ENT_QUOTES, 'UTF-8');
    $amount = htmlspecialchars($_POST['amount'], ENT_QUOTES, 'UTF-8');
    
    $first_name = htmlspecialchars($_POST['first_name'], ENT_QUOTES, 'UTF-8');
    $last_name = htmlspecialchars($_POST['last_name'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8');
    $country = htmlspecialchars($_POST['country'], ENT_QUOTES, 'UTF-8');
    $payment = htmlspecialchars($_POST['payment'], ENT_QUOTES, 'UTF-8');
    
   // $sql="insert into reservations (booking_id,checkin_date,checkout_date,amount,title,first_name,last_name,email,phone,country,payment) values('$booking_id','$checkin_date','$checkout_date','$amount','$title','$first_name','$last_name','$email','$phone','$country','$payment')";

   $sql = "INSERT INTO reservations (booking_id, checkin_date, checkout_date, r_name, amount, first_name, last_name , email, phone, country, payment, status)
            VALUES ('$booking_id','$checkin_date','$checkout_date', '$r_name', '$amount','$first_name','$last_name','$email','$phone','$country','$payment', 'Pending')";


    // $result=mysqli_query($mysqli,$sql);

    if ($conn->query($sql) === TRUE)
    // if($result)
        {
            echo '<script>
                    window.onload = function() {
                        Swal.fire({
                            title: "Success!",
                            text: "Your booking sent successfully.Booking number is "+"'.$booking_id.'",
                            icon: "success"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "check_status.php";
                            }
                        });
                    };
                  </script>';
                    
        }
        else
        {
           echo '<script>
                    window.onload = function() {
                        Swal.fire({
                            title: "Error!",
                            text: "failed to booked.",
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
                    <?php
                                    $sql = "SELECT * FROM room Where r_name = 'Standard Single Room'";
                                    $result = $conn->query($sql);
                                   
                                    
  // output data of each row
                                    while($row = $result->fetch_assoc()) {

                                    ?>

                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="room-item shadow rounded overflow-hidden">
                            <div class="position-relative">
                                <img class="img-fluid" src="img/room-1.jpg" style="height: 200px; width: 500px;" alt="">
                                <small class="position-absolute start-0 top-100 translate-middle-y bg-primary text-white rounded py-1 px-3 ms-4">₱<?php echo $row['price']; ?>/Night</small>
                            </div>
                            <div class="p-2 mt-2">
                                <div class="d-flex justify-content-between mb-3">
                                <h5 class="mb-0"><?php echo $row['r_name']; ?></h5>
                                    <div class="ps-2">
                                        <small class="fa fa-star text-primary"></small>
                                        <small class="fa fa-star text-primary"></small>
                                        <small class="fa fa-star text-primary"></small>
                                        <small class="fa fa-star text-primary"></small>
                                        <small class="fa fa-star text-primary"></small>

                                    </div>

                                </div>
                                <h6 class="mb-0"><i class="fa fa-home text-primary me-2"></i><?php echo $row['available']; ?></h6><br>
                                <div class="d-flex mb-3">
                                    <small class="border-end me-3 pe-3"><i class="fa fa-bed text-primary me-2"></i><?php echo $row['bed']; ?></small>
                                    <small class="border-end me-3 pe-3"><i class="fa fa-bath text-primary me-2"></i><?php echo $row['bath']; ?></small>
                                    <small><i class="fa fa-wifi text-primary me-2"></i>Wifi</small>

                                </div>
                               
                               
                                <div class="d-flex justify-content-between">
                                   <div class="col-md-12">
                                        <div class="form-floating">
                                            <select id="guest-count-single" class="form-control" name="guest-count-single" onchange="calculateAmount()">
                                <option value="0">--Select--</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <!-- Add more options as needed -->
                            </select>
                                            <label for="guest-count-single">Select Guest</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                    <?php
                                    $sql = "SELECT * FROM room Where r_name = 'Standard Twin Room'";
                                    $result = $conn->query($sql);
                                   
                                    
  // output data of each row
                                    while($row = $result->fetch_assoc()) {

                                    ?>

                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="room-item shadow rounded overflow-hidden">
                            <div class="position-relative">
                                <img class="img-fluid" src="img/js.jpg" style="height: 200px; width: 500px;" alt="">
                                <small class="position-absolute start-0 top-100 translate-middle-y bg-primary text-white rounded py-1 px-3 ms-4">₱<?php echo $row['price']; ?>/Night</small>
                            </div>
                            <div class="p-2 mt-2">
                                <div class="d-flex justify-content-between mb-3">
                                <h5 class="mb-0"><?php echo $row['r_name']; ?></h5>
                                    <div class="ps-2">
                                        <small class="fa fa-star text-primary"></small>
                                        <small class="fa fa-star text-primary"></small>
                                        <small class="fa fa-star text-primary"></small>
                                        <small class="fa fa-star text-primary"></small>
                                        <small class="fa fa-star text-primary"></small>
                                        <small class="fa fa-star text-primary"></small>

                                    </div>

                                </div>
                                <h6 class="mb-0"><i class="fa fa-home text-primary me-2"></i><?php echo $row['available']; ?></h6><br>
                                <div class="d-flex mb-3">
                                    <small class="border-end me-3 pe-3"><i class="fa fa-bed text-primary me-2"></i><?php echo $row['bed']; ?></small>
                                    <small class="border-end me-3 pe-3"><i class="fa fa-bath text-primary me-2"></i><?php echo $row['bath']; ?></small>
                                    <small><i class="fa fa-wifi text-primary me-2"></i>Wifi</small>

                                </div>
                               
                               
                                <div class="d-flex justify-content-between">
                                   <div class="col-md-12">
                                        <div class="form-floating">
                                            <select id="guest-count-twin" class="form-control" name="guest-count-twin" onchange="calculateAmount()">

                                <option value="0">--Select--</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <!-- Add more options as needed -->
                            </select>
                                            <label for="guest-count-twin">Select Guest</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                    <div class="col-lg-4">
                        <div class="wow fadeInUp" data-wow-delay="0.2s">
                            <form method="POST">
                                <div class="row g-3">
                                     <div class="form-floating">
                                            <input type="date" name="checkin_date" class="form-control" id="checkin_date" placeholder="Check-in Date" value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>" required>
                                            <label for="checkin_date">Check-in Date</label>
                                        </div>
                                    
                                       <div class="form-floating">
                                            <input type="date" id="checkout_date" class="form-control"name="checkout_date" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                                            <label for="checkout_date">Check-out Date</label>
                                        </div>
                                    <div class="col-md-12">
                                        <div class="form-floating">
                                        <select id="r_name" name="r_name" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Standard Single Room">Standard Single Room</option>
                                            <option value="Standard Twin Room">Standard Twin Room</option>
                                            <!-- Add more countries as needed -->
                                        </select>
                                            <label for="r_name">Select Room</label>
                                        </div>
                                    </div>
                                   
                                   
                                    
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                           <input type="text" class="form-control" id="fname" name="first_name" placeholder="Enter Firstname"  required>
                                            <label for="first_name">First Name</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                         <div class="form-floating">
                                           <input type="text" class="form-control" id="lname" name="last_name" placeholder="Enter Lastname"  required>
                                            <label for="last_name">Last Name</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                           <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                            <label for="email">Email</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
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


                                    <div class="col-md-6">
                                        <div class="form-floating">
                                        <select id="country" name="country" class="form-control" required>
                                            <option value="">--Select--</option>
                                            <option value="Philippines">Philippines</option>
                                            <option value="America">America</option>
                                            <!-- Add more countries as needed -->
                                        </select>
                                            <label for="country">Select Country</label>
                                        </div>
                                    </div>
                                     <div class="col-md-6">
                                        <div class="form-floating">
                                           <input type="text" class="form-control" id="amount" name="amount" placeholder="Enter amount" readonly required>
                                            <label for="amount">Amount</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                           <input type="text" class="form-control" id="payment" name="payment" placeholder="Payment Method" value="cash" readonly required>
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

        function calculateAmount() {
           
            var guestCountSingle = parseInt(document.getElementById("guest-count-single").value);
            var priceSingle = 850; // Example price for Standard Single Room

            var guestCountTwin = parseInt(document.getElementById("guest-count-twin").value);
            var priceTwin = 1600; // Example price for Standard Twin Room


            

            var checkinDate = new Date(document.getElementById("checkin_date").value);
            var checkoutDate = new Date(document.getElementById("checkout_date").value);
            var diffTime = Math.abs(checkoutDate - checkinDate);
            var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            var totalAmount = (guestCountSingle * priceSingle +
                               guestCountTwin * priceTwin
                               ) * diffDays;

            document.getElementById("amount").value = '₱ ' + totalAmount.toFixed(2);
        }

      



        // Call calculateAmount initially to set the initial amount
        calculateAmount();
    </script>

    <!-- SweetAlert JS -->
    <script src="js/sweetalert.js"></script>

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
<script>
document.querySelector('form').addEventListener('submit', function(e) {
    var checkinDate = document.getElementById("checkin_date").value;
    var checkoutDate = document.getElementById("checkout_date").value;
    var email = document.getElementById("email").value;
    var phone = document.getElementById("phone").value;

    if (new Date(checkinDate) >= new Date(checkoutDate)) {
        e.preventDefault();
        Swal.fire({
            title: "Error!",
            text: "Check-out date must be after check-in date.",
            icon: "error"
        });
        return false;
    }

    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        e.preventDefault();
        Swal.fire({
            title: "Error!",
            text: "Please enter a valid email address.",
            icon: "error"
        });
        return false;
    }

    var phonePattern = /^\d{11}$/;
    if (!phonePattern.test(phone)) {
        e.preventDefault();
        Swal.fire({
            title: "Error!",
            text: "Please enter a valid 11-digit phone number.",
            icon: "error"
        });
        return false;
    }
});
</script>
</body>

</html>