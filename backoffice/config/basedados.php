<?php
require 'credentials.php';

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


function pdo_connect_mysql() {
    $host = '94.46.180.24';
    $db = 'Tech-Art';
    $user = 'jmrrg';
    $pass = '12345678';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

    try {
        return new PDO($dsn, $user, $pass);
    }

    catch (PDOException $e) {
        throw new PDOException($e->getMessage(), (int)$e->getCode());
    }
}
?>