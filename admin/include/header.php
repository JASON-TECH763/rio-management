<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration
include("config/connect.php");

// Check if the MySQLi connection is open and valid before executing any queries
if ($conn instanceof mysqli && !$conn->connect_errno) {
    // Your query goes here
    $result = mysqli_query($conn, "SELECT * FROM some_table WHERE condition = 'value'");

    // Check if the query executed successfully
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Process the result rows
            echo "Data: " . htmlspecialchars($row['column_name'], ENT_QUOTES, 'UTF-8') . "<br>";
        }
    } else {
        // Log query error if the query failed
        error_log("Query failed: " . mysqli_error($conn));
    }
} else {
    // Log error if the connection is not available
    error_log("Error: MySQLi connection is not available.");
}
?> 
 <div class="main-header">
          <div class="main-header-logo">
            <!-- Logo Header -->
            <div class="logo-header" data-background-color="dark">
              <a href="dashboard.php" class="logo">
                <img
                  src="assets/img/kaiadmin/logo_light.svg"
                  alt="navbar brand"
                  class="navbar-brand"
                  height="20"
                />
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
          <nav
            class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom" style="background-color: #2a2f5b; color: #FEA116;  font-family: Arial;">
            <div class="container-fluid">
              <nav
                class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex"
              >
              <div class="" >
    <h3>Rio Management System</h3>
</div>

              </nav>

              <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                <li
                  class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none"
                >
                  <a
                    class="nav-link dropdown-toggle"
                    data-bs-toggle="dropdown"
                    href="#"
                    role="button"
                    aria-expanded="false"
                    aria-haspopup="true"
                  >
                    <i class="fa fa-search"></i>
                  </a>
                  <ul class="dropdown-menu dropdown-search animated fadeIn">
                    <form class="navbar-left navbar-form nav-search">
                      <div class="input-group">
                        <input
                          type="text"
                          placeholder="Search ..."
                          class="form-control"
                        />
                      </div>
                    </form>
                  </ul>
                </li>
                
          
<?php
            
            $id=$_SESSION['uname'];
            $sql="select * from admin where uname='$id'";
            $result=mysqli_query($conn,$sql);
            while($row=mysqli_fetch_array($result))
            {
            ?>
                <li class="nav-item topbar-user dropdown hidden-caret">
                  <a
                    class="dropdown-toggle profile-pic"
                    data-bs-toggle="dropdown"
                    href="#"
                    aria-expanded="false"
                  >
                    <div class="avatar-sm">
                      <img
                        src="assets/img/p.png"
                        alt="..."
                        class="avatar-img rounded-circle"
                      />
                    </div>
                    <span class="profile-username" style="color:white;">
                      <span class="op-7">3J'E,</span>
                      <span class="fw-bold"> <?php echo $row['3']; ?></span>
                    </span>
                  </a>
                  <ul class="dropdown-menu dropdown-user animated fadeIn">
                    <div class="dropdown-user-scroll scrollbar-outer">
                      <li>
                        <div class="user-box">
                          <div class="avatar-lg">
                            <img
                              src="assets/img/p.png"
                              alt="image profile"
                              class="avatar-img rounded"
                            />
                          </div>
                          <div class="u-text">
                            <h4> <?php echo $row['3']; ?></h4>
                            <p class="text-muted">
                             <?php echo $row['4']; ?>
                            </p>
                            <?php } ?>
                            <!--  <a
                              href="profile.html"
                              class="btn btn-xs btn-secondary btn-sm"
                              >View Profile</a
                            >-->
                          </div>
                        </div>
                      </li>
                      <li>
                        <!--  <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">My Profile</a>-->
                        
                      
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="include/logout.php">Logout</a>
                      </li>
                    </div>
                  </ul>
                </li>
              </ul>
            </div>
          </nav>
          <!-- End Navbar -->
        </div>
      
