 <div class="container-fluid bg-dark px-0">
            <div class="row gx-0">
                <div class="col-lg-6 bg-dark d-none d-lg-block">
                <a href="index.php" class="navbar-brand w-100 h-100 m-0 p-0 d-flex align-items-center justify-content-center">
                <h1 class="m-0 text-primary text-uppercase display-4 display-md-3 display-lg-1">Rio Management System</h1>
            </a>
                </div>
                <div class="col-lg-6">
                 
                    <nav class="navbar navbar-expand-lg bg-dark navbar-dark p-3 p-lg-0">
                        <a href="index.php" class="navbar-brand d-block d-lg-none">
                            <h1 class="m-0 text-primary text-uppercase">Rio Management System</h1>
                        </a>
                        <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                            <div class="navbar-nav mr-auto py-0">
                                <a href="https://rio-lawis.com/" class="nav-item nav-link active">Home</a>
                                <a href="about.php" class="nav-item nav-link">About</a>
                                <a href="service.php" class="nav-item nav-link">Services</a>
                                <a href="room.php" class="nav-item nav-link">Rooms</a>         
                                <a href="contact.php" class="nav-item nav-link">Contact</a>
                                <a href="check_status.php" class="nav-item nav-link">Check Status</a>
                                 
                                <style>
  .dropdown-menu {
  background-color: #14165b; /* Change background color */
}

.dropdown-item {
  color: #FEA116; /* Change text color to white for visibility */
}

.dropdown-item:hover {
  background-color: #FEA116; /* Optional: Lighter shade on hover */
  color: #fff; /* Ensure text stays white */
}
.profile-pic {
  color: #fff; /* Change login text to white */
}

</style>


    
  
          <!-- Dropdown trigger -->
          <a class="dropdown-toggle profile-pic" href="#" id="loginTrigger" role="button">
            <!-- Dynamically display the login name -->
            <span class="fw-bold">Login</span>
          </a>
          <!-- Dropdown menu (hidden by default) -->
          <ul class="dropdown-menu dropdown-user animated fadeIn" id="loginDropdown" style="display: none;">
            <div class="dropdown-user-scroll scrollbar-outer">
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="staff">
                <i class="fas fa-user-tie"></i> Staff
              </a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="customer">
                <i class="fas fa-users"></i> Customer
              </a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="admin">
                <i class="fas fa-user-shield"></i> Admin
              </a>
            </div>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- JavaScript to control the dropdown behavior -->
<script>
  // Get the login trigger and dropdown elements
  const loginTrigger = document.getElementById('loginTrigger');
  const loginDropdown = document.getElementById('loginDropdown');

  // Add a click event listener to the login trigger
  loginTrigger.addEventListener('click', function (event) {
    event.preventDefault(); // Prevent default anchor behavior
    loginDropdown.style.display = (loginDropdown.style.display === 'none' || loginDropdown.style.display === '') ? 'block' : 'none';
  });

  // Close the dropdown if the user clicks outside of it
  document.addEventListener('click', function (event) {
    const isClickInside = loginTrigger.contains(event.target) || loginDropdown.contains(event.target);
    if (!isClickInside) {
      loginDropdown.style.display = 'none';
    }
  });
</script>
              
                              
                            </div>
                           
                        </div>
                    </nav>
                </div>
            </div>
        </div>