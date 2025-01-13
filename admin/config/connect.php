<?php
$servername = "127.0.0.1:3306";
$username = "u510162695_rposystem";
$password = "1Rposystem";
$db = "u510162695_rposystem";
// Create connection
$conn = new mysqli($servername, $username, $password, $db);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

?>