<?php
namespace App\Foundation;
use PDO;
use PDOException;

class Database{
  private PDO $pdo;
  public function __construct(array $config)
  {
    $port = isset($config['port']) ? ";port={$config['port']}" : "";
    $dsn = "mysql:host={$config['host']}{$port};dbname={$config['dbname']};charset={$config['charset']}";
    try {
      $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
      ];

      // Auto-detect TiDB Cloud Serverless and force SSL (required for Render)
      if (strpos($config['host'], 'tidbcloud.com') !== false) {
          $options[PDO::MYSQL_ATTR_SSL_CA] = '/etc/ssl/certs/ca-certificates.crt';
          $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
      }

      $this->pdo = new PDO($dsn, $config['username'], $config['password'], $options);

    } catch (PDOException $e) {
      die("Database connection failed" . $e->getMessage());
    }
  }
  public function getConnection():PDO{
    return $this->pdo;
  } 
}
