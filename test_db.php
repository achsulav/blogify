<?php
require_once __DIR__ . '/app/bootstrap.php';
$db = (new \App\Foundation\Application())->db->getConnection();
$stmt = $db->query("SELECT c.name, COUNT(p.id) FROM posts p JOIN categories c ON p.category_id = c.id GROUP BY c.name");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

$stmt = $db->query("SELECT user_id, COUNT(category_id) FROM user_interests GROUP BY user_id");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
