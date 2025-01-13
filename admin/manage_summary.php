<?php
session_start();
include("config/connect.php");

header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; img-src 'self' data:; font-src 'self' https://fonts.googleapis.com https://cdn.jsdelivr.net; frame-ancestors 'none'; form-action 'self'; base-uri 'self';");
if (!isset($_SESSION['uname'])) {
    header("location:index.php");
    exit();
}


// Include PHPMailer
require 'phpmailer/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Handle order actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];
    $action = $_POST['action'];

    // Fetch the order details based on order_id
    $sql = "SELECT o.*, c.email, c.name 
            FROM orders o 
            JOIN customer c ON o.customer_id = c.id 
            WHERE o.order_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    if (!$order) {
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Order Not Found!",
                    text: "The specified order does not exist.",
                    icon: "error"
                }).then(() => {
                    window.location.href = "manage_summary.php";
                });
            });
        </script>';
        exit();
    }

    // Calculate total price for the order
    $sql_total = "SELECT SUM(prod_price * quantity) AS total_price 
                  FROM order_details 
                  WHERE order_id=?";
    $stmt_total = $conn->prepare($sql_total);
    $stmt_total->bind_param('i', $order_id);
    $stmt_total->execute();
    $result_total = $stmt_total->get_result();
    $total_row = $result_total->fetch_assoc();
    $total_price = $total_row['total_price'];
    $order_date = $order['order_date']; // Fetch the order date

    // Fetch product details for the order
    $sql_products = "SELECT prod_name, quantity, prod_price 
                     FROM order_details 
                     WHERE order_id=?";
    $stmt_products = $conn->prepare($sql_products);
    $stmt_products->bind_param('i', $order_id);
    $stmt_products->execute();
    $result_products = $stmt_products->get_result();

    // Build product details for email
    $product_details = "";
    while ($product = $result_products->fetch_assoc()) {
        $product_details .= "<p><strong>Product Name:</strong> {$product['prod_name']}<br>
                             <strong>Quantity:</strong> {$product['quantity']}<br>
                             <strong>Price:</strong> ₱" . number_format($product['prod_price'], 2) . "</p>";
    }

    // PHPMailer setup
    function sendEmail($order, $subject, $body) {
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'riomanagement123@gmail.com';
            $mail->Password = 'vilenbrazfimbkbl';  // Update with correct password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            // Recipients
            if (!filter_var($order['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email address.');
            }
            $mail->setFrom('riomanagement123@gmail.com', 'RMS-Admin');
            $mail->addAddress($order['email']);

            // Email content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();

            // SweetAlert for successful email
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Email Sent!",
                        text: "The email has been successfully sent to ' . $order['email'] . '",
                        icon: "success"
                    });
                });
            </script>';

        } catch (Exception $e) {
            // SweetAlert for failed email
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Email Failed!",
                        text: "Failed to send email. Error: ' . $mail->ErrorInfo . '",
                        icon: "error"
                    });
                });
            </script>';
        }
    }

    // Action: Confirm order
    if ($action == 'confirm') {
        // Update order status
        $sql_confirm = "UPDATE orders SET status='Confirmed' WHERE order_id=?";
        $stmt_confirm = $conn->prepare($sql_confirm);
        $stmt_confirm->bind_param('i', $order_id);
        $stmt_confirm->execute();

        // Prepare and send confirmation email
        $subject = "Order Confirmed";
        $body = "<html><body>
                 <h2>Order Confirmed</h2>
                 <p>Dear {$order['name']},</p>
                 <p>Your order has been confirmed. We look forward to delivering your product!</p>
                 <p><strong>Order Date:</strong> " . date('Y-m-d H:i:s', strtotime($order_date)) . "</p>
                 <p><strong>Total Price:</strong> ₱" . number_format($total_price, 2) . "</p>
                 <h3>Product Details:</h3>
                 $product_details
                 <p>Thank you for choosing our service.</p>
                 </body></html>";
        sendEmail($order, $subject, $body);

        // Success notification with SweetAlert for confirmation
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Order Confirmed!",
                    text: "The order has been confirmed and the customer has been notified via email.",
                    icon: "success"
                }).then(() => {
                    window.location.href = "manage_summary.php";
                });
            });
        </script>';
    }

    // Action: Reject order
    if ($action == 'reject') {
        // Update order status
        $sql_reject = "UPDATE orders SET status='Rejected' WHERE order_id=?";
        $stmt_reject = $conn->prepare($sql_reject);
        $stmt_reject->bind_param('i', $order_id);
        $stmt_reject->execute();

        // Prepare and send rejection email
        $subject = "Order Rejected";
        $body = "<html><body>
                 <h2>Order Rejected</h2>
                 <p>Dear {$order['name']},</p>
                 <p>We regret to inform you that your order has been rejected.</p>
                 <h3>Product Details:</h3>
                 $product_details
                 <p>If you have any questions, please contact us.</p></body></html>";
        sendEmail($order, $subject, $body);

        // Error notification with SweetAlert for rejection
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Order Rejected!",
                    text: "The order has been rejected and the customer has been notified via email.",
                    icon: "error"
                }).then(() => {
                    window.location.href = "manage_summary.php";
                });
            });
        </script>';
    }

    // Action: Delete order
    if ($action == 'delete') {
        // First, delete from order_details
        $sql_delete_order_details = "DELETE FROM order_details WHERE order_id=?";
        $stmt_delete_order_details = $conn->prepare($sql_delete_order_details);
        $stmt_delete_order_details->bind_param('i', $order_id);
        $stmt_delete_order_details->execute();

        // Then, delete from orders
        $sql_delete_order = "DELETE FROM orders WHERE order_id=?";
        $stmt_delete_order = $conn->prepare($sql_delete_order);
        $stmt_delete_order->bind_param('i', $order_id);
        $stmt_delete_order->execute();

        // Success notification with SweetAlert for deletion
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Order Deleted!",
                    text: "The order has been deleted.",
                    icon: "success"
                }).then(() => {
                    window.location.href = "manage_summary.php";
                });
            });
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
                        <h3 class="fw-bold mb-3">Order Summary</h3>
                        <h6 class="op-7 mb-2">Details of Reserved Foods & Drinks</h6>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">List of Reservations</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="summary-datatables" class="display table table-striped table-hover" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Order Date</th>
                                            <th>Email</th>
                                            <th>Total Price (₱)</th> <!-- Added Total Price Column -->
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
    // Fetch order summary with customer email
    $sql_orders = "
    SELECT o.order_id, o.order_date, o.status, c.email AS customer_email 
    FROM orders o
    JOIN customer c ON o.customer_id = c.id
    ORDER BY o.order_date DESC";
$result_orders = $conn->query($sql_orders);
if ($result_orders->num_rows > 0) {
    while ($row = $result_orders->fetch_assoc()) {
        $order_total_price = 0; // Initialize total price for the order
        ?>
            <tr>
                <td><?php echo $row['order_id']; ?></td>
                <td><?php echo date('Y-m-d H:i:s', strtotime($row['order_date'])); ?></td>
                
                <td><?php echo htmlspecialchars($row['customer_email']); ?></td>
                
                <!-- Calculate total price for each order -->
                <?php
                $sql_products = "SELECT prod_name, prod_price, quantity FROM order_details WHERE order_id=?"; // Add prod_name to query
                $stmt_products = $conn->prepare($sql_products);
                $stmt_products->bind_param('i', $row['order_id']);
                $stmt_products->execute();
                $result_products = $stmt_products->get_result();

                if ($result_products->num_rows > 0) {
                    while ($product = $result_products->fetch_assoc()) {
                        $order_total_price += $product['prod_price'] * $product['quantity']; // Add price for each product
                    }
                }
                ?>

                <!-- Display total price for the order -->
                <td><?php echo number_format($order_total_price, 2); ?></td>
                <td >
                <?php 
    $status = strtolower($row['status']);  // Convert the status to lowercase for case-insensitive comparison
    if ($status == 'new reserved') {
        echo "<span class='badge badge-primary'>New Reserved</span>";
    } elseif ($status == 'rejected') {
        echo "<span class='badge badge-danger'>Rejected</span>";
    } elseif ($status == 'paid') {
        echo "<span class='badge badge-primary'>Paid</span>";
    } else {
        echo "<span class='badge badge-success'>Confirmed</span>";
    }
    ?>

                         
                     </td>
                <td>
                    <div class="btn-group dropstart">
                        <button type="button" class="btn btn-primary btn-border dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Action
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li>
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="display: inline;">
                            <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                            <input type="hidden" name="action" value="confirm">
                             <a class="dropdown-item" href="#"><button type="submit" class="btn btn-success btn-sm"><i class="fa fa-check"></i>Confirm</button></a>
                        </form>
                            
                                <div class="dropdown-divider"></div>
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="display: inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <a class="dropdown-item" href="#"><button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Reject</button>
                                </form>
                                <div class="dropdown-divider"></div>
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="display: inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn btn-warning btn-sm"><i class="fa fa-exclamation-circle"></i> Delete</button>
                                </form>
                                <div class="dropdown-divider"></div>
                    <!-- Print Action -->
                <!-- payment Action -->
                <form method="get" action="payment.php" target="_blank" style="display: inline;">
                    <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                    <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-credit-card"></i> Payment</button>
                </form>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>

            <!-- Nested table for product details -->
            <tr>
                <td colspan="6">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Product Price</th>
                                <th>Quantity</th>
                                <th>Subtotal (₱)</th> <!-- Added Subtotal column -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch product details for this order again to display them
                            $stmt_products->execute();
                            $result_products = $stmt_products->get_result();

                            if ($result_products->num_rows > 0) {
                                while ($product = $result_products->fetch_assoc()) {
                                    $subtotal = $product['prod_price'] * $product['quantity'];
                                    ?>
                                    <tr>
                                        <td><?php echo $product['prod_name']; ?></td> <!-- Now it should work fine -->
                                        <td><?php echo number_format($product['prod_price'], 2); ?></td>
                                        <td><?php echo $product['quantity']; ?></td>
                                        <td><?php echo number_format($subtotal, 2); ?></td> <!-- Display subtotal for each product -->
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='4'>No products found for this order.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </td>
            </tr>
            <?php
        }
    } else {
        echo "<tr><td colspan='6'>No orders found.</td></tr>";
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/core/jquery-3.7.1.min.js"></script>
<script src="assets/js/core/popper.min.js"></script>
<script src="assets/js/core/bootstrap.min.js"></script>
<script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
<script src="assets/js/plugin/datatables/datatables.min.js"></script>
<script src="assets/js/kaiadmin.min.js"></script>
<script>
    $(document).ready(function() {
        $('#summary-datatables').DataTable();
    });
</script>
</body>
</html>
