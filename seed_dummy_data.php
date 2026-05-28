<?php
require_once __DIR__ . '/app/bootstrap.php';

use App\Foundation\Application;

$app = new Application();
$db = $app->db->getConnection();

echo "Starting Realistic Database Seeder...\n";

// Helper functions
function randomElement($array) { return $array[array_rand($array)]; }

// WIPE ALL EXISTING CONTENT FOR A CLEAN SLATE
$db->exec("SET FOREIGN_KEY_CHECKS = 0;");
$db->exec("TRUNCATE TABLE post_views");
$db->exec("TRUNCATE TABLE post_likes");
$db->exec("TRUNCATE TABLE bookmarks");
$db->exec("TRUNCATE TABLE posts");
$db->exec("TRUNCATE TABLE user_interests");
$db->exec("DELETE FROM users WHERE email LIKE 'dummy_%@example.com'");
$db->exec("SET FOREIGN_KEY_CHECKS = 1;");

$roles = ['Primary Audience', 'Writer', 'Researcher', 'Developer', 'Designer', 'Founder', 'Student', 'Reader', 'Marketer'];

// Structure: Category => [Topics/Titles, Tags]
$contentStructure = [
    'AI' => [
        'titles' => ['The Rise of Autonomous AI Agents', 'Understanding Retrieval Augmented Generation', 'How LLMs are changing writing', 'Open Source LLM Tools', 'Neural Networks in 2026'],
        'tags' => ['LLMs', 'AI Agents', 'Neural Networks', 'Machine Learning']
    ],
    'Programming' => [
        'titles' => ['React Server Components Explained', 'Scaling Node.js APIs', 'TypeScript Performance Tips', 'Building Microservices in Go'],
        'tags' => ['React', 'Node.js', 'TypeScript', 'Go', 'Performance']
    ],
    'Fashion' => [
        'titles' => ['2026 Streetwear Trends', 'Minimalist Capsule Wardrobe Guide', 'Luxury Fashion Brands Growing Fast', 'Sustainable Fashion Innovations'],
        'tags' => ['Streetwear', 'Luxury fashion', 'Seasonal trends', 'Sustainability']
    ],
    'Finance' => [
        'titles' => ['Best ETFs for Long-Term Investors', 'Understanding Compound Interest', 'How Startups Raise Seed Funding', 'Navigating the 2026 Economy'],
        'tags' => ['Investing', 'ETFs', 'Economy', 'Personal Finance']
    ],
    'Startups' => [
        'titles' => ['Bootstrapping vs VC Funding', 'Finding Product-Market Fit', 'SaaS Growth Strategies', 'Founder Mental Health'],
        'tags' => ['Founders', 'SaaS', 'Venture Capital', 'Growth']
    ],
    'Design' => [
        'titles' => ['UI/UX Trends in 2026', 'Mastering Figma Components', 'The Psychology of Color in Design', 'Creating Glassmorphism UI'],
        'tags' => ['UI/UX', 'Figma', 'Web Design', 'Aesthetics']
    ],
    'Productivity' => [
        'titles' => ['Deep Work Routines', 'Building a Second Brain in Notion', 'Time Blocking Methods', 'Beating Procrastination'],
        'tags' => ['Deep Work', 'Notion', 'Time Management', 'Habits']
    ],
    'Marketing' => [
        'titles' => ['SEO Best Practices 2026', 'Building a Personal Brand', 'Email Marketing Strategies', 'Viral Social Media Campaigns'],
        'tags' => ['SEO', 'Branding', 'Social Media', 'Growth']
    ],
    'Health' => [
        'titles' => ['Optimizing Sleep for Performance', 'Science-Based Nutrition', 'Mental Health for Remote Workers', 'Benefits of Zone 2 Cardio'],
        'tags' => ['Sleep', 'Nutrition', 'Mental Health', 'Fitness']
    ],
    'Science' => [
        'titles' => ['Advances in Quantum Computing', 'Space Exploration in 2026', 'CRISPR Gene Editing Breakthroughs', 'Understanding Dark Matter'],
        'tags' => ['Physics', 'Space', 'Biology', 'Quantum']
    ],
    'Fitness' => [
        'titles' => ['Best Home Workout Routines', 'Building Muscle After 30', 'Cardio vs Weightlifting', 'Mobility Exercises for Programmers'],
        'tags' => ['Workout', 'Muscle', 'Cardio', 'Mobility']
    ],
    'Lifestyle' => [
        'titles' => ['Minimalism in Modern Life', 'Digital Nomad Destinations', 'Finding Work-Life Balance', 'Morning Routines of Successful People'],
        'tags' => ['Minimalism', 'Nomad', 'Balance', 'Routines']
    ],
    'Photography' => [
        'titles' => ['Street Photography Tips', 'Best Mirrorless Cameras 2026', 'Mastering Lightroom Editing', 'Portrait Lighting Techniques'],
        'tags' => ['Cameras', 'Editing', 'Portraits', 'Lighting']
    ]
];

// Seed Categories
echo "Seeding Categories...\n";
$categoryIds = [];
foreach (array_keys($contentStructure) as $catName) {
    $slug = strtolower(str_replace(' ', '-', $catName));
    $db->prepare("INSERT IGNORE INTO categories (name, slug, icon) VALUES (?, ?, '📌')")->execute([$catName, $slug]);
    $stmt = $db->prepare("SELECT id FROM categories WHERE name = ?");
    $stmt->execute([$catName]);
    $categoryIds[$catName] = $stmt->fetchColumn();
}

// Seed Users (30)
echo "Seeding Users...\n";
$userIds = [];
for ($i = 1; $i <= 30; $i++) {
    $name = "Dummy User " . $i;
    $username = "dummy_user_" . $i;
    $email = $username . "@example.com";
    $password = password_hash('password123', PASSWORD_BCRYPT);
    $role = randomElement($roles);
    $phone = '+97798' . str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);
    
    // Set onboarding_completed to 0 so you can manually select interests!
    $stmt = $db->prepare("INSERT INTO users (name, username, email, password, role, onboarding_completed, phone) VALUES (?, ?, ?, ?, ?, 0, ?)");
    $stmt->execute([$name, $username, $email, $password, $role, $phone]);
    $userIds[] = $db->lastInsertId();
}

// Seed Posts (250+)
echo "Seeding Posts (250+)...\n";
$postIds = [];
for ($i = 1; $i <= 250; $i++) {
    $catName = randomElement(array_keys($contentStructure));
    $catId = $categoryIds[$catName];
    
    $titleBase = randomElement($contentStructure[$catName]['titles']);
    $title = $titleBase . " - Case Study " . rand(100, 999);
    $slug = strtolower(str_replace(' ', '-', $title));
    
    // Pick 2 random tags for this category
    $availTags = $contentStructure[$catName]['tags'];
    shuffle($availTags);
    $tags = implode(',', array_slice($availTags, 0, 2));

    $content = "<p>This is a realistic, deeply researched post about {$titleBase}. Relevant to {$catName} enthusiasts.</p>";
    $authorId = randomElement($userIds);
    
    // Simulate real views (Some highly viewed, some not)
    $views = rand(10, 500) + (rand(0,10) > 8 ? rand(5000, 20000) : 0);
    $createdAt = date('Y-m-d H:i:s', strtotime('-' . rand(0, 100) . ' days'));
    
    $stmt = $db->prepare("INSERT INTO posts (user_id, category_id, title, slug, content_html, views_count, tags, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$authorId, $catId, $title, $slug, $content, $views, $tags, $createdAt]);
    $postIds[] = $db->lastInsertId();
}

// Seed Engagement
echo "Seeding Likes and Views...\n";
for ($i = 0; $i < 1000; $i++) {
    $uid = randomElement($userIds);
    $pid = randomElement($postIds);
    $db->prepare("INSERT IGNORE INTO post_likes (user_id, post_id) VALUES (?, ?)")->execute([$uid, $pid]);
}
for ($i = 0; $i < 300; $i++) {
    $uid = randomElement($userIds);
    $pid = randomElement($postIds);
    $db->prepare("INSERT IGNORE INTO bookmarks (user_id, post_id) VALUES (?, ?)")->execute([$uid, $pid]);
}

echo "Database Seeding Completed Successfully!\n";
