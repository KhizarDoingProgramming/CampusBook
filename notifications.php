<?php
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


$pdo->prepare("UPDATE notifications SET is_read = TRUE WHERE user_id = ?")->execute([$user_id]);


$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll();
?>

<div class="card">
    <h2 style="margin-bottom: 1.5rem; color: var(--primary-color);">Notifications</h2>

    <div class="notifications-list">
        <?php if (count($notifications) > 0): ?>
            <?php foreach ($notifications as $notif): ?>
                <div style="padding: 1rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: flex-start; gap: 1rem; <?php echo !$notif['is_read'] ? 'background-color: #F8FAFC;' : ''; ?>">
                    <div style="background-color: var(--primary-color); color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; justify-content: center; align-items: center; flex-shrink: 0;">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <div style="flex: 1;">
                        <p style="margin-bottom: 0.25rem;"><?php echo htmlspecialchars($notif['message']); ?></p>
                        <span style="font-size: 0.8rem; color: var(--text-muted);">
                            <?php echo date('M j, Y g:i A', strtotime($notif['created_at'])); ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: var(--text-muted); text-align: center; padding: 2rem 0;">No notifications yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
