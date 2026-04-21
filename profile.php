<?php
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';


$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bio = trim($_POST['bio']);


    $profile_pic = $user['profile_pic'];
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['profile_pic']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            if (!is_dir('uploads/profiles')) {
                mkdir('uploads/profiles', 0777, true);
            }
            $new_name = 'user_' . $user_id . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], 'uploads/profiles/' . $new_name)) {
                $profile_pic = $new_name;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Invalid image format. Allowed: jpg, jpeg, png.";
        }
    }

    if (!$error) {
        $update = $pdo->prepare("UPDATE users SET bio = ?, profile_pic = ? WHERE id = ?");
        if ($update->execute([$bio, $profile_pic, $user_id])) {
            $success = "Profile updated successfully!";

            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
        } else {
            $error = "Failed to update profile.";
        }
    }
}
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
        <h2 style="color: var(--primary-color); font-family: var(--heading-font); margin: 0;">Account Settings</h2>
        <div class="badge badge-study" style="font-size: 0.8rem;"><?php echo htmlspecialchars($user['department']); ?> Student</div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 3rem; align-items: start;">
        <div style="text-align: center; background: var(--hover-bg); padding: 2rem; border-radius: var(--radius-lg); border: 1px dashed var(--border-color);">
            <div style="position: relative; display: inline-block;">
                <img src="<?php echo $user['profile_pic'] ? (strpos($user['profile_pic'], 'http') === 0 ? $user['profile_pic'] : 'uploads/profiles/'.$user['profile_pic']) : 'https://ui-avatars.com/api/?name='.urlencode($user['name']).'&size=150'; ?>" alt="Profile Picture" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid var(--card-bg); box-shadow: var(--shadow-md);">
                <div style="position: absolute; bottom: 5px; right: 5px; background: var(--primary-color); color: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid var(--card-bg);">
                    <i class="fa-solid fa-camera" style="font-size: 0.9rem;"></i>
                </div>
            </div>
            <h3 style="margin-top: 1.5rem; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($user['name']); ?></h3>
            <p style="color: var(--text-muted); font-weight: 500;"><?php echo htmlspecialchars($user['email']); ?></p>
        </div>

        <div>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" disabled style="background: var(--hover-bg); cursor: not-allowed;">
                </div>

                <div class="form-group">
                    <label class="form-label">Change Profile Picture</label>
                    <input type="file" name="profile_pic" class="form-control" accept=".jpg,.jpeg,.png">
                    <small style="color: var(--text-muted); font-size: 0.8rem; margin-top: 0.4rem; display: block;">JPG, PNG or JPEG. Max 2MB.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Professional Bio</label>
                    <textarea name="bio" class="form-control" rows="5" placeholder="Tell us about yourself, your interests, or what you're studying..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn" style="flex: 1;">Save Profile Changes</button>
                    <button type="reset" class="btn" style="background: var(--hover-bg); color: var(--text-main); flex: 0.5;">Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
