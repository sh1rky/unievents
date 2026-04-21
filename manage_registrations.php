<?php
// ============================================
// fichier: admin/manage_registrations.php
// chemin: C:\xampp\htdocs\unievents\admin\manage_registrations.php
// ============================================

require_once 'header.php';

// Récupérer toutes les inscriptions
try {
    $stmt = $pdo->query("
        SELECT r.*, e.title as event_title, e.date as event_date, 
               u.name as user_name, u.email as user_email
        FROM registrations r
        JOIN events e ON r.event_id = e.id
        JOIN users u ON r.user_id = u.id
        ORDER BY r.registered_at DESC
    ");
    $registrations = $stmt->fetchAll();
} catch(PDOException $e) {
    $error = $e->getMessage();
}
?>

<h1>📝 Gestion des inscriptions</h1>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Étudiant</th>
                <th>Email</th>
                <th>Événement</th>
                <th>Date événement</th>
                <th>Statut</th>
                <th>Date inscription</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($registrations as $reg): ?>
                <tr>
                    <td><?php echo $reg['id']; ?></td>
                    <td><?php echo htmlspecialchars($reg['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($reg['user_email']); ?></td>
                    <td><?php echo htmlspecialchars($reg['event_title']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($reg['event_date'])); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo $reg['status']; ?>">
                            <?php 
                                $statusLabels = [
                                    'inscrit' => 'Inscrit',
                                    'waitlist' => 'Liste d\'attente',
                                    'annulé' => 'Annulé',
                                    'présent' => 'Présent'
                                ];
                                echo $statusLabels[$reg['status']] ?? $reg['status'];
                            ?>
                        </span>
                    </td>
                    <td><?php echo date('d/m/Y H:i', strtotime($reg['registered_at'])); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>