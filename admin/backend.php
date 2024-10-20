<?php
// backend.php
session_start();
include("config/connect.php");

header("Content-Type: application/json");

if (!isset($_SESSION['uname'])) {
  echo json_encode(['error' => 'Unauthorized']);
  exit();
}

function sanitizeRoomName($input) {
    $sanitized = strip_tags($input);
    $sanitized = htmlspecialchars($sanitized, ENT_QUOTES, 'UTF-8');
    $sanitized = preg_replace("/[^a-zA-Z0-9 '-]/", "", $sanitized);
    return $sanitized;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $r_name = sanitizeRoomName($_POST['r_name']);
    $available = $_POST['available'];
    $bath = $_POST['bath'];
    $bed = $_POST['bed'];
    $price = $_POST['price'];

    if (empty($r_name) || strlen($r_name) > 100) {
        echo json_encode(['error' => 'Invalid room name.']);
        exit();
    }

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
            echo json_encode(['error' => 'File is not an image.']);
            exit();
        }

        if (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
            echo json_encode(['error' => 'Only JPG, JPEG, PNG & GIF files are allowed.']);
            exit();
        }

        if (move_uploaded_file($_FILES["r_img"]["tmp_name"], $target_file)) {
            $r_img = basename($target_file);
            $sql = "INSERT INTO room (r_name, available, bath, bed, price, r_img) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $r_name, $available, $bath, $bed, $price, $r_img);

            if ($stmt->execute()) {
                echo json_encode(['success' => 'Room added successfully']);
            } else {
                echo json_encode(['error' => 'Database error: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['error' => 'Error uploading file.']);
        }
    } else {
        echo json_encode(['error' => 'No file uploaded or an error occurred.']);
    }
}
?>
