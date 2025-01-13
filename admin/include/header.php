 
 <div class="main-header">
          <div class="main-header-logo">
            <!-- Logo Header -->
            <div class="logo-header" data-background-color="dark">
              
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
      
