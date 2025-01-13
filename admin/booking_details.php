<?php
session_start();
include("config/connect.php");

header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; img-src 'self' data:; font-src 'self' https://fonts.googleapis.com https://cdn.jsdelivr.net; frame-ancestors 'none'; form-action 'self'; base-uri 'self';");

if (!isset($_SESSION['uname'])) {
  header("location:index.php");
  exit();
}


require 'phpmailer/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Database connection parameters


// Fetch bookings from database
$sql = "SELECT id, booking_id, checkin_date, checkout_date, r_name, amount, first_name, last_name , email, phone, payment, status FROM reservations order by created_at desc";
$result = $conn->query($sql);

$bookings = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cnt = 1;

        $bookings[] = $row;
    }
}

// Check if form is submitted via POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['confirm_booking']) && isset($_POST['action'])) {
        $bookingId = $_POST['confirm_booking'];
        $action = $_POST['action'];

        // Find the booking details based on $bookingId
        $booking = array();
        foreach ($bookings as $b) {
            if ($b['id'] == $bookingId) {
                $booking = $b;
                break;
            }
        }

        if (empty($booking)) {
            echo "Booking not found.";
            exit;
        }

        // Example: Send email notification using PHPMailer
        try {
            // Initialize PHPMailer
            $mail = new PHPMailer(true); // Passing `true` enables exceptions

            // Server settings
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                        // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'riomanagement123@gmail.com';                // SMTP username
            $mail->Password   = 'vilenbrazfimbkbl';                        // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
            $mail->Port       = 465;                                    // TCP port to connect to

            // Recipients
            $mail->setFrom('riomanagement123@gmail.com', 'RMS-Admin');
            $mail->addAddress($booking['email'], htmlspecialchars ($booking['first_name'].''.$booking['last_name']));    // Add a recipient

            // Email content
            $mail->isHTML(true);                                        // Set email format to HTML
            $mail->Subject = 'Booking Status Update';

            // Determine email body and new status based on action
            if ($action == 'confirm') {
                $mail->Body = '<html>
                    <head>
                        <style>
                            /* CSS styles */
                            body {
                                font-family: Arial, sans-serif;
                                background-color: #f0f0f0;
                                padding: 20px;
                            }
                            .container {
                                background-color: #ffffff;
                                border-radius: 5px;
                                padding: 20px;
                                margin: 20px auto;
                                max-width: 600px;
                                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                            }
                            .logo {
                                text-align: center;
                                margin-bottom: 20px;
                            }
                            .logo img {
                                max-width: 150px;
                                height: auto;
                            }
                            h2 {
                                color: #333333;
                            }
                            p {
                                color: #666666;
                            }
                            .booking-details {
                                margin-top: 20px;
                                border-top: 1px solid #dddddd;
                                padding-top: 10px;
                            }
                            .website-name {
                                text-align: center;
                                margin-top: 20px;
                                font-size: 14px;
                                color: #888888;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            <div class="logo">
                                <img src="localhost://RIO/admin/assets/img/a.jpg" alt="Logo">
                            </div>
                            <h2>Booking Confirmed</h2>
                            <p>Dear ' .  htmlspecialchars($booking['first_name'] .''.$booking['last_name']). ',</p>
                            <p>Your booking has been confirmed. We look forward to welcoming you!</p>
                            <div class="booking-details">
                                <p><strong>Booking Details:</strong></p>
                                <p><strong>Check-in:</strong> ' .  htmlspecialchars($booking['checkin_date']) . '</p>
                                <p><strong>Check-out:</strong> ' .  htmlspecialchars($booking['checkout_date']) . '</p>
                                <p><strong>Room name:</strong> ' .  htmlspecialchars($booking['r_name']) . '</p>
                                <p><strong>Amount:</strong> ' .  htmlspecialchars($booking['amount']) . '</p>
                                 <p><strong>Booking No.:</strong> ' .  htmlspecialchars($booking['booking_id']) . '</p>
                                
                            </div>
                            <p>Thank you for choosing our GuestHouse.</p>
                            <p>Best regards,<br>Rio Admin</p>
                            <div class="website-name">
                                <p>Visit us at <a href="https://rio-lawis.com">rio-lawis.com</a></p>
                            </div>
                        </div>
                    </body>
                </html>';
                $newStatus = 'Confirmed';
            } else if ($action == 'reject') {
                $mail->Body = '<html>
                    <head>
                        <style>
                            /* CSS styles */
                            body {
                                font-family: Arial, sans-serif;
                                background-color: #f0f0f0;
                                padding: 20px;
                            }
                            .container {
                                background-color: #ffffff;
                                border-radius: 5px;
                                padding: 20px;
                                margin: 20px auto;
                                max-width: 600px;
                                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                            }
                            .logo {
                                text-align: center;
                                margin-bottom: 20px;
                            }
                            .logo img {
                                max-width: 150px;
                                height: auto;
                            }
                            h2 {
                                color: #333333;
                            }
                            p {
                                color: #666666;
                            }
                            .booking-details {
                                margin-top: 20px;
                                border-top: 1px solid #dddddd;
                                padding-top: 10px;
                            }
                            .website-name {
                                text-align: center;
                                margin-top: 20px;
                                font-size: 14px;
                                color: #888888;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            <div class="logo">
                               <img src="localhost://RIO/admin/assets/img/a.jpg" alt="Logo">
                            </div>
                            <h2>Booking Rejected</h2>
                            <p>Dear ' .  htmlspecialchars($booking['first_name'] .''.$booking['last_name']). ',</p>
                            <p>We regret to inform you that your booking has been rejected.</p>
                            <div class="booking-details">
                                <p><strong>Booking Details:</strong></p>
                                <p><strong>Check-in:</strong> ' .  htmlspecialchars($booking['checkin_date']) . '</p>
                                <p><strong>Check-out:</strong> ' .  htmlspecialchars($booking['checkout_date']) . '</p>
                                <p><strong>Room name:</strong> ' .  htmlspecialchars($booking['r_name']) . '</p>
                                <p><strong>Amount:</strong> ' .  htmlspecialchars($booking['amount']) . '</p>
                                 <p><strong>Booking No.:</strong> ' .  htmlspecialchars($booking['booking_id']) . '</p>
                                
                            </div>
                            <p>If you have any questions, please contact us.</p>
                            <p>Best regards,<br>Admin</p>
                            <div class="website-name">
                                <p>Visit us at <a href="https   ://rio-lawis.com">rio-lawis.com</a></p>
                            </div>
                        </div>
                    </body>
                </html>';
                $newStatus = 'Rejected';
            }

            // Send email
            $mail->send();
            echo '<script>
                    window.onload = function() {
                        Swal.fire({
                            title: "Success!",
                            text: "Booking status updated successfully. Email notification sent to  "+"'.$booking['email'].'",
                            icon: "success"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "booking_details.php";
                            }
                        });
                    };
                  </script>';

            // Update booking status in database
            $sqlUpdate = "UPDATE reservations SET status = '$newStatus' WHERE id = $bookingId";
            if ($conn->query($sqlUpdate) === TRUE) {
                echo " Database status updated successfully.";
            } else {
                echo "Error updating booking status: " . $conn->error;
            }
        } catch (Exception $e) {
            echo "Failed to send email. Error: {$mail->ErrorInfo}";
        }

        // After processing, fetch updated bookings from the database
        $sql = "SELECT id, booking_id, checkin_date, checkout_date, r_name, amount, first_name, last_name , email, phone, country, payment, status FROM reservations order by created_at desc";
        $result = $conn->query($sql);

        $bookings = array();
       
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $bookings[] = $row;
            }
        }
    }
}

// Close connection

if (isset($_GET['delete']))   {
    // Assuming $conn is your database connection object

    // Sanitize the input to avoid SQL injection (assuming $conn is a PDO object)
    $id = htmlspecialchars($_GET['delete']);

    // Prepare the SQL statement with a placeholder for the ID
    $sql = "DELETE FROM reservations WHERE id = ?";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind the parameter (ID)
    $stmt->bind_param('i', $id); // Assuming 'i' for integer type of ID

    // Execute the statement
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

    // Close the statement

} 



?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Rio Management System</title>
    <meta
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
      name="viewport"
    />
    <link
      rel="icon"
      href="assets/img/a.jpg"
      type="image/x-icon"
    />

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
                      <table
                        id="basic-datatables"
                        class="display table table-striped table-hover" style="width: 100%;">
                        <thead>
                          <tr>
                                        <th>#</th>
                                        <!-- <th>Booking No.</th> -->
                                        <th>Check-In</th>
                                        <th>Check-Out</th>
                                        <th>Room</th>

                                        <th>Amount</th>

                                        <th>Name</th>
                                        <th>Email</th>
                                        <!-- <th>Phone</th> -->
                                        
                                        
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
                    
                    
                    <td >
                    <?php if ($booking['status'] == 'Pending'){

                        echo "<span class='badge badge-primary'>New booking</span>";
                    }
                    elseif ($booking['status'] == 'Rejected'){
                         echo "<span class='badge badge-danger'>Rejected</span>";
                    }else{
                        echo "<span class='badge badge-success'>Confirmed</span>";


                    }

                     ?>
                         
                     </td>

                         
                    <td>
                      
                        <div class="btn-group dropstart">
                        <button
                          type="button"
                          class="btn btn-primary btn-border dropdown-toggle"
                          data-bs-toggle="dropdown"
                          aria-haspopup="true"
                          aria-expanded="false"
                        >
                          Action
                        </button>
                        <ul class="dropdown-menu" role="menu">
                          <li>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="display: inline;">
                            <input type="hidden" name="confirm_booking" value="<?php echo $booking['id']; ?>">
                            <input type="hidden" name="action" value="confirm">
                             <a class="dropdown-item" href="#"><button type="submit" class="btn btn-success btn-sm"><i class="fa fa-check"></i>Confirm</button></a>
                        </form>
                            
                            <div class="dropdown-divider"></div>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="display: inline;">
                            <input type="hidden" name="confirm_booking" value="<?php echo $booking['id']; ?>">
                            <input type="hidden" name="action" value="reject">
                            <a class="dropdown-item" href="#"><button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Reject</button></a>
                        </form>
                           
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="booking_details.php?delete=<?php echo $booking['id']; ?>"
                              ><button class="btn btn-warning btn-sm"><i class="fa fa-exclamation-circle"></i>Delete</button></a
                            >
                          </li>
                        </ul>
                      </div>
                    </td>

                </tr>
                <?php  $cnt = $cnt+1; ?>
            <?php endforeach; ?>

                        
                       
                                </tbody>
                            </table>
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
    <!--   Core JS Files   -->
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

    <!-- Bootstrap Notify -->
    

    <!-- jQuery Vector Maps -->
    <script src="assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
    <script src="assets/js/plugin/jsvectormap/world.js"></script>

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

        $("#multi-filter-select").DataTable({
          pageLength: 5,
          initComplete: function () {
            this.api()
              .columns()
              .every(function () {
                var column = this;
                var select = $(
                  '<select class="form-select"><option value=""></option></select>'
                )
                  .appendTo($(column.footer()).empty())
                  .on("change", function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());

                    column
                      .search(val ? "^" + val + "$" : "", true, false)
                      .draw();
                  });

                column
                  .data()
                  .unique()
                  .sort()
                  .each(function (d, j) {
                    select.append(
                      '<option value="' + d + '">' + d + "</option>"
                    );
                  });
              });
          },
        });

        // Add Row
        $("#add-row").DataTable({
          pageLength: 5,
        });

        var action =
          '<td> <div class="form-button-action"> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg" data-original-title="Edit Task"> <i class="fa fa-edit"></i> </button> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-danger" data-original-title="Remove"> <i class="fa fa-times"></i> </button> </div> </td>';

        $("#addRowButton").click(function () {
          $("#add-row")
            .dataTable()
            .fnAddData([
              $("#addName").val(),
              $("#addPosition").val(),
              $("#addOffice").val(),
              action,
            ]);
          $("#addRowModal").modal("hide");
        });
      });
    </script>

  </body>
</html>
