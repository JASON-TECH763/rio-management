<?php 
include("config/connect.php");

// Fetch all rooms and their images from the database
$sql = "
    SELECT r.id, r.r_name, r.available, r.bath, r.bed, r.price, ri.image_path
    FROM room AS r
    LEFT JOIN room_images AS ri ON r.id = ri.room_id
";

$result = $conn->query($sql);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}

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


        <!-- Page Header Start -->
        <div class="container-fluid page-header mb-5 p-0" style="background-image: url(img/carousel4.jpg);">
            <div class="container-fluid page-header-inner py-5">
                <div class="container text-center pb-5">
                    <h1 class="display-3 text-white mb-3 animated slideInDown">Rooms</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center text-uppercase">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            
                            <li class="breadcrumb-item text-white active" aria-current="page">Rooms</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <!-- Page Header End -->



    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title text-center text-primary text-uppercase">Our Rooms</h6>
                <h1 class="mb-5">Explore Our <span class="text-primary text-uppercase">Rooms</span></h1>
            </div>
            <div class="row g-4">

            <?php
            if ($result->num_rows > 0) {
                // Prepare an array to collect room data
                $rooms = [];
                while ($row = $result->fetch_assoc()) {
                    $rooms[$row['id']]['id'] = $row['id'];
                    $rooms[$row['id']]['r_name'] = $row['r_name'];
                    $rooms[$row['id']]['available'] = $row['available'];
                    $rooms[$row['id']]['bath'] = $row['bath'];
                    $rooms[$row['id']]['bed'] = $row['bed'];
                    $rooms[$row['id']]['price'] = $row['price'];
                    $rooms[$row['id']]['images'][] = $row['image_path']; // Collect images in an array
                }

                // Display each room
                foreach ($rooms as $room) {
            ?>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="room-item shadow rounded overflow-hidden">
                        <div class="position-relative">
                            <!-- Carousel for room images -->
                            <div id="carousel-<?php echo $room['id']; ?>" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    <?php
                                    // Check if images exist and create carousel items
                                    if (!empty($room['images'])) {
                                        foreach ($room['images'] as $index => $image) {
                                            if (!empty($image)) { // Check if image path is not empty
                                                echo '<div class="carousel-item ' . ($index === 0 ? 'active' : '') . '">';
                                                echo '<img src="admin/uploads/' . htmlspecialchars($image) . '" class="d-block w-100" alt="Room Image" style="width: 100%; height: 300px; object-fit: cover;">';
                                                echo '</div>';
                                            }
                                        }
                                    } else {
                                        // Display a default image if no images are found
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
                            <!-- Display the dynamic room price -->
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
                            <div class="d-flex justify-content-between">
                                <a class="btn btn-sm btn-dark rounded py-2 px-4" href="booking.php?room_id=<?php echo $room['id']; ?>">Book Now</a>
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
    </div>

    <!-- Footer Start -->
    <?php include("footer.php");?>
    <!-- Footer End -->

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
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
