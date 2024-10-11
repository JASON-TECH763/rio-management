<?php
session_start();
include("config/connect.php");


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $r_name = $_POST['r_name'];
    $available = $_POST['available'];
    $bath = $_POST['bath'];
    $bed = $_POST['bed'];
    $price = $_POST['price'];

   // $sql="insert into reservations (booking_id,checkin_date,checkout_date,amount,title,first_name,last_name,email,phone,country,payment) values('$booking_id','$checkin_date','$checkout_date','$amount','$title','$first_name','$last_name','$email','$phone','$country','$payment')";

   $sql = "INSERT INTO room (r_name, available, bath, bed, price)
            VALUES ('$r_name','$available','$bath','$bed', '$price')";


    // $result=mysqli_query($mysqli,$sql);

    if ($conn->query($sql) === TRUE)
    // if($result)
        {
            echo '<script>
                    window.onload = function() {
                        Swal.fire({
                            title: "Success!",
                            text: "Data added Successfully",
                            icon: "success"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "room.php";
                            }
                        });
                    };
                  </script>';
                    
        }
        else
        {
           echo '<script>
                    window.onload = function() {
                        Swal.fire({
                            title: "Error!",
                            text: "failed to add data.",
                            icon: "error"
                        });
                    };
                  </script>';
        }
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
            <div
              class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4"
            >
              <div>
                <h3 class="fw-bold mb-3">Room List</h3>
                <h6 class="op-7 mb-2">Information</h6>
              </div>
            
              </div>
            </div>
            <div class="container mb-3">
              <div class="card">
                <center>
                  <div class="card-header">
                  Add New Room
                  </div>
               </center>
                <div class="card-body">
                 <div class="col-md-6 ms-auto me-auto">
                        <div class="wow fadeInUp" data-wow-delay="0.2s">
                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                <div class="row g-3">
                              <div class="col-md-12">
                                        <div class="form-floating">
                                          <select class="form-control" id="r_name" name="r_name" required>
                                            <option value="Standard Single Room">Standard Single Room</option>
                                            <option value="Standard Twin Room">Standard Twin Room</option>
                                            
                                          </select>
                                            <label for="r_name">Room Name</label>
                                        
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                          <select class="form-control" id="available" name="available" required>
                                            <option value="1 Available Room">1 Available Room</option>
                                            <option value="2 Availble Room">2 Availble Room</option>
                                            <option value="Not Available">Not Available</option>
                                          </select>
                                            <label for="available">Availabilty</label>
                                        
                                        </div>
                                    </div>
                                   <div class="col-md-6">
                                        <div class="form-floating">
                                          <select class="form-control" id="bath" name="bath" required>
                                            <option value="1 Bath">1 Bath</option>
                                            <option value="2 Bath">2 Bath</option>
                                            <option value="3 Bath">3 Bath</option>
                                          </select>
                                            <label for="bath">Bath</label>
                                        
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                          <select class="form-control" id="bed" name="bed" required>
                                            <option value="1 Bed">1 Bed</option>
                                            <option value="2 Bed">2 Bed</option>
                                            <option value="3 Bed">3 Bed</option>
                                          </select>
                                            <label for="bed">Availabilty</label>
                                        
                                        </div>
                                    </div>
                                    <div class="col-md-6">
    <div class="form-floating">
        <input type="text" class="form-control" id="price" name="price" placeholder="Price" required oninput="this.value = this.value.replace(/[^0-9]/g, '');">
        <label for="price">Price</label>
    </div>
</div>
                                    <div class="col-12">
                                        <button class="btn btn-primary w-100 py-3" name="submit" type="submit">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                  </div>
                  </div>
                </div>
                       
 <?php include("include/footer.php");?>
        
      <!-- Custom template | don't include it in your project! -->
    
      <!-- End Custom template -->
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
