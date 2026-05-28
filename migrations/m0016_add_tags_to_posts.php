<?php
use App\Foundation\Migration;

class m0016_add_tags_to_posts extends Migration {
    public function up() {
        // Add tags column for search indexing
        $this->db->exec("ALTER TABLE posts ADD COLUMN tags VARCHAR(255) DEFAULT NULL");
    }

    public function down() {
        $this->db->exec("ALTER TABLE posts DROP COLUMN tags");
    }
}
