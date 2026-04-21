<?php
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


$query = "
    SELECT p.id, p.content, p.image, p.category, p.created_at, u.name as author_name, u.profile_pic,
           (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
           (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count,
           (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = ?) as user_liked
    FROM posts p
    JOIN users u ON p.user_id = u.id
    JOIN saved_posts sp ON sp.post_id = p.id
    WHERE sp.user_id = ?
    ORDER BY sp.created_at DESC
";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id, $user_id]);
$posts = $stmt->fetchAll();

// Get current user avatar for comments
$stmt_user = $pdo->prepare("SELECT profile_pic, name FROM users WHERE id = ?");
$stmt_user->execute([$user_id]);
$current_user = $stmt_user->fetch();
$current_user_avatar = $current_user['profile_pic'] ? (strpos($current_user['profile_pic'], 'http') === 0 ? $current_user['profile_pic'] : 'uploads/profiles/' . $current_user['profile_pic']) : 'https://ui-avatars.com/api/?name=' . urlencode($current_user['name']) . '&background=random';

?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h2 style="color: var(--primary-color);">Saved Posts</h2>
</div>

<div id="saved-feed-container">
    <?php if (count($posts) == 0): ?>
        <p style="text-align: center; padding: 2rem; color: var(--text-muted);">You haven't saved any posts yet.</p>
    <?php else: ?>
        <?php foreach ($posts as $post): 
            $avatarUrl = $post['profile_pic'] ? (strpos($post['profile_pic'], 'http') === 0 ? $post['profile_pic'] : 'uploads/profiles/' . $post['profile_pic']) : 'https://ui-avatars.com/api/?name=' . urlencode($post['author_name']) . '&background=random';
            $badgeClass = '';
            if ($post['category'] === 'Study Help') $badgeClass = 'badge-study';
            else if ($post['category'] === 'Events') $badgeClass = 'badge-event';
            $postDate = date('M j, Y, h:i A', strtotime($post['created_at']));
            $isLiked = $post['user_liked'] > 0;
            $likesCount = (int)$post['likes_count'];
            $commentsCount = (int)$post['comments_count'];
        ?>
            <div class="card" style="padding: 1rem 1rem 0.5rem 1rem;" id="post-card-<?php echo $post['id']; ?>">
                <div class="post-header">
                    <img src="<?php echo htmlspecialchars($avatarUrl); ?>" alt="Avatar" class="avatar">
                    <div class="post-meta">
                        <span class="post-author"><?php echo htmlspecialchars($post['author_name']); ?></span>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span class="post-time"><?php echo $postDate; ?></span>
                            <i class="fa-solid fa-earth-americas" style="font-size: 0.75rem; color: var(--text-muted);"></i>
                            <span class="badge <?php echo $badgeClass; ?>" style="transform: scale(0.85); transform-origin: left center;"><?php echo htmlspecialchars($post['category']); ?></span>
                        </div>
                    </div>
                    <div style="width: 36px; height: 36px; border-radius: 50%; display: flex; justify-content: center; align-items: center; cursor: pointer; color: var(--text-muted);" class="nav-circle-btn">
                        <i class="fa-solid fa-ellipsis"></i>
                    </div>
                </div>
                
                <div class="post-content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></div>
                
                <?php if ($post['image']): ?>
                    <?php $imgUrl = strpos($post['image'], 'http') === 0 ? $post['image'] : 'uploads/posts/' . $post['image']; ?>
                    <img src="<?php echo htmlspecialchars($imgUrl); ?>" class="post-image" alt="Post Image">
                <?php endif; ?>
                
                <div class="post-stats">
                    <div>
                        <span id="likes-container-<?php echo $post['id']; ?>" style="display: <?php echo $likesCount > 0 ? 'flex' : 'none'; ?>; align-items: center; gap: 0.25rem;">
                            <i class="fa-solid fa-thumbs-up" style="color: white; background: var(--primary-color); border-radius: 50%; padding: 0.3rem; font-size: 0.7rem;"></i>
                            <span id="likes-count-<?php echo $post['id']; ?>"><?php echo $likesCount; ?></span>
                        </span>
                    </div>
                    <div>
                        <span id="comments-count-<?php echo $post['id']; ?>" style="display: <?php echo $commentsCount > 0 ? 'inline' : 'none'; ?>;">
                            <?php echo $commentsCount . ($commentsCount == 1 ? ' comment' : ' comments'); ?>
                        </span>
                    </div>
                </div>

                <div class="post-actions">
                    <button class="action-btn <?php echo $isLiked ? 'active' : ''; ?>" id="like-btn-<?php echo $post['id']; ?>" onclick="toggleLike(<?php echo $post['id']; ?>, this)">
                        <i class="<?php echo $isLiked ? 'fa-solid' : 'fa-regular'; ?> fa-thumbs-up"></i> Like
                    </button>
                    <button class="action-btn" onclick="toggleComments(<?php echo $post['id']; ?>)">
                        <i class="fa-regular fa-comment"></i> Comment
                    </button>
                    <button class="action-btn" onclick="window.location.href='post_details.php?id=<?php echo $post['id']; ?>'">
                        <i class="fa-solid fa-share"></i> Share
                    </button>
                    <button class="action-btn active" id="save-btn-<?php echo $post['id']; ?>" onclick="unsavePost(<?php echo $post['id']; ?>, this)">
                        <i class="fa-solid fa-bookmark"></i> Unsave
                    </button>
                </div>

                <div class="comments-section" id="comments-section-<?php echo $post['id']; ?>" style="display: none;">
                    <div class="comment-input-area">
                        <img src="<?php echo htmlspecialchars($current_user_avatar); ?>" class="avatar" style="width: 32px; height: 32px;" alt="Your Avatar">
                        <form onsubmit="submitComment(event, <?php echo $post['id']; ?>)" style="flex: 1; position: relative;">
                            <input type="text" id="comment-input-<?php echo $post['id']; ?>" class="form-control" style="border-radius: 20px; padding-right: 2.5rem;" placeholder="Write a comment..." required>
                            <button type="submit" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--primary-color); cursor: pointer;">
                                <i class="fa-solid fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
    function toggleLike(postId, btnEl) {
        fetch('api/like_post.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ post_id: postId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const icon = btnEl.querySelector('i');
                const likesCountSpan = document.getElementById(`likes-count-${postId}`);
                const likesContainer = document.getElementById(`likes-container-${postId}`);

                if (data.liked) {
                    btnEl.classList.add('active');
                    icon.classList.remove('fa-regular');
                    icon.classList.add('fa-solid');
                } else {
                    btnEl.classList.remove('active');
                    icon.classList.remove('fa-solid');
                    icon.classList.add('fa-regular');
                }
                
                if (likesCountSpan) {
                    likesCountSpan.textContent = data.likes_count;
                }
                if (likesContainer) {
                    if (data.likes_count > 0) {
                        likesContainer.style.display = 'flex';
                    } else {
                        likesContainer.style.display = 'none';
                    }
                }
            }
        });
    }

    function toggleComments(postId) {
        const section = document.getElementById(`comments-section-${postId}`);
        if (section.style.display === 'none') {
            section.style.display = 'block';
        } else {
            section.style.display = 'none';
        }
    }

    function submitComment(e, postId) {
        e.preventDefault();
        const input = document.getElementById(`comment-input-${postId}`);
        const text = input.value.trim();
        if (!text) return;

        fetch('api/add_comment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ post_id: postId, comment: text })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                input.value = '';
                const countSpan = document.getElementById(`comments-count-${postId}`);
                if (countSpan) {
                    let countMatch = countSpan.textContent.match(/\d+/);
                    let currentCount = countMatch ? parseInt(countMatch[0]) : 0;
                    currentCount++;
                    countSpan.textContent = currentCount + (currentCount === 1 ? ' comment' : ' comments');
                    countSpan.style.display = 'inline';
                }
            }
        });
    }

    function unsavePost(postId, btnEl) {
        fetch('api/save_post.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ post_id: postId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && !data.saved) {
                // Remove the post card visually
                const card = document.getElementById(`post-card-${postId}`);
                if(card) {
                    card.style.display = 'none';
                }
            }
        });
    }
</script>

<?php require_once 'includes/footer.php'; ?>
