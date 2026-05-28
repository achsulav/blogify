<?php
use App\Foundation\Migration;

class m0014_add_role_to_users extends Migration {
    public function up() {
        // Add role column with default 'Primary Audience'
        $this->db->exec("ALTER TABLE users ADD COLUMN role VARCHAR(50) DEFAULT 'Primary Audience'");
    }

    public function down() {
        $this->db->exec("ALTER TABLE users DROP COLUMN role");
    }
}
