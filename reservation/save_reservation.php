<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rposystem";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$name = $_POST['name'];
$email = $_POST['email'];
$checkin_date = $_POST['checkin_date'];
$checkout_date = $_POST['checkout_date'];
$room_type = $_POST['room_type'];

// Insert reservation data into the database
$sql = "INSERT INTO reservations (name, email, checkin_date, checkout_date, room_type)
        VALUES ('$name', '$email', '$checkin_date', '$checkout_date', '$room_type')";

if ($conn->query($sql) === TRUE) {
    echo "Reservation successful!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close connection
$conn->close();
?>
