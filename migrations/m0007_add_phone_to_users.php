<?php
use App\Foundation\Migration;
class m0007_add_phone_to_users extends Migration{
  public function up()
  {
    $sql = "ALTER TABLE users ADD COLUMN phone VARCHAR(20) DEFAULT NULL, ADD COLUMN is_verified TINYINT DEFAULT 0";
    $this->db->exec($sql);
  }
  public function down()
  {
    $this->db->exec("ALTER TABLE users DROP COLUMN phone, DROP COLUMN is_verified");
  }
}
