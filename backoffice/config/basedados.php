<?php
require 'credentials.php';

// $servername = "127.0.0.1";
// $username = USERNAME;
// $password = PASSWORD;
// $dbname = "technart";
// $charset = "utf8mb4";

$servername = "94.46.180.24";
$username = 'costa';
$password = '12345';
$dbname = "Tech-Art";
$charset = "utf8mb4";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, $charset);
?>