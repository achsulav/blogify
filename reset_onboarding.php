<?php
require_once __DIR__ . '/app/bootstrap.php';
use App\Foundation\Application;

$db = (new Application())->db->getConnection();

// Reset all dummy users to require onboarding
$db->exec("UPDATE users SET onboarding_completed = 0 WHERE email LIKE 'dummy_user_%'");

// Delete existing auto-generated user interests for dummy users so the user can select them manually
$db->exec("DELETE ui FROM user_interests ui JOIN users u ON ui.user_id = u.id WHERE u.email LIKE 'dummy_user_%'");

echo "Successfully reset onboarding for all dummy users!\n";
