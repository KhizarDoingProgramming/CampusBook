<?php
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


$query = "
    SELECT g.id, g.name, g.description, g.cover_image,
           (SELECT COUNT(*) FROM group_members WHERE group_id = g.id) as members_count,
           (SELECT COUNT(*) FROM group_members WHERE group_id = g.id AND user_id = ?) as is_member
    FROM groups g
    ORDER BY g.created_at DESC
";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$groups = $stmt->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h2 style="color: var(--primary-color);">Campus Groups</h2>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
    <?php if (count($groups) == 0): ?>
        <div class="card" style="grid-column: 1 / -1;">
            <p style="color: var(--text-muted); text-align: center;">No groups found.</p>
        </div>
    <?php else: ?>
        <?php foreach ($groups as $group): ?>
            <div class="card" style="margin-bottom: 0; padding-bottom: 1rem;">
                <?php if ($group['cover_image']): ?>
                    <img src="<?php echo htmlspecialchars($group['cover_image']); ?>" style="width: calc(100% + 2rem); margin: -1rem -1rem 1rem -1rem; height: 120px; object-fit: cover; border-top-left-radius: 12px; border-top-right-radius: 12px;" alt="Group Cover">
                <?php else: ?>
                    <div style="width: calc(100% + 2rem); margin: -1rem -1rem 1rem -1rem; height: 120px; background: var(--primary-color); border-top-left-radius: 12px; border-top-right-radius: 12px;"></div>
                <?php endif; ?>
                
                <h3 style="margin-bottom: 0.5rem; color: var(--text-main);"><?php echo htmlspecialchars($group['name']); ?></h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.5; margin-bottom: 1rem; height: 40px; overflow: hidden;">
                    <?php echo htmlspecialchars($group['description']); ?>
                </p>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.85rem; color: var(--text-muted);">
                        <i class="fa-solid fa-users"></i> <span id="members-count-<?php echo $group['id']; ?>"><?php echo $group['members_count']; ?></span> members
                    </span>
                    <button class="btn <?php echo $group['is_member'] ? 'btn-outline' : ''; ?>" id="join-btn-<?php echo $group['id']; ?>" onclick="toggleJoin(<?php echo $group['id']; ?>, this)" style="padding: 0.4rem 1rem; font-size: 0.9rem;">
                        <?php echo $group['is_member'] ? 'Leave' : 'Join'; ?>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
    function toggleJoin(groupId, btnEl) {
        fetch('api/join_group.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ group_id: groupId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const countSpan = document.getElementById(`members-count-${groupId}`);
                let count = parseInt(countSpan.textContent);
                
                if (data.joined) {
                    btnEl.classList.add('btn-outline');
                    btnEl.textContent = 'Leave';
                    countSpan.textContent = count + 1;
                } else {
                    btnEl.classList.remove('btn-outline');
                    btnEl.textContent = 'Join';
                    countSpan.textContent = count - 1;
                }
            }
        });
    }
</script>

<?php require_once 'includes/footer.php'; ?>
