<?php
namespace App\Http\Controllers;

use App\Foundation\Application;
use PDO;

class FeedController extends BaseController
{
    public function getForYouFeed()
    {
        header('Content-Type: application/json');
        
        $userId = Application::$app->session->get('user');
        if (!$userId) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $db = Application::$app->db->getConnection();

        // 1. Fetch user interests directly first (safer than subqueries for PDO bindings)
        $stmt = $db->prepare("SELECT category_id FROM user_interests WHERE user_id = :id");
        $stmt->execute(['id' => $userId]);
        $interestIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $categoryNamesStmt = $db->prepare("SELECT c.name FROM categories c JOIN user_interests ui ON c.id = ui.category_id WHERE ui.user_id = :id");
        $categoryNamesStmt->execute(['id' => $userId]);
        $interestNames = $categoryNamesStmt->fetchAll(PDO::FETCH_COLUMN);

        // Build IN clause string safely
        $inClause = !empty($interestIds) ? implode(',', array_map('intval', $interestIds)) : '0';

        // Recommendation Engine Formula: 
        // FINAL_SCORE = (category_match*0.7 + engagement*0.15 + recency*0.1 + trending*0.05)
        $sql = "
          SELECT p.*, u.name as author_name, u.username as author_username, c.name as category_name,
          (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id) as likes_count,
          EXISTS(SELECT 1 FROM post_likes WHERE post_id = p.id AND user_id = :user_id) as is_liked,
          EXISTS(SELECT 1 FROM bookmarks WHERE post_id = p.id AND user_id = :user_id) as is_bookmarked,
          IF(p.category_id IN ($inClause), 1, 0) as category_match,
          (
              IF(p.category_id IN ($inClause), 1, 0) * 0.7 +
              LEAST((p.views_count + ((SELECT COUNT(*) FROM post_likes WHERE post_id = p.id)*5))/1000, 1) * 0.15 +
              EXP(-DATEDIFF(NOW(), p.created_at)/30) * 0.10 +
              (LEAST(p.views_count/1000, 1) * EXP(-DATEDIFF(NOW(), p.created_at)/7)) * 0.05
          ) as final_score
          FROM posts p
          JOIN users u ON p.user_id = u.id
          LEFT JOIN categories c ON p.category_id = c.id
          ORDER BY final_score DESC
          LIMIT 40
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format for frontend
        $formattedPosts = [];
        $matchedCount = 0;

        foreach ($posts as $post) {
            if ($post['category_match'] == 1) {
                $matchedCount++;
            }
            $authorUsername = htmlspecialchars($post['author_username'] ?? '');
            $postUrl = is_production() ? "/$authorUsername/{$post['slug']}" : "http://$authorUsername.blogify.dev/{$post['slug']}";
            
            $formattedPosts[] = [
                'id' => $post['id'],
                'title' => htmlspecialchars($post['title']),
                'excerpt' => htmlspecialchars(substr(strip_tags($post['content_html'] ?? ''), 0, 120)) . '...',
                'url' => $postUrl,
                'category_name' => htmlspecialchars($post['category_name'] ?? 'General'),
                'tags' => array_filter(array_map('trim', explode(',', $post['tags'] ?? ''))),
                'likes_count' => $post['likes_count'],
                'is_liked' => (bool)$post['is_liked'],
                'is_bookmarked' => (bool)$post['is_bookmarked'],
                'category_match' => (bool)$post['category_match'],
                'final_score' => $post['final_score']
            ];
        }

        // Phase 6: Add Debugging Block
        $debug = [
            'current_user_id' => $userId,
            'selected_interests_ids' => $interestIds,
            'selected_interests_names' => $interestNames,
            'total_returned' => count($formattedPosts),
            'matched_categories_count' => $matchedCount,
            'generated_sql' => $sql
        ];

        echo json_encode([
            'success' => true,
            'debug' => $debug,
            'data' => $formattedPosts
        ]);
    }
}
