<?php
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


$stmt = $pdo->query("SELECT * FROM events ORDER BY event_date ASC");
$events = $stmt->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h2 style="color: var(--primary-color);">Campus Events</h2>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
    <?php if (count($events) > 0): ?>
        <?php foreach ($events as $event): ?>
            <div class="card" style="margin-bottom: 0;">
                <div class="badge badge-event" style="margin-bottom: 1rem;">
                    <?php echo date('F j, Y', strtotime($event['event_date'])); ?>
                </div>
                <h3 style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($event['title']); ?></h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
                    <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                </p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="card" style="grid-column: 1 / -1;">
            <p style="color: var(--text-muted); text-align: center;">No upcoming events found.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
