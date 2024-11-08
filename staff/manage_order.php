<?php
session_start();
include("config/connect.php");


if (!isset($_SESSION['uname'])) {
    header("location:index.php");
    exit();
}


// Handle order actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];
    $action = $_POST['action'];

    if ($action == 'confirm') {
        $sql_confirm = "UPDATE orders SET status='Confirmed' WHERE order_id=?";
        $stmt_confirm = $conn->prepare($sql_confirm);
        $stmt_confirm->bind_param('i', $order_id);
        $stmt_confirm->execute();
        echo '<script>
            Swal.fire({
                title: "Order Confirmed!",
                text: "The order has been confirmed.",
                icon: "success"
            }).then(() => {
                window.location.href = "manage_summary.php";
            });
        </script>';
    }

    if ($action == 'reject') {
        $sql_reject = "UPDATE orders SET status='Rejected' WHERE order_id=?";
        $stmt_reject = $conn->prepare($sql_reject);
        $stmt_reject->bind_param('i', $order_id);
        $stmt_reject->execute();
        echo '<script>
            Swal.fire({
                title: "Order Rejected!",
                text: "The order has been rejected.",
                icon: "error"
            }).then(() => {
                window.location.href = "manage_summary.php";
            });
        </script>';
    }

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

        echo '<script>
            Swal.fire({
                title: "Order Deleted!",
                text: "The order has been deleted.",
                icon: "success"
            }).then(() => {
                window.location.href = "manage_summary.php";
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
                    <th>Product Name</th>
                    <th>Product Price</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="ordersTableBody">
                <?php
                // Fetch order details
                $sql_orders = "SELECT o.order_id, o.order_date, o.status, od.prod_name, od.prod_price, od.quantity 
                               FROM orders o 
                               JOIN order_details od ON o.order_id = od.order_id 
                               ORDER BY o.order_date DESC";
                $result_orders = $conn->query($sql_orders);
                if ($result_orders->num_rows > 0) {
                    while ($row = $result_orders->fetch_assoc()) {
                        $total_price = $row['prod_price'] * $row['quantity'];
                ?>
                    <tr data-order-id="<?php echo $row['order_id']; ?>">
                        <td class="order-id"><?php echo $row['order_id']; ?></td>
                        <td class="order-date"><?php echo date('Y-m-d H:i:s', strtotime($row['order_date'])); ?></td>
                        <td class="product-name"><?php echo $row['prod_name']; ?></td>
                        <td class="product-price"><?php echo number_format($row['prod_price'], 2); ?></td>
                        <td class="quantity"><?php echo $row['quantity']; ?></td>
                        <td class="total-price"><?php echo number_format($total_price, 2); ?></td>
                        <td class="status"><?php echo $row['status']; ?></td>
                        <td>
                            <div class="btn-group dropstart">
                                <button type="button" class="btn btn-primary btn-border dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Action
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <div class="dropdown-divider"></div>
                                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="display: inline;">
                                            <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <a class="dropdown-item" href="#"><button type="submit" class="btn btn-warning btn-sm"><i class="fa fa-exclamation-circle"></i>Delete</button></a>
                                        </form>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="#" onclick="printOrder(<?php echo $row['order_id']; ?>);">
                                            <button type="button" class="btn btn-info btn-sm"><i class="fa fa-print"></i> Print</button>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='8'>No orders found.</td></tr>";
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
<script src="assets/js/plugin/datatables/datatables.min.js"></script>
<script src="assets/js/kaiadmin.min.js"></script>
<script>

function searchOrders() {
    const input = document.getElementById('searchField').value.toLowerCase();
    const rows = document.querySelectorAll('#ordersTableBody tr');

    rows.forEach(row => {
        const orderId = row.querySelector('.order-id').innerText.toLowerCase();
        const productName = row.querySelector('.product-name').innerText.toLowerCase();
        if (orderId.includes(input) || productName.includes(input)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

    $(document).ready(function() {
        $('#summary-datatables').DataTable();
    });

    function printOrder(orderId) {
    // Get the row's details based on the order_id
    const row = document.querySelector(`tr[data-order-id='${orderId}']`);

    if (row) {
        // Capture the relevant column data from the row
        const orderID = row.querySelector('.order-id').innerText;
        const orderDate = row.querySelector('.order-date').innerText;
        const productName = row.querySelector('.product-name').innerText;
        const productPrice = row.querySelector('.product-price').innerText;
        const quantity = row.querySelector('.quantity').innerText;
        const totalPrice = row.querySelector('.total-price').innerText;
        const status = row.querySelector('.status').innerText;

        // Create a new window for printing
        const printWindow = window.open('', '', 'height=600,width=800');

        // Write the HTML content for the print
        printWindow.document.write('<html><head><title>Order Details</title></head><body>');
        
        // Add the receipt header with logo and company details
        printWindow.document.write(`
            <div class="receipt" style="text-align: center;">
                <div class="address-header">
                    <img src="assets/img/a.jpg" alt="navbar brand" class="navbar-brand" height="70">
                    <h2>RIO MANAGEMENT</h2>
                    <p>Poblacion, Madridejos, Cebu</p>
                    <p>Phone: (123) 456-7890</p>
                    <p>Email: riomanagement123@gmail.com</p>
                </div>
            </div>
        `);

        // Order details
        printWindow.document.write('<h3>Order Details</h3>');
        printWindow.document.write('<p><strong>Order ID:</strong> ' + orderID + '</p>');
        printWindow.document.write('<p><strong>Order Date:</strong> ' + orderDate + '</p>');
        printWindow.document.write('<p><strong>Product Name:</strong> ' + productName + '</p>');
        printWindow.document.write('<p><strong>Product Price:</strong> ' + productPrice + '</p>');
        printWindow.document.write('<p><strong>Quantity:</strong> ' + quantity + '</p>');
        printWindow.document.write('<p><strong>Total Price:</strong> ' + totalPrice + '</p>');
        printWindow.document.write('<p><strong>Status:</strong> ' + status + '</p>');
        
        printWindow.document.write('</body></html>');

        // Close the document and trigger print
        printWindow.document.close();
        printWindow.print();
    } else {
        alert("Order details not found.");
    }
}

</script>
</body>
</html>
