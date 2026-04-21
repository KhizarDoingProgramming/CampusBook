<?php

session_start();
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$query = "
    SELECT s.id, s.image_url, s.duration, s.created_at, u.name as author_name, u.profile_pic
    FROM stories s
    JOIN users u ON s.user_id = u.id
    WHERE s.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
    ORDER BY s.created_at DESC
";

$stmt = $pdo->query($query);
$stories = $stmt->fetchAll();

echo json_encode($stories);
