<?php
session_start();
include("config/connect.php");

header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; img-src 'self' data:; font-src 'self' https://fonts.googleapis.com https://cdn.jsdelivr.net; frame-ancestors 'none'; form-action 'self'; base-uri 'self';");

if (!isset($_SESSION['uname'])) {
    header("location:index.php");
    exit();
}


if (!isset($_GET['order_id']) || !isset($_GET['payment_amount']) || !isset($_GET['change'])) {
    die("Invalid request.");
}

$order_id = intval($_GET['order_id']);
$payment_amount = floatval($_GET['payment_amount']);
$change = floatval($_GET['change']);

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Receipt</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/plugins.min.css" />
    <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
    <link rel="stylesheet" href="assets/css/demo.css" />
    <style>
        .receipt {
            max-width: 600px;
            margin: auto;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-family: Arial, sans-serif;
        }
        .address-header {
            margin-bottom: 20px;
            text-align: center;
        }
        .address-header h2 {
            margin: 0;
            font-size: 18px;
        }
        .address-header p {
            margin: 5px 0;
            font-size: 14px;
        }
        .receipt h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .receipt table {
            width: 100%;
            border-collapse: collapse;
        }
        .receipt th, .receipt td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .receipt th {
            background-color: #f2f2f2;
        }
        .receipt-footer {
            margin-top: 20px;
            text-align: right;
        }
        .print-btn {
            text-align: center;
            margin-top: 20px;
        }
        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Address Header Section -->
         <center>
        <div class="address-header">
        <img src="assets/img/a.jpg" alt="navbar brand" class="navbar-brand" height="70">
            <h2>RIO MANAGEMENT</h2>
       
            <p>Poblacion, Madridejos, Cebu</p>
            <p>Phone: (123) 456-7890</p>
            <p>Email: riomanagement123@gmail.com</p>
        </div>
    </center>
        <table>
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
                <tr>
                    <td colspan="4" class="text-right"><strong>Payment Amount:</strong></td>
                    <td><?php echo number_format($payment_amount, 2); ?></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right"><strong>Change:</strong></td>
                    <td><?php echo number_format($change, 2); ?></td>
                </tr>
            </tbody>
        </table>
        <div class="receipt-footer">
            <p>Thank you for your purchase!</p>
        </div>
        <div class="print-btn">
            <button onclick="window.print()" class="btn btn-primary">Print Receipt</button>
        </div>
    </div>
</body>
</html>
