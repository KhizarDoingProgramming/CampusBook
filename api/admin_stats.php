<?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json');

// Admin Check
$stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id'] ?? 0]);
$user_email = $stmt->fetchColumn();

if ($user_email !== 'admin@campusbook.com') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_posts = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();

$users = $pdo->query("SELECT id, name, email, department, profile_pic, created_at FROM users ORDER BY created_at DESC")->fetchAll();
$posts = $pdo->query("SELECT p.id, p.content, p.category, p.created_at, u.name as author_name FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT 50")->fetchAll();

echo json_encode([
    'stats' => [
        'total_users' => $total_users,
        'total_posts' => $total_posts,
    ],
    'users' => $users,
    'posts' => $posts
]);
