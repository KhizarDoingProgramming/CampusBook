<?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['post_id'])) {
    echo json_encode(['success' => false, 'error' => 'Post ID missing']);
    exit();
}

$post_id = $data['post_id'];
$user_id = $_SESSION['user_id'];

try {

    $stmt = $pdo->prepare("SELECT * FROM saved_posts WHERE user_id = ? AND post_id = ?");
    $stmt->execute([$user_id, $post_id]);
    $saved = $stmt->fetch();

    if ($saved) {

        $stmt = $pdo->prepare("DELETE FROM saved_posts WHERE user_id = ? AND post_id = ?");
        $stmt->execute([$user_id, $post_id]);
        echo json_encode(['success' => true, 'saved' => false]);
    } else {

        $stmt = $pdo->prepare("INSERT INTO saved_posts (user_id, post_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $post_id]);
        echo json_encode(['success' => true, 'saved' => true]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
