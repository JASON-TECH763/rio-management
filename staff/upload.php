<?php
session_start();
include("config/connect.php");

if (!isset($_SESSION['uname'])) {
    header("location:index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $r_name = $_POST['r_name'];
    $available = $_POST['available'];
    $bath = $_POST['bath'];
    $bed = $_POST['bed'];
    $price = $_POST['price'];

    // Save room details first
    $sql = "INSERT INTO room (r_name, available, bath, bed, price) VALUES ('$r_name', '$available', '$bath', '$bed', '$price')";

    if ($conn->query($sql) === TRUE) {
        $room_id = $conn->insert_id; // Get the ID of the newly inserted room

        // Check if files were uploaded
        if (isset($_FILES["r_img"]) && count($_FILES["r_img"]["name"]) > 0) {
            $target_dir = "uploads/"; // Directory for storing uploads
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true); // Create uploads folder if not exists
            }

            // Loop through each uploaded file
            foreach ($_FILES["r_img"]["tmp_name"] as $key => $tmp_name) {
                if ($_FILES["r_img"]["error"][$key] == 0) {
                    $imageFileType = strtolower(pathinfo($_FILES["r_img"]["name"][$key], PATHINFO_EXTENSION));
                    $target_file = $target_dir . uniqid() . '.' . $imageFileType;

                    $uploadOk = 1;
                    $check = getimagesize($tmp_name);

                    if ($check === false) {
                        echo "File is not an image for file index $key.";
                        $uploadOk = 0;
                    }

                    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed for file index $key.";
                        $uploadOk = 0;
                    }

                    if ($uploadOk == 1) {
                        if (move_uploaded_file($tmp_name, $target_file)) {
                            // Save image path into room_images table
                            $r_img = basename($target_file); // Save just the filename
                            $sql_image = "INSERT INTO room_images (room_id, image_path) VALUES ('$room_id', '$r_img')";

                            if ($conn->query($sql_image) === FALSE) {
                                echo "Error uploading image for file index $key: " . $conn->error;
                            }
                        } else {
                            echo "Sorry, there was an error uploading your file for file index $key.";
                        }
                    }
                } else {
                    echo "Error with file index $key: " . $_FILES["r_img"]["error"][$key];
                }
            }

            // Success message
            echo '<script>
                    window.onload = function() {
                        Swal.fire({
                            title: "Success!",
                            text: "Data added successfully",
                            icon: "success"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "room.php";
                            }
                        });
                      };
                  </script>';
        } else {
            echo "No files were uploaded or an error occurred.";
        }
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
