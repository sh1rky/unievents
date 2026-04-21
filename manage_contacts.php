<?php
// ============================================
// fichier: admin/manage_contacts.php
// chemin: C:\xampp\htdocs\unievents\admin\manage_contacts.php
// ============================================

require_once 'header.php';

// Marquer un message comme lu
if (isset($_GET['mark_read'])) {
    $stmt = $pdo->prepare("UPDATE contacts SET is_read = 1 WHERE id = :id");
    $stmt->execute([':id' => $_GET['mark_read']]);
    header('Location: manage_contacts.php');
    exit();
}

// Récupérer tous les messages
try {
    $stmt = $pdo->query("SELECT * FROM contacts ORDER BY sent_at DESC");
    $messages = $stmt->fetchAll();
} catch(PDOException $e) {
    $error = $e->getMessage();
}
?>

<h1>📧 Messages de contact</h1>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Sujet</th>
                <th>Message</th>
                <th>Statut</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($messages as $msg): ?>
                <tr style="<?php echo !$msg['is_read'] ? 'background: rgba(100, 255, 218, 0.05);' : ''; ?>">
                    <td><?php echo $msg['id']; ?></td>
                    <td><?php echo htmlspecialchars($msg['name']); ?></td>
                    <td><?php echo htmlspecialchars($msg['email']); ?></td>
                    <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                    <td style="max-width: 300px;"><?php echo htmlspecialchars(substr($msg['message'], 0, 100)) . '...'; ?></td>
                    <td>
                        <?php if (!$msg['is_read']): ?>
                            <span class="status-badge status-en_attente">Non lu</span>
                        <?php else: ?>
                            <span class="status-badge status-approuve">Lu</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('d/m/Y H:i', strtotime($msg['sent_at'])); ?></td>
                    <td>
                        <a href="?mark_read=<?php echo $msg['id']; ?>" class="btn btn-primary btn-sm">📖 Marquer lu</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>