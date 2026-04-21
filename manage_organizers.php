<?php
// ============================================
// fichier: admin/manage_organizers.php
// chemin: C:\xampp\htdocs\unievents\admin\manage_organizers.php
// ============================================

require_once 'header.php';

// Traitement des demandes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $newStatus = $_POST['action'] === 'approve' ? 'approuvé' : 'refusé';
        
        // Mettre à jour la demande
        $stmt = $pdo->prepare("UPDATE organizer_requests SET status = :status, reviewed_by = :reviewed_by, reviewed_at = NOW() WHERE id = :id");
        $stmt->execute([
            ':status' => $newStatus,
            ':reviewed_by' => $_SESSION['admin_id'],
            ':id' => $_POST['request_id']
        ]);
        
        // Si approuvé, créer un compte organisateur
        if ($newStatus === 'approuvé') {
            $stmt = $pdo->prepare("SELECT * FROM organizer_requests WHERE id = :id");
            $stmt->execute([':id' => $_POST['request_id']]);
            $request = $stmt->fetch();
            
            // Vérifier si l'utilisateur existe déjà
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute([':email' => $request['email']]);
            $existingUser = $stmt->fetch();
            
            if (!$existingUser) {
                // Créer un nouvel utilisateur organisateur
                $stmt = $pdo->prepare("INSERT INTO users (name, email, role, club) VALUES (:name, :email, 'organisateur', :club)");
                $stmt->execute([
                    ':name' => $request['name'],
                    ':email' => $request['email'],
                    ':club' => $request['club']
                ]);
            } else {
                // Mettre à jour le rôle
                $stmt = $pdo->prepare("UPDATE users SET role = 'organisateur', club = :club WHERE id = :id");
                $stmt->execute([
                    ':club' => $request['club'],
                    ':id' => $existingUser['id']
                ]);
            }
        }
        
        $success = "Demande " . ($newStatus === 'approuvé' ? 'approuvée' : 'refusée');
    }
}

// Récupérer toutes les demandes
$requests = [];
try {
    $stmt = $pdo->query("
        SELECT r.*, u.name as reviewer_name 
        FROM organizer_requests r
        LEFT JOIN users u ON r.reviewed_by = u.id
        ORDER BY r.requested_at DESC
    ");
    $requests = $stmt->fetchAll();
} catch(PDOException $e) {
    $error = $e->getMessage();
}
?>

<h1>🤝 Demandes organisateur</h1>

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
                <th>Nom</th>
                <th>Email</th>
                <th>Club</th>
                <th>Rôle</th>
                <th>Statut</th>
                <th>Date demande</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $req): ?>
                <tr>
                    <td><?php echo $req['id']; ?></td>
                    <td><?php echo htmlspecialchars($req['name']); ?></td>
                    <td><?php echo htmlspecialchars($req['email']); ?></td>
                    <td><?php echo htmlspecialchars($req['club']); ?></td>
                    <td><?php echo htmlspecialchars($req['role'] ?? '-'); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo $req['status']; ?>">
                            <?php 
                                $statusLabels = [
                                    'en_attente' => 'En attente',
                                    'approuvé' => 'Approuvé',
                                    'refusé' => 'Refusé'
                                ];
                                echo $statusLabels[$req['status']] ?? $req['status'];
                            ?>
                        </span>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($req['requested_at'])); ?></td>
                    <td>
                        <?php if ($req['status'] === 'en_attente'): ?>
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="action" value="approve">
                                <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                <button type="submit" class="btn btn-primary btn-sm">✅ Approuver</button>
                            </form>
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="action" value="reject">
                                <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">❌ Refuser</button>
                            </form>
                        <?php else: ?>
                            <span class="btn-sm" style="opacity: 0.5;">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>