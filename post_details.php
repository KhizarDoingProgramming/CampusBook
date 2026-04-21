<?php
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$post_id = $_GET['id'] ?? null;
if (!$post_id) {
    echo "<div class='alert alert-error'>Post not found.</div>";
    require_once 'includes/footer.php';
    exit();
}


$stmt = $pdo->prepare("
    SELECT p.*, u.name as author_name, u.profile_pic 
    FROM posts p 
    JOIN users u ON p.user_id = u.id 
    WHERE p.id = ?
");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    echo "<div class='alert alert-error'>Post not found.</div>";
    require_once 'includes/footer.php';
    exit();
}


$stmt = $pdo->prepare("
    SELECT c.*, u.name as author_name, u.profile_pic 
    FROM comments c 
    JOIN users u ON c.user_id = u.id 
    WHERE c.post_id = ? 
    ORDER BY c.created_at ASC
");
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll();


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment_text'])) {
    $comment_text = trim($_POST['comment_text']);
    if (!empty($comment_text)) {
        $user_id = $_SESSION['user_id'];
        $insert = $pdo->prepare("INSERT INTO comments (user_id, post_id, comment) VALUES (?, ?, ?)");
        $insert->execute([$user_id, $post_id, $comment_text]);

        header("Location: post_details.php?id=$post_id");
        exit();
    }
}
?>

<div class="card">
    <div class="post-header">
        <img src="<?php echo $post['profile_pic'] ? (strpos($post['profile_pic'], 'http') === 0 ? $post['profile_pic'] : 'uploads/profiles/'.$post['profile_pic']) : 'https://ui-avatars.com/api/?name='.urlencode($post['author_name']); ?>" alt="Avatar" class="avatar">
        <div class="post-meta">
            <span class="post-author"><?php echo htmlspecialchars($post['author_name']); ?></span>
            <span class="post-time"><?php echo date('M j, Y g:i A', strtotime($post['created_at'])); ?></span>
        </div>
        <span class="badge <?php echo $post['category'] == 'Study Help' ? 'badge-study' : ($post['category'] == 'Events' ? 'badge-event' : ''); ?>">
            <?php echo htmlspecialchars($post['category']); ?>
        </span>
    </div>
    
    <div class="post-content" style="font-size: 1.1rem; margin-bottom: 1.5rem;">
        <?php echo nl2br(htmlspecialchars($post['content'])); ?>
    </div>
    
    <?php if ($post['image']): ?>
        <img src="uploads/posts/<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image" class="post-image">
    <?php endif; ?>
</div>

<div class="card">
    <h3>Comments (<?php echo count($comments); ?>)</h3>
    <hr style="margin: 1rem 0; border: none; border-top: 1px solid var(--border-color);">
    
    <div class="comments-list">
        <?php if (count($comments) > 0): ?>
            <?php foreach ($comments as $c): ?>
                <div class="comment-item">
                    <img src="uploads/profiles/<?php echo htmlspecialchars($c['profile_pic'] ?: 'default.png'); ?>" alt="Avatar" class="avatar" style="width: 32px; height: 32px;" onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($c['author_name']); ?>'">
                    <div class="comment-content">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                            <span class="comment-author"><?php echo htmlspecialchars($c['author_name']); ?></span>
                            <span style="font-size: 0.75rem; color: var(--text-muted);"><?php echo date('M j', strtotime($c['created_at'])); ?></span>
                        </div>
                        <div class="comment-text">
                            <?php echo nl2br(htmlspecialchars($c['comment'])); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: var(--text-muted);">No comments yet.</p>
        <?php endif; ?>
    </div>

    <div class="comments-section" style="margin-top: 2rem;">
        <form method="POST" action="">
            <div class="form-group">
                <textarea name="comment_text" class="form-control" rows="2" placeholder="Write a comment..." required></textarea>
            </div>
            <button type="submit" class="btn btn-secondary">Post Comment</button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
