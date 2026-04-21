<?php
session_start();
require_once '../includes/db.php';

$stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id'] ?? 0]);
$user_email = $stmt->fetchColumn();

if ($user_email !== 'admin@campusbook.com') {
    header("Location: ../index.php");
    exit();
}

$success = '';

if (isset($_GET['delete_user'])) {
    $del_id = $_GET['delete_user'];
    if ($del_id != $_SESSION['user_id']) {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$del_id]);
        $success = "User deleted successfully.";
    }
}

if (isset($_GET['delete_post'])) {
    $del_id = $_GET['delete_post'];
    $pdo->prepare("DELETE FROM posts WHERE id = ?")->execute([$del_id]);
    $success = "Post deleted successfully.";
}

$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_posts = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
$posts = $pdo->query("SELECT p.*, u.name as author_name FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT 20")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | CampusBook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background: var(--bg-color); }
        .admin-layout { max-width: 1400px; margin: 2rem auto; padding: 0 1.5rem; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 3rem; }
        .stat-card { 
            background: var(--card-bg); padding: 2rem; border-radius: var(--radius-lg); 
            box-shadow: var(--shadow-sm); border: 1px solid var(--border-color); 
            text-align: left; display: flex; align-items: center; gap: 1.5rem;
        }
        .stat-icon { width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .stat-info h3 { font-size: 2.2rem; margin-bottom: 0.2rem; font-family: var(--heading-font); }
        .stat-info p { color: var(--text-muted); font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; }

        .table-container { 
            background: var(--card-bg); border-radius: var(--radius-lg); 
            box-shadow: var(--shadow-sm); border: 1px solid var(--border-color); 
            overflow: hidden; margin-bottom: 3rem; 
        }
        .table-header { padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
        .table-header h3 { margin: 0; font-family: var(--heading-font); }

        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 1.25rem 1.5rem; text-align: left; border-bottom: 1px solid var(--border-color); }
        th { background: var(--hover-bg); font-weight: 700; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(0,0,0,0.01); }
        
        .action-link { padding: 0.5rem 1rem; border-radius: var(--radius); font-size: 0.85rem; font-weight: 700; text-decoration: none !important; }
        .delete-btn { background: rgba(255, 77, 77, 0.1); color: var(--danger); border: none; padding: 0.5rem 1rem; border-radius: var(--radius); cursor: pointer; }
        .delete-btn:hover { background: var(--danger); color: white; }

        .success-msg { background: #dcfce7; color: #166534; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #bbf7d0; }
        
        @media (max-width: 768px) {
            th:nth-child(4), td:nth-child(4), th:nth-child(5), td:nth-child(5) { display: none; }
        }
    </style>
</head>
<body>

<nav class="navbar" style="background: var(--text-main); border-bottom: none;">
    <div class="nav-container">
        <a href="dashboard.php" class="nav-brand" style="color: white !important;">
            <div style="background: var(--primary-color); width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-shield-halved" style="font-size: 1.2rem;"></i>
            </div>
            <span>Admin Control</span>
        </a>
        <div class="nav-menu">
            <a href="../index.php" class="btn" style="background: rgba(255,255,255,0.1); color: white;">Exit Admin</a>
        </div>
    </div>
</nav>

<div class="admin-layout">
    <?php if ($success): ?>
        <div class="success-msg"><?php echo $success; ?></div>
    <?php endif; ?>

    <div style="margin-bottom: 2.5rem;">
        <h1 style="font-family: var(--heading-font); margin-bottom: 0.5rem;">System Overview</h1>
        <p style="color: var(--text-muted); font-weight: 500;">Manage users, posts, and monitor platform health.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--primary-soft); color: var(--primary-color);">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $total_users; ?></h3>
                <p>Total Members</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(69, 189, 98, 0.1); color: var(--secondary-color);">
                <i class="fa-solid fa-newspaper"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $total_posts; ?></h3>
                <p>Community Posts</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(247, 185, 40, 0.1); color: #F7B928;">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <div class="stat-info">
                <h3>Active</h3>
                <p>System Status</p>
            </div>
        </div>
    </div>

    <div class="table-container">
        <div class="table-header">
            <h3>Manage Users</h3>
            <span class="badge badge-study"><?php echo count($users); ?> Users</span>
        </div>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Email</th>
                        <th>Dept</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <?php 
                                    $u_avatar = $u['profile_pic'] ? (strpos($u['profile_pic'], 'http') === 0 ? $u['profile_pic'] : '../uploads/profiles/' . $u['profile_pic']) : 'https://ui-avatars.com/api/?name=' . urlencode($u['name']);
                                ?>
                                <img src="<?php echo $u_avatar; ?>" style="width: 32px; height: 32px; border-radius: 50%;" />
                                <span style="font-weight: 600;"><?php echo htmlspecialchars($u['name']); ?></span>
                            </div>
                        </td>
                        <td style="color: var(--text-muted);"><?php echo htmlspecialchars($u['email']); ?></td>
                        <td><span class="badge" style="background: var(--hover-bg); color: var(--text-main);"><?php echo htmlspecialchars($u['department']); ?></span></td>
                        <td style="color: var(--text-muted); font-size: 0.9rem;"><?php echo date('Y-m-d', strtotime($u['created_at'])); ?></td>
                        <td>
                            <?php if ($u['email'] !== 'admin@campusbook.com'): ?>
                                <a href="?delete_user=<?php echo $u['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
                            <?php else: ?>
                                <span class="badge badge-study">SYSTEM ADMIN</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="table-container">
        <div class="table-header">
            <h3>Recent Activity</h3>
            <span class="badge badge-study"><?php echo count($posts); ?> Posts</span>
        </div>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Author</th>
                        <th>Content Preview</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $p): ?>
                    <tr>
                        <td style="font-weight: 600;"><?php echo htmlspecialchars($p['author_name']); ?></td>
                        <td style="color: var(--text-muted); max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <?php echo htmlspecialchars(strip_tags($p['content'])); ?>
                        </td>
                        <td>
                            <span class="badge <?php echo $p['category'] === 'Study Help' ? 'badge-study' : ($p['category'] === 'Events' ? 'badge-event' : ''); ?>">
                                <?php echo htmlspecialchars($p['category']); ?>
                            </span>
                        </td>
                        <td style="color: var(--text-muted); font-size: 0.9rem;"><?php echo date('Y-m-d', strtotime($p['created_at'])); ?></td>
                        <td>
                            <a href="?delete_post=<?php echo $p['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Remove</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
