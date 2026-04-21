<?php
require_once 'header.php';

// حذف منظم
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id AND role = 'organisateur'");
        $stmt->execute([':id' => $id]);
        $success = "Organisateur supprimé avec succès";
    } catch(PDOException $e) {
        $error = "Erreur lors de la suppression";
    }
}

// Modifier le rôle (Admin/Organisateur)
if (isset($_GET['role']) && isset($_GET['id'])) {
    $role = $_GET['role'];
    $id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE id = :id");
        $stmt->execute([':role' => $role, ':id' => $id]);
        $success = "Rôle mis à jour avec succès";
    } catch(PDOException $e) {
        $error = "Erreur lors de la mise à jour";
    }
}

// Ajouter un organisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'] ?? 'password';
    $role = $_POST['role'] ?? 'organisateur';
    $phone = $_POST['phone'] ?? '';
   
    try {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $error = "Cet email existe déjà";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, phone) VALUES (:name, :email, :password, :role, :phone)");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $password,
                ':role' => $role,
                ':phone' => $phone
            ]);
            $success = "Organisateur ajouté avec succès";
        }
    } catch(PDOException $e) {
        $error = "Erreur: " . $e->getMessage();
    }
}

// Récupérer tous les organisateurs et admins
try {
    $stmt = $pdo->query("
        SELECT * FROM users
        WHERE role IN ('organisateur', 'admin')
        ORDER BY role DESC, created_at DESC
    ");
    $organizers = $stmt->fetchAll();
} catch(PDOException $e) {
    $error = $e->getMessage();
}

// Compter les statistiques
$totalAdmins = 0;
$totalOrganisateurs = 0;
foreach ($organizers as $org) {
    if ($org['role'] == 'admin') $totalAdmins++;
    else $totalOrganisateurs++;
}
?>

<style>
    .btn {
        padding: 6px 12px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 12px;
        display: inline-block;
        margin: 2px;
        border: none;
        cursor: pointer;
    }
    .btn-danger { background: #ff4646; color: white; }
    .btn-warning { background: #fbbf24; color: #0a192f; }
    .btn-success { background: #4ade80; color: #0a192f; }
    .btn-info { background: #64ffda; color: #0a192f; }
    .btn-primary { background: #64ffda; color: #0a192f; }
    .role-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
    }
    .role-admin { background: #64ffda; color: #0a192f; }
    .role-organisateur { background: #4ade80; color: #0a192f; }
    .alert-success {
        background: rgba(74, 222, 128, 0.1);
        border: 1px solid #4ade80;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        color: #4ade80;
    }
    .alert-error {
        background: rgba(255, 70, 70, 0.1);
        border: 1px solid #ff4646;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        color: #ff4646;
    }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .stat-card {
        background: #112240;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        border: 1px solid #233554;
    }
    .stat-card h3 { font-size: 32px; color: #64ffda; margin-bottom: 10px; }
    .stat-card p { color: #8892b0; font-size: 14px; }
    .add-form {
        background: #112240;
        padding: 25px;
        border-radius: 8px;
        margin-bottom: 30px;
        border: 1px solid #233554;
    }
    .add-form h3 { margin-bottom: 20px; color: #64ffda; }
    .add-form .form-row {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        align-items: flex-end;
    }
    .add-form .form-group {
        flex: 1;
        min-width: 150px;
    }
    .add-form label {
        display: block;
        margin-bottom: 8px;
        color: #8892b0;
        font-size: 13px;
    }
    .add-form input, .add-form select {
        width: 100%;
        padding: 10px 12px;
        background: #0a192f;
        border: 1px solid #233554;
        color: #ccd6f6;
        border-radius: 4px;
        font-size: 14px;
    }
    .add-form input:focus, .add-form select:focus {
        outline: none;
        border-color: #64ffda;
    }
    .table-container {
        background: #112240;
        border-radius: 8px;
        border: 1px solid #233554;
        overflow-x: auto;
    }
    .table-container h3 {
        padding: 20px;
        border-bottom: 1px solid #233554;
        margin: 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #233554;
    }
    th {
        background: #0a192f;
        color: #64ffda;
        font-weight: 600;
    }
    tr:hover {
        background: rgba(100, 255, 218, 0.05);
    }
    @media (max-width: 768px) {
        th, td { padding: 8px 10px; font-size: 12px; }
        .add-form .form-row { flex-direction: column; }
        .add-form .form-group { width: 100%; }
    }
</style>

<h1>🤝 Gestion des organisateurs</h1>

<?php if (isset($success)): ?>
    <div class="alert-success">✅ <?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert-error">❌ <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<!-- Statistiques -->
<div class="stats-grid">
    <div class="stat-card">
        <h3><?php echo count($organizers); ?></h3>
        <p>Total organisateurs & admins</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $totalAdmins; ?></h3>
        <p>👑 Administrateurs</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $totalOrganisateurs; ?></h3>
        <p>🤝 Organisateurs</p>
    </div>
</div>

<!-- Formulaire pour ajouter un organisateur -->
<div class="add-form">
    <h3>➕ Ajouter un organisateur</h3>
    <form method="POST" action="">
        <input type="hidden" name="action" value="add">
        <div class="form-row">
            <div class="form-group">
                <label>Nom complet *</label>
                <input type="text" name="name" required placeholder="Ex: Ahmed Ben Ali">
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" required placeholder="exemple@univ.tn">
            </div>
            <div class="form-group">
                <label>Téléphone</label>
                <input type="text" name="phone" placeholder="+216 XX XXX XXX">
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="text" name="password" value="password">
            </div>
            <div class="form-group">
                <label>Rôle</label>
                <select name="role">
                    <option value="organisateur">🤝 Organisateur</option>
                    <option value="admin">👑 Administrateur</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success" style="width: 100%;">➕ Ajouter</button>
            </div>
        </div>
    </form>
</div>

<!-- Liste des organisateurs -->
<div class="table-container">
    <h3>📋 Liste des organisateurs et administrateurs</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Rôle</th>
                <th>Date d'inscription</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($organizers) > 0): ?>
                <?php foreach ($organizers as $org): ?>
                    <tr>
                        <td><?php echo $org['id']; ?></td>
                        <td><?php echo htmlspecialchars($org['name']); ?></td>
                        <td><?php echo htmlspecialchars($org['email']); ?></td>
                        <td><?php echo htmlspecialchars($org['phone'] ?? '-'); ?></td>
                        <td>
                            <span class="role-badge role-<?php echo $org['role']; ?>">
                                <?php echo $org['role'] == 'admin' ? '👑 Admin' : '🤝 Organisateur'; ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($org['created_at'])); ?></td>
                        <td>
                            <?php if ($org['role'] == 'organisateur'): ?>
                                <a href="?role=admin&id=<?php echo $org['id']; ?>" class="btn btn-info" onclick="return confirm('Promouvoir cet utilisateur en administrateur ?')">👑 Promouvoir</a>
                                <a href="?delete=<?php echo $org['id']; ?>" class="btn btn-danger" onclick="return confirm('Supprimer cet organisateur ?')">🗑️ Supprimer</a>
                            <?php else: ?>
                                <a href="?role=organisateur&id=<?php echo $org['id']; ?>" class="btn btn-warning" onclick="return confirm('Rétrograder cet administrateur en organisateur ?')">⬇️ Rétrograder</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center;">Aucun organisateur trouvé</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>
