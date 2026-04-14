<?php

$host = "sql100.infinityfree.com";
$db   = "if0_41658227_pay2";
$user = "if0_41658227";
$pass = "79WfvkhlDwttS";

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {

    die("Database connection failed: " . $e->getMessage());

}

?>
