<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusBook | Connect & Learn</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">


    <script>
        // Set theme immediately to avoid flash
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
</head>
<body>

<?php if (isset($_SESSION['user_id'])): ?>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="nav-brand">
                <div style="background: var(--primary-color); color: white; width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-sm);">
                    <i class="fa-solid fa-graduation-cap" style="font-size: 1.6rem;"></i>
                </div>
                <span>CampusBook</span>
            </a>

            <div class="nav-center">
                <a href="index.php" class="nav-icon <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" title="Home">
                    <i class="fa-solid fa-house"></i>
                </a>
                <a href="events.php" class="nav-icon <?php echo $current_page == 'events.php' ? 'active' : ''; ?>" title="Events">
                    <i class="fa-solid fa-calendar-alt"></i>
                </a>
                <a href="study_partner.php" class="nav-icon <?php echo $current_page == 'study_partner.php' ? 'active' : ''; ?>" title="Study Partners">
                    <i class="fa-solid fa-user-group"></i>
                </a>
                <a href="groups.php" class="nav-icon <?php echo $current_page == 'groups.php' ? 'active' : ''; ?>" title="Groups">
                    <i class="fa-solid fa-users"></i>
                </a>
            </div>

            <div class="nav-menu">
                <a href="#" id="theme-toggle" class="nav-circle-btn" title="Toggle Dark Mode">
                    <i class="fa-solid fa-moon"></i>
                </a>
                <a href="profile.php" class="nav-circle-btn" title="Profile">
                    <i class="fa-solid fa-user"></i>
                </a>
                <a href="notifications.php" class="nav-circle-btn" title="Notifications">
                    <i class="fa-solid fa-bell"></i>
                </a>
                <a href="logout.php" class="nav-circle-btn" title="Logout" style="background: var(--primary-soft); color: var(--primary-color);">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </a>
            </div>
        </div>
    </nav>

    <div class="main-layout">
        <!-- Left Sidebar -->
        <aside class="sidebar left-sidebar">
            <ul class="sidebar-menu">
                <li>
                    <a href="profile.php" class="<?php echo $current_page == 'profile.php' ? 'active' : ''; ?>">
                        <div class="sidebar-icon">
                            <i class="fa-solid fa-user" style="color: var(--primary-color);"></i>
                        </div>
                        <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                    </a>
                </li>
                <li>
                    <a href="study_partner.php" class="<?php echo $current_page == 'study_partner.php' ? 'active' : ''; ?>">
                        <div class="sidebar-icon">
                            <i class="fa-solid fa-book-open" style="color: #45BD62;"></i>
                        </div>
                        Study Partners
                    </a>
                </li>
                <li>
                    <a href="events.php" class="<?php echo $current_page == 'events.php' ? 'active' : ''; ?>">
                        <div class="sidebar-icon">
                            <i class="fa-solid fa-calendar-day" style="color: #F7B928;"></i>
                        </div>
                        Campus Events
                    </a>
                </li>
                <li>
                    <a href="saved_posts.php" class="<?php echo $current_page == 'saved_posts.php' ? 'active' : ''; ?>">
                        <div class="sidebar-icon">
                            <i class="fa-solid fa-bookmark" style="color: #9360F7;"></i>
                        </div>
                        Saved Posts
                    </a>
                </li>
                <li>
                    <a href="groups.php" class="<?php echo $current_page == 'groups.php' ? 'active' : ''; ?>">
                        <div class="sidebar-icon">
                            <i class="fa-solid fa-users" style="color: #2ABCE0;"></i>
                        </div>
                        Groups
                    </a>
                </li>
            </ul>
        </aside>

        <main class="content-area">
<?php else: ?>
<?php endif; ?>
