<?php
use App\Foundation\Migration;

class m0011_add_icon_to_categories_and_seed extends Migration
{
    public function up()
    {
        // Add icon column
        $sql = "ALTER TABLE categories ADD COLUMN icon VARCHAR(255) DEFAULT NULL";
        $this->db->exec($sql);

        // Seed new categories
        $categories = [
            ['name' => 'Technology', 'slug' => 'technology', 'icon' => '💻'],
            ['name' => 'AI', 'slug' => 'ai', 'icon' => '🤖'],
            ['name' => 'Programming', 'slug' => 'programming', 'icon' => '⌨️'],
            ['name' => 'Startups', 'slug' => 'startups', 'icon' => '🚀'],
            ['name' => 'Business', 'slug' => 'business', 'icon' => '💼'],
            ['name' => 'Finance', 'slug' => 'finance', 'icon' => '💰'],
            ['name' => 'Crypto', 'slug' => 'crypto', 'icon' => '🪙'],
            ['name' => 'Design', 'slug' => 'design', 'icon' => '🎨'],
            ['name' => 'Science', 'slug' => 'science', 'icon' => '🔬'],
            ['name' => 'Space', 'slug' => 'space', 'icon' => '🌌'],
            ['name' => 'Movies', 'slug' => 'movies', 'icon' => '🎬'],
            ['name' => 'Music', 'slug' => 'music', 'icon' => '🎵'],
            ['name' => 'Sports', 'slug' => 'sports', 'icon' => '🏅'],
            ['name' => 'Gaming', 'slug' => 'gaming', 'icon' => '🎮'],
            ['name' => 'Health', 'slug' => 'health', 'icon' => '🏥'],
            ['name' => 'Fitness', 'slug' => 'fitness', 'icon' => '💪'],
            ['name' => 'Travel', 'slug' => 'travel', 'icon' => '✈️'],
            ['name' => 'Food', 'slug' => 'food', 'icon' => '🍔'],
            ['name' => 'Fashion', 'slug' => 'fashion', 'icon' => '👗'],
            ['name' => 'Photography', 'slug' => 'photography', 'icon' => '📷'],
            ['name' => 'Books', 'slug' => 'books', 'icon' => '📚'],
            ['name' => 'Education', 'slug' => 'education', 'icon' => '🎓'],
            ['name' => 'Productivity', 'slug' => 'productivity', 'icon' => '⚡'],
            ['name' => 'Politics', 'slug' => 'politics', 'icon' => '🏛️'],
            ['name' => 'News', 'slug' => 'news', 'icon' => '📰']
        ];

        $stmt = $this->db->prepare("INSERT INTO categories (name, slug, icon) VALUES (:name, :slug, :icon) ON DUPLICATE KEY UPDATE icon = VALUES(icon)");
        
        foreach ($categories as $category) {
            $stmt->execute($category);
        }
    }

    public function down()
    {
        $sql = "ALTER TABLE categories DROP COLUMN icon";
        $this->db->exec($sql);
    }
}
