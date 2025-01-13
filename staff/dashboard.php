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

// Get total customers
$sql_total_customers = "SELECT COUNT(*) as total_customers FROM customer";
$stmt = $conn->prepare($sql_total_customers);
$stmt->execute();
$result = $stmt->get_result();
$total_customers = $result->fetch_assoc()['total_customers'];



// Get monthly sales data for the current year
$sql_monthly_sales = "SELECT MONTH(order_date) AS month, SUM(order_details.prod_price * order_details.quantity) AS total_sales
                      FROM orders 
                      JOIN order_details ON orders.order_id = order_details.order_id 
                      WHERE YEAR(order_date) = YEAR(CURDATE())
                      GROUP BY MONTH(order_date)";
$stmt = $conn->prepare($sql_monthly_sales);
$stmt->execute();
$result = $stmt->get_result();
$monthly_sales_data = [];
while ($row = $result->fetch_assoc()) {
    $monthly_sales_data[(int)$row['month']] = $row['total_sales'];
}

$stmt->close();
// Get daily sales data for the current week
$sql_daily_sales = "SELECT DAYOFWEEK(order_date) AS day, SUM(order_details.prod_price * order_details.quantity) AS total_sales
                    FROM orders 
                    JOIN order_details ON orders.order_id = order_details.order_id 
                    WHERE YEARWEEK(order_date, 1) = YEARWEEK(CURDATE(), 1)
                    GROUP BY DAYOFWEEK(order_date)";
$stmt = $conn->prepare($sql_daily_sales);
$stmt->execute();
$result = $stmt->get_result();
$daily_sales_data = [];
while ($row = $result->fetch_assoc()) {
    $daily_sales_data[(int)$row['day']] = $row['total_sales'];
}
// Get monthly bookings data for the current year
$sql_monthly_bookings = "SELECT MONTH(checkin_date) AS month, COUNT(*) AS total_bookings
                         FROM reservations
                         WHERE YEAR(checkin_date) = YEAR(CURDATE())
                         GROUP BY MONTH(checkin_date)";
$stmt = $conn->prepare($sql_monthly_bookings);
$stmt->execute();
$result = $stmt->get_result();
$monthly_bookings_data = [];
while ($row = $result->fetch_assoc()) {
    $monthly_bookings_data[(int)$row['month']] = $row['total_bookings'];
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                                                <p class="card-category"  style="color: white;">Total Check-in</p>
                                                <h4 class="card-title"  style="color: white;"><?php echo $total_checkins; ?></h4>
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
                                                <i class="fas fa-users"  style="background-color: #ff0000;"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category"  style="color: white;">Total Customers</p>
                                                <h4 class="card-title"  style="color: white;"><?php echo $total_customers; ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <canvas id="monthlySalesChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <canvas id="dailySalesChart"></canvas>
            </div>
        </div>
    </div>
</div>
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <canvas id="monthlyBookingsChart"></canvas>
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
            <script>
        // Monthly sales data from PHP
        const monthlySalesData = <?php echo json_encode($monthly_sales_data); ?>;

        // Prepare data for Chart.js
        const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const salesData = [];
        for (let i = 1; i <= 12; i++) {
            salesData.push(monthlySalesData[i] ? parseFloat(monthlySalesData[i]) : 0);
        }

        const ctx = document.getElementById('monthlySalesChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Monthly Sales',
                    data: salesData,
                    backgroundColor: '#FEA116',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        // Daily sales data from PHP
const dailySalesData = <?php echo json_encode($daily_sales_data); ?>;

// Prepare data for Chart.js
const dailyLabels = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
const dailySales = [];
for (let i = 1; i <= 7; i++) {
    dailySales.push(dailySalesData[i] ? Math.round(dailySalesData[i]) : 0); // Convert to whole numbers
}

const ctxDaily = document.getElementById('dailySalesChart').getContext('2d');
const dailySalesChart = new Chart(ctxDaily, {
    type: 'bar',
    data: {
        labels: dailyLabels,
        datasets: [{
            label: 'Daily Sales',
            data: dailySales,
            backgroundColor: '#FEA116',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true, // Start y-axis at 0
            }
        }
    }
});


 // Monthly bookings data from PHP
const monthlyBookingsData = <?php echo json_encode($monthly_bookings_data); ?>;

// Prepare data for Chart.js
const bookingLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
const bookingData = [];
for (let i = 1; i <= 12; i++) {
    bookingData.push(monthlyBookingsData[i] ? parseFloat(monthlyBookingsData[i]) : 0);
}

const ctxBookings = document.getElementById('monthlyBookingsChart').getContext('2d');
const bookingsChart = new Chart(ctxBookings, {
    type: 'line',
    data: {
        labels: bookingLabels,
        datasets: [{
            label: 'Total Bookings',
            data: bookingData,
            backgroundColor: '#ff0000',
            borderColor: '#2a2f5b',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return Number.isInteger(value) ? value : null;
                    },
                    stepSize: 1
                }
            }
        }
    }
});


    </script>   

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
    <script src="assets/js/setting-zdemo.js"></script>
    <script src="assets/js/demo.js"></script>
</body>
</html>
