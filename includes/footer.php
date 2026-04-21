<?php if (isset($_SESSION['user_id'])): ?>
        </main>
        
        <!-- Right Sidebar (Contacts) -->
        <aside class="sidebar right-sidebar">
            <h4 style="color: var(--text-muted); font-size: 1rem; margin-bottom: 1rem; display: flex; justify-content: space-between;">
                Contacts
                <div style="display: flex; gap: 0.75rem;">
                    <i class="fa-solid fa-video" style="cursor:pointer;"></i>
                    <i class="fa-solid fa-search" style="cursor:pointer;"></i>
                    <i class="fa-solid fa-ellipsis" style="cursor:pointer;"></i>
                </div>
            </h4>

            <div class="contacts-list">
                <?php
                // Fetch random users as contacts
                $stmt = $pdo->prepare("SELECT id, name, profile_pic FROM users WHERE id != ? ORDER BY RAND() LIMIT 8");
                $stmt->execute([$_SESSION['user_id']]);
                $contacts = $stmt->fetchAll();

                foreach ($contacts as $contact):
                    $avatar = $contact['profile_pic'] ? (strpos($contact['profile_pic'], 'http') === 0 ? $contact['profile_pic'] : 'uploads/profiles/'.$contact['profile_pic']) : 'https://ui-avatars.com/api/?name='.urlencode($contact['name']).'&background=random';
                ?>
                <div class="contact-item" onclick="openChat('<?php echo addslashes($contact['name']); ?>', '<?php echo $avatar; ?>')">
                    <div class="contact-avatar-wrapper">
                        <img src="<?php echo $avatar; ?>" class="avatar" style="width:36px; height:36px;">
                        <div class="online-dot"></div>
                    </div>
                    <span class="contact-name"><?php echo htmlspecialchars($contact['name']); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </aside>
    </div> <!-- End main-layout -->

    <!-- Mobile Bottom Navigation -->
    <nav class="bottom-nav">
        <a href="index.php" class="bottom-nav-item <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-house"></i>
            <span>Home</span>
        </a>
        <a href="events.php" class="bottom-nav-item <?php echo $current_page == 'events.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-calendar-alt"></i>
            <span>Events</span>
        </a>
        <a href="study_partner.php" class="bottom-nav-item <?php echo $current_page == 'study_partner.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-user-group"></i>
            <span>Partners</span>
        </a>
        <a href="groups.php" class="bottom-nav-item <?php echo $current_page == 'groups.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-users"></i>
            <span>Groups</span>
        </a>
        <a href="profile.php" class="bottom-nav-item <?php echo $current_page == 'profile.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-user"></i>
            <span>Profile</span>
        </a>
    </nav>
<?php endif; ?>

<script src="assets/js/main.js"></script>
</body>
</html>
