<?php
use App\Foundation\Migration;

class m0009_add_onboarding_to_users extends Migration
{
    public function up()
    {
        $sql = "ALTER TABLE users ADD COLUMN onboarding_completed TINYINT(1) DEFAULT 0";
        $this->db->exec($sql);
    }

    public function down()
    {
        $sql = "ALTER TABLE users DROP COLUMN onboarding_completed";
        $this->db->exec($sql);
    }
}
