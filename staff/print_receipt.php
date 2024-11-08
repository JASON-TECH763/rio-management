<?php
session_start();
include("config/connect.php");

// Set timezone to Philippines
date_default_timezone_set('Asia/Manila');

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
    <style>
        .receipt {
            max-width: 700px;
            margin: auto;
            padding: 10px;
            border: 1px solid #000;
            font-family: Arial, sans-serif;
            position: relative;
        }
        .header-section {
            text-align: center;
            margin-bottom: 20px;
        }
        .header-section h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header-section p {
            margin: 5px 0;
            font-size: 14px;
        }
        .invoice-number {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
        }
        .details-section {
            margin-bottom: 20px;
            font-size: 14px;
        }
        .details-section p {
            margin: 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .totals {
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
        }
        .footer .signature {
            margin-top: 10px;
            text-align: left;
            padding-left: 10px;
            font-family: 'Brush Script MT', cursive; /* Apply a cursive font for the signature */
            font-size: 20px;
        }
        .print-btn {
            text-align: center;
            margin-top: 20px;
        }
        .logo {
            position: absolute;
            top: 10px;
            left: 10px;
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
        <!-- Logo -->
        <div class="logo">
            <img src="assets/img/a.jpg" alt="navbar brand" height="70">
        </div>
        
        <!-- Header Section -->
        <div class="header-section">
            <h2>RIO CAFE & RESTOBAR</h2>
            <p>Poblacion Madridejos, 6053 Madridejos, Cebu, Philippines</p>
            <p>VAT Reg. TIN: 108-427-007-00008</p>
        </div>

        <div class="invoice-number">
            No. 001177
        </div>

        <!-- Customer and Transaction Details -->
        <div class="details-section">
    <p><strong>Date:</strong> <?php echo date('Y-m-d'); ?></p>
    <p><strong>Time:</strong> <?php echo date('H:i:s'); ?></p>
</div>


        <!-- Order Table -->
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Article/Description</th>
                    <th>Unit Price</th>
                    <th>Quantity</th>
                    <th>Amount (P)</th>
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
                <!-- Totals -->
                <tr class="totals">
                    <td colspan="4" class="text-right">Total Sales:</td>
                    <td><?php echo number_format($total_price, 2); ?></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right">Payment Amount:</td>
                    <td><?php echo number_format($payment_amount, 2); ?></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right">Change:</td>
                    <td><?php echo number_format($change, 2); ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Footer Section -->
        <div class="footer">
            <p>Thank you for your purchase!</p>
            <p>This document is not valid for claim of input taxes.</p>
        </div>

        <!-- Print Button -->
        <div class="print-btn">
            <button onclick="window.print()" class="btn btn-primary">Print Receipt</button>
        </div>
    </div>
</body>
</html>
