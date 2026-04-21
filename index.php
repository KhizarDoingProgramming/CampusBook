<?php
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch current user details
$stmt = $pdo->prepare("SELECT profile_pic, name FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$current_user = $stmt->fetch();
$current_user_avatar = $current_user['profile_pic'] ? (strpos($current_user['profile_pic'], 'http') === 0 ? $current_user['profile_pic'] : 'uploads/profiles/' . $current_user['profile_pic']) : 'https://ui-avatars.com/api/?name=' . urlencode($current_user['name']) . '&background=random';

// Fetch stories
$stories_query = "
    SELECT s.id, s.image_url, s.duration, s.created_at, u.name as author_name, u.profile_pic
    FROM stories s
    JOIN users u ON s.user_id = u.id
    WHERE s.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
    ORDER BY s.created_at DESC
";
$stories = $pdo->query($stories_query)->fetchAll();

// Fetch posts
$posts_query = '
    SELECT p.id, p.content, p.image, p.category, p.created_at, u.name as author_name, u.profile_pic, 
    (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count, 
    (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count, 
    (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = ?) as user_liked, 
    (SELECT COUNT(*) FROM saved_posts WHERE post_id = p.id AND user_id = ?) as user_saved 
    FROM posts p 
    JOIN users u ON p.user_id = u.id 
    ORDER BY p.created_at DESC
';
$stmt = $pdo->prepare($posts_query);
$stmt->execute([$user_id, $user_id]);
$posts = $stmt->fetchAll();
?>

<div class="feed-wrapper">
    <!-- Stories Section -->
    <div class="stories-container">
        <!-- Create Story -->
        <div class="story-card" style="background-color: var(--card-bg); border: 1px solid var(--border-color); display: flex; flexDirection: column">
            <div style="flex: 1; overflow: hidden; position: relative">
                <img src="<?php echo $current_user_avatar; ?>" style="width: 100%; height: 100%; object-fit: cover" alt="Your Story" />
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.1)"></div>
            </div>
            <div style="padding: 1rem 0.5rem 0.5rem; text-align: center; position: relative">
                <div style="background: var(--primary-color); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 4px solid var(--card-bg); color: white; cursor: pointer; position: absolute; top: -18px; left: 50%; transform: translateX(-50%)">
                    <i class="fa-solid fa-plus"></i>
                </div>
                <div style="fontSize: 0.8rem; fontWeight: 700; color: var(--text-main)">Create Story</div>
            </div>
        </div>

        <?php foreach ($stories as $story): 
            $avatar = $story['profile_pic'] ? (strpos($story['profile_pic'], 'http') === 0 ? $story['profile_pic'] : 'uploads/profiles/' . $story['profile_pic']) : 'https://ui-avatars.com/api/?name=' . urlencode($story['author_name']) . '&background=random';
        ?>
            <div class="story-card" onclick="openStory('<?php echo $story['image_url']; ?>', '<?php echo $story['author_name']; ?>', '<?php echo $avatar; ?>')">
                <img src="<?php echo $story['image_url']; ?>" class="story-bg" style="width: 100%; height: 100%; object-fit: cover" alt="Story" />
                <div class="story-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to top, rgba(0,0,0,0.6) 0%, transparent 40%)"></div>
                <img src="<?php echo $avatar; ?>" class="story-avatar" style="position: absolute; top: 0.75rem; left: 0.75rem; width: 40px; height: 40px; borderRadius: 50%; border: 3px solid var(--primary-color)" alt="Avatar" />
                <div class="story-name" style="position: absolute; bottom: 0.75rem; left: 0.75rem; color: white; fontWeight: 700; fontSize: 0.85rem; textShadow: 0 1px 3px rgba(0,0,0,0.8)"><?php echo htmlspecialchars($story['author_name']); ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Composer Section -->
    <div class="card composer-section">
        <div class="composer-box" style="display: flex; gap: 1rem; align-items: center; margin-bottom: 1rem;">
            <img src="<?php echo $current_user_avatar; ?>" class="avatar" alt="Avatar" />
            <div class="composer-input" onclick="window.location.href='create_post.php'" style="flex: 1; background: var(--hover-bg); padding: 0.75rem 1.25rem; border-radius: 25px; color: var(--text-muted); cursor: pointer; font-weight: 500;">
                What's on your mind, <?php echo explode(' ', $_SESSION['user_name'])[0]; ?>?
            </div>
        </div>
        <div class="composer-actions" style="display: flex; border-top: 1px solid var(--border-color); padding-top: 0.5rem;">
            <div class="comp-action-btn" onclick="window.location.href='create_post.php'" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.5rem; border-radius: 8px; cursor: pointer; color: var(--text-muted); font-weight: 600;">
                <i class="fa-solid fa-video" style="color: #F3425F"></i> <span>Live Video</span>
            </div>
            <div class="comp-action-btn" onclick="window.location.href='create_post.php'" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.5rem; border-radius: 8px; cursor: pointer; color: var(--text-muted); font-weight: 600;">
                <i class="fa-solid fa-images" style="color: #45BD62"></i> <span>Photo/Video</span>
            </div>
            <div class="comp-action-btn" onclick="window.location.href='create_post.php'" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.5rem; border-radius: 8px; cursor: pointer; color: var(--text-muted); font-weight: 600;">
                <i class="fa-regular fa-face-smile" style="color: #F7B928"></i> <span>Feeling</span>
            </div>
        </div>
    </div>

    <!-- Feed Section -->
    <div id="posts-container">
        <?php foreach ($posts as $post): 
            $author_avatar = $post['profile_pic'] ? (strpos($post['profile_pic'], 'http') === 0 ? $post['profile_pic'] : 'uploads/profiles/' . $post['profile_pic']) : 'https://ui-avatars.com/api/?name=' . urlencode($post['author_name']) . '&background=random';
            $post_date = date('M j, g:i a', strtotime($post['created_at']));
        ?>
            <div class="card" style="padding: 1.25rem 1.25rem 0.5rem 1.25rem">
                <div class="post-header">
                    <img src="<?php echo $author_avatar; ?>" alt="Avatar" class="avatar" />
                    <div class="post-meta" style="flex: 1">
                        <span class="post-author"><?php echo htmlspecialchars($post['author_name']); ?></span>
                        <div style="display: flex; align-items: center; gap: 0.5rem">
                            <span class="post-time"><?php echo $post_date; ?></span>
                            <i class="fa-solid fa-earth-americas" style="font-size: 0.75rem; color: var(--text-muted)"></i>
                            <span class="badge <?php echo $post['category'] === 'Study Help' ? 'badge-study' : ($post['category'] === 'Events' ? 'badge-event' : ''); ?>">
                                <?php echo htmlspecialchars($post['category']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="nav-circle-btn">
                        <i class="fa-solid fa-ellipsis"></i>
                    </div>
                </div>
                
                <div class="post-content"><?php echo $post['content']; ?></div>
                
                <?php if ($post['image']): ?>
                    <img src="<?php echo (strpos($post['image'], 'http') === 0 ? $post['image'] : 'uploads/posts/' . $post['image']); ?>" class="post-image" alt="Post" />
                <?php endif; ?>

                <div class="post-stats" style="display: flex; justify-content: space-between; color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.75rem;">
                    <div>
                        <?php if ($post['likes_count'] > 0): ?>
                            <span style="display: flex; align-items: center; gap: 0.4rem">
                                <i class="fa-solid fa-thumbs-up" style="color: white; background: var(--primary-color); borderRadius: 50%; padding: 0.35rem; fontSize: 0.7rem"></i>
                                <span><?php echo $post['likes_count']; ?></span>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php if ($post['comments_count'] > 0): ?>
                            <span><?php echo $post['comments_count']; ?> comments</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="post-actions">
                    <button class="action-btn <?php echo $post['user_liked'] ? 'active' : ''; ?>" onclick="handleLike(this, <?php echo $post['id']; ?>)">
                        <i class="<?php echo $post['user_liked'] ? 'fa-solid' : 'fa-regular'; ?> fa-thumbs-up"></i> Like
                    </button>
                    <button class="action-btn" onclick="toggleComments(this)">
                        <i class="fa-regular fa-comment"></i> Comment
                    </button>
                    <button class="action-btn" onclick="window.location.href='post_details.php?id=<?php echo $post['id']; ?>'">
                        <i class="fa-solid fa-share"></i> Share
                    </button>
                    <button class="action-btn <?php echo $post['user_saved'] ? 'active' : ''; ?>" onclick="handleSave(this, <?php echo $post['id']; ?>)">
                        <i class="<?php echo $post['user_saved'] ? 'fa-solid' : 'fa-regular'; ?> fa-bookmark"></i> Save
                    </button>
                </div>

                <!-- Simple Vanilla JS Comment Area (Hidden by default) -->
                <div class="comments-section" style="display: none; padding-top: 1rem;">
                    <div class="comment-input-area" style="display: flex; gap: 0.75rem; align-items: center;">
                        <img src="<?php echo $current_user_avatar; ?>" class="avatar" style="width: 32px; height: 32px;" alt="Your Avatar" />
                        <form onsubmit="handleComment(event, this, <?php echo $post['id']; ?>)" style="flex: 1; position: relative;">
                            <input type="text" class="form-control" style="border-radius: 20px; padding: 0.6rem 2.5rem 0.6rem 1rem" placeholder="Write a comment..." required />
                            <button type="submit" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--primary-color); cursor: pointer;">
                                <i class="fa-solid fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Story Viewer Modal -->
<div id="story-viewer" class="story-modal" style="display: none;" onclick="if(event.target == this) closeStory()">
    <div class="story-close" onclick="closeStory()">&times;</div>
    <div class="story-modal-content">
        <div class="story-progress-container">
            <div class="story-progress-bar">
                <div id="story-progress-fill" class="story-progress-fill" style="width: 0%"></div>
            </div>
        </div>
        <div class="story-header">
            <img id="story-author-avatar" src="" style="width: 32px; height: 32px; border-radius: 50%" alt="Author" />
            <span id="story-author-name" style="color: white; fontWeight: 600; textShadow: 0 1px 2px rgba(0,0,0,0.5)"></span>
        </div>
        <img id="story-img" src="" class="story-modal-img" alt="Story content" />
    </div>
</div>

<script>
let storyTimer;
function openStory(img, name, avatar) {
    const modal = document.getElementById('story-viewer');
    document.getElementById('story-img').src = img;
    document.getElementById('story-author-name').innerText = name;
    document.getElementById('story-author-avatar').src = avatar;
    modal.style.display = 'flex';
    
    let progress = 0;
    const fill = document.getElementById('story-progress-fill');
    clearInterval(storyTimer);
    storyTimer = setInterval(() => {
        progress += 1;
        fill.style.width = progress + '%';
        if (progress >= 100) {
            closeStory();
        }
    }, 50);
}

function closeStory() {
    document.getElementById('story-viewer').style.display = 'none';
    clearInterval(storyTimer);
}

function toggleComments(btn) {
    const card = btn.closest('.card');
    const section = card.querySelector('.comments-section');
    section.style.display = section.style.display === 'none' ? 'block' : 'none';
}

async function handleLike(btn, postId) {
    const isLiked = btn.classList.contains('active');
    btn.classList.toggle('active');
    const icon = btn.querySelector('i');
    icon.className = isLiked ? 'fa-regular fa-thumbs-up' : 'fa-solid fa-thumbs-up';
    
    try {
        const res = await fetch('api/like_post.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ post_id: postId })
        });
        const data = await res.json();
        if (!data.success) {
            btn.classList.toggle('active');
            icon.className = !isLiked ? 'fa-regular fa-thumbs-up' : 'fa-solid fa-thumbs-up';
        } else {
            // Optionally update like count UI here
            location.reload(); // Simple way to refresh counts
        }
    } catch (e) {
        btn.classList.toggle('active');
        icon.className = !isLiked ? 'fa-regular fa-thumbs-up' : 'fa-solid fa-thumbs-up';
    }
}

async function handleSave(btn, postId) {
    btn.classList.toggle('active');
    const icon = btn.querySelector('i');
    icon.className = btn.classList.contains('active') ? 'fa-solid fa-bookmark' : 'fa-regular fa-bookmark';
    
    try {
        await fetch('api/save_post.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ post_id: postId })
        });
    } catch (e) {}
}

async function handleComment(e, form, postId) {
    e.preventDefault();
    const input = form.querySelector('input');
    const text = input.value;
    if (!text.trim()) return;

    try {
        const res = await fetch('api/add_comment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ post_id: postId, comment: text })
        });
        const data = await res.json();
        if (data.success) {
            input.value = '';
            location.reload();
        }
    } catch (e) {}
}
</script>

<?php require_once 'includes/footer.php'; ?>
