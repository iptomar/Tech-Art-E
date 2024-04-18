<?php
require 'credentials.php';

function pdo_connect_mysql() {
    // $host = '127.0.0.1';
    // $db = 'technart';
    // $user = USERNAME;
    // $pass = PASSWORD;
    $host = "94.46.180.24";
    $user = 'costa';
    $pass = '12345';

    $db = "Tech-Art";
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    }

    catch (PDOException $e) {
        throw new PDOException($e->getMessage(), (int)$e->getCode());
    }
}
?>


