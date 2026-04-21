<?php
// ============================================
// fichier: api/login.php
// chemin: C:\xampp\htdocs\unievents\api\login.php
// ============================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../db_connect.php';

session_start();

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    $data = $_POST;
}

if (!isset($data['email']) || empty($data['email'])) {
    echo json_encode(['success' => false, 'message' => 'Email obligatoire']);
    exit;
}

if (!isset($data['password']) || empty($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Mot de passe obligatoire']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $data['email']]);
    $user = $stmt->fetch();
    
    // Pour le test, le mot de passe par défaut est "password" pour tous
    // En production, utilisez password_verify()
    if ($user && ($data['password'] === 'password' || password_verify($data['password'], $user['password']))) {
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        
        echo json_encode([
            'success' => true,
            'message' => 'Connexion réussie',
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Email ou mot de passe incorrect'
        ]);
    }
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}
?>