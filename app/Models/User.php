<?php
namespace App\Models;
use App\Foundation\Database;
use PDO;
class User{
  protected PDO $db;
  public function __construct(Database $database)
  {
    $this->db = $database->getConnection();
  }

  public static function validateEmail(string $email): bool {
    // Strict email validation using PHP's built-in filter
    // This ensures it works even if the HTML input is type="text"
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
  }

  public static function validatePassword(string $password): bool {
    // Enforce minimum 8 characters
    return strlen($password) >= 8;
  }
  public function create(string $name, string $username, string $email, string $password, string $phone = null) {
    $stmt = $this->db->prepare("INSERT INTO users(name, username, email, password, phone) VALUES(:name, :username, :email, :password, :phone)");
    $stmt->execute([
      'name'     => $name,
      'username' => $username,
      'email'    => $email,
      'password' => password_hash($password, PASSWORD_DEFAULT),
      'phone'    => $phone
    ]);
    return $this->db->lastInsertId();
  }

  public function findByEmail(string $email) {
    $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function findByUsername(string $username) {
    $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function verifyUser(int $userId) {
    $stmt = $this->db->prepare("UPDATE users SET is_verified = 1 WHERE id = :id");
    return $stmt->execute(['id' => $userId]);
  }

  public function updatePhone(int $userId, string $phone) {
    $stmt = $this->db->prepare("UPDATE users SET phone = :phone WHERE id = :id");
    return $stmt->execute(['phone' => $phone, 'id' => $userId]);
  }

}
