<?php
session_start();
include("config/connect.php");


if (!isset($_SESSION['uname'])) {
    header("location:index.php");
    exit();
}


$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $staff_name = htmlspecialchars($_POST['staff_name']);
    $staff_last_name = htmlspecialchars($_POST['staff_last_name']);
    $staff_gender = htmlspecialchars($_POST['staff_gender']);
    $staff_email = htmlspecialchars($_POST['staff_email']);
    $staff_password = $_POST['staff_password'];

    // Sanitize input to prevent SQL injection
    $staff_name = mysqli_real_escape_string($conn, $staff_name);
    $staff_last_name = mysqli_real_escape_string($conn, $staff_last_name);
    $staff_gender = mysqli_real_escape_string($conn, $staff_gender);
    $staff_email = mysqli_real_escape_string($conn, $staff_email);
    $staff_password = mysqli_real_escape_string($conn, $staff_password);

    // Check if all fields are filled
    if (!empty($staff_name) && !empty($staff_last_name) && !empty($staff_gender) && !empty($staff_email) && !empty($staff_password)) {
        // Check if the email already exists
        $query = "SELECT * FROM rpos_staff WHERE staff_email='$staff_email'";
        $result = mysqli_query($conn, $query);
        $num_rows = mysqli_num_rows($result);

        if ($num_rows > 0) {
            // Email already exists
            $_SESSION['status'] = "error";
            $_SESSION['message'] = "An account with this email already exists.";
            header("Location: add_staff.php");
            exit();
        } else {
            // Prepare the SQL statement
            $sql = "INSERT INTO rpos_staff (staff_name, staff_last_name, staff_email, staff_password, staff_gender, date_created) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            
            if ($stmt = $conn->prepare($sql)) {
                // Bind parameters and pass the plain password (no hashing)
                $stmt->bind_param("sssss", $staff_name, $staff_last_name, $staff_email, $staff_password, $staff_gender);
                
                // Execute the query
                if ($stmt->execute()) {
                    // Account created successfully
                    $_SESSION['status'] = "success";
                    $_SESSION['message'] = "Staff account has been created successfully.";
                    header("Location: add_staff.php");
                    exit();
                } else {
                    // Error creating account
                    $_SESSION['status'] = "error";
                    $_SESSION['message'] = "There was an error creating the staff account.";
                    header("Location: add_staff.php");
                    exit();
                }
            }
        }
    } else {
        $_SESSION['status'] = "error";
        $_SESSION['message'] = "Please fill all the fields.";
        header("Location: add_staff.php");
        exit();
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
 <!-- SweetAlert2 CSS & JS -->
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                            <h3 class="fw-bold mb-3">Staff List</h3>
                            <h6 class="op-7 mb-2">Information</h6>
                        </div>
                    </div>
                </div>
        <div class="container mb-3">
            <center>
            <h2 class="my-4">Add New Staff</h2>
</center>
<div class="card-body">
                 <div class="col-md-6 ms-auto me-auto">
                        <div class="wow fadeInUp" data-wow-delay="0.2s">
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
                        <div class="row">
                        <div class="row">
    <div class="col-md-6 mb-3">
        <label for="staff_name" class="form-label">First Name</label>
        <input type="text" class="form-control" id="staff_name" name="staff_name" placeholder="" required 
        pattern="[A-Za-zÀ-ž' -]+" title="First Name can contain only letters, hyphens, apostrophes, and spaces." oninput="validateStaffName()">
    </div>
    <div class="col-md-6 mb-3">
        <label for="staff_last_name" class="form-label">Last Name</label>
        <input type="text" class="form-control" id="staff_last_name" name="staff_last_name" placeholder="" required 
        pattern="[A-Za-zÀ-ž' -]+" title="Last Name can contain only letters, hyphens, apostrophes, and spaces." oninput="validateStaffLastName()">
    </div>
</div>

<script>
    // JavaScript function to validate First Name
    function validateStaffName() {
        var nameField = document.getElementById('staff_name');
        var value = nameField.value;

        // Regular expression to allow letters, hyphens, apostrophes, and spaces, but no < or > (to prevent script tags)
        var regex = /^[A-Za-zÀ-ž' -]+$/;

        if (!regex.test(value)) {
            nameField.setCustomValidity("Please enter a valid first name (letters, hyphens, apostrophes, and spaces allowed).");
        } else {
            nameField.setCustomValidity(""); // Clear the message if valid
        }
    }

    // JavaScript function to validate Last Name
    function validateStaffLastName() {
        var nameField = document.getElementById('staff_last_name');
        var value = nameField.value;

        // Regular expression to allow letters, hyphens, apostrophes, and spaces, but no < or > (to prevent script tags)
        var regex = /^[A-Za-zÀ-ž' -]+$/;

        if (!regex.test(value)) {
            nameField.setCustomValidity("Please enter a valid last name (letters, hyphens, apostrophes, and spaces allowed).");
        } else {
            nameField.setCustomValidity(""); // Clear the message if valid
        }
    }
</script>

<?php
// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $staff_name = $_POST['staff_name'];
    $staff_last_name = $_POST['staff_last_name'];

    // Sanitize inputs to remove any HTML or script tags
    $staff_name_sanitized = htmlspecialchars($staff_name, ENT_QUOTES, 'UTF-8');
    $staff_last_name_sanitized = htmlspecialchars($staff_last_name, ENT_QUOTES, 'UTF-8');

    // Validate the input: allow letters, hyphens, apostrophes, and spaces, but block < or >
   if (!preg_match("/^[A-Za-zÀ-ž' -]+$/", $staff_name)) {
        echo '<div class="alert alert-danger">Invalid input: Please enter a valid first name (letters, hyphens, apostrophes, and spaces only).</div>';
    } elseif ($staff_name !== $staff_name_sanitized) {
        echo '<div class="alert alert-danger">Invalid input: HTML or script tags are not allowed in the first name.</div>';
    } elseif (!preg_match("/^[A-Za-zÀ-ž' -]+$/", $staff_last_name)) {
        echo '<div class="alert alert-danger">Invalid input: Please enter a valid last name (letters, hyphens, apostrophes, and spaces only).</div>';
    } elseif ($staff_last_name !== $staff_last_name_sanitized) {
        echo '<div class="alert alert-danger">Invalid input: HTML or script tags are not allowed in the last name.</div>';
    } else {
        // If valid, display success message
        echo '<div class="alert alert-success">Input is valid. Form submitted successfully!</div>';
        // Proceed with storing or processing the sanitized input.
    }
}
?>

                    <div class="col-md-6 mb-3">
                        <label for="staff_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="staff_email" name="staff_email" required>
                    </div>
                    <div class="col-md-6 mb-3">
    <label for="staff_password" class="form-label">Password</label>
    <input type="password" class="form-control" id="staff_password" name="staff_password" 
           pattern="(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}" 
           title="Password must contain at least one uppercase letter, one number, one special character, and be at least 8 characters long" 
           minlength="8" required>
</div>

                    <div class="col-md-6 mb-3">
                        <label for="staff_gender" class="form-label">Gender</label>
                        <select class="form-control" id="staff_gender" name="staff_gender" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <center>
                    <div class="col-8 mb-4">
                        <button type="submit" class="btn btn-primary w-100 py-3">Add Staff</button>
                    </div>
</center>
                </div>
            </form>
        </div>

        <?php include("include/footer.php"); ?>

        <!-- SweetAlert Trigger -->
        <?php if (isset($_SESSION['status']) && $_SESSION['status'] != ""): ?>
        <script>
            Swal.fire({
                title: '<?php echo ($_SESSION["status"] == "success") ? "Account Created!" : "Error!"; ?>',
                text: '<?php echo $_SESSION["message"]; ?>',
                icon: '<?php echo $_SESSION["status"]; ?>',
                confirmButtonText: 'OK'
            }).then(function() {
                window.location = 'staff.php';
            });
        </script>
        <?php
            unset($_SESSION['status']);
            unset($_SESSION['message']);
        endif;
        ?>
    </div>
</body>
</html>
