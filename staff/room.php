<?php
session_start();
include("config/connect.php");


if (!isset($_SESSION['uname'])) {
  header("location:index.php");
  exit();
}

if (isset($_GET['delete'])) {
    // Assuming $conn is your database connection object

    // Sanitize the input to avoid SQL injection (assuming $conn is a PDO object)
    $id = htmlspecialchars($_GET['delete']);

    // Prepare the SQL statement with a placeholder for the ID
    $sql = "DELETE FROM room WHERE id = ?";

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
                                window.location.href = "room.php";
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
    $stmt->close();
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
    <link rel="icon" href="assets/img/a.jpg" type="image/x-icon" />
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
                <h3 class="fw-bold mb-3">Room List</h3>
                <h6 class="op-7 mb-2">Information</h6>
              </div>
            
              </div>
            </div>
          <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    
                     <div class="d-flex align-items-center">
                      <h4 class="card-title">Manage Room</h4>
                      <button onclick="location.href='add-room.php'" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus"></i>
                        Add Room
                      </button>
                    </div>
                  </div>
                  <div class="card-body">
    <div class="table-responsive">
        <table id="basic-datatables" class="display table table-striped table-hover" style="width: 100%;">
        <thead>
    <tr>
        <th>#</th>
        <th>Room</th>
        <th>Availability</th>
        <th>Bath</th>
        <th>Bed</th>
        <th>Price</th>
        <th>Image</th> <!-- New column for Room Image -->
        <th>Action</th>
    </tr>
</thead>
<tbody>
    <?php
    // Adjust the SQL query to join the room and room_images tables
    $sql = "
        SELECT r.id, r.r_name, r.available, r.bath, r.bed, r.price, ri.image_path
        FROM room AS r
        LEFT JOIN room_images AS ri ON r.id = ri.room_id
    ";

    $result = $conn->query($sql);
    $cnt = 1;
    if ($result->num_rows > 0) {
        $rooms = [];
        while ($row = $result->fetch_assoc()) {
            $rooms[$row['id']]['id'] = $row['id'];
            $rooms[$row['id']]['r_name'] = $row['r_name'];
            $rooms[$row['id']]['available'] = $row['available'];
            $rooms[$row['id']]['bath'] = $row['bath'];
            $rooms[$row['id']]['bed'] = $row['bed'];
            $rooms[$row['id']]['price'] = $row['price'];
            $rooms[$row['id']]['images'][] = $row['image_path']; // Collect images in an array
        }

        foreach ($rooms as $room) {
    ?>
            <tr>
                <td><?php echo htmlspecialchars($cnt); ?></td>
                <td><?php echo htmlspecialchars($room['r_name']); ?></td>
                <td><?php echo htmlspecialchars($room['available']); ?></td>
                <td><?php echo htmlspecialchars($room['bath']); ?></td>
                <td><?php echo htmlspecialchars($room['bed']); ?></td>
                <td><?php echo htmlspecialchars($room['price']); ?></td>
                <td>
                    <!-- Carousel for room images -->
                    <div id="carousel-<?php echo $room['id']; ?>" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php
                            // Check if images exist and create carousel items
                            if (!empty($room['images'])) {
                                foreach ($room['images'] as $index => $image) {
                                    if ($index === 0) {
                                        // Make the first image active
                                        echo '<div class="carousel-item active">';
                                    } else {
                                        echo '<div class="carousel-item">';
                                    }
                                    echo '<img src="uploads/' . htmlspecialchars($image) . '" class="d-block w-100" alt="Room Image" style="width: 100px; height: 50px; object-fit: cover;">';

                                    echo '</div>';
                                }
                            } else {
                                echo '<div class="carousel-item active"><img src="uploads/default.jpg" class="d-block w-100" alt="No Image" style="width: 100px; height: 50px; object-fit: cover;"></div>';
                            }
                            ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carousel-<?php echo $room['id']; ?>" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carousel-<?php echo $room['id']; ?>" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </td>
                <td>
                    <div class="btn-group dropstart">
                        <button type="button" class="btn btn-primary btn-border dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Action
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li>
                                <a class="dropdown-item" href="update_room.php?id=<?php echo $room['id']; ?>">
                                    <button class="btn btn-info btn-sm"><i class="fa fa-info"></i> Edit</button>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="room.php?delete=<?php echo $room['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Delete</button>
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
    <?php
            $cnt++;
        }
    } else {
        echo "<tr><td colspan='8'>No rooms found.</td></tr>";
    }
    ?>
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
