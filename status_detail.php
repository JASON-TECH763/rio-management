<?php
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
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Rio Management System</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
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


       


       


<br><br><br><br><br>
        <!-- Room Start -->
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                   
                    <h1 class="mb-5">Booking<span class="text-primary text-uppercase">Details</span></h1>
                </div>
        <div class="cart-section mt-150 mb-150">
    <div class="container">
        <div class="table-wrapper" id="empty">
            <div class="table-title">
                <div class="row">
                    <div class="col-sm-10">
                        <h2>Details</h2>
                    </div>
                    <div class="col-sm-2">                  
                        <a href="#" onclick="window.print()" class="btn btn-warning"> <i class="fas fa-print"></i> <span>Print</span></a>
                    </div>
                </div>
            </div>
            
            <table class="table table-striped table-hover text-center">
                <thead>
                    <tr>
                        <th>Booking No.</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone No.</th>
                        <th>Amount</th>
                        <th>Booking Date</th>                   
                        <th>Status</th>
                                         
                    </tr>
                </thead>
                <tbody>
                <?php
                    include("config/connect.php");
                    if($_SERVER["REQUEST_METHOD"] == "POST") {
                    if(isset($_POST['check'])) {
                        $booking_no = $_POST['booking_number'];
                        $fetch_details = mysqli_query($conn, "select * from reservations where booking_id='$booking_no'");
                        $fetch_row = mysqli_num_rows($fetch_details);
                        if($fetch_row>0)
                        {
                        while($row = mysqli_fetch_assoc($fetch_details)){
                            $booking_id = htmlspecialchars($row['booking_id']);
                            $first_name = htmlspecialchars($row['first_name']);
                            $last_name = htmlspecialchars($row['last_name']);
                            $email = htmlspecialchars($row['email']);
                            $mobile = htmlspecialchars($row['phone']);
                            $booking_date = htmlspecialchars($row['checkin_date']);
                            
                            $amount = htmlspecialchars($row['amount']);
                            $status = htmlspecialchars($row['status']);
                        
                            
                            echo '<tr>
                                    <td>' . $booking_id . '</td>
                                    <td>' . $first_name.' '.$last_name.'</td>
                                    
                                    <td>' . $email . '</td>
                                    <td>' . $mobile . '</td>
                                    <td>' . $amount . '</td>
                                    <td>' . $booking_date . '</td>
                                     
                                    
                                    
                                    <td>'.$status.'</td>       

                                    </tr>';
                        }
                    }
                    else
                    {
                    echo '<script>
                    window.onload = function() {
                        Swal.fire({
                            title: "Error!",
                            text: "Invalid booked number.",
                            icon: "error"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "check_status.php";
                            }
                        });
                    };
                  </script>';

                    }
                }
            }

            ?>
                </tbody>
            </table>
        </div>
    </div> 
</div><br><br><br><br><br><br><br><br>
        <!-- Room End -->


      
        

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
</body>

</html>