<?php
// ============================================
// fichier: admin/manage_events.php
// chemin: C:\xampp\htdocs\unievents\admin\manage_events.php
// ============================================

require_once 'header.php';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM events WHERE id = :id");
                $stmt->execute([':id' => $_POST['event_id']]);
                $success = "Événement supprimé";
                break;
            case 'update_status':
                $stmt = $pdo->prepare("UPDATE events SET status = :status WHERE id = :id");
                $stmt->execute([
                    ':status' => $_POST['status'],
                    ':id' => $_POST['event_id']
                ]);
                $success = "Statut mis à jour";
                break;
        }
    }
}

// Récupérer tous les événements
try {
    $stmt = $pdo->query("
        SELECT e.*, u.name as organizer_name 
        FROM events e
        LEFT JOIN users u ON e.created_by = u.id
        ORDER BY e.date DESC
    ");
    $events = $stmt->fetchAll();
} catch(PDOException $e) {
    $error = $e->getMessage();
}
?>

<h1>📅 Gestion des événements</h1>

<?php if (isset($success)): ?>
    <div class="alert-success" style="background: rgba(100, 255, 218, 0.1); border: 1px solid #64ffda; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        ✅ <?php echo $success; ?>
    </div>
<?php endif; ?>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Catégorie</th>
                <th>Date</th>
                <th>Lieu</th>
                <th>Inscriptions</th>
                <th>Statut</th>
                <th>Organisateur</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $event): ?>
                <tr>
                    <td><?php echo $event['id']; ?></td>
                    <td><?php echo htmlspecialchars($event['title']); ?></td>
                    <td><?php echo $event['category']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($event['date'])); ?></td>
                    <td><?php echo htmlspecialchars($event['location']); ?></td>
                    <td><?php echo $event['current_registrations']; ?>/<?php echo $event['capacity']; ?></td>
                    <td>
                        <span class="status-badge status-<?php echo $event['status']; ?>">
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
                    </td>
                    <td><?php echo htmlspecialchars($event['organizer_name'] ?? '-'); ?></td>
                    <td>
                        <form method="POST" style="display: inline-block;" onsubmit="return confirm('Confirmer la suppression ?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
                        </form>
                        <button onclick="viewEvent(<?php echo $event['id']; ?>)" class="btn btn-primary btn-sm">👁️</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function viewEvent(id) {
    window.location.href = 'event_details.php?id=' + id;
}
</script>

<?php require_once 'footer.php'; ?>