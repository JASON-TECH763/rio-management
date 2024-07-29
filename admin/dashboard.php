<?php
session_start();
require("config/connect.php");

if (!isset($_SESSION['uname'])) {
    header("location:index.php");
    exit();
}

// Get total sales today
$sql_today_sales = "SELECT SUM(order_details.prod_price * order_details.quantity) as total_sales
                    FROM orders 
                    JOIN order_details ON orders.order_id = order_details.order_id 
                    WHERE DATE(order_date) = CURDATE()";
$stmt = $conn->prepare($sql_today_sales);
$stmt->execute();
$result = $stmt->get_result();
$today_sales = $result->fetch_assoc()['total_sales'];

// Get total sales for the past week
// $sql_week_sales = "SELECT SUM(order_details.prod_price * order_details.quantity) as total_sales
//                    FROM orders 
//                    JOIN order_details ON orders.order_id = order_details.order_id 
//                    WHERE DATE(order_date) >= CURDATE() - INTERVAL 7 DAY";
// $stmt = $conn->prepare($sql_week_sales);
// $stmt->execute();
// $result = $stmt->get_result();
// $week_sales = $result->fetch_assoc()['total_sales'];

// Get total sales for the past month
$sql_month_sales = "SELECT SUM(order_details.prod_price * order_details.quantity) as total_sales
                    FROM orders 
                    JOIN order_details ON orders.order_id = order_details.order_id 
                    WHERE DATE(order_date) >= CURDATE() - INTERVAL 1 MONTH";
$stmt = $conn->prepare($sql_month_sales);
$stmt->execute();
$result = $stmt->get_result();
$month_sales = $result->fetch_assoc()['total_sales'];

// Get total number of orders
$sql_total_orders = "SELECT COUNT(*) as total_orders FROM orders";
$stmt = $conn->prepare($sql_total_orders);
$stmt->execute();
$result = $stmt->get_result();
$total_orders = $result->fetch_assoc()['total_orders'];

// Get number of bookings
$sql_total_bookings = "SELECT COUNT(*) as total_bookings FROM reservations";
$stmt = $conn->prepare($sql_total_bookings);
$stmt->execute();
$result = $stmt->get_result();
$total_bookings = $result->fetch_assoc()['total_bookings'];

// Get number of check-ins
$sql_total_checkins = "SELECT COUNT(*) as total_checkins FROM reservations WHERE checkin_date IS NOT NULL";
$stmt = $conn->prepare($sql_total_checkins);
$stmt->execute();
$result = $stmt->get_result();
$total_checkins = $result->fetch_assoc()['total_checkins'];

// Get total products
$sql_total_products = "SELECT COUNT(*) as total_products FROM rpos_products";
$stmt = $conn->prepare($sql_total_products);
$stmt->execute();
$result = $stmt->get_result();
$total_products = $result->fetch_assoc()['total_products'];

$stmt->close();
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
                            <h3 class="fw-bold mb-3">Dashboard</h3>
                        </div>
                       
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round"  style="background-color: #2a2f5b;" >
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-warning bubble-shadow-small"  style="background-color: #FEA116;">
                                                <i class="fas fa-dollar-sign"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category" style="color: white;">Sales Today</p>
                                                <h4 class="card-title"  style="color: white;">₱ <?php echo number_format($today_sales, 2); ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-info bubble-shadow-small">
                                                <i class="fas fa-dollar-sign"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Sales Past Week</p>
                                                <h4 class="card-title">$ <?php echo number_format($week_sales, 2); ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round" style="background-color: #2a2f5b;">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-warning bubble-shadow-small" style="background-color: #FEA116;">
                                                <i class="fas fa-dollar-sign"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category"  style="color: white;">Monthly Sales</p>
                                                <h4 class="card-title"  style="color: white;">₱<?php echo number_format($month_sales, 2); ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round" style="background-color: #2a2f5b;">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-warning bubble-shadow-small" style="background-color: #d2691e;">
                                                <i class="fas fa-shopping-cart"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category"  style="color: white;">Total Orders</p>
                                                <h4 class="card-title"  style="color: white;"><?php echo $total_orders; ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round" style="background-color: #2a2f5b;">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-warning bubble-shadow-small"  style="background-color: #d2691e;">
                                                <i class="fas fa-cube"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category"  style="color: white;">Total Products</p>
                                                <h4 class="card-title"  style="color: white;"><?php echo $total_products; ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round" style="background-color: #2a2f5b;">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-warning bubble-shadow-small"  style="background-color: #ff0000;">
                                                <i class="fas fa-calendar-check"  style="background-color: #ff0000;"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category"  style="color: white;">Total Bookings</p>
                                                <h4 class="card-title"  style="color: white;"><?php echo $total_bookings; ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round" style="background-color: #2a2f5b;">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-warning bubble-shadow-small"  style="background-color: #ff0000;">
                                                <i class="fas fa-check-circle"  style="background-color: #ff0000;"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category"  style="color: white;">Total Check-ins</p>
                                                <h4 class="card-title"  style="color: white;"><?php echo $total_checkins; ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                       
                        <!-- <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-danger bubble-shadow-small">
                                                <i class="fas fa-sign-out-alt"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Total Check-outs</p>
                                                <h4 class="card-title"><?php echo $total_checkouts; ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>

            <?php include("include/footer.php") ?>
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
    <script src="assets/js/setting-demo.js"></script>
    <script src="assets/js/demo.js"></script>
</body>
</html>
