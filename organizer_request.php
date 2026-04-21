<?php
// ============================================
// fichier: api/organizer_request.php
// chemin: C:\xampp\htdocs\unievents\api\organizer_request.php
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

if (!isset($data['club']) || empty($data['club'])) {
    echo json_encode(['success' => false, 'message' => 'Nom du club obligatoire']);
    exit;
}

try {
    $sql = "INSERT INTO organizer_requests (name, email, club, role) VALUES (:name, :email, :club, :role)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $data['name'],
        ':email' => $data['email'],
        ':club' => $data['club'],
        ':role' => $data['role'] ?? ''
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => '📋 Demande envoyée ! L\'équipe UniEvents vous contactera sous 24h.'
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}
?>