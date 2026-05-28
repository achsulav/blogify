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

  public static function validateName(string $name): bool {
    $name = trim($name);
    // Must be at least 3 characters and contain only letters and spaces
    if (strlen($name) < 3) {
        return false;
    }
    return (bool) preg_match('/^[a-zA-Z\s]+$/', $name);
  }

  public static function validatePhone(string $phone): bool {
    // Compulsory +977 prefix followed by NTC (984, 985, 986, 974, 975, 976) or Ncell (980, 981, 982) numbers
    return (bool) preg_match('/^\+977(98[0-2]|98[4-6]|97[4-6])\d{7}$/', $phone);
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

  public function isOnboarded(int $userId): bool {
      $stmt = $this->db->prepare("SELECT onboarding_completed FROM users WHERE id = :id");
      $stmt->execute(['id' => $userId]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result ? (bool)$result['onboarding_completed'] : false;
  }

  public function markOnboarded(int $userId) {
      $stmt = $this->db->prepare("UPDATE users SET onboarding_completed = 1 WHERE id = :id");
      return $stmt->execute(['id' => $userId]);
  }

  public function saveInterests(int $userId, array $categoryIds) {
      // Begin transaction
      $this->db->beginTransaction();
      try {
          // Clear existing
          $stmt = $this->db->prepare("DELETE FROM user_interests WHERE user_id = :user_id");
          $stmt->execute(['user_id' => $userId]);

          // Insert new
          if (!empty($categoryIds)) {
              $stmt = $this->db->prepare("INSERT INTO user_interests (user_id, category_id) VALUES (:user_id, :category_id)");
              foreach ($categoryIds as $categoryId) {
                  $stmt->execute([
                      'user_id' => $userId,
                      'category_id' => $categoryId
                  ]);
              }
          }
          $this->db->commit();
          return true;
      } catch (\Exception $e) {
          $this->db->rollBack();
          return false;
      }
  }

  public function getInterests(int $userId): array {
      $stmt = $this->db->prepare("
          SELECT c.* FROM categories c
          JOIN user_interests ui ON c.id = ui.category_id
          WHERE ui.user_id = :user_id
      ");
      $stmt->execute(['user_id' => $userId]);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

}
