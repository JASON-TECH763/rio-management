<?php
session_start();
include("config/connect.php");
include("config/code-generator.php");

// Check if the user is authenticated
if (!isset($_SESSION['uname'])) {
    header("location:index.php");
    exit();
}

// Function to sanitize and validate inputs
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $prod_id = sanitize_input($_POST['prod_id']);
    $prod_name = sanitize_input($_POST['prod_name']);
    $prod_price = sanitize_input($_POST['prod_price']);

    // Prepared statement to prevent SQL injection
    if (isset($_FILES['prod_img']) && $_FILES['prod_img']['error'] == UPLOAD_ERR_OK) {
        $file_name = basename($_FILES['prod_img']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validate file type (allow only certain image types)
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_ext, $allowed_exts)) {
            // Generate a unique file name to prevent file overwrite
            $new_file_name = uniqid() . '.' . $file_ext;
            $upload_dir = 'assets/img/products/';
            $upload_path = $upload_dir . $new_file_name;

            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES["prod_img"]["tmp_name"], $upload_path)) {
                // Prepare the SQL query
                $stmt = $conn->prepare("INSERT INTO rpos_products (prod_id, prod_name, prod_price, prod_img) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssds", $prod_id, $prod_name, $prod_price, $new_file_name);

                if ($stmt->execute()) {
                    echo '<script>
                            window.onload = function() {
                                Swal.fire({
                                    title: "Success!",
                                    text: "Data added successfully",
                                    icon: "success"
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "product.php";
                                    }
                                });
                            };
                          </script>';
                } else {
                    echo '<script>
                            window.onload = function() {
                                Swal.fire({
                                    title: "Error!",
                                    text: "Failed to add data.",
                                    icon: "error"
                                });
                            };
                          </script>';
                }
                $stmt->close();
            } else {
                echo '<script>
                        window.onload = function() {
                            Swal.fire({
                                title: "Error!",
                                text: "Failed to upload image.",
                                icon: "error"
                            });
                        };
                      </script>';
            }
        } else {
            echo '<script>
                    window.onload = function() {
                        Swal.fire({
                            title: "Invalid File Type!",
                            text: "Please upload a valid image file.",
                            icon: "error"
                        });
                    };
                  </script>';
        }
    } else {
        echo '<script>
                window.onload = function() {
                    Swal.fire({
                        title: "Error!",
                        text: "No file uploaded or file upload error.",
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
                families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
                urls: ["assets/css/fonts.min.css"],
            },
            active: function() {
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
                            <h3 class="fw-bold mb-3">Product List</h3>
                            <h6 class="op-7 mb-2">Information</h6>
                        </div>
                    </div>
                </div>
                <div class="container mb-3">
                    <div class="card">
                        <center>
                            <div class="card-header">
                                Add New Product
                            </div>
                        </center>
                        <div class="card-body">
                            <div class="col-md-6 ms-auto me-auto">
                                <div class="wow fadeInUp" data-wow-delay="0.2s">
                                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                                        <div class="row g-3">
                                        <div class="col-md-6">
    <div class="form-floating">
        <!-- Product Name input with validation -->
        <input type="text" class="form-control" id="pname" name="prod_name" placeholder="Product Name" required 
               pattern="[A-Za-zÀ-ž' -]+" title="Product Name can contain only letters, hyphens, apostrophes, and spaces." 
               oninput="validateProductName()">
        <input type="hidden" name="prod_id" value="<?php echo htmlspecialchars($prod_id); ?>" class="form-control">
        <label for="pname">Product Name</label>
    </div>
</div>

<script>
    // JavaScript function to validate product name input
    function validateProductName() {
        var nameField = document.getElementById('pname');
        var value = nameField.value;

        // Regular expression to allow letters, hyphens, apostrophes, and spaces
        var regex = /^[A-Za-zÀ-ž' -]+$/;

        if (!regex.test(value)) {
            nameField.setCustomValidity("Please enter a valid product name (letters, hyphens, apostrophes, and spaces allowed).");
        } else {
            nameField.setCustomValidity(""); // Clear the message if valid
        }
    }
</script>

<?php
// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prod_name = $_POST['prod_name'];
    $prod_id = $_POST['prod_id'];

    // Sanitize the input to prevent HTML or script tags
    $prod_name_sanitized = htmlspecialchars($prod_name, ENT_QUOTES, 'UTF-8');

    // Validate the input: only allow letters, hyphens, apostrophes, and spaces
    if (!preg_match("/^[A-Za-zÀ-ž' -]+$/", $prod_name)) {
        echo '<div class="alert alert-danger">Invalid input: Please enter a valid product name (letters, hyphens, apostrophes, and spaces only).</div>';
    } else if ($prod_name !== $prod_name_sanitized) {
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
                             <input type="text" class="form-control" id="price" name="prod_price" placeholder="Price" required oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                <label for="price">Price</label>
                                     </div>
                                    </div>

                                            <div class="col-md-12">
                                                <div class="form-floating">
                                                    <input type="file" class="form-control" id="prod_img" name="prod_img" placeholder="Image" required>
                                                    <label for="prod_img">Image</label>
                                                </div>
                                            </div>
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

                <?php include("include/footer.php"); ?>

                <!-- Custom template | don't include it in your project! -->

                <!-- End Custom template -->
            </div>
            <!-- Core JS Files -->
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
                $(document).ready(function() {
                    $("#basic-datatables").DataTable({});

                    $("#multi-filter-select").DataTable({
                        pageLength: 5,
                        initComplete: function() {
                            this.api().columns().every(function() {
                                var column = this;
                                var select = $('<select class="form-select"><option value=""></option></select>')
                                    .appendTo($(column.footer()).empty())
                                    .on("change", function() {
                                        var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                        column.search(val ? "^" + val + "$" : "", true, false).draw();
                                    });

                                column.data().unique().sort().each(function(d, j) {
                                    select.append('<option value="' + d + '">' + d + "</option>");
                                });
                            });
                        },
                    });

                    // Add Row
                    $("#add-row").DataTable({
                        pageLength: 5,
                    });

                    var action = '<td> <div class="form-button-action"> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg" data-original-title="Edit Task"> <i class="fa fa-edit"></i> </button> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-danger" data-original-title="Remove"> <i class="fa fa-times"></i> </button> </div> </td>';

                    $("#addRowButton").click(function() {
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
