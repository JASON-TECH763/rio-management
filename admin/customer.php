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
    $customer_id = htmlspecialchars($_GET['delete']);

    // Prepare the SQL statement with a placeholder for the ID
    $sql = "DELETE FROM customer WHERE id = ?";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind the parameter (ID)
        $stmt->bind_param('i', $customer_id);

        // Execute the statement
        if ($stmt->execute()) {
            echo '<script>
                    window.onload = function() {
                        Swal.fire({
                            title: "Success!",
                            text: "Customer deleted successfully.",
                            icon: "success"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "customer.php";
                            }
                        });
                    };
                  </script>';
        } else {
            echo '<script>
                    window.onload = function() {
                        Swal.fire({
                            title: "Error!",
                            text: "Failed to delete the customer. ' . $stmt->error . '",
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
                <h3 class="fw-bold mb-3">Customer List</h3>
                <h6 class="op-7 mb-2">Information</h6>
              </div>
            </div>
          </div>
          <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <div class="d-flex align-items-center">
                      <h4 class="card-title mb-0">Manage Customers</h4>
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
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          // Change to customer table query to show only verified customers
                          $sql = "SELECT id, name, email, phone FROM customer WHERE verified = 1";
                          $result = $conn->query($sql);
                          $cnt = 1;
                          if ($result->num_rows > 0) {
                              while ($row = $result->fetch_assoc()) {
                          ?>
                              <tr>
                                  <td><?php echo $cnt; ?></td>
                                  <td><?php echo $row['name']; ?></td>
                                  <td><?php echo $row['email']; ?></td>
                                  <td><?php echo $row['phone']; ?></td>
                                  <td>
                                  
                                          <a href="customer.php?delete=<?php echo $row['id']; ?>">
                                            <button type="button" class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Delete</button></a>
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
