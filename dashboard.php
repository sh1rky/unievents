<?php
// ============================================
// fichier: admin/dashboard.php
// chemin: C:\xampp\htdocs\unievents\admin\dashboard.php
// ============================================

require_once 'header.php';

// Récupérer les statistiques
$error = '';
$totalEvents = 0;
$upcomingEvents = 0;
$totalUsers = 0;
$totalRegistrations = 0;
$unreadMessages = 0;
$pendingRequests = 0;
$recentEvents = [];
$recentRegistrations = [];
$recentMessages = [];
$recentOrganizers = [];

try {
    // Nombre d'événements
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM events");
    $totalEvents = $stmt->fetch()['count'] ?? 0;
    
    // Événements à venir
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM events WHERE date >= CURDATE()");
    $upcomingEvents = $stmt->fetch()['count'] ?? 0;
    
    // Nombre d'utilisateurs
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $totalUsers = $stmt->fetch()['count'] ?? 0;
    
    // Nombre d'inscriptions
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM registrations WHERE status = 'inscrit'");
    $totalRegistrations = $stmt->fetch()['count'] ?? 0;
    
    // Nombre de messages non lus
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM contacts WHERE is_read = 0");
    $unreadMessages = $stmt->fetch()['count'] ?? 0;
    
    // Demandes organisateur en attente
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM organizer_requests WHERE status = 'en_attente'");
    $pendingRequests = $stmt->fetch()['count'] ?? 0;
    
    // Derniers événements
    $stmt = $pdo->query("SELECT * FROM events ORDER BY id DESC LIMIT 5");
    $recentEvents = $stmt->fetchAll() ?: [];
    
    // Dernières inscriptions
    $stmt = $pdo->query("
        SELECT r.*, e.title as event_title, u.name as user_name 
        FROM registrations r
        JOIN events e ON r.event_id = e.id
        JOIN users u ON r.user_id = u.id
        ORDER BY r.id DESC
        LIMIT 5
    ");
    $recentRegistrations = $stmt->fetchAll() ?: [];
    
    // Derniers messages
    $stmt = $pdo->query("
        SELECT * FROM contacts
        ORDER BY id DESC
        LIMIT 5
    ");
    $recentMessages = $stmt->fetchAll() ?: [];
    
    // Organisateurs
    $stmt = $pdo->query("
        SELECT id, name, email, role FROM users
        WHERE role = 'organisateur'
        ORDER BY id DESC
        LIMIT 10
    ");
    $recentOrganizers = $stmt->fetchAll() ?: [];
    
} catch(PDOException $e) {
    $error = $e->getMessage();
}
?>

<h1>Tableau de bord</h1>
<p style="color: #8892b0; margin-bottom: 30px;">Bienvenue, <?php echo htmlspecialchars($_SESSION['admin_name']); ?> 👋</p>

<div class="stats-grid">
    <div class="stat-card">
        <h3><?php echo $totalEvents ?? 0; ?></h3>
        <p>📅 Événements totaux</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $upcomingEvents ?? 0; ?></h3>
        <p>⏰ Événements à venir</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $totalUsers ?? 0; ?></h3>
        <p>👥 Utilisateurs</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $totalRegistrations ?? 0; ?></h3>
        <p>📝 Inscriptions</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $unreadMessages ?? 0; ?></h3>
        <p>📧 Messages non lus</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $pendingRequests ?? 0; ?></h3>
        <p>🤝 Demandes organisateur</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 30px;">
    <!-- Derniers événements -->
    <div class="table-container">
        <div style="padding: 20px; border-bottom: 1px solid #233554;">
            <h3>📅 Derniers événements</h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Date</th>
                    <th>Capacité</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recentEvents)): ?>
                    <?php foreach ($recentEvents as $event): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($event['title']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($event['date'])); ?></td>
                            <td><?php echo $event['current_registrations']; ?>/<?php echo $event['capacity']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3">Aucun événement</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Dernières inscriptions -->
    <div class="table-container">
        <div style="padding: 20px; border-bottom: 1px solid #233554;">
            <h3>📝 Dernières inscriptions</h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Étudiant</th>
                    <th>Événement</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recentRegistrations)): ?>
                    <?php foreach ($recentRegistrations as $reg): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reg['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($reg['event_title']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($reg['registered_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3">Aucune inscription</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Derniers messages -->
    <div class="table-container">
        <div style="padding: 20px; border-bottom: 1px solid #233554;">
            <h3>📧 Derniers messages</h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>De</th>
                    <th>Sujet</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recentMessages)): ?>
                    <?php foreach ($recentMessages as $msg): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(substr($msg['name'], 0, 15)); ?></td>
                            <td><?php echo htmlspecialchars(substr($msg['subject'], 0, 20)); ?></td>
                            <td>Aujourd'hui</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3">Aucun message</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr; gap: 30px; margin-top: 30px;">
    <!-- Organisateurs -->
    <div class="table-container">
        <div style="padding: 20px; border-bottom: 1px solid #233554;">
            <h3>🤝 Organisateurs</h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recentOrganizers)): ?>
                    <?php foreach ($recentOrganizers as $org): ?>
                        <tr>
                            <td><?php echo $org['id']; ?></td>
                            <td><?php echo htmlspecialchars($org['name']); ?></td>
                            <td><?php echo htmlspecialchars($org['email']); ?></td>
                            <td><span style="color: #64ffda;">Organisateur</span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4">Aucun organisateur</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'footer.php'; ?>