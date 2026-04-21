<?php
// ============================================
// fichier: admin/manage_users.php
// chemin: C:\xampp\htdocs\unievents\admin\manage_users.php
// ============================================

require_once 'header.php';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
                $stmt->execute([':id' => $_POST['user_id']]);
                $success = "Utilisateur supprimé";
                break;
            case 'update_role':
                $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE id = :id");
                $stmt->execute([
                    ':role' => $_POST['role'],
                    ':id' => $_POST['user_id']
                ]);
                $success = "Rôle mis à jour";
                break;
        }
    }
}

// Récupérer tous les utilisateurs
try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} catch(PDOException $e) {
    $error = $e->getMessage();
}
?>

<h1>👥 Gestion des utilisateurs</h1>

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
                <th>Téléphone</th>
                <th>Rôle</th>
                <th>Niveau</th>
                <th>Date d'inscription</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="update_role">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <select name="role" onchange="this.form.submit()" style="background: #0a192f; border: 1px solid #233554; color: #ccd6f6; padding: 5px;">
                                <option value="etudiant" <?php echo $user['role'] == 'etudiant' ? 'selected' : ''; ?>>Étudiant</option>
                                <option value="organisateur" <?php echo $user['role'] == 'organisateur' ? 'selected' : ''; ?>>Organisateur</option>
                                <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </form>
                    </td>
                    <td><?php echo htmlspecialchars($user['niveau'] ?? '-'); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <?php if ($user['id'] != $_SESSION['admin_id']): ?>
                            <form method="POST" style="display: inline-block;" onsubmit="return confirm('Confirmer la suppression ?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
                            </form>
                        <?php else: ?>
                            <span class="btn-sm" style="opacity: 0.5;">👑</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>