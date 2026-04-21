<?php

session_start();
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$post_id = $data['post_id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$post_id) {
    echo json_encode(['success' => false, 'error' => 'Invalid post ID']);
    exit();
}


$stmt = $pdo->prepare("SELECT id FROM likes WHERE user_id = ? AND post_id = ?");
$stmt->execute([$user_id, $post_id]);
$already_liked = $stmt->rowCount() > 0;

if ($already_liked) {

$stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
    $stmt->execute([$user_id, $post_id]);
    $liked = false;
} else {

$stmt = $pdo->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $post_id]);
    $liked = true;


    $stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post_owner = $stmt->fetchColumn();

    if ($post_owner && $post_owner != $user_id) {
        $message = $_SESSION['user_name'] . " liked your post.";
        $notify = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $notify->execute([$post_owner, $message]);
    }
}


$stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
$stmt->execute([$post_id]);
$likes_count = $stmt->fetchColumn();

echo json_encode([
    'success' => true,
    'liked' => $liked,
    'likes_count' => $likes_count
]);
