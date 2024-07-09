<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Fetch reservation details
    $stmt = $mysqli->prepare("SELECT * FROM reservation WHERE id = ?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $reservation = $res->fetch_object();
    $stmt->close();
}

// Process payment
if (isset($_POST['pay'])) {
    // Payment processing logic here

    // Example: Update reservation status to 'Paid'
    $stmt = $mysqli->prepare("UPDATE reservation SET status = 'Paid' WHERE id = ?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $stmt->close();

    if ($stmt) {
        $success = "Payment Successful" && header("refresh:1; url=reservation.php");
    } else {
        $err = "Payment Failed. Try Again Later.";
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
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <h3 class="mb-0">Payment</h3>
                        </div>
                        <div class="card-body">
                            <?php if (isset($success)) { echo "<div class='alert alert-success'>$success</div>"; } ?>
                            <?php if (isset($err)) { echo "<div class='alert alert-danger'>$err</div>"; } ?>
                            <form method="post">
                                <input type="hidden" name="id" value="<?php echo $reservation->id; ?>">
                                <div class="form-group">
                                    <label for="amount">Amount</label>
                                    <input type="text" name="amount" class="form-control" value="<?php echo $reservation->amount; ?>" required>
                                </div>
                                <button type="submit" name="pay" class="btn btn-primary">Pay Now</button>
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
