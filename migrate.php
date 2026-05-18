<?php
require_once __DIR__ . '/app/bootstrap.php';
use App\Foundation\Application;

$app = new Application();
$db = $app->db->getConnection();

// Ensure migrations table exists
$db->exec("CREATE TABLE IF NOT EXISTS migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$command = $argv[1] ?? null;
/*
|----------------------------------------------
|ROLLBACK
|----------------------------------------------
*/

if($command === 'rollback'){
  $stmt = $db->query("SELECT migration FROM migrations ORDER BY id DESC LIMIT 1");
  $lastMigration = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$lastMigration){
  echo "No migrations to rollback" . PHP_EOL;
  exit;
}
$migrationFile = $lastMigration['migration'];
require_once __DIR__ . "/migrations/$migrationFile";
$className = pathinfo($migrationFile, PATHINFO_FILENAME);

$instance = new $className();

echo "Rolling back $migrationFile..." . PHP_EOL;
$instance->down();
$stmt = $db->prepare("DELETE FROM migrations WHERE migration = :migration");
$stmt->execute([':migration'=>$migrationFile]);
echo "$migrationFile rolled back successfully" . PHP_EOL;
exit;
}
/*
|----------------------------------------------
|Apply Migrations
|----------------------------------------------
*/

$stmt = $db->query("SELECT migration FROM migrations");
$appliedMigrations = $stmt->fetchAll(PDO::FETCH_COLUMN);

$files = scandir(__DIR__ . '/migrations');
$files = array_diff($files,['.','..']);
$newMigrations = array_diff($files,$appliedMigrations);

foreach ($newMigrations as $migration) {
  require_once __DIR__ . "/migrations/$migration";
  $className = pathinfo($migration, PATHINFO_FILENAME);
  $instance = new $className();
  echo "Applying migration..." . PHP_EOL;
  $instance->up();
  $stmt = $db->prepare("INSERT INTO migrations (migration) VALUES (:migration)");
  $stmt->execute([':migration'=>$migration]);
  echo "$migration applied successfully" . PHP_EOL;
}
