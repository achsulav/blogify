<?php
use App\Foundation\Migration;
class m0008_create_otps_table extends Migration{
  public function up()
  {
    $sql = "CREATE TABLE otps (
      id INT AUTO_INCREMENT PRIMARY KEY,
      user_id INT NOT NULL,
      otp_code VARCHAR(10) NOT NULL,
      expires_at TIMESTAMP NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $this->db->exec($sql);
  }
  public function down()
  {
    $this->db->exec("DROP TABLE IF EXISTS otps");
  }
}
