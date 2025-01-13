<?php
session_start();
include("config/connect.php");

if (!isset($_SESSION['uname'])) {
    header("location:index.php");
    exit();
}

// Initialize the orders session if not set
if (!isset($_SESSION['orders'])) {
    $_SESSION['orders'] = [];
}

// Handle adding product to the order
if (isset($_POST['add_order'])) {
    $prod_id = htmlspecialchars($_POST['prod_id']);
    $prod_name = htmlspecialchars($_POST['prod_name']);
    $prod_price = htmlspecialchars($_POST['prod_price']);
    $quantity = htmlspecialchars($_POST['quantity']);

    if ($quantity <= 0) {
        echo '<script>
            window.onload = function() {
                Swal.fire({
                    title: "Error!",
                    text: "Quantity must be greater than zero.",
                    icon: "error"
                });
            };
        </script>';
    } else {
        $_SESSION['orders'][] = [
            'prod_id' => $prod_id,
            'prod_name' => $prod_name,
            'prod_price' => $prod_price,
            'quantity' => $quantity
        ];

        echo '<script>
            window.onload = function() {
                Swal.fire({
                    title: "Success!",
                    text: "Product added to order",
                    icon: "success"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "order.php";
                    }
                });
            };
        </script>';
    }
}

// Handle placing the order
if (isset($_POST['place_order'])) {
    // Check if there are items in the order
    if (empty($_SESSION['orders'])) {
        echo '<script>
            window.onload = function() {
                Swal.fire({
                    title: "Error!",
                    text: "Please add products to the order before placing it.",
                    icon: "error"
                });
            };
        </script>';
    } else {
        $guest_email = "Guest";  // You can replace this with a dynamic email if needed

        // Insert into orders table with guest email
        $sql_order = "INSERT INTO orders (order_date, customer_email) VALUES (NOW(), ?)";
        $stmt_order = $conn->prepare($sql_order);
        $stmt_order->bind_param('s', $guest_email);

        if ($stmt_order->execute()) {
            $order_id = $conn->insert_id;

            // Insert into order_details table
            foreach ($_SESSION['orders'] as $order) {
                $sql_order_details = "INSERT INTO order_details (order_id, prod_id, prod_name, prod_price, quantity) VALUES (?, ?, ?, ?, ?)";
                $stmt_order_details = $conn->prepare($sql_order_details);
                $stmt_order_details->bind_param('issss', $order_id, $order['prod_id'], $order['prod_name'], $order['prod_price'], $order['quantity']);
                $stmt_order_details->execute();
            }

            // Clear the session orders
            unset($_SESSION['orders']);

            // Redirect to order summary with order_id
            header("Location: order_summary.php?order_id=" . $order_id);
            exit;
        } else {
            echo '<script>
                window.onload = function() {
                    Swal.fire({
                        title: "Error!",
                        text: "Failed to place the order.",
                        icon: "error"
                    });
                };
            </script>';
        }
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
                        <h3 class="fw-bold mb-3">Order List</h3>
                        <h6 class="op-7 mb-2">Information</h6>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                
                               <form method="post" action="">
                                <button type="submit" name="place_order" class="btn btn-primary"> <i class="fas fa-cart-plus" ></i> Place Order</button>

                            </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover" style="width: 100%;">
                                    <thead>
                                        <tr>
                                             <tr>
                                                    <th>#</th>
                                                    <th>Image</th>
                                                    <th>Name</th>
                                                    <th>Price</th>
                                                    
                                                    <th>Action</th>
                                                </tr>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $sql = "SELECT prod_id, prod_img, prod_name, prod_price FROM rpos_products";
                                    $result = $conn->query($sql);
                                    $cnt = 1;
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                    ?>
                                        <tr>
                                            <td><?php echo $cnt; ?></td>
                                            <td><?php
                                                if ($row['prod_img']) {
                                                    echo "<img src='assets/img/products/" . $row['prod_img'] . "' height='60' width='60' class='img-thumbnail'>";
                                                } else {
                                                    echo "<img src='assets/img/products/default.jpg' height='60' width='60' class='img-thumbnail'>";
                                                }
                                            ?></td>
                                            <td><?php echo $row['prod_name']; ?></td>
                                            <td><?php echo $row['prod_price']; ?></td>
                                            <td>
                                                 <form method="POST" action="">
                                                            <input type="hidden" name="prod_id" value="<?php echo $row['prod_id']; ?>" />
                                                            <input type="hidden" name="prod_name" value="<?php echo $row['prod_name']; ?>" />
                                                            <input type="hidden" name="prod_price" value="<?php echo $row['prod_price']; ?>" />
                                                            <input type="number" name="quantity" min="1" placeholder="Qty" required />
                                                            <button type="submit" name="add_order" class="btn btn-sm btn-warning">
                                                                <i class="fas fa-cart-plus"></i>
                                                                Add to Order
                                                            </button>
                                                        </form>
                                            </td>
                                        </tr>
                                    <?php
                                            $cnt++;
                                        }
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
    $("#basic-datatables").DataTable({});
});
</script>
</body>
</html>
