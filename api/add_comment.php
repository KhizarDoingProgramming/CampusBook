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
$comment = trim($data['comment'] ?? '');
$user_id = $_SESSION['user_id'];

if (!$post_id || empty($comment)) {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
    exit();
}

$stmt = $pdo->prepare("INSERT INTO comments (user_id, post_id, comment) VALUES (?, ?, ?)");
if ($stmt->execute([$user_id, $post_id, $comment])) {

    // Notify post owner
    $stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post_owner = $stmt->fetchColumn();

    if ($post_owner && $post_owner != $user_id) {
        $message = $_SESSION['user_name'] . " commented on your post: '" . substr($comment, 0, 20) . "...'";
        $notify = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $notify->execute([$post_owner, $message]);
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to add comment']);
}
