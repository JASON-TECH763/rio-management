<?php
session_start();
include("config/connect.php");


if (!isset($_SESSION['uname'])) {
    header("location:index.php");
    exit();
}


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

// Process payment
$show_receipt = false;
if (isset($_POST['confirm_payment'])) {
    $payment_amount = floatval($_POST['payment_amount']);
    $total_price = 0;
    foreach ($order_details as $detail) {
        $total_price += floatval($detail['prod_price']) * intval($detail['quantity']);
    }

    if ($payment_amount >= $total_price) {
        // Update order status to "Paid"
        $sql_update = "UPDATE orders SET status = 'Paid' WHERE order_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param('i', $order_id);
        $stmt_update->execute();
        $stmt_update->close();

        // Compute change
        $change = $payment_amount - $total_price;

        // Generate receipt URL
        $receipt_url = "print_receipt.php?order_id={$order_id}&payment_amount={$payment_amount}&change={$change}";

        // Redirect to receipt URL
        echo '<script>
                window.location.href = "'.$receipt_url.'";
              </script>';

        $show_receipt = true;
    } else {
        $receipt = '<div class="alert alert-danger">Payment amount is insufficient.</div>';
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
                                    <!-- Payment Form -->
                                    <form method="POST" action="">
                                        <div class="form-group">
                                            <label for="payment_amount">Payment Amount:</label>
                                            <input type="number" id="payment_amount" name="payment_amount" class="form-control" step="0.01" required />
                                        </div>
                                        <button type="submit" name="confirm_payment" class="btn btn-primary">Confirm Payment</button>
                                    </form>
                                    <!-- Receipt Display -->
                                    <?php if ($show_receipt): ?>
                                    <div class="receipt mt-4">
                                        <!-- The receipt will be displayed in a new window -->
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
    <script src="assets/js/kaiadmin.min.js"></script>
</body>
</html>
