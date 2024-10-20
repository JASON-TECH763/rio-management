<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Rio Management System - Add Room</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="assets/img/a.jpg" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: { families: ["Public Sans:300,400,500,600,700"] },
            custom: {
                families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
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
                            <div class="card-header text-center">
                                Add New Room
                            </div>
                            <div class="card-body">
                                <div class="col-md-6 mx-auto">
                                    <div class="wow fadeInUp" data-wow-delay="0.2s">
                                        <form id="addRoomForm" enctype="multipart/form-data">
                                            <div class="row g-3">
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" id="r_name" name="r_name" placeholder="Enter Room Name" required 
                                                               pattern="[A-Za-z0-9 '-]{1,100}" title="Room Name can contain only letters, numbers, spaces, hyphens, and apostrophes (up to 100 characters)." oninput="validateRoomName(this)">
                                                        <label for="r_name">Room Name</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <select class="form-control" id="available" name="available" required>
                                                            <option value="1 Available Room">1 Available Room</option>
                                                            <option value="2 Available Room">2 Available Room</option>
                                                            <option value="Not Available">Not Available</option>
                                                        </select>
                                                        <label for="available">Availability</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <select class="form-control" id="bath" name="bath" required>
                                                            <option value="1 Bath">1 Bath</option>
                                                            <option value="2 Bath">2 Bath</option>
                                                            <option value="3 Bath">3 Bath</option>
                                                        </select>
                                                        <label for="bath">Bath</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <select class="form-control" id="bed" name="bed" required>
                                                            <option value="1 Bed">1 Bed</option>
                                                            <option value="2 Bed">2 Bed</option>
                                                            <option value="3 Bed">3 Bed</option>
                                                        </select>
                                                        <label for="bed">Bed</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" id="price" name="price" placeholder="Price" required oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                                        <label for="price">Price</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input type="file" class="form-control" id="r_img" name="r_img" required>
                                                        <label for="r_img">Room Image</label>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <button class="btn btn-primary w-100 py-3" type="submit">Submit</button>
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

    <!-- Core JS Files -->
    <script src="assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>
    <script src="assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <script src="assets/js/kaiadmin.min.js"></script>

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

        $(document).ready(function() {
            $('#addRoomForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: 'add_room_backend.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.success) {
                            Swal.fire({
                                title: "Success!",
                                text: result.message,
                                icon: "success"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "room.php";
                                }
                            });
                        } else {
                            Swal.fire({
                                title: "Error!",
                                text: result.message,
                                icon: "error"
                            });
                        }
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            });
        });
    </script>
</body>
</html>