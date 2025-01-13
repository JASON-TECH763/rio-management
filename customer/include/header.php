<div class="main-header">
  <div class="main-header-logo">
    <!-- Logo Header -->
    <div class="logo-header" data-background-color="dark">
      <a href="dashboard.php" class="logo">
        <img src="assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" />
      </a>
      <div class="nav-toggle">
        <button class="btn btn-toggle toggle-sidebar">
          <i class="gg-menu-right"></i>
        </button>
        <button class="btn btn-toggle sidenav-toggler">
          <i class="gg-menu-left"></i>
        </button>
      </div>
      <button class="topbar-toggler more">
        <i class="gg-more-vertical-alt"></i>
      </button>
    </div>
    <!-- End Logo Header -->
  </div>

  <!-- Navbar Header -->
  <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom" style="background-color: #2a2f5b; color: #FEA116; font-family: Arial;">
    <div class="container-fluid">
      <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
        <div>
          <h3>Rio Management System</h3>
        </div>
      </nav>

      <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
    <li class="nav-item topbar-user dropdown hidden-caret">
        <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
            <div class="avatar-sm">
                <img src="assets/img/p.png" alt="Profile Picture" class="avatar-img rounded-circle" />
            </div>
            <span class="profile-username" style="color:white;">
                <span class="fw-bold">
                    <?php 
                    // Check if user is logged in and account is verified
                    if (isset($_SESSION['email']) && isset($_SESSION['verified']) && $_SESSION['verified'] == 1) {
                        echo htmlspecialchars($_SESSION['email']); // Display the logged-in user's email
                    } else {
                        echo "Guest"; // Display 'Guest' if no user is logged in
                    }
                    ?>
                </span>
            </span>
        </a>
        <ul class="dropdown-menu dropdown-user animated fadeIn">
            <div class="dropdown-user-scroll scrollbar-outer">
                <?php if (isset($_SESSION['email'])): ?>
                <li>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="index.php">Logout</a> <!-- Link to logout page -->
                </li>
                <?php endif; ?>
            </div>
        </ul>
    </li>
</ul>



    </div>
  </nav>
  <!-- End Navbar -->
</div>
