<?php
session_start();
include('config/config.php');

// Handle reservation form submission
if (isset($_POST['reserve'])) {
  $name = $_POST['name'];
  $phone = $_POST['phone']; // Add phone field
  $email = $_POST['email'];
  $checkin_date = $_POST['checkin_date'];
  $checkout_date = $_POST['checkout_date'];
  $room_type = $_POST['room_type'];

  $stmt = $mysqli->prepare("INSERT INTO reservation (name, phone, email, checkin_date, checkout_date, room_type) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param('ssssss', $name, $phone, $email, $checkin_date, $checkout_date, $room_type);

  if ($stmt->execute()) {
    $success = "Reservation successful!";
  } else {
    $err = "Reservation failed. Please try again.";
  }
}
require_once('partials/_head.php');
?>

<body class="bg-dark">
  <div class="main-content">
    <div class="header bg-gradient-primary py-7">
      <div class="container">
        <div class="header-body text-center mb-7">
          <div class="row justify-content-center">
            <div class="col-lg-5 col-md-6">
              <h1 class="text-white">Fill this Form</h1>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Page content -->
    <div class="container mt--8 pb-5">
      <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
          <div class="card bg-secondary shadow border-0">
            <div class="card-body px-lg-5 py-lg-5">
              <?php if (isset($err)) { ?>
                <div class="alert alert-danger" role="alert">
                  <?php echo $err; ?>
                </div>
              <?php } ?>
              <?php if (isset($success)) { ?>
                <div class="alert alert-success" role="alert">
                  <?php echo $success; ?>
                </div>
              <?php } ?>
              <form method="post" role="form">
                <div class="form-group mb-3">
                  <div class="input-group input-group-alternative">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="ni ni-single-02"></i></span>
                    </div>
                    <input class="form-control" required name="name" placeholder="Name" type="text">
                  </div>
                </div>
                <div class="form-group mb-3">
                  <div class="input-group input-group-alternative">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="ni ni-mobile-button"></i></span>
                    </div>
                    <input class="form-control" required name="phone" placeholder="Phone" type="text"> <!-- Add phone field -->
                  </div>
                </div>
                <div class="form-group mb-3">
                  <div class="input-group input-group-alternative">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                    </div>
                    <input class="form-control" required name="email" placeholder="Email" type="email">
                  </div>
                </div>
                <div class="form-group mb-3">
                  <div class="input-group input-group-alternative">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                    </div>
                    <input class="form-control" required name="checkin_date" placeholder="Check-in Date" type="date">
                  </div>
                </div>
                <div class="form-group mb-3">
                  <div class="input-group input-group-alternative">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                    </div>
                    <input class="form-control" required name="checkout_date" placeholder="Check-out Date" type="date">
                  </div>
                </div>
                <div class="form-group mb-3">
                  <div class="input-group input-group-alternative">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="ni ni-building"></i></span>
                    </div>
                    <select class="form-control" required name="room_type">
                      <option value="">Select Room Type</option>
                      <option value="single">Single</option>
                      <option value="double">Double</option>
                      <option value="suite">Suite</option>
                    </select>
                  </div>
                </div>
                <div class="text-center">
                  <button type="submit" name="reserve" class="btn btn-primary my-4">Reserve</button>
                </div>
              </form>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-6">
              <!-- Additional links or information can be added here -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Footer -->
  <?php
  require_once('partials/_footer.php');
  ?>
  <!-- Argon Scripts -->
  <?php
  require_once('partials/_scripts.php');
  ?>
</body>
</html>
