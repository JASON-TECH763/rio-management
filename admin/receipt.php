<?php
session_start();
include("config/connect.php");

// Set timezone to Philippines
date_default_timezone_set('Asia/Manila');

// Check for required parameters
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

// Fetch order and customer details
$sql = "SELECT o.order_id, o.order_date, o.status, c.name, c.email 
        FROM orders o 
        JOIN customer c ON o.customer_id = c.id 
        WHERE o.order_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    echo "Order not found.";
    exit();
}

// Fetch product details for the order
$sql_products = "SELECT prod_name, quantity, prod_price 
                 FROM order_details 
                 WHERE order_id=?";
$stmt_products = $conn->prepare($sql_products);
$stmt_products->bind_param('i', $order_id);
$stmt_products->execute();
$result_products = $stmt_products->get_result();

$total_price = 0;
$products = [];
while ($product = $result_products->fetch_assoc()) {
    $products[] = $product;
    $total_price += $product['prod_price'] * $product['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        .receipt-container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            font-family: Arial, sans-serif;
        }
        .receipt-header, .receipt-footer {
            text-align: center;
        }
        .receipt-header h2 {
            font-size: 18px;
            font-weight: bold;
        }
        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }
        .receipt-table th, .receipt-table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .totals-row td {
            font-weight: bold;
        }
        .logo img {
            height: 50px;
        }
        .back-print-btns {
            margin-top: 20px;
            text-align: center;
        }
        @media print {
            .back-print-btns {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header Section -->
        <div class="receipt-header">
            <div class="logo">
                <img src="assets/img/a.jpg" alt="RIO CAFE & RESTOBAR Logo">
            </div>
            <h2>RIO CAFE & RESTOBAR</h2>
            <p>Poblacion Madridejos, 6053 Madridejos, Cebu, Philippines</p>
            <p>VAT Reg. TIN: 108-427-007-00008</p>
        </div>

        <!-- Order & Customer Details -->
        <p><strong>Order Date:</strong> <?php echo date('Y-m-d H:i:s', strtotime($order['order_date'])); ?></p>
        <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($order['name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>

        <!-- Order Table -->
        <table class="receipt-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <?php $line_total = $product['prod_price'] * $product['quantity']; ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['prod_name']); ?></td>
                        <td><?php echo $product['quantity']; ?></td>
                        <td>₱<?php echo number_format($product['prod_price'], 2); ?></td>
                        <td>₱<?php echo number_format($line_total, 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="totals-row">
                    <td colspan="3">Total Price</td>
                    <td>₱<?php echo number_format($total_price, 2); ?></td>
                </tr>
                <tr class="totals-row">
                    <td colspan="3">Payment Amount</td>
                    <td>₱<?php echo number_format($payment_amount, 2); ?></td>
                </tr>
                <tr class="totals-row">
                    <td colspan="3">Change</td>
                    <td>₱<?php echo number_format($change, 2); ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Footer Section -->
        <div class="receipt-footer">
            <p>Thank you for your purchase!</p>
            <p>This document is not valid for claim of input taxes.</p>
        </div>

        <!-- Back and Print Buttons -->
        <div class="back-print-btns">
            <button onclick="window.location.href='manage_summary.php'" class="btn btn-secondary">Back</button>
            <button onclick="window.print();" class="btn btn-primary">Print Receipt</button>
        </div>
    </div>
</body>
</html>
