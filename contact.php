<?php
// ============================================
// fichier: api/contact.php
// chemin: C:\xampp\htdocs\unievents\api\contact.php
// ============================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    $data = $_POST;
}

// Vérifier les champs
if (!isset($data['name']) || empty($data['name'])) {
    echo json_encode(['success' => false, 'message' => 'Nom obligatoire']);
    exit;
}

if (!isset($data['email']) || empty($data['email'])) {
    echo json_encode(['success' => false, 'message' => 'Email obligatoire']);
    exit;
}

if (!isset($data['message']) || empty($data['message'])) {
    echo json_encode(['success' => false, 'message' => 'Message obligatoire']);
    exit;
}

try {
    $sql = "INSERT INTO contacts (name, email, subject, message) VALUES (:name, :email, :subject, :message)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $data['name'],
        ':email' => $data['email'],
        ':subject' => $data['subject'] ?? 'Général',
        ':message' => $data['message']
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => '📨 Votre message a été envoyé. Nous vous répondrons dans les plus brefs délais.'
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}
?>