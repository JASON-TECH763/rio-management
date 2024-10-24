<?php
session_start();
include("config/connect.php");

header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; img-src 'self' data:; font-src 'self' https://fonts.googleapis.com https://cdn.jsdelivr.net; frame-ancestors 'none'; form-action 'self'; base-uri 'self';");

if (!isset($_SESSION['uname'])) {
    header("location:index.php");
    exit();
}

if (isset($_GET['id']) && isset($_POST['submit'])) {
    $id = intval($_GET['id']);
    $r_name = trim($_POST['r_name']);
    $available = trim($_POST['available']);
    $bed = trim($_POST['bed']);
    $bath = trim($_POST['bath']);
    $price = trim($_POST['price']);
    
    // Sanitize user inputs before using them
    $r_name = htmlspecialchars($r_name, ENT_QUOTES, 'UTF-8');
    $available = htmlspecialchars($available, ENT_QUOTES, 'UTF-8');
    $bed = htmlspecialchars($bed, ENT_QUOTES, 'UTF-8');
    $bath = htmlspecialchars($bath, ENT_QUOTES, 'UTF-8');
    $price = htmlspecialchars($price, ENT_QUOTES, 'UTF-8');
    
    // Handle image upload securely
    $image_path = ""; 
    if (isset($_FILES["r_img"]) && $_FILES["r_img"]["error"] == 0) {
        $target_dir = __DIR__ . "/uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $imageFileType = strtolower(pathinfo($_FILES["r_img"]["name"], PATHINFO_EXTENSION));
        $target_file = $target_dir . uniqid() . '.' . $imageFileType;

        $uploadOk = 1;
        $check = getimagesize($_FILES["r_img"]["tmp_name"]);

        // Validate image upload
        if ($check === false) {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        $allowed_file_types = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $allowed_file_types)) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Limit file size to 5MB
        if ($_FILES["r_img"]["size"] > 5000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["r_img"]["tmp_name"], $target_file)) {
                $image_path = basename($target_file);
            } else {
                echo "Sorry, there was an error uploading your file.";
                var_dump(error_get_last());
            }
        }
    }

    // Use prepared statements for database updates
    if ($image_path) {
        $sql = "UPDATE room SET r_name = ?, available = ?, bed = ?, bath = ?, price = ?, r_img = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssi", $r_name, $available, $bed, $bath, $price, $image_path, $id);
    } else {
        $sql = "UPDATE room SET r_name = ?, available = ?, bed = ?, bath = ?, price = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssi", $r_name, $available, $bed, $bath, $price, $id);
    }

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        echo '<script>
                window.onload = function() {
                    Swal.fire({
                        title: "Success!",
                        text: "Room data successfully updated!",
                        icon: "success"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "room.php";
                        }
                    });
                };
              </script>';
    } else {
        echo '<script>
                window.onload = function() {
                    Swal.fire({
                        title: "Error!",
                        text: "Error updating room data.",
                        icon: "error"
                    });
                };
              </script>';
    }

    mysqli_stmt_close($stmt);
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Rio Management System</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="assets/img/a.jpg" type="image/x-icon" />
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
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/plugins.min.css" />
    <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
    <link rel="stylesheet" href="assets/css/demo.css" />
  </head>
  <body>
    <div class="wrapper">
      <?php include("include/sidenavigation.php");?>
      <div class="main-panel">
        <?php include("include/header.php");?>
        <div class="container">
          <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
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
                   Update Room
                </div>
              </center>
              <div class="card-body">
                <div class="col-md-6 ms-auto me-auto">
                  <div class="wow fadeInUp" data-wow-delay="0.2s">
                    <form method="POST" action="" enctype="multipart/form-data">
                      <?php
                      $id = intval($_GET['id']);
                      $query = mysqli_query($conn, "SELECT * FROM room WHERE id='$id'");
                      if ($query) {
                          while ($row = mysqli_fetch_array($query)) {
                      ?>
                          <div class="row g-3">
                          <div class="col-md-12">
    <div class="form-floating">
        <!-- Room Name input with validation -->
        <input type="text" class="form-control" id="r_name" name="r_name" placeholder="Room" required 
               pattern="[A-Za-zÀ-ž' -]+" title="Room Name can contain only letters, hyphens, apostrophes, and spaces." 
               value="<?php echo htmlspecialchars($row['r_name']); ?>" oninput="validateRoomName()">
        <input type="hidden" class="form-control" name="rid" placeholder="rid" required readonly value="<?php echo htmlspecialchars($row['id']); ?>">
        <label for="r_name">Room Name</label>
    </div>
</div>

<script>
    // JavaScript function to validate room name input
    function validateRoomName() {
        var nameField = document.getElementById('r_name');
        var value = nameField.value;

        // Regular expression to allow letters, hyphens, apostrophes, and spaces, but no < or >
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
    $rid = $_POST['rid'];

    // Sanitize the input to prevent HTML or script tags
    $r_name_sanitized = htmlspecialchars($r_name, ENT_QUOTES, 'UTF-8');

    // Validate the input: only allow letters, hyphens, apostrophes, and spaces
    if (!preg_match("/^[A-Za-zÀ-ž' -]+$/", $r_name)) {
        echo '<div class="alert alert-danger">Invalid input: Please enter a valid room name (letters, hyphens, apostrophes, and spaces only).</div>';
    } else if ($r_name !== $r_name_sanitized) {
        echo '<div class="alert alert-danger">Invalid input: HTML or script tags are not allowed.</div>';
    } else {
        // If valid, display a success message
        echo '<div class="alert alert-success">Input is valid. Form submitted successfully!</div>';
        // Here, you can proceed with updating the record in the database
    }
}
?>

                            <div class="col-md-6">
                              <div class="form-floating">
                                <select class="form-control" id="available" name="available" required>
                                  <option value="1 Available Room" <?php if($row['available'] == '1 Available Room') echo 'selected'; ?>>1 Available Room</option>
                                  <option value="2 Availble Room" <?php if($row['available'] == '2 Availble Room') echo 'selected'; ?>>2 Availble Room</option>
                                  <option value="Not Available" <?php if($row['available'] == 'Not Available') echo 'selected'; ?>>Not Available</option>
                                </select>
                                <label for="available">Availability</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-floating">
                                <select class="form-control" id="bath" name="bath" required>
                                  <option value="1 Bath" <?php if($row['bath'] == '1 Bath') echo 'selected'; ?>>1 Bath</option>
                                  <option value="2 Bath" <?php if($row['bath'] == '2 Bath') echo 'selected'; ?>>2 Bath</option>
                                  <option value="3 Bath" <?php if($row['bath'] == '3 Bath') echo 'selected'; ?>>3 Bath</option>
                                </select>
                                <label for="bath">Bath</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-floating">
                                <select class="form-control" id="bed" name="bed" required>
                                  <option value="1 Bed" <?php if($row['bed'] == '1 Bed') echo 'selected'; ?>>1 Bed</option>
                                  <option value="2 Bed" <?php if($row['bed'] == '2 Bed') echo 'selected'; ?>>2 Bed</option>
                                  <option value="3 Bed" <?php if($row['bed'] == '3 Bed') echo 'selected'; ?>>3 Bed</option>
                                </select>
                                <label for="bed">Bed</label>
                              </div>
                            </div>
                            <div class="col-md-6">
    <div class="form-floating">
        <input type="number" class="form-control" id="price" name="price" placeholder="Price" required 
               value="<?php echo htmlspecialchars($row['price']); ?>" 
               min="0" step="0.01" title="Please enter a valid price. Only numbers are allowed.">
        <label for="price">Price</label>
    </div>
</div>

                            <div class="col-md-12">
        <div class="form-floating">
            <input type="file" class="form-control" id="r_img" name="r_img" required>
            <label for="r_img">Room Image</label>
        </div>
    </div>

                            <div class="col-6">
                              <button class="btn btn-primary w-100 py-3" name="submit" type="submit">Submit</button>
                            </div>
                             <div class="col-6">
                              <button onclick="location.href='room.php'" class="btn btn-black w-100 py-3"  type="button">Cancel</button>
                            </div>
                          </div>
                      <?php
                          }
                      }
                      mysqli_close($conn);
                      ?>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php include("include/footer.php");?>
        </div>
      </div>
    </div>
    <script src="assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>
    <script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="assets/js/plugin/chart.js/chart.min.js"></script>
    <script src="assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>
    <script src="assets/js/plugin/chart-circle/circles.min.js"></script>
    <script src="assets/js/plugin/datatables/datatables.min.js"></script>
    <script src="assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
    <script src="assets/js/plugin/jsvectormap/world.js"></script>
    <script src="assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <script src="assets/js/kaiadmin.min.js"></script>
    <script src="assets/js/sweetalert.js"></script>
    <script>
      $(document).ready(function () {
        $("#basic-datatables").DataTable({});
        $("#multi-filter-select").DataTable({
          pageLength: 5,
          initComplete: function () {
            this.api().columns().every(function () {
              var column = this;
              var select = $('<select class="form-select"><option value=""></option></select>')
                .appendTo($(column.footer()).empty())
                .on("change", function () {
                  var val = $.fn.dataTable.util.escapeRegex($(this).val());
                  column.search(val ? "^" + val + "$" : "", true, false).draw();
                });
              column.data().unique().sort().each(function (d) {
                select.append('<option value="' + d + '">' + d + "</option>");
              });
            });
          },
        });
        $("#add-row").DataTable({ pageLength: 5 });
        var action = '<td> <div class="form-button-action"> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg" data-original-title="Edit Task"> <i class="fa fa-edit"></i> </button> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-danger" data-original-title="Remove"> <i class="fa fa-times"></i> </button> </div> </td>';
        $("#addRowButton").click(function () {
          $("#add-row").dataTable().fnAddData([
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
