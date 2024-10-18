<?php
session_start();
include("config/connect.php");

// Check if order_id is provided
if (!isset($_GET['order_id'])) {
    die("No order ID provided");
}

// Fetch order details
$order_id = intval($_GET['order_id']);
$order_details = [];
$sql = "SELECT * FROM order_details WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $order_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $order_details[] = $row;
}
$stmt->close();

// Process reservation
if (isset($_POST['reserve'])) {
    $total_price = 0;
    foreach ($order_details as $detail) {
        $total_price += floatval($detail['prod_price']) * intval($detail['quantity']);
    }

    // Update order status to "Reserved"
    $sql_update = "UPDATE orders SET status = 'New Reserved' WHERE order_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param('i', $order_id);
    $stmt_update->execute();
    $stmt_update->close();

    // Set a session variable to show the alert
    $_SESSION['reservation_success'] = true;

    // Redirect to the same page to display the SweetAlert
    header("Location: ".$_SERVER['PHP_SELF']."?order_id=".$order_id);
    exit();
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
    <!-- SweetAlert -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  </head>
  <body>
    <div class="wrapper">
        <!-- Sidebar -->
        <?php include("include/sidenavigation.php");?>

        <div class="main-panel">
            <!-- Header -->
            <?php include("include/header.php");?>

            <div class="container">
                <div class="page-inner">
                    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                        <div>
                            <h3 class="fw-bold mb-3">Order Summary</h3>
                            <h6 class="op-7 mb-2">Order Details</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="display table table-striped table-hover" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Product Name</th>
                                                    <th>Price</th>
                                                    <th>Quantity</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $cnt = 1;
                                                $total_price = 0;
                                                foreach ($order_details as $detail) {
                                                    $price = floatval($detail['prod_price']);
                                                    $quantity = intval($detail['quantity']);
                                                    $total = $price * $quantity;
                                                    $total_price += $total;
                                                ?>
                                                <tr>
                                                    <td><?php echo $cnt; ?></td>
                                                    <td><?php echo $detail['prod_name']; ?></td>
                                                    <td><?php echo number_format($price, 2); ?></td>
                                                    <td><?php echo $quantity; ?></td>
                                                    <td><?php echo number_format($total, 2); ?></td>
                                                </tr>
                                                <?php
                                                    $cnt++;
                                                }
                                                ?>
                                                <tr>
                                                    <td colspan="4" class="text-right"><strong>Total:</strong></td>
                                                    <td><?php echo number_format($total_price, 2); ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Reservation Form -->
                                    <form method="POST" action="">
                                        <button type="submit" name="reserve" class="btn btn-primary">Reserve</button>
                                    </form>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Core JS Files -->
    <script src="assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>
    <script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="assets/js/plugin/chart.js/chart.min.js"></script>
    <script src="assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>
    <script src="assets/js/plugin/chart-circle/circles.min.js"></script>
    <script src="assets/js/plugin/datatables/datatables.min.js"></script>
    <script src="assets/js/kaiadmin.min.js"></script>

    <!-- SweetAlert Trigger -->
    <?php if (isset($_SESSION['reservation_success'])): ?>
    <script>
        Swal.fire({
            title: "Success!",
            text: "Your reservation of foods & drinks has been sent! Waiting for confirmation from the 3J'E Company.",
            icon: "success"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "order.php";
            }
        });
    </script>
    <?php
        // Unset the session variable to prevent the alert from showing again
        unset($_SESSION['reservation_success']);
    endif;
    ?>
  </body>
</html>
