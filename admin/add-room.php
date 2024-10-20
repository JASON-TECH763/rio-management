<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Room Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php include("include/sidenavigation.php"); ?>
    <div class="container">
        <h3>Add New Room</h3>
        <form id="roomForm" enctype="multipart/form-data">
            <div class="form-group">
                <label for="r_name">Room Name</label>
                <input type="text" class="form-control" id="r_name" name="r_name" placeholder="Enter Room Name" required>
            </div>
            <div class="form-group">
                <label for="available">Availability</label>
                <select class="form-control" id="available" name="available" required>
                    <option value="1 Available Room">1 Available Room</option>
                    <option value="2 Available Room">2 Available Room</option>
                    <option value="Not Available">Not Available</option>
                </select>
            </div>
            <div class="form-group">
                <label for="bath">Bath</label>
                <select class="form-control" id="bath" name="bath" required>
                    <option value="1 Bath">1 Bath</option>
                    <option value="2 Bath">2 Bath</option>
                    <option value="3 Bath">3 Bath</option>
                </select>
            </div>
            <div class="form-group">
                <label for="bed">Bed</label>
                <select class="form-control" id="bed" name="bed" required>
                    <option value="1 Bed">1 Bed</option>
                    <option value="2 Bed">2 Bed</option>
                    <option value="3 Bed">3 Bed</option>
                </select>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="text" class="form-control" id="price" name="price" placeholder="Price" required>
            </div>
            <div class="form-group">
                <label for="r_img">Room Image</label>
                <input type="file" class="form-control" id="r_img" name="r_img" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script>
        document.getElementById('roomForm').addEventListener('submit', function(event) {
            event.preventDefault();
            var formData = new FormData(this);

            fetch('backend.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    Swal.fire('Error', data.error, 'error');
                } else {
                    Swal.fire('Success', data.success, 'success')
                        .then(() => window.location.reload());
                }
            })
            .catch(error => console.error('Error:', error));
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
