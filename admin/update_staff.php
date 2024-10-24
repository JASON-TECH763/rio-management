<?php
session_start();
include("config/connect.php");

header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; img-src 'self' data:; font-src 'self' https://fonts.googleapis.com https://cdn.jsdelivr.net; frame-ancestors 'none'; form-action 'self'; base-uri 'self';");

if (!isset($_SESSION['uname'])) {
    header("location:index.php");
    exit();
}

$staff = []; // Initialize an empty staff array

// Check if 'id' is passed in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $staff_id = $_GET['id'];

    // Fetch the existing staff record
    $query = "SELECT * FROM rpos_staff WHERE id = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $staff_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $staff = $result->fetch_assoc(); // Fetch the staff data
        } else {
            $_SESSION['status'] = "error";
            $_SESSION['message'] = "Staff record not found.";
            header("Location: staff.php");
            exit();
        }
    } else {
        $_SESSION['status'] = "error";
        $_SESSION['message'] = "Error fetching staff record.";
        header("Location: staff.php");
        exit();
    }
} 
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $staff_name = htmlspecialchars($_POST['staff_name']);
    $staff_last_name = htmlspecialchars($_POST['staff_last_name']);
    $staff_gender = htmlspecialchars($_POST['staff_gender']);
    $staff_email = htmlspecialchars($_POST['staff_email']);
    $staff_password = $_POST['staff_password'];

    // Sanitize input
    $staff_name = mysqli_real_escape_string($conn, $staff_name);
    $staff_last_name = mysqli_real_escape_string($conn, $staff_last_name);
    $staff_gender = mysqli_real_escape_string($conn, $staff_gender);
    $staff_email = mysqli_real_escape_string($conn, $staff_email);
    $staff_password = mysqli_real_escape_string($conn, $staff_password);

    // Check if all fields are filled
    if (!empty($staff_name) && !empty($staff_last_name) && !empty($staff_gender) && !empty($staff_email)) {
        // Prepare the SQL statement to update the record
        $sql = "UPDATE rpos_staff SET staff_name=?, staff_last_name=?, staff_email=?, staff_password=?, staff_gender=? WHERE id=?";
        
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters
            $stmt->bind_param("sssssi", $staff_name, $staff_last_name, $staff_email, $staff_password, $staff_gender, $staff_id);
            
            // Execute the query
            if ($stmt->execute()) {
                // Account updated successfully
                $_SESSION['status'] = "success";
                $_SESSION['message'] = "Staff account has been updated successfully.";
                header("Location: update_staff.php");
                exit();
            } else {
                // Error updating account
                $_SESSION['status'] = "error";
                $_SESSION['message'] = "There was an error updating the staff account.";
                header("Location: update_staff.php?id=$staff_id");
                exit();
            }
        }
    } else {
        $_SESSION['status'] = "error";
        $_SESSION['message'] = "Please fill all the fields.";
        header("Location: update_staff.php?id=$staff_id");
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
                        <h3 class="fw-bold mb-3">Update Staff Information</h3>
                    </div>
                </div>
            </div>
            <div class="container mb-3">
                <center>
                <h2 class="my-4">Update Staff Details</h2>
                </center>
                <div class="card-body">
                <div class="col-md-6 ms-auto me-auto">
                  <div class="wow fadeInUp" data-wow-delay="0.2s">
                    <form method="POST" action="" enctype="multipart/form-data">
                    <form action="update_staff.php?id=<?php echo $staff_id; ?>" method="POST">
                        <div class="row">
                        <div class="row">
    <div class="col-md-6 mb-3">
        <label for="staff_name" class="form-label">First Name</label>
        <input type="text" class="form-control" id="staff_name" name="staff_name" 
            value="<?php echo isset($staff['staff_name']) ? htmlspecialchars($staff['staff_name'], ENT_QUOTES, 'UTF-8') : ''; ?>" 
            placeholder="Enter First Name" required 
            pattern="[A-Za-zÀ-ž' -]+" 
            title="First Name can contain only letters, hyphens, apostrophes, and spaces."
            oninput="validateStaffName()">
    </div>
    <div class="col-md-6 mb-3">
        <label for="staff_last_name" class="form-label">Last Name</label>
        <input type="text" class="form-control" id="staff_last_name" name="staff_last_name" 
            value="<?php echo isset($staff['staff_last_name']) ? htmlspecialchars($staff['staff_last_name'], ENT_QUOTES, 'UTF-8') : ''; ?>" 
            placeholder="Enter Last Name" required 
            pattern="[A-Za-zÀ-ž' -]+" 
            title="Last Name can contain only letters, hyphens, apostrophes, and spaces."
            oninput="validateStaffLastName()">
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
                                <input type="email" class="form-control" id="staff_email" name="staff_email" value="<?php echo isset($staff['staff_email']) ? $staff['staff_email'] : ''; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="staff_password" class="form-label">Password</label>
                                <input type="text" class="form-control" id="staff_password" name="staff_password" value="<?php echo isset($staff['staff_password']) ? $staff['staff_password'] : ''; ?>" required>
                            </div>
                            <div class="col-md-12">
                                <label for="staff_gender" class="form-label">Gender</label>
                                <select class="form-control" id="staff_gender" name="staff_gender" required>
                                    <option value="Male" <?php if (isset($staff['staff_gender']) && $staff['staff_gender'] == 'Male') echo 'selected'; ?>>Male</option>
                                    <option value="Female" <?php if (isset($staff['staff_gender']) && $staff['staff_gender'] == 'Female') echo 'selected'; ?>>Female</option>
                                </select>
                            </div>
                         <br> <br> <br> <br> <br>
                           
                            <div class="col-6">
                              <button class="btn btn-primary w-100 py-3" name="submit" type="submit">Submit</button>
                            </div>
                             <div class="col-6">
                              <button onclick="location.href='staff.php'" class="btn btn-black w-100 py-3"  type="button">Cancel</button>
                            </div>
                            
                        </div>
                    </form>
                </div>

                <?php include("include/footer.php"); ?>

                <!-- SweetAlert Trigger -->
                <?php if (isset($_SESSION['status']) && $_SESSION['status'] != ""): ?>
                <script>
                    Swal.fire({
                        title: '<?php echo ($_SESSION["status"] == "success") ? "Account Updated!" : "Error!"; ?>',
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
        </div>
    </div>
</div>
<!-- End Wrapper -->
</body>
</html>
