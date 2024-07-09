<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

// Get the reservation details
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $mysqli->prepare("SELECT * FROM reservation WHERE id = ?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $reservation = $res->fetch_object();
    $stmt->close();
}

// Update the reservation
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $checkin_date = $_POST['checkin_date'];
    $checkout_date = $_POST['checkout_date'];
    $room_type = $_POST['room_type'];

    $stmt = $mysqli->prepare("UPDATE reservation SET name=?, phone=?, email=?, checkin_date=?, checkout_date=?, room_type=? WHERE id=?");
    $stmt->bind_param('ssssssi', $name, $phone, $email, $checkin_date, $checkout_date, $room_type, $id);
    $stmt->execute();
    if ($stmt) {
        $success = "Reservation Updated" && header("refresh:1; url=reservation.php");
    } else {
        $err = "Try Again Later";
    }
    $stmt->close();
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
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <h3 class="mb-0">Update Reservation</h3>
                        </div>
                        <div class="card-body">
                            <?php if (isset($success)) { echo "<div class='alert alert-success'>$success</div>"; } ?>
                            <?php if (isset($err)) { echo "<div class='alert alert-danger'>$err</div>"; } ?>
                            <form method="post">
                                <input type="hidden" name="id" value="<?php echo $reservation->id; ?>">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" class="form-control" value="<?php echo $reservation->name; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" name="phone" class="form-control" value="<?php echo $reservation->phone; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?php echo $reservation->email; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="checkin_date">Check-in Date</label>
                                    <input type="date" name="checkin_date" class="form-control" value="<?php echo $reservation->checkin_date; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="checkout_date">Check-out Date</label>
                                    <input type="date" name="checkout_date" class="form-control" value="<?php echo $reservation->checkout_date; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="room_type">Room Type</label>
                                    <select name="room_type" class="form-control" required>
                                        <option value="single" <?php if ($reservation->room_type == 'single') echo 'selected'; ?>>Single</option>
                                        <option value="double" <?php if ($reservation->room_type == 'double') echo 'selected'; ?>>Double</option>
                                        <option value="suite" <?php if ($reservation->room_type == 'suite') echo 'selected'; ?>>Suite</option>
                                    </select>
                                </div>
                                <button type="submit" name="update" class="btn btn-primary">Update Reservation</button>
                            </form>
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
