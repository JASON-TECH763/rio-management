<?php
session_start();
include("config/connect.php");

if (!isset($_SESSION['uname'])) {
    header("location:index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $r_name = $_POST['r_name'];
    $available = $_POST['available'];
    $bath = $_POST['bath'];
    $bed = $_POST['bed'];
    $price = $_POST['price'];

    if (isset($_FILES["r_img"]) && $_FILES["r_img"]["error"][0] == 0) {
        $target_dir = "uploads/"; // Relative path for front-end
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Create uploads folder if not exists
        }

        $uploadOk = 1;
        $uploadedImages = [];

        foreach ($_FILES['r_img']['tmp_name'] as $key => $tmp_name) {
            $imageFileType = strtolower(pathinfo($_FILES["r_img"]["name"][$key], PATHINFO_EXTENSION));
            $target_file = $target_dir . uniqid() . '.' . $imageFileType;

            $check = getimagesize($tmp_name);
            if ($check === false) {
                echo "File is not an image.";
                $uploadOk = 0;
                break;
            }

            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
                break;
            }

            if ($uploadOk == 1) {
                if (move_uploaded_file($tmp_name, $target_file)) {
                    $uploadedImages[] = basename($target_file); // Save just the filename
                } else {
                    echo "Sorry, there was an error uploading your file.";
                    var_dump(error_get_last());
                    $uploadOk = 0;
                }
            }
        }

        if ($uploadOk == 1 && count($uploadedImages) > 0) {
            // Insert room data into database
            $sql = "INSERT INTO room (r_name, available, bath, bed, price) VALUES ('$r_name', '$available', '$bath', '$bed', '$price')";
            if ($conn->query($sql) === TRUE) {
                $room_id = $conn->insert_id; // Get the last inserted room ID

                // Now insert the images into a separate table or store them in a way that associates with the room
                foreach ($uploadedImages as $image) {
                    $sql_image = "INSERT INTO room_images (room_id, image_path) VALUES ('$room_id', '$image')";
                    $conn->query($sql_image); // Store image names in room_images table
                }

                echo '<script>
                        window.onload = function() {
                            Swal.fire({
                                title: "Success!",
                                text: "Data added successfully",
                                icon: "success"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "room.php";
                                }
                            });
                        };
                      </script>';
            } else {
                echo "Error: " . $conn->error;
            }
        }
    } else {
        echo "No file was uploaded or an error occurred.";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Rio Management System</title>
    <meta
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
      name="viewport"
    />
    <link
      rel="icon"
      href="assets/img/a.jpg"
      type="image/x-icon"
    />

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
  </head>
  <body>
  <div class="wrapper">
        <!-- Sidebar -->
        <?php include("include/sidenavigation.php"); ?>

        <div class="main-panel">
            <?php include("include/header.php"); ?>


        <div class="container">
          <div class="page-inner">
            <div
              class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4"
            >
              <div>
                <h3 class="fw-bold mb-3">Room List</h3>
                <h6 class="op-7 mb-2">Information</h6>
              </div>
            
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
        pattern="[A-Za-zÀ-ž' -]+" title="Room Name can contain only letters, hyphens, apostrophes, and spaces." oninput="validateRoomName()">
        <label for="r_name">Room Name</label>
    </div>
</div>

<script>
    // JavaScript function to prevent script tags and allow certain symbols
    function validateRoomName() {
        var nameField = document.getElementById('r_name');
        var value = nameField.value;

        // Regular expression to allow letters, hyphens, apostrophes, and spaces, but no < or > (to prevent script tags)
        var regex = /^[A-Za-zÀ-ž' -]+$/;

        if (!regex.test(value)) {
            nameField.setCustomValidity("Please enter a valid room name (letters, hyphens, apostrophes, and spaces allowed).");
        } else {
            nameField.setCustomValidity(""); // Clear the message if valid
        }
    }
</script>

<?php
// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $r_name = $_POST['r_name'];

    // Sanitize input to remove any HTML or script tags
    $r_name_sanitized = htmlspecialchars($r_name, ENT_QUOTES, 'UTF-8');

    // Validate the input: allow letters, hyphens, apostrophes, and spaces, but block < or >
    if (!preg_match("/^[A-Za-zÀ-ž' -]+$/", $r_name)) {
        echo '<div class="alert alert-danger">Invalid input: Please enter a valid room name (letters, hyphens, apostrophes, and spaces only).</div>';
    } else if ($r_name !== $r_name_sanitized) {
        echo '<div class="alert alert-danger">Invalid input: HTML or script tags are not allowed.</div>';
    } else {
        // If valid, display success message
        echo '<div class="alert alert-success">Input is valid. Form submitted successfully!</div>';
        // Here, you can proceed with storing or processing the sanitized input.
    }
}
?>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                          <select class="form-control" id="available" name="available" required>
                                            <option value="1 Available Room">1 Available Room</option>
                                            <option value="2 Availble Room">2 Availble Room</option>
                                            <option value="Not Available">Not Available</option>
                                          </select>
                                            <label for="available">Availabilty</label>
                                        
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
                                            <label for="bed">Availabilty</label>
                                        
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
        <input type="file" class="form-control" id="r_img" name="r_img[]" multiple required>
        <label for="r_img">Room Images (select multiple)</label>
    </div>
    </div>


<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

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
                       
 <?php include("include/footer.php");?>
        
      <!-- Custom template | don't include it in your project! -->
    
      <!-- End Custom template -->
    </div>
    <!--   Core JS Files   -->
    <script src="assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

    <!-- Chart JS -->
    <script src="assets/js/plugin/chart.js/chart.min.js"></script>

    <!-- jQuery Sparkline -->
    <script src="assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

    <!-- Chart Circle -->
    <script src="assets/js/plugin/chart-circle/circles.min.js"></script>

    <!-- Datatables -->
    <script src="assets/js/plugin/datatables/datatables.min.js"></script>

    <!-- Bootstrap Notify -->
   

    <!-- jQuery Vector Maps -->
    <script src="assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
    <script src="assets/js/plugin/jsvectormap/world.js"></script>

    <!-- Sweet Alert -->
    <script src="assets/js/plugin/sweetalert/sweetalert.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="assets/js/kaiadmin.min.js"></script>

    <!-- Kaiadmin DEMO methods, don't include it in your project! -->
    <script src="assets/js/setting-demo.js"></script>
    <script src="assets/js/demo.js"></script>
    <script src="assets/js/sweetalert.js"></script>
    <script>
      $(document).ready(function () {
        $("#basic-datatables").DataTable({});

        $("#multi-filter-select").DataTable({
          pageLength: 5,
          initComplete: function () {
            this.api()
              .columns()
              .every(function () {
                var column = this;
                var select = $(
                  '<select class="form-select"><option value=""></option></select>'
                )
                  .appendTo($(column.footer()).empty())
                  .on("change", function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());

                    column
                      .search(val ? "^" + val + "$" : "", true, false)
                      .draw();
                  });

                column
                  .data()
                  .unique()
                  .sort()
                  .each(function (d, j) {
                    select.append(
                      '<option value="' + d + '">' + d + "</option>"
                    );
                  });
              });
          },
        });

        // Add Row
        $("#add-row").DataTable({
          pageLength: 5,
        });

        var action =
          '<td> <div class="form-button-action"> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg" data-original-title="Edit Task"> <i class="fa fa-edit"></i> </button> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-danger" data-original-title="Remove"> <i class="fa fa-times"></i> </button> </div> </td>';

        $("#addRowButton").click(function () {
          $("#add-row")
            .dataTable()
            .fnAddData([
              $("#addName").val(),
              $("#addPosition").val(),
              $("#addOffice").val(),
              action,
            ]);
          $("#addRowModal").modal("hide");
        });
      });
    </script>

  </body>
</html>
