<?php
session_start();
include("config/connect.php");

header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; img-src 'self' data:; font-src 'self' https://fonts.googleapis.com https://cdn.jsdelivr.net; frame-ancestors 'none'; form-action 'self'; base-uri 'self';");

if (!isset($_SESSION['uname'])) {
    header("location:index.php");
    exit();
}

function sanitizeRoomName($input) {
    $sanitized = strip_tags($input);
    $sanitized = htmlspecialchars($sanitized, ENT_QUOTES, 'UTF-8');
    $sanitized = preg_replace("/[^a-zA-Z0-9 '-]/", "", $sanitized);
    return $sanitized;
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $r_name = sanitizeRoomName($_POST['r_name']);
    $available = $_POST['available'];
    $bath = $_POST['bath'];
    $bed = $_POST['bed'];
    $price = $_POST['price'];

    if (empty($r_name) || strlen($r_name) > 100) {
        $message = '<div class="alert alert-danger">Invalid room name. Please enter a valid name (up to 100 characters).</div>';
    } else {
        if (isset($_FILES["r_img"]) && $_FILES["r_img"]["error"] == 0) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $imageFileType = strtolower(pathinfo($_FILES["r_img"]["name"], PATHINFO_EXTENSION));
            $target_file = $target_dir . uniqid() . '.' . $imageFileType;

            $uploadOk = 1;
            $check = getimagesize($_FILES["r_img"]["tmp_name"]);

            if ($check === false) {
                $message = "File is not an image.";
                $uploadOk = 0;
            }

            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }

            if ($uploadOk == 1) {
                if (move_uploaded_file($_FILES["r_img"]["tmp_name"], $target_file)) {
                    $r_img = basename($target_file);
                    $sql = "INSERT INTO room (r_name, available, bath, bed, price, r_img) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssssss", $r_name, $available, $bath, $bed, $price, $r_img);

                    if ($stmt->execute()) {
                        $message = '<script>
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
                        $message = "Error: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $message = "Sorry, there was an error uploading your file.";
                }
            }
        } else {
            $message = "No file was uploaded or an error occurred.";
        }
    }
}
?>