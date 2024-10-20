<?php
require_once 'add_room_backend.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Rio Management System</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="assets/img/a.jpg" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: { families: ["Public Sans:300,400,500,600,700"] },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["assets/css/fonts.min.css"],
            },
            active: function () {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/plugins.min.css" />
    <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
    <link rel="stylesheet" href="assets/css/demo.css" />
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <?php include("include/sidenavigation.php"); ?>

        <div class="main-panel">
            <?php include("include/header.php"); ?>

            <div class="container">
                <div class="page-inner">
                    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                        <div>
                            <h3 class="fw-bold mb-3">Room List</h3>
                            <h6 class="op-7 mb-2">Information</h6>
                        </div>
                    </div>
                    <div class="container mb-3">
                        <div class="card">
                            <center>
                                <div class="card-header">
                                    Add New Room
                                </div>
                            </center>
                            <div class="card-body">
                                <div class="col-md-6 ms-auto me-auto">
                                    <div class="wow fadeInUp" data-wow-delay="0.2s">
                                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
                                            <div class="row g-3">
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" id="r_name" name="r_name" placeholder="Enter Room Name" required 
                                                               pattern="[A-Za-z0-9 '-]{1,100}" title="Room Name can contain only letters, numbers, spaces, hyphens, and apostrophes (up to 100 characters)." oninput="validateRoomName(this)">
                                                        <label for="r_name">Room Name</label>
                                                    </div>
                                                </div>
                                                <!-- Rest of the form fields... -->
                                                <div class="col-12">
                                                    <button class="btn btn-primary w-100 py-3" name="submit" type="submit">Submit</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php include("include/footer.php"); ?>
        </div>
    </div>

    <!-- JavaScript files... -->
    <script src="assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>
    <!-- Other JS files... -->
    <script>
        function validateRoomName(input) {
            var value = input.value;
            var regex = /^[A-Za-z0-9 '-]{1,100}$/;
            
            if (!regex.test(value)) {
                input.setCustomValidity("Please enter a valid room name (up to 100 characters, letters, numbers, spaces, hyphens, and apostrophes allowed).");
            } else {
                input.setCustomValidity("");
            }
        }

        $(document).ready(function () {
            // DataTables initialization...
        });
    </script>
</body>
</html>