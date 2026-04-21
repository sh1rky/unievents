<?php
// ============================================
// fichier: admin/event_details.php
// chemin: C:\xampp\htdocs\unievents\admin\event_details.php
// ============================================

require_once 'header.php';

// Vérifier que l'ID est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: manage_events.php');
    exit;
}

$eventId = intval($_GET['id']);
$event = null;
$registrations = [];
$error = '';

try {
    // Récupérer les détails de l'événement
    $stmt = $pdo->prepare("
        SELECT e.*, u.name as organizer_name, u.email as organizer_email
        FROM events e
        LEFT JOIN users u ON e.created_by = u.id
        WHERE e.id = :id
    ");
    $stmt->execute([':id' => $eventId]);
    $event = $stmt->fetch();
    
    if (!$event) {
        $error = "Événement non trouvé";
    } else {
        // Récupérer les inscriptions
        $stmt = $pdo->prepare("
            SELECT r.*, u.name, u.email, u.phone, u.niveau, u.specialite
            FROM registrations r
            JOIN users u ON r.user_id = u.id
            WHERE r.event_id = :event_id
            ORDER BY r.registered_at DESC
        ");
        $stmt->execute([':event_id' => $eventId]);
        $registrations = $stmt->fetchAll();
    }
} catch(PDOException $e) {
    $error = $e->getMessage();
}

?>

<h1>📋 Détails de l'événement</h1>

<?php if (!empty($error)): ?>
    <div class="alert-error" style="background: rgba(255, 100, 100, 0.1); border: 1px solid #ff6464; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        ❌ <?php echo htmlspecialchars($error); ?>
    </div>
    <a href="manage_events.php" class="btn btn-secondary">← Retour aux événements</a>
<?php elseif ($event): ?>
    
    <div style="display: flex; gap: 20px; margin-bottom: 30px;">
        <a href="manage_events.php" class="btn btn-secondary">← Retour</a>
    </div>

    <!-- Informations principales -->
    <div class="table-container" style="margin-bottom: 30px;">
        <div style="padding: 20px; border-bottom: 1px solid #233554;">
            <h2><?php echo htmlspecialchars($event['title']); ?></h2>
        </div>
        <div style="padding: 20px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <p><strong>ID:</strong> <?php echo $event['id']; ?></p>
                    <p><strong>Catégorie:</strong> <?php echo $event['category']; ?></p>
                    <p><strong>Date:</strong> <?php echo date('d/m/Y', strtotime($event['date'])); ?></p>
                    <p><strong>Heure:</strong> <?php echo substr($event['time'], 0, 5); ?></p>
                    <p><strong>Lieu:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
                </div>
                <div>
                    <p><strong>Capacité:</strong> <?php echo $event['capacity']; ?> places</p>
                    <p><strong>Inscriptions:</strong> <?php echo $event['current_registrations']; ?>/<?php echo $event['capacity']; ?></p>
                    <p><strong>Places restantes:</strong> <?php echo max(0, $event['capacity'] - $event['current_registrations']); ?></p>
                    <p><strong>Taux de remplissage:</strong> 
                        <span style="color: #64ffda;">
                            <?php echo round(($event['current_registrations'] / $event['capacity']) * 100, 1); ?>%
                        </span>
                    </p>
                    <p><strong>Statut:</strong> 
                        <span class="status-badge status-<?php echo $event['status']; ?>" style="padding: 5px 10px; border-radius: 4px; background: rgba(100, 255, 218, 0.2); color: #64ffda;">
                            <?php 
                                $statusLabels = [
                                    'à_venir' => 'À venir',
                                    'en_cours' => 'En cours',
                                    'terminé' => 'Terminé',
                                    'annulé' => 'Annulé'
                                ];
                                echo $statusLabels[$event['status']] ?? $event['status'];
                            ?>
                        </span>
                    </p>
                </div>
            </div>

            <div style="margin-top: 20px;">
                <h3>Description</h3>
                <p style="color: #8892b0; line-height: 1.6;">
                    <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                </p>
            </div>

            <?php if ($event['speaker']): ?>
                <div style="margin-top: 20px;">
                    <h3>Intervenant/Conférencier</h3>
                    <p style="color: #8892b0;"><?php echo htmlspecialchars($event['speaker']); ?></p>
                </div>
            <?php endif; ?>

            <div style="margin-top: 20px;">
                <h3>Organisateur</h3>
                <p style="color: #8892b0;">
                    <strong><?php echo htmlspecialchars($event['organizer_name'] ?? 'Admin'); ?></strong><br>
                    <?php echo htmlspecialchars($event['organizer_email'] ?? '-'); ?>
                </p>
            </div>

            <?php if ($event['created_at']): ?>
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #233554;">
                    <p style="color: #6a7088; font-size: 0.9em;">
                        Créé le: <?php echo date('d/m/Y à H:i', strtotime($event['created_at'])); ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Liste des inscriptions -->
    <div class="table-container">
        <div style="padding: 20px; border-bottom: 1px solid #233554;">
            <h3>👥 Inscriptions (<?php echo count($registrations); ?>)</h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Niveau</th>
                    <th>Spécialité</th>
                    <th>Statut</th>
                    <th>Date d'inscription</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($registrations) > 0): ?>
                    <?php foreach ($registrations as $reg): ?>
                        <tr>
                            <td><?php echo $reg['id']; ?></td>
                            <td><?php echo htmlspecialchars($reg['name']); ?></td>
                            <td><?php echo htmlspecialchars($reg['email']); ?></td>
                            <td><?php echo htmlspecialchars($reg['phone'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($reg['niveau'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($reg['specialite'] ?? '-'); ?></td>
                            <td>
                                <span style="padding: 4px 8px; border-radius: 4px; background: rgba(100, 255, 218, 0.2); color: #64ffda;">
                                    <?php 
                                        $statusLabels = [
                                            'inscrit' => 'Inscrit',
                                            'en_attente' => 'En attente',
                                            'annulé' => 'Annulé'
                                        ];
                                        echo $statusLabels[$reg['status']] ?? $reg['status'];
                                    ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($reg['registered_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; color: #8892b0;">Aucune inscription</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php else: ?>
    <p style="color: #8892b0;">Événement non trouvé</p>
    <a href="manage_events.php" class="btn btn-secondary">← Retour aux événements</a>
<?php endif; ?>

<?php require_once 'footer.php'; ?>
