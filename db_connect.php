<?php
$host = 'localhost';
$dbname = 'unievents_db';
$username = 'root';
$password = '';   // خليها فارغة

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("❌ Erreur : " . $e->getMessage());
}
?>