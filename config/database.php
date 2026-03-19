<?php

$host = "sql204.infinityfree.com";
$db   = "if0_41428895_pay2";
$user = "if0_41428895";
$pass = "lsmJ6r4kEE";

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
