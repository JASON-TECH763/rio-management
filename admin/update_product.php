<?php
session_start();
include("config/connect.php");

// Check if `prod_id` is present in the URL
if (isset($_GET['prod_id']) && isset($_POST['submit'])) {
    $prod_id = intval($_GET['prod_id']); // Get the prod_id from the URL
    $prod_name = $_POST['prod_name'];
    $prod_price = $_POST['prod_price'];

    // Handle file upload
    if (isset($_FILES['prod_img']['name']) && $_FILES['prod_img']['name'] != "") {
        $prod_img = $_FILES['prod_img']['name'];
        move_uploaded_file($_FILES["prod_img"]["tmp_name"], "assets/img/products/" . $_FILES["prod_img"]["name"]);

        // Prepare SQL statement with image
        $sql = "UPDATE rpos_products SET prod_name = ?, prod_price = ?, prod_img = ? WHERE prod_id = ?";
        $stmt = mysqli_prepare($conn, $sql);

        // Bind parameters and execute
        mysqli_stmt_bind_param($stmt, "sssi", $prod_name, $prod_price, $prod_img, $prod_id);
    } else {
        // Prepare SQL statement without image
        $sql = "UPDATE rpos_products SET prod_name = ?, prod_price = ? WHERE prod_id = ?";
        $stmt = mysqli_prepare($conn, $sql);

        // Bind parameters and execute
        mysqli_stmt_bind_param($stmt, "ssi", $prod_name, $prod_price, $prod_id);
    }
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        echo '<script>
               window.onload = function() {
            Swal.fire({
                title: "Success!",
                text: "Product data successfully updated!",
                icon: "success"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "product.php";
                }
            });
        };
      </script>';
    } else {
       echo '<script>
              window.onload = function() {
                Swal.fire({
                title: "Error!",
                text: "Error updating product data.",
                icon: "error"
            });
        };
      </script>';
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Rio Management System</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="assets/img/a.jpg" type="image/x-icon" />
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
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/plugins.min.css" />
    <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
    <link rel="stylesheet" href="assets/css/demo.css" />
  </head>
  <body>
    <div class="wrapper">
      <?php include("include/sidenavigation.php");?>
      <div class="main-panel">
        <?php include("include/header.php");?>
        <div class="container">
          <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
              <div>
                <h3 class="fw-bold mb-3">Product List</h3>
                <h6 class="op-7 mb-2">Information</h6>
              </div>
            </div>
          </div>
          <div class="container mb-3">
            <div class="card">
              <center>
                <div class="card-header">
                   Update Product
                </div>
              </center>
              <div class="card-body">
                <div class="col-md-6 ms-auto me-auto">
                  <div class="wow fadeInUp" data-wow-delay="0.2s">
                    <form method="POST" action="" enctype="multipart/form-data">
                      <?php
                      $prod_id = intval($_GET['prod_id']);
                      $query = mysqli_query($conn, "SELECT * FROM rpos_products WHERE prod_id='$prod_id'");
                      if ($query) {
                          while ($row = mysqli_fetch_array($query)) {
                      ?>
                          <div class="row g-3">
                            <div class="col-md-12">
                              <div class="form-floating">
                                <input type="text" class="form-control" name="prod_name" placeholder="Product Name" required value="<?php echo htmlspecialchars($row['prod_name']); ?>">
                                <input type="hidden" class="form-control" name="rid" placeholder="rid" required readonly value="<?php echo htmlspecialchars($row['prod_id']); ?>">
                                <label for="prod_name">Product Name</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-floating">
                                <input type="text" class="form-control" id="prod_price" name="prod_price" placeholder="Price" required value="<?php echo htmlspecialchars($row['prod_price']); ?>">
                                <label for="prod_price">Price</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-floating">
                                <input type="file" class="form-control" id="prod_img" name="prod_img" placeholder="Product Image">
                                <label for="prod_img">Product Image</label>
                                <?php if ($row['prod_img'] != ""): ?>
                                    <img src="assets/img/products/<?php echo htmlspecialchars($row['prod_img']); ?>" alt="Product Image" style="max-width: 100%; height: auto;">
                                <?php endif; ?>
                              </div>
                            </div>
                            <div class="col-6">
                              <button class="btn btn-primary w-100 py-3" name="submit" type="submit">Submit</button>
                            </div>
                             <div class="col-6">
                              <button onclick="location.href='products.php'" class="btn btn-black w-100 py-3"  type="button">Cancel</button>
                            </div>
                          </div>
                      <?php
                          }
                      }
                      mysqli_close($conn);
                      ?>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php include("include/footer.php");?>
        </div>
      </div>
    </div>
    <script src="assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>
    <script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="assets/js/plugin/chart.js/chart.min.js"></script>
    <script src="assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>
    <script src="assets/js/plugin/chart-circle/circles.min.js"></script>
    <script src="assets/js/plugin/datatables/datatables.min.js"></script>
    <script src="assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
    <script src="assets/js/plugin/jsvectormap/world.js"></script>
    <script src="assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <script src="assets/js/kaiadmin.min.js"></script>
    <script src="assets/js/sweetalert.js"></script>
    <script>
      $(document).ready(function () {
        $("#basic-datatables").DataTable({});
        $("#multi-filter-select").DataTable({
          pageLength: 5,
          initComplete: function () {
            this.api().columns().every(function () {
              var column = this;
              var select = $('<select class="form-select"><option value=""></option></select>')
                .appendTo($(column.footer()).empty())
                .on("change", function () {
                  var val = $.fn.dataTable.util.escapeRegex($(this).val());
                  column.search(val ? "^" + val + "$" : "", true, false).draw();
                });
              column.data().unique().sort().each(function (d) {
                select.append('<option value="' + d + '">' + d + "</option>");
              });
            });
          },
        });
        $("#add-row").DataTable({ pageLength: 5 });
        var action = '<td> <div class="form-button-action"> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg" data-original-title="Edit Task"> <i class="fa fa-edit"></i> </button> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-danger" data-original-title="Remove"> <i class="fa fa-times"></i> </button> </div> </td>';
        $("#addRowButton").click(function () {
          $("#add-row").dataTable().fnAddData([
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
