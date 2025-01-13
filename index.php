<?php
session_start();
include('config/connect.php');

// Set security headers
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; img-src 'self' data:; font-src 'self' https://fonts.googleapis.com https://cdn.jsdelivr.net; frame-ancestors 'none'; form-action 'self'; base-uri 'self';");
header("Permissions-Policy: geolocation=(), camera=(), microphone=(), payment=(), usb=(), battery=(), interest-cohort=()");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
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


        <!-- Carousel Start -->
<div class="container-fluid p-0 mb-5">
    <div id="header-carousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="w-100 carousel-image" src="img/carousel1.jpg" alt="Image">
                <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                    <div class="p-3" style="max-width: 700px;">
                        <h6 class="section-title text-white text-uppercase mb-3 animated slideInDown">Chilling</h6>
                        <h1 class="display-3 text-white mb-4 animated slideInDown">Discover An Affordable GuestHouse</h1>
                        <a href="room.php" class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">Book A Room</a>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <img class="w-100 carousel-image" src="img/carousel2.jpg" alt="Image">
                <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                    <div class="p-3" style="max-width: 700px;">
                        <h6 class="section-title text-white text-uppercase mb-3 animated slideInDown">Chilling</h6>
                        <h1 class="display-3 text-white mb-4 animated slideInDown">Discover An Affordable GuestHouse</h1>
                        <a href="room.php" class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">Book A Room</a>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <img class="w-100 carousel-image" src="img/carousel3.jpg" alt="Image">
                <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                    <div class="p-3" style="max-width: 700px;">
                        <h6 class="section-title text-white text-uppercase mb-3 animated slideInDown">Chilling</h6>
                        <h1 class="display-3 text-white mb-4 animated slideInDown">Discover An Affordable GuestHouse</h1>
                        <a href="room.php" class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">Book A Room</a>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <img class="w-100 carousel-image" src="img/carousel4.jpg" alt="Image">
                <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                    <div class="p-3" style="max-width: 700px;">
                        <h6 class="section-title text-white text-uppercase mb-3 animated slideInDown">Chilling</h6>
                        <h1 class="display-3 text-white mb-4 animated slideInDown">Discover An Affordable GuestHouse</h1>
                        <a href="room.php" class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">Book A Room</a>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#header-carousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#header-carousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>

<style>
    .carousel-image {
        height: 500px; /* Adjust this height as needed */
        object-fit: cover;
        object-position: center;
    }
</style>
<!-- Carousel End -->

        <!-- Carousel End -->


        <!-- Booking Start -->
      <!--   <div class="container-fluid booking pb-5 wow fadeIn" data-wow-delay="0.1s">
            <div class="container">
                <div class="bg-white shadow" style="padding: 35px;">
                    <div class="row g-2">
                        <div class="col-md-10">
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <div class="date" id="date1" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input"
                                            placeholder="Check in" data-target="#date1" data-toggle="datetimepicker" />
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="date" id="date2" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input" placeholder="Check out" data-target="#date2" data-toggle="datetimepicker"/>
                                    </div>
                                </div>
                               
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <!-- Booking End -->


        <!-- About Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6">
                        <h6 class="section-title text-start text-primary text-uppercase">About Us</h6>
                        <h1 class="mb-4">Welcome to <span class="text-primary text-uppercase">RIO GUESTHOUSE</span></h1>
                        <p class="mb-4">Your home away from home in the heart of Rio de Janeiro. Nestled in the vibrant city center, our guesthouse offers a luxurious and comfortable stay for travelers from around the world. Whether you're here for business or leisure, our dedicated staff is committed to ensuring you have a memorable experience. Discover our beautifully appointed rooms, exceptional amenities, and personalized service that makes Rio Guesthouse the perfect choice for your stay in Rio.</p>
                        <div class="row g-3 pb-4">
                            <div class="col-sm-4 wow fadeIn" data-wow-delay="0.1s">
                                <div class="border rounded p-1">
                                    <div class="border rounded text-center p-4">
                                        <i class="fa fa-hotel fa-2x text-primary mb-2"></i>
                                        <h2 class="mb-1" data-toggle="counter-up">4</h2>
                                        <p class="mb-0">Rooms</p>
                                    </div>
                                </div>
                            </div>
                            <!--div class="col-sm-4 wow fadeIn" data-wow-delay="0.3s">
                                <div class="border rounded p-1">
                                    <div class="border rounded text-center p-4">
                                        <i class="fa fa-users-cog fa-2x text-primary mb-2"></i>
                                        <h2 class="mb-1" data-toggle="counter-up">6</h2>
                                        <p class="mb-0">Staffs</p>
                                    </div>
                                </div>
                            </div>-->
                           
                        </div>
                        <a class="btn btn-primary py-3 px-5 mt-2" href="">Explore More</a>
                    </div>
                    <div class="col-lg-6">
                    <div class="row g-3">
                            <div class="col-6 text-end">
                                <img class="img-fluid rounded w-75 wow zoomIn" data-wow-delay="0.1s" src="img/jp.jpg" style="margin-top: 25%;">
                            </div>
                            <div class="col-6 text-start">
                                <img class="img-fluid rounded w-100 wow zoomIn" data-wow-delay="0.3s" src="img/b.jpg">
                            </div>
                            <div class="col-6 text-end">
                                <img class="img-fluid rounded w-50 wow zoomIn" data-wow-delay="0.5s" src="img/js.jpg">
                            </div>
                            <div class="col-6 text-start">
                                <img class="img-fluid rounded w-75 wow zoomIn" data-wow-delay="0.7s" src="img/carousel1.jpg">
                            </div>
        
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- About End -->


        <!-- Room Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    <h6 class="section-title text-center text-primary text-uppercase">Our Rooms</h6>
                    <h1 class="mb-5">Explore Our <span class="text-primary text-uppercase">Rooms</span></h1>
                </div>
                <div class="row g-4">
                <?php
// Fetch all rooms from the 'room' table
$sql = "SELECT * FROM room";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Fetch all images for the current room
        $room_id = $row['id'];
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

    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
        <div class="room-item shadow rounded overflow-hidden">
            <div class="position-relative">
                <!-- Carousel for room images -->
                <div id="carousel-<?php echo $row['id']; ?>" class="carousel slide" data-bs-ride="carousel">
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
                    <button class="carousel-control-prev" type="button" data-bs-target="#carousel-<?php echo $row['id']; ?>" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carousel-<?php echo $row['id']; ?>" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
                <!-- Display the dynamic room price -->
                <small class="position-absolute start-0 top-100 translate-middle-y bg-primary text-white rounded py-1 px-3 ms-4">₱<?php echo htmlspecialchars($row['price']); ?>/Night</small>
            </div>
       
            <div class="p-2 mt-2">
                <div class="d-flex justify-content-between mb-3">
                    <!-- Display the dynamic room name -->
                    <h5 class="mb-0"><?php echo $row['r_name']; ?></h5>
                    <div class="ps-2">
                        <!-- Display 5 stars -->
                        <small class="fa fa-star text-primary"></small>
                        <small class="fa fa-star text-primary"></small>
                        <small class="fa fa-star text-primary"></small>
                        <small class="fa fa-star text-primary"></small>
                        <small class="fa fa-star text-primary"></small>
                    </div>
                </div>

                <!-- Display the dynamic room availability -->
                <h6 class="mb-0"><i class="fa fa-home text-primary me-2"></i><?php echo $row['available']; ?></h6><br>

               <div class="d-flex mb-3">
    <!-- Display the dynamic bed and bath information -->
    <small class="border-end me-3 pe-3"><i class="fa fa-bed text-primary me-2"></i><?php echo $row['bed']; ?></small>
    <small class="border-end me-3 pe-3"><i class="fa fa-bath text-primary me-2"></i><?php echo $row['bath']; ?></small>
    <small><i class="fa fa-snowflake text-primary me-2"></i>Aircon</small>
</div>

                <div class="d-flex justify-content-between">
                    <!-- Book Now button linking to booking page -->
<a class="btn btn-sm btn-dark rounded py-2 px-4" href="booking.php?room_id=<?php echo $row['id']; ?>">Book Now</a>

                </div>
            </div>
        </div>
    </div>

<?php
    }
} else {
    echo "No rooms available.";
}
?>
                                                
                </div>
            </div>
        </div><br><br><br><br>
        <!-- Room End -->



        <!-- Service Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    <h6 class="section-title text-center text-primary text-uppercase">Our Services</h6>
                    <h1 class="mb-5">Explore Our <span class="text-primary text-uppercase">Services</span></h1>
                </div>
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <a class="service-item rounded" href="">
                            <div class="service-icon bg-transparent border rounded p-1">
                                <div class="w-100 h-100 border rounded d-flex align-items-center justify-content-center">
                                    <i class="fa fa-hotel fa-2x text-primary"></i>
                                </div>
                            </div>
                            <h5 class="mb-3">Rooms & Appartment</h5>
                            <p class="text-body mb-0"></p>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.2s">
                        <a class="service-item rounded" href="">
                            <div class="service-icon bg-transparent border rounded p-1">
                                <div class="w-100 h-100 border rounded d-flex align-items-center justify-content-center">
                                    <i class="fa fa-utensils fa-2x text-primary"></i>
                                </div>
                            </div>
                            <h5 class="mb-3">Food & Restaurant</h5>
                            <p class="text-body mb-0"></p>
                        </a>
                    </div>
                   
                   
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                        <a class="service-item rounded" href="">
                            <div class="service-icon bg-transparent border rounded p-1">
                                <div class="w-100 h-100 border rounded d-flex align-items-center justify-content-center">
                                    <i class="fa fa-glass-cheers fa-2x text-primary"></i>
                                </div>
                            </div>
                            <h5 class="mb-3">Live Band</h5>
                            <p class="text-body mb-0"></p>
                        </a>
                    </div>
                   
                </div>
            </div>
        </div><br><br><br><br>
        <!-- Service End -->


       
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