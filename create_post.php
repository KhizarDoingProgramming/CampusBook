<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'includes/db.php';

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = trim($_POST['content']);
    $category = $_POST['category'];
    $user_id = $_SESSION['user_id'];
    $image_name = null;

    if (empty($content)) {
        $error = "Post content cannot be empty.";
    } else {

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {

            if (!is_dir('uploads/posts')) {
                    mkdir('uploads/posts', 0777, true);
                }
                $image_name = uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/posts/' . $image_name);
            } else {
                $error = "Invalid image format. Allowed: jpg, jpeg, png, gif.";
            }
        }

        if (!$error) {
            $stmt = $pdo->prepare("INSERT INTO posts (user_id, content, image, category) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$user_id, $content, $image_name, $category])) {
                $success = true;
            } else {
                $error = "Failed to create post.";
            }
        }
    }
}


require_once 'includes/header.php';
?>

<?php if ($success): ?>
<div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); display: flex; align-items: center; justify-content: center; z-index: 9999;">
    <div style="background: var(--card-bg); padding: 2rem; border-radius: 12px; text-align: center; max-width: 300px;">
        <i class="fa-solid fa-circle-check" style="color: #45BD62; font-size: 3rem; margin-bottom: 1rem;"></i>
        <h3 style="color: var(--text-main); margin-bottom: 0.5rem;">Success!</h3>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Your post has been created.</p>
        <button onclick="window.location.href='index.php'" class="btn" style="width: 100%;">Go to Feed</button>
    </div>
</div>
<script>
    setTimeout(() => {
        window.location.href = 'index.php';
    }, 2000);
</script>
<?php else: ?>

<div class="card">
    <h2 style="margin-bottom: 1.5rem; color: var(--primary-color);">Create New Post</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label class="form-label">Category</label>
            <select name="category" class="form-control">
                <option value="General">General</option>
                <option value="Study Help">Study Help</option>
                <option value="Events">Events</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">What's on your mind?</label>
            <textarea name="content" class="form-control" required placeholder="Write something..."></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Attach Image (Optional)</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn">Post</button>
        <a href="index.php" class="btn btn-outline" style="margin-left: 0.5rem;">Cancel</a>
    </form>
</div>

<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
