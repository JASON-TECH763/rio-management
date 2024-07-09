<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

// Cancel Reservation
if (isset($_GET['cancel'])) {
    $id = $_GET['cancel'];
    $adn = "DELETE FROM reservation WHERE id = ?";
    $stmt = $mysqli->prepare($adn);
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $stmt->close();
    if ($stmt) {
        $success = "Deleted" && header("refresh:1; url=reservation.php");
    } else {
        $err = "Try Again Later";
    }
}

require_once('partials/_head.php');
?>

<body>
    <!-- Sidenav -->
    <?php
    require_once('partials/_sidebar.php');
    ?>
    <!-- Main content -->
    <div class="main-content">
        <!-- Top navbar -->
        <?php
        require_once('partials/_topnav.php');
        ?>
        <!-- Header -->
        <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" class="header pb-8 pt-5 pt-md-8">
            <span class="mask bg-gradient-dark opacity-8"></span>
            <div class="container-fluid">
                <div class="header-body">
                </div>
            </div>
        </div>
        <!-- Page content -->
        <div class="container-fluid mt--8">
            <!-- Table -->
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <!-- Add any necessary buttons here -->
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Phone</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Check-in Date</th>
                                        <th scope="col">Check-out Date</th>
                                        <th scope="col">Room Type</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $ret = "SELECT * FROM reservation ORDER BY created_at DESC";
                                    $stmt = $mysqli->prepare($ret);
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    while ($reservation = $res->fetch_object()) {
                                    ?>
                                        <tr>
                                            <th class="text-success" scope="row"><?php echo $reservation->id; ?></th>
                                            <td><?php echo $reservation->name; ?></td>
                                            <td><?php echo $reservation->phone; ?></td>
                                            <td><?php echo $reservation->email; ?></td>
                                            <td><?php echo date('d/M/Y', strtotime($reservation->checkin_date)); ?></td>
                                            <td><?php echo date('d/M/Y', strtotime($reservation->checkout_date)); ?></td>
                                            <td><?php echo $reservation->room_type; ?></td>
                                            <td>
                                                <a href="reservation.php?cancel=<?php echo $reservation->id; ?>">
                                                    <button class="btn btn-sm btn-danger">
                                                        <i class="fas fa-window-close"></i>
                                                        Cancel Reservation
                                                    </button>
                                                </a>
                                                <a href="update_reservation.php?id=<?php echo $reservation->id; ?>">
                                                    <button class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                        Update Reservation
                                                    </button>
                                                </a>
                                                <a href="payment_reservation.php?id=<?php echo $reservation->id; ?>">
                                                    <button class="btn btn-sm btn-success">
                                                        <i class="fas fa-credit-card"></i>
                                                        Payment
                                                    </button>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <?php
            require_once('partials/_footer.php');
            ?>
        </div>
    </div>
    <!-- Argon Scripts -->
    <?php
    require_once('partials/_scripts.php');
    ?>
</body>
</html>
