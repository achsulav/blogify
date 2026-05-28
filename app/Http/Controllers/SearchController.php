<?php
namespace App\Http\Controllers;

use App\Foundation\Application;

class SearchController extends BaseController
{
    public function index()
    {
        $query = $_GET['q'] ?? '';
        if (empty($query)) {
            echo json_encode([]);
            return;
        }

        $userId = Application::$app->session->get('user');
        $db = Application::$app->db->getConnection();

        // Search in title, tags, and category, and rank by user interests match
        // We use a simplified scoring: 
        // match_score = interest_match * 100 + likes + views
        
        $sql = "
            SELECT p.id, p.title, p.slug, p.tags, u.username as author_username, c.name as category_name,
            IF(p.category_id IN (SELECT category_id FROM user_interests WHERE user_id = :user_id), 1, 0) as category_match
            FROM posts p
            JOIN users u ON p.user_id = u.id
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.title LIKE :query 
               OR p.tags LIKE :query 
               OR c.name LIKE :query
            ORDER BY category_match DESC, (p.views_count + (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id)*5) DESC
            LIMIT 10
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            'query' => '%' . $query . '%',
            'user_id' => $userId ?? 0
        ]);

        $results = $stmt->fetchAll();
        
        $response = [];
        foreach ($results as $res) {
            $authorUsername = htmlspecialchars($res['author_username'] ?? '');
            $postUrl = is_production() ? "/$authorUsername/{$res['slug']}" : "http://$authorUsername.blogify.dev/{$res['slug']}";
            
            $response[] = [
                'id' => $res['id'],
                'title' => htmlspecialchars($res['title']),
                'url' => $postUrl,
                'category' => htmlspecialchars($res['category_name']),
                'category_match' => (bool)$res['category_match']
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
