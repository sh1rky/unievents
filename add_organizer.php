<?php
require_once '../db_connect.php';
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'] ?? 'password';
    $role = $_POST['role'] ?? 'organisateur';
   
    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $password,
            ':role' => $role
        ]);
        header('Location: manage_organizers.php?success=added');
    } catch(PDOException $e) {
        header('Location: manage_organizers.php?error=' . urlencode($e->getMessage()));
    }
} else {
    header('Location: manage_organizers.php');
}
?>