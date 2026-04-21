<?php

require_once 'includes/db.php';
$user_id = 1;
$queries = [
    "INSERT INTO groups (name, description, cover_image) VALUES ('Computer Science Society', 'For all CS majors to discuss code, projects, and career advice.', 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=800&q=80')",
    "INSERT INTO groups (name, description, cover_image) VALUES ('Photography Club', 'Share your best shots and organize weekend photo walks.', 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&q=80')"
];

try {
    foreach ($queries as $q) {
        $pdo->exec($q);
    }
    echo "Success: Groups inserted.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}


try {
    foreach ($queries as $q) {
        $pdo->exec($q);
    }
    echo "Success: New tables created.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
