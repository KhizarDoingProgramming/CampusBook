<?php
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_content'])) {
    $content = trim($_POST['request_content']);
    if (!empty($content)) {
        $stmt = $pdo->prepare("INSERT INTO study_requests (user_id, content) VALUES (?, ?)");
        if ($stmt->execute([$_SESSION['user_id'], $content])) {
            $success = "Study request posted successfully!";
        } else {
            $error = "Failed to post request.";
        }
    } else {
        $error = "Request content cannot be empty.";
    }
}

// Fetch Study Requests
$stmt = $pdo->query("
    SELECT s.*, u.name as author_name, u.profile_pic, u.department 
    FROM study_requests s 
    JOIN users u ON s.user_id = u.id 
    ORDER BY s.created_at DESC
");
$requests = $stmt->fetchAll();
?>

<div class="card" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; border: none;">
    <h2 style="margin-bottom: 0.5rem;">Find a Study Partner</h2>
    <p style="opacity: 0.9;">Struggling with a topic? Post a request here and connect with classmates!</p>
</div>

<div class="card">
    <h3 style="margin-bottom: 1rem;">Post a Study Request</h3>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <textarea name="request_content" class="form-control" rows="3" placeholder="E.g., I need help understanding Data Structures before the midterm next week..." required></textarea>
        </div>
        <button type="submit" class="btn btn-secondary"><i class="fa-solid fa-paper-plane"></i> Post Request</button>
    </form>
</div>

<div class="requests-feed">
    <?php if (count($requests) > 0): ?>
        <?php foreach ($requests as $req): ?>
            <div class="card">
                <div class="post-header">
                    <img src="<?php echo $req['profile_pic'] ? (strpos($req['profile_pic'], 'http') === 0 ? $req['profile_pic'] : 'uploads/profiles/'.$req['profile_pic']) : 'https://ui-avatars.com/api/?name='.urlencode($req['author_name']); ?>" alt="Avatar" class="avatar">
                    <div class="post-meta">
                        <span class="post-author"><?php echo htmlspecialchars($req['author_name']); ?></span>
                        <span class="post-time"><?php echo htmlspecialchars($req['department']); ?> • <?php echo date('M j, Y', strtotime($req['created_at'])); ?></span>
                    </div>
                    <span class="badge badge-study">Study Request</span>
                </div>
                
                <div class="post-content">
                    <?php echo nl2br(htmlspecialchars($req['content'])); ?>
                </div>

                <div class="post-actions" style="margin-top: 1rem;">

                <a href="mailto:?subject=Study Partner Request&body=Hi <?php echo urlencode($req['author_name']); ?>, I saw your study request on CampusBook..." class="btn btn-outline">
                        <i class="fa-regular fa-envelope"></i> Message
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="card">
            <p style="color: var(--text-muted); text-align: center;">No study requests at the moment.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
