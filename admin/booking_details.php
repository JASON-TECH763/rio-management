<?php
session_start();
include("config/connect.php");

require 'phpmailer/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Fetch bookings from database
$sql = "SELECT id, booking_id, checkin_date, checkout_date, r_name, amount, first_name, last_name , email, phone, country, payment, status FROM reservations ORDER BY created_at DESC";
$result = $conn->query($sql);

$bookings = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

// Check if form is submitted via POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['confirm_booking']) && isset($_POST['action'])) {
        $bookingId = intval($_POST['confirm_booking']); // Ensure it's an integer
        $action = $_POST['action'];

        // Find the booking details based on $bookingId
        $booking = array_filter($bookings, function ($b) use ($bookingId) {
            return $b['id'] == $bookingId;
        });

        if (empty($booking)) {
            echo "Booking not found.";
            exit;
        }
        
        $booking = array_values($booking)[0]; // Get the booking details
        
        // Initialize PHPMailer
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('SMTP_USERNAME'); // Get from environment
            $mail->Password   = getenv('SMTP_PASSWORD'); // Get from environment
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            // Recipients
            $mail->setFrom(getenv('SMTP_USERNAME'), 'RMS-Admin');
            $mail->addAddress($booking['email'], htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']));

            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'Booking Status Update';

            // Determine email body and new status based on action
            $newStatus = '';
            if ($action == 'confirm') {
                $newStatus = 'Confirmed';
                $mail->Body = generateEmailBody('confirmed', $booking);
            } elseif ($action == 'reject') {
                $newStatus = 'Rejected';
                $mail->Body = generateEmailBody('rejected', $booking);
            }

            // Send email
            $mail->send();
            updateBookingStatus($conn, $bookingId, $newStatus, $booking['email']);

        } catch (Exception $e) {
            logError("Failed to send email. Error: {$mail->ErrorInfo}");
            echo "An error occurred. Please try again.";
        }
    }
}

// Function to generate email body
function generateEmailBody($status, $booking) {
    $statusText = $status === 'confirmed' ? 'Booking Confirmed' : 'Booking Rejected';
    $body = '<html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; background-color: #f0f0f0; padding: 20px; }
                        .container { background-color: #ffffff; border-radius: 5px; padding: 20px; margin: 20px auto; max-width: 600px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                        .logo { text-align: center; margin-bottom: 20px; }
                        .logo img { max-width: 150px; height: auto; }
                        h2 { color: #333333; }
                        p { color: #666666; }
                        .booking-details { margin-top: 20px; border-top: 1px solid #dddddd; padding-top: 10px; }
                        .website-name { text-align: center; margin-top: 20px; font-size: 14px; color: #888888; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="logo">
                            <img src="localhost://RIO/admin/assets/img/a.jpg" alt="Logo">
                        </div>
                        <h2>' . $statusText . '</h2>
                        <p>Dear ' . htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']) . ',</p>
                        <p>' . ($status === 'confirmed' ? 'Your booking has been confirmed. We look forward to welcoming you!' : 'We regret to inform you that your booking has been rejected.') . '</p>
                        <div class="booking-details">
                            <p><strong>Booking Details:</strong></p>
                            <p><strong>Check-in:</strong> ' . htmlspecialchars($booking['checkin_date']) . '</p>
                            <p><strong>Check-out:</strong> ' . htmlspecialchars($booking['checkout_date']) . '</p>
                            <p><strong>Room name:</strong> ' . htmlspecialchars($booking['r_name']) . '</p>
                            <p><strong>Amount:</strong> ' . htmlspecialchars($booking['amount']) . '</p>
                            <p><strong>Booking No.:</strong> ' . htmlspecialchars($booking['booking_id']) . '</p>
                        </div>
                        <p>Thank you for choosing our GuestHouse.</p>
                        <p>Best regards,<br>Rio Admin</p>
                        <div class="website-name">
                            <p>Visit us at <a href="https://rio-lawis.com">rio-lawis.com</a></p>
                        </div>
                    </div>
                </body>
            </html>';
    return $body;
}

// Function to update booking status
function updateBookingStatus($conn, $bookingId, $newStatus, $email) {
    // Prepare and bind statement
    $stmt = $conn->prepare("UPDATE reservations SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $newStatus, $bookingId);

    // Execute statement
    if ($stmt->execute()) {
        echo '<script>
                window.onload = function() {
                    Swal.fire({
                        title: "Success!",
                        text: "Booking status updated successfully. Email notification sent to ' . htmlspecialchars($email) . '",
                        icon: "success"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "booking_details.php";
                        }
                    });
                };
              </script>';
    } else {
        logError("Error updating booking status: " . $conn->error);
        echo "An error occurred while updating the booking status.";
    }

    // Close statement
    $stmt->close();
}

// Function to log errors
function logError($message) {
    // Implement logging to a file or logging system
    error_log($message, 3, 'error_log.txt');
}

// Delete booking logic (if applicable)
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']); // Sanitize input

    // Prepare and bind statement
    $stmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
    $stmt->bind_param('i', $id);

    // Execute statement
    if ($stmt->execute()) {
        echo '<script>
                window.onload = function() {
                    Swal.fire({
                        title: "Success!",
                        text: "Data Deleted successfully",
                        icon: "success"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "booking_details.php";
                        }
                    });
                };
              </script>';
    } else {
        logError("Error deleting booking: " . $conn->error);
        echo '<script>
                window.onload = function() {
                    Swal.fire({
                        title: "Error!",
                        text: "Failed to delete the data.",
                        icon: "error"
                    });
                };
              </script>';
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>
<?php
session_start();
include("config/connect.php");

// Initialize the counter
$cnt = 1;

// Fetch bookings from the database
$sql = "SELECT id, booking_id, checkin_date, checkout_date, r_name, amount, first_name, last_name, email, phone, country, payment, status FROM reservations ORDER BY created_at DESC";
$result = $conn->query($sql);

$bookings = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
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
        <?php include("include/header.php");?>

        <div class="container">
            <div class="page-inner">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                    <div>
                        <h3 class="fw-bold mb-3">Booking List</h3>
                        <h6 class="op-7 mb-2">Information</h6>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h4 class="card-title">Manage Booking</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Check-In</th>
                                            <th>Check-Out</th>
                                            <th>Room</th>
                                            <th>Amount</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($bookings as $booking): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($cnt); ?></td>
                                            <td><?php echo htmlspecialchars($booking['checkin_date']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['checkout_date']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['r_name']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['amount']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['first_name'].' '.$booking['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['email']); ?></td>
                                            <td>
                                                <?php 
                                                switch ($booking['status']) {
                                                    case 'Pending':
                                                        echo "<span class='badge badge-primary'>New booking</span>";
                                                        break;
                                                    case 'Rejected':
                                                        echo "<span class='badge badge-danger'>Rejected</span>";
                                                        break;
                                                    default:
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
                                                                <input type="hidden" name="confirm_booking" value="<?php echo htmlspecialchars($booking['id']); ?>">
                                                                <input type="hidden" name="action" value="confirm">
                                                                <button type="submit" class="dropdown-item btn btn-success btn-sm"><i class="fa fa-check"></i> Confirm</button>
                                                            </form>
                                                            <div class="dropdown-divider"></div>
                                                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="display: inline;">
                                                                <input type="hidden" name="confirm_booking" value="<?php echo htmlspecialchars($booking['id']); ?>">
                                                                <input type="hidden" name="action" value="reject">
                                                                <button type="submit" class="dropdown-item btn btn-danger btn-sm"><i class="fa fa-times"></i> Reject</button>
                                                            </form>
                                                            <div class="dropdown-divider"></div>
                                                            <form method="post" action="booking_details.php" style="display: inline;">
                                                                <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($booking['id']); ?>">
                                                                <button type="button" class="dropdown-item btn btn-warning btn-sm" onclick="confirmDelete(this.form)"><i class="fa fa-exclamation-circle"></i> Delete</button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php $cnt++; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php include("include/footer.php");?>
        
        <!-- Custom template | don't include it in your project! -->
        
        <!-- End Custom template -->
    </div>
</div>
</div>

<!-- Core JS Files -->
<script src="assets/js/core/jquery-3.7.1.min.js"></script>
<script src="assets/js/core/popper.min.js"></script>
<script src="assets/js/core/bootstrap.min.js"></script>

<!-- jQuery Scrollbar -->
<script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

<!-- Chart JS -->
<script src="assets/js/plugin/chart.js/chart.min.js"></script>

<!-- jQuery Sparkline -->
<script src="assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

<!-- Chart Circle -->
<script src="assets/js/plugin/chart-circle/circles.min.js"></script>

<!-- Datatables -->
<script src="assets/js/plugin/datatables/datatables.min.js"></script>

<!-- Sweet Alert -->
<script src="assets/js/plugin/sweetalert/sweetalert.min.js"></script>

<!-- Kaiadmin JS -->
<script src="assets/js/kaiadmin.min.js"></script>

<!-- Kaiadmin DEMO methods, don't include it in your project! -->
<script src="assets/js/setting-demo.js"></script>
<script src="assets/js/demo.js"></script>
<script src="assets/js/sweetalert.js"></script>
<script>
    $(document).ready(function () {
        $("#basic-datatables").DataTable({});

        // Confirm delete function
        function confirmDelete(form) {
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this booking!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    form.submit();
                }
            });
        }

        // Attach confirm delete function to the delete button
        $('button[data-action="delete"]').on('click', function() {
            confirmDelete($(this).closest('form')[0]);
        });
    });
</script>
</body>
</html>
