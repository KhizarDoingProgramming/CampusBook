<?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

$query = 'SELECT p.id, p.content, p.image, p.category, p.created_at, u.name as author_name, u.profile_pic, (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count, (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count, (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = ?) as user_liked, (SELECT COUNT(*) FROM saved_posts WHERE post_id = p.id AND user_id = ?) as user_saved FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC';

$stmt = $pdo->prepare($query);
$stmt->execute([$user_id, $user_id]);
$posts = $stmt->fetchAll();

echo json_encode($posts);
