<?php
session_start();
include("config/connect.php");


if (!isset($_SESSION['uname'])) {
  header("location:index.php");
  exit();
}


// Ensure $conn is a valid MySQLi connection object
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['delete'])) {
    // Sanitize the input to avoid SQL injection
    $staff_id = htmlspecialchars($_GET['delete']);

    // Prepare the SQL statement with a placeholder for the ID
    $sql = "DELETE FROM rpos_staff WHERE id = ?"; // Changed from staff_id to id as per DB field

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind the parameter (ID)
        $stmt->bind_param('i', $staff_id); // Assuming 'i' for integer type of ID

        // Execute the statement
        if ($stmt->execute()) {
            echo '<script>
                    window.onload = function() {
                        Swal.fire({
                            title: "Success!",
                            text: "Staff deleted successfully.",
                            icon: "success"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "staff.php";
                            }
                        });
                    };
                  </script>';
        } else {
            echo '<script>
                    window.onload = function() {
                        Swal.fire({
                            title: "Error!",
                            text: "Failed to delete the staff. ' . $stmt->error . '",
                            icon: "error"
                        });
                    };
                  </script>';
        }

        // Close the statement
        $stmt->close();
    } else {
        echo '<script>
                window.onload = function() {
                    Swal.fire({
                        title: "Error!",
                        text: "Failed to prepare the SQL statement.",
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
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport"/>
    <link rel="icon" href="assets/img/a.jpg" type="image/x-icon"/>

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
    <?php include("include/sidenavigation.php");?>

    <div class="main-panel">
        <?php include("include/header.php");?>

        <div class="container">
          <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
              <div>
                <h3 class="fw-bold mb-3">Staff List</h3>
                <h6 class="op-7 mb-2">Information</h6>
              </div>
            </div>
          </div>
          <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <div class="d-flex align-items-center">
                      <h4 class="card-title mb-0">Manage Staff</h4>
                      <button onclick="location.href='add_staff.php'" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus"></i>
                        Add Staff
                      </button>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table
                        id="basic-datatables"
                        class="display table table-striped table-hover" style="width: 100%;">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Gender</th>
                            <th>Email</th>
                            <th>Password</th> 
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $sql = "SELECT id, staff_name, staff_last_name, staff_gender, staff_email,  staff_password  FROM rpos_staff";

                          $result = $conn->query($sql);
                          $cnt = 1;
                          if ($result->num_rows > 0) {
                              while ($row = $result->fetch_assoc()) {
                          ?>
                              <tr>
                                  <td><?php echo $cnt; ?></td>
                                  <td><?php echo $row['staff_name']; ?></td>
                                  <td><?php echo $row['staff_last_name']; ?></td>
                                  <td><?php echo $row['staff_gender']; ?></td>
                                  <td><?php echo $row['staff_email']; ?></td>
                                  <td><?php echo htmlspecialchars($row['staff_password']); ?></td>

                                  <td>
                                      <div class="btn-group dropstart">
                                      <button
                                        type="button"
                                        class="btn btn-primary btn-border dropdown-toggle"
                                        data-bs-toggle="dropdown"
                                        aria-haspopup="true"
                                        aria-expanded="false"
                                      >
                                        Action
                                      </button>
                                      <ul class="dropdown-menu" role="menu">
                                        <li>
                                        <a class="dropdown-item" href="update_staff.php?id=<?php echo $row['id']; ?>"
                                        ><button class="btn btn-info btn-sm"><i class="fa fa-info"></i> Edit</button></a>
                                          <div class="dropdown-divider"></div>
                                          <a class="dropdown-item" href="staff.php?delete=<?php echo $row['id']; ?>"
                                            ><button type="button" class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Delete</button></a>
                                        </li>
                                      </ul>
                                    </div>
                                  </td>
                              </tr>
                          <?php
                              $cnt++;
                              }
                          }
                          $conn->close();
                          ?>
                        </tbody>
                    </table>
                    </div>
                  </div>
                </div>
              </div>
          </div>
          <?php include("include/footer.php");?>
    </div>
</div>
</div>

<!--   Core JS Files   -->
<script src="assets/js/core/jquery-3.7.1.min.js"></script>
<script src="assets/js/core/popper.min.js"></script>
<script src="assets/js/core/bootstrap.min.js"></script>

<!-- jQuery Scrollbar -->
<script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

<!-- Datatables -->
<script src="assets/js/plugin/datatables/datatables.min.js"></script>

<!-- Sweet Alert -->
<script src="assets/js/plugin/sweetalert/sweetalert.min.js"></script>

<!-- Kaiadmin JS -->
<script src="assets/js/kaiadmin.min.js"></script>

<script>
  $(document).ready(function () {
    $("#basic-datatables").DataTable({});
  });
</script>

</body>
</html>
