<?php
// ============================================
// fichier: api/register.php
// chemin: C:\xampp\htdocs\unievents\api\register.php
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

// Vérifier les champs obligatoires
if (!isset($data['event_id']) || empty($data['event_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID événement manquant']);
    exit;
}

if (!isset($data['nom']) || empty($data['nom'])) {
    echo json_encode(['success' => false, 'message' => 'Nom complet obligatoire']);
    exit;
}

if (!isset($data['email']) || empty($data['email'])) {
    echo json_encode(['success' => false, 'message' => 'Email obligatoire']);
    exit;
}

try {
    // Vérifier si l'utilisateur existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute([':email' => $data['email']]);
    $user = $stmt->fetch();
    
    if ($user) {
        $userId = $user['id'];
    } else {
        // Créer un nouvel utilisateur
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, niveau, specialite) 
                                VALUES (:name, :email, :phone, :niveau, :specialite)");
        $stmt->execute([
            ':name' => $data['nom'],
            ':email' => $data['email'],
            ':phone' => $data['tel'] ?? '',
            ':niveau' => $data['niveau'] ?? '',
            ':specialite' => $data['specialite'] ?? ''
        ]);
        $userId = $pdo->lastInsertId();
    }
    
    // Vérifier si déjà inscrit à cet événement
    $stmt = $pdo->prepare("SELECT * FROM registrations WHERE event_id = :event_id AND user_id = :user_id");
    $stmt->execute([
        ':event_id' => $data['event_id'],
        ':user_id' => $userId
    ]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Vous êtes déjà inscrit à cet événement']);
        exit;
    }
    
    // Vérifier les places disponibles
    $stmt = $pdo->prepare("SELECT capacity, current_registrations FROM events WHERE id = :event_id");
    $stmt->execute([':event_id' => $data['event_id']]);
    $event = $stmt->fetch();
    
    if (!$event) {
        echo json_encode(['success' => false, 'message' => 'Événement non trouvé']);
        exit;
    }
    
    $placesLeft = $event['capacity'] - $event['current_registrations'];
    
    if ($placesLeft > 0) {
        // Inscription normale
        $status = 'inscrit';
        $message = '✅ Inscription confirmée !';
        
        // Ajouter l'inscription
        $stmt = $pdo->prepare("INSERT INTO registrations (event_id, user_id, status, registered_at) VALUES (:event_id, :user_id, :status, NOW())");
        $stmt->execute([
            ':event_id' => $data['event_id'],
            ':user_id' => $userId,
            ':status' => $status
        ]);
        
        // Mettre à jour le nombre d'inscrits
        $stmt = $pdo->prepare("UPDATE events SET current_registrations = current_registrations + 1 WHERE id = :event_id");
        $stmt->execute([':event_id' => $data['event_id']]);
        
    } else {
        // Liste d'attente
        $status = 'waitlist';
        $message = '⏳ Complet ! Vous êtes sur la liste d\'attente';
        
        $stmt = $pdo->prepare("INSERT INTO registrations (event_id, user_id, status, registered_at) VALUES (:event_id, :user_id, :status, NOW())");
        $stmt->execute([
            ':event_id' => $data['event_id'],
            ':user_id' => $userId,
            ':status' => $status
        ]);
    }
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'status' => $status,
        'user_id' => $userId
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}
?>