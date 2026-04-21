<?php
// ============================================
// fichier: admin/header.php
// chemin: C:\xampp\htdocs\unievents\admin\header.php
// ============================================

session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Connexion à la base de données
require_once '../db_connect.php';

// Récupérer les notifications non lues
$unreadNotifications = 0;
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM contacts WHERE is_read = 0");
    $stmt->execute();
    $unreadNotifications = $stmt->fetch()['count'];
} catch(PDOException $e) {
    // Ignorer
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin UniEvents</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style_admin.css">
</head>
<body>
    <div class="admin-container">
        <nav class="admin-nav">
            <div class="nav-brand">
                <span class="logo">🎓 UniEvents</span>
                <span class="admin-badge">Admin</span>
            </div>
            <div class="nav-links">
                <a href="dashboard.php" class="nav-link">📊 Dashboard</a>
                <a href="manage_events.php" class="nav-link">📅 Événements</a>
                <a href="manage_users.php" class="nav-link">👥 Utilisateurs</a>
                <a href="manage_registrations.php" class="nav-link">📝 Inscriptions</a>
                <a href="manage_contacts.php" class="nav-link">
                    📧 Messages
                    <?php if ($unreadNotifications > 0): ?>
                        <span class="badge"><?php echo $unreadNotifications; ?></span>
                    <?php endif; ?>
                </a>
                <a href="manage_organizers.php" class="nav-link">🤝 Organisateurs</a>
            </div>
            <div class="nav-user">
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                <a href="logout.php" class="logout-btn">🚪 Déconnexion</a>
            </div>
        </nav>
        <main class="admin-main">