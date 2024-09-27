<?php
session_start();
include("config/connect.php");

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
                families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
                urls: ["assets/css/fonts.min.css"]
            },
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/plugins.min.css" />
    <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
    <link rel="stylesheet" href="assets/css/demo.css" />
</head>
<body>
<div class="wrapper">
    <?php include("include/sidenavigation.php"); ?>
    <div class="main-panel">
        <?php include("include/header.php"); ?>
        <div class="container">
            <div class="page-inner">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                    <div>
                        <h3 class="fw-bold mb-3">Paid Orders</h3>
                        <h6 class="op-7 mb-2">Information</h6>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Paid Orders</h4>
                            <button onclick="printTable()" class="btn btn-primary"><i class="fas fa-print"></i> Print</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="paid-orders-datatables" class="display table table-striped table-hover" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Order Date</th>
                                            <th>Total Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    // Query to select orders where status is 'paid'
                                    $sql_paid_orders = "SELECT * FROM orders WHERE status = 'paid'";
                                    $result_paid_orders = $conn->query($sql_paid_orders);

                                    if ($result_paid_orders->num_rows > 0) {
                                        while ($order = $result_paid_orders->fetch_assoc()) {
                                            $order_id = $order['order_id'];
                                            
                                            // Calculate total amount for the order
                                            $sql_total_amount = "SELECT SUM(prod_price * quantity) as total_amount FROM order_details WHERE order_id = ?";
                                            $stmt_total_amount = $conn->prepare($sql_total_amount);
                                            $stmt_total_amount->bind_param('i', $order_id);
                                            $stmt_total_amount->execute();
                                            $total_amount_result = $stmt_total_amount->get_result();
                                            $total_amount_row = $total_amount_result->fetch_assoc();
                                            $total_amount = $total_amount_row['total_amount'];
                                    ?>
                                        <tr>
                                            <td><?php echo $order_id; ?></td>
                                            <td><?php echo $order['order_date']; ?></td>
                                            <td><?php echo number_format($total_amount, 2); ?></td>
                                            <td><?php echo $order['status']; ?></td>
                                        </tr>
                                    <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="4">No paid orders found</td></tr>';
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <?php include("include/footer.php"); ?>
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
<script src="assets/js/plugin/sweetalert/sweetalert.min.js"></script>
<script src="assets/js/kaiadmin.min.js"></script>
<script src="assets/js/sweetalert.js"></script>
<script>
$(document).ready(function () {
    $("#paid-orders-datatables").DataTable({});
});

function printTable() {
    var divToPrint = document.getElementById("paid-orders-datatables");
    var newWin = window.open("");
    newWin.document.write("<html><head><title>Print</title>");
    newWin.document.write('<link rel="stylesheet" href="assets/css/bootstrap.min.css" />');
    newWin.document.write('</head><body>');
    newWin.document.write(divToPrint.outerHTML);
    newWin.document.write('</body></html>');
    newWin.document.close();
    newWin.print();
}
</script>
</body>
</html>
