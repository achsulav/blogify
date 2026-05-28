<?php
use App\Foundation\Migration;

class m0015_create_post_views_table extends Migration {
    public function up() {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS post_views (
                id INT AUTO_INCREMENT PRIMARY KEY,
                post_id INT NOT NULL,
                user_id INT DEFAULT NULL, -- Nullable for anonymous views
                ip_address VARCHAR(45) DEFAULT NULL,
                viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        
        // Add caching columns to posts for performance
        $this->db->exec("ALTER TABLE posts ADD COLUMN views_count INT DEFAULT 0");
    }

    public function down() {
        $this->db->exec("ALTER TABLE posts DROP COLUMN views_count");
        $this->db->exec("DROP TABLE IF EXISTS post_views");
    }
}
