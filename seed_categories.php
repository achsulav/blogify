<?php
require_once __DIR__ . '/app/bootstrap.php';
use App\Foundation\Application;

$app = new Application();
$db = $app->db->getConnection();

$categories = [
    ['name' => 'Technology', 'slug' => 'technology'],
    ['name' => 'Lifestyle', 'slug' => 'lifestyle'],
    ['name' => 'Education', 'slug' => 'education'],
    ['name' => 'Health', 'slug' => 'health'],
    ['name' => 'Travel', 'slug' => 'travel'],
];

echo "Seeding categories..." . PHP_EOL;

foreach ($categories as $category) {
    $stmt = $db->prepare("INSERT INTO categories (name, slug) VALUES (:name, :slug)");
    try {
        $stmt->execute($category);
        echo "Inserted: {$category['name']}" . PHP_EOL;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicate entry
            echo "Skipped (exists): {$category['name']}" . PHP_EOL;
        } else {
            echo "Error: " . $e->getMessage() . PHP_EOL;
        }
    }
}

echo "Seeding completed." . PHP_EOL;
