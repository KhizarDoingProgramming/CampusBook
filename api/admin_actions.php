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

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';
$id = $data['id'] ?? 0;

if ($action === 'delete_user') {
    if ($id != $_SESSION['user_id']) {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Cannot delete yourself']);
    }
} elseif ($action === 'delete_post') {
    $pdo->prepare("DELETE FROM posts WHERE id = ?")->execute([$id]);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
