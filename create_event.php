<?php
// ============================================
// fichier: api/create_event.php
// chemin: C:\xampp\htdocs\unievents\api\create_event.php
// ============================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../db_connect.php';

// Lecture des données JSON envoyées
$data = json_decode(file_get_contents('php://input'), true);

// Si pas de JSON, essayer les données POST normales
if (!$data) {
    $data = $_POST;
}

// Vérifier les champs obligatoires
if (!isset($data['title']) || empty($data['title'])) {
    echo json_encode(['success' => false, 'message' => 'Le titre est obligatoire']);
    exit;
}

if (!isset($data['category']) || empty($data['category'])) {
    echo json_encode(['success' => false, 'message' => 'La catégorie est obligatoire']);
    exit;
}

if (!isset($data['capacity']) || empty($data['capacity'])) {
    echo json_encode(['success' => false, 'message' => 'La capacité est obligatoire']);
    exit;
}

try {
    $sql = "INSERT INTO events (title, category, description, date, time, location, capacity, speaker, created_by) 
            VALUES (:title, :category, :description, :date, :time, :location, :capacity, :speaker, :created_by)";
    
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        ':title' => $data['title'],
        ':category' => $data['category'],
        ':description' => $data['description'] ?? '',
        ':date' => $data['date'] ?? date('Y-m-d'),
        ':time' => $data['time'] ?? '00:00:00',
        ':location' => $data['location'] ?? '',
        ':capacity' => $data['capacity'],
        ':speaker' => $data['speaker'] ?? '',
        ':created_by' => $data['created_by'] ?? 1  // 1 = admin par défaut
    ]);
    
    $eventId = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Événement créé avec succès',
        'event_id' => $eventId
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}
?>