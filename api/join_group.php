<?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['group_id'])) {
    echo json_encode(['success' => false, 'error' => 'Group ID missing']);
    exit();
}

$group_id = $data['group_id'];
$user_id = $_SESSION['user_id'];

try {

$stmt = $pdo->prepare("SELECT * FROM group_members WHERE user_id = ? AND group_id = ?");
    $stmt->execute([$user_id, $group_id]);
    $member = $stmt->fetch();

    if ($member) {
        
        $stmt = $pdo->prepare("DELETE FROM group_members WHERE user_id = ? AND group_id = ?");
        $stmt->execute([$user_id, $group_id]);
        echo json_encode(['success' => true, 'joined' => false]);
    } else {
        
        $stmt = $pdo->prepare("INSERT INTO group_members (user_id, group_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $group_id]);
        echo json_encode(['success' => true, 'joined' => true]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
