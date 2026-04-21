<?php
// ============================================
// API: Récupérer tous les événements
// Fichier: api/get_events.php
// URL: http://localhost/unievents/api/get_events.php
// ============================================

// Autoriser les requêtes depuis n'importe quelle origine (CORS)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Inclure la configuration
require_once '../config.php';

// Obtenir la connexion à la base de données
$pdo = getDBConnection();

// Paramètres de filtrage (optionnels)
$category = isset($_GET['category']) ? $_GET['category'] : null;
$status = isset($_GET['status']) ? $_GET['status'] : 'à_venir';
$search = isset($_GET['search']) ? $_GET['search'] : null;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

try {
    // Construire la requête SQL de base
    $sql = "SELECT 
                e.id,
                e.title,
                e.category,
                e.description,
                e.date,
                e.time,
                e.location,
                e.capacity,
                e.current_registrations,
                e.speaker,
                e.status,
                (e.capacity - e.current_registrations) AS places_restantes,
                ROUND((e.current_registrations / e.capacity) * 100, 2) AS taux_remplissage,
                u.name AS organizer_name
            FROM events e
            LEFT JOIN users u ON e.created_by = u.id
            WHERE 1=1";
    
    $params = [];
    
    // Filtrer par catégorie
    if ($category && $category != 'all') {
        $sql .= " AND e.category = :category";
        $params[':category'] = $category;
    }
    
    // Filtrer par statut
    if ($status && $status != 'all') {
        $sql .= " AND e.status = :status";
        $params[':status'] = $status;
    }
    
    // Rechercher par titre ou description
    if ($search) {
        $sql .= " AND (e.title LIKE :search OR e.description LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    // Trier par date (les plus proches d'abord)
    $sql .= " ORDER BY e.date ASC, e.time ASC";
    
    // Limiter les résultats
    $sql .= " LIMIT :limit OFFSET :offset";
    $params[':limit'] = $limit;
    $params[':offset'] = $offset;
    
    // Exécuter la requête
    $stmt = $pdo->prepare($sql);
    
    // Liaison des paramètres
    foreach ($params as $key => &$value) {
        if ($key == ':limit' || $key == ':offset') {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        } else {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
    }
    
    $stmt->execute();
    $events = $stmt->fetchAll();
    
    // Compter le nombre total d'événements (pour la pagination)
    $countSql = "SELECT COUNT(*) as total FROM events e WHERE 1=1";
    $countParams = [];
    
    if ($category && $category != 'all') {
        $countSql .= " AND e.category = :category";
        $countParams[':category'] = $category;
    }
    if ($status && $status != 'all') {
        $countSql .= " AND e.status = :status";
        $countParams[':status'] = $status;
    }
    if ($search) {
        $countSql .= " AND (e.title LIKE :search OR e.description LIKE :search)";
        $countParams[':search'] = "%$search%";
    }
    
    $countStmt = $pdo->prepare($countSql);
    foreach ($countParams as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $total = $countStmt->fetch()['total'];
    
    // Formater les événements pour l'affichage
    $formattedEvents = [];
    foreach ($events as $event) {
        $placesLeft = $event['capacity'] - $event['current_registrations'];
        
        // Déterminer la classe CSS pour les places
        if ($placesLeft <= 5) {
            $placesClass = 'critical';
        } elseif ($placesLeft <= 15) {
            $placesClass = 'warning';
        } else {
            $placesClass = 'available';
        }
        
        // Calculer le pourcentage de remplissage
        $fillPercentage = ($event['current_registrations'] / $event['capacity']) * 100;
        
        $formattedEvents[] = [
            'id' => $event['id'],
            'title' => $event['title'],
            'category' => $event['category'],
            'description' => $event['description'],
            'date' => date('d/m/Y', strtotime($event['date'])),
            'date_raw' => $event['date'],
            'time' => substr($event['time'], 0, 5),
            'location' => $event['location'],
            'capacity' => $event['capacity'],
            'current_registrations' => $event['current_registrations'],
            'places_restantes' => $placesLeft,
            'taux_remplissage' => $event['taux_remplissage'],
            'fill_percentage' => round($fillPercentage, 1),
            'places_class' => $placesClass,
            'speaker' => $event['speaker'],
            'organizer' => $event['organizer_name'],
            'status' => $event['status']
        ];
    }
    
    // Retourner la réponse JSON
    echo json_encode([
        'success' => true,
        'data' => $formattedEvents,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $limit,
            'total' => (int)$total,
            'total_pages' => ceil($total / $limit)
        ],
        'filters' => [
            'category' => $category,
            'status' => $status,
            'search' => $search
        ]
    ]);
    
} catch(PDOException $e) {
    if (DEBUG_MODE) {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur SQL: ' . $e->getMessage()
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Une erreur est survenue lors de la récupération des événements'
        ]);
    }
}
?>