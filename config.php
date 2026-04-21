<?php
// ============================================
// Fichier de configuration principal - config.php
// Chemin: C:\xampp\htdocs\unievents\config.php
// ============================================

// ====== 1. Configuration de la base de données ======
define('DB_HOST', 'localhost');
define('DB_NAME', 'unievents_db');
define('DB_USER', 'root');           // Utilisateur par défaut dans XAMPP
define('DB_PASS', '');               // Mot de passe vide dans XAMPP

// ====== 2. Configuration générale du site ======
define('SITE_NAME', 'UniEvents 🎓');
define('SITE_URL', 'http://localhost/unievents/');
define('SITE_EMAIL', 'contact@unievents.tn');
define('ADMIN_EMAIL', 'admin@unievents.tn');

// ====== 3. Configuration du temps et langue ======
date_default_timezone_set('Africa/Tunis');
setlocale(LC_TIME, 'fr_FR', 'fr_FR.utf8', 'fra');

// ====== 4. Configuration des uploads (fichiers/images) ======
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('EVENTS_IMG_DIR', UPLOAD_DIR . 'events/');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// ====== 5. Configuration de l'authentification ======
define('SESSION_LIFETIME', 7200);    // 2 heures (en secondes)
define('PASSWORD_BCRYPT_COST', 12);  // Coût de hachage du mot de passe

// ====== 6. Configuration des événements ======
define('EVENTS_PER_PAGE', 9);        // Nombre d'événements par page
define('WAITLIST_AUTO_APPROVE', true); // Inscription auto depuis liste d'attente

// ====== 7. Configuration des emails (SMTP) ======
// À configurer plus tard si vous utilisez l'envoi d'emails
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_FROM_EMAIL', 'noreply@unievents.tn');
define('SMTP_FROM_NAME', 'UniEvents');

// ====== 8. Configuration de la sécurité ======
define('CSRF_PROTECTION', true);     // Protection contre les attaques CSRF
define('RATE_LIMIT', 60);            // 60 requêtes par minute max

// ====== 9. Mode debug ======
define('DEBUG_MODE', true);          // Mettre false en production
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ====== 10. Création automatique des dossiers ======
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}
if (!file_exists(EVENTS_IMG_DIR)) {
    mkdir(EVENTS_IMG_DIR, 0777, true);
}

// ====== Fonction de connexion à la base de données ======
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch(PDOException $e) {
        if (DEBUG_MODE) {
            die("❌ Erreur de connexion : " . $e->getMessage());
        } else {
            die("❌ Erreur de connexion à la base de données");
        }
    }
}

// ====== Fonction pour obtenir l'URL du site ======
function url($path = '') {
    return SITE_URL . ltrim($path, '/');
}

// ====== Fonction pour rediriger ======
function redirect($path = '') {
    header('Location: ' . url($path));
    exit();
}

// ====== Fonction pour afficher les messages flash ======
function setFlashMessage($type, $message) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash'] = [
        'type' => $type,  // success, error, warning, info
        'message' => $message
    ];
}

function getFlashMessage() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function displayFlashMessage() {
    $flash = getFlashMessage();
    if ($flash) {
        $icons = [
            'success' => '✅',
            'error' => '❌',
            'warning' => '⚠️',
            'info' => 'ℹ️'
        ];
        $icon = $icons[$flash['type']] ?? '📌';
        echo '<div class="flash-message flash-' . $flash['type'] . '">';
        echo $icon . ' ' . htmlspecialchars($flash['message']);
        echo '</div>';
    }
}

// ====== Fonction pour échapper le HTML ======
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// ====== Fonction pour formater les dates ======
function formatDate($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}

function formatDateTime($datetime) {
    return date('d/m/Y à H:i', strtotime($datetime));
}

// ====== Fonction pour tronquer le texte ======
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

// ====== Démarrage de la session ======
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>