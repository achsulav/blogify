<?php
require_once __DIR__ . '/app/bootstrap.php';

use App\Foundation\Application;

$app = new Application();
$db = $app->db->getConnection();

$db->exec("UPDATE users SET phone = CONCAT('+97798', LPAD(FLOOR(RAND() * 99999999), 8, '0')) WHERE phone IS NULL");
echo "Updated missing phones successfully!\n";
