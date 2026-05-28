<?php
namespace App\Http\Controllers;

use App\Foundation\Application;

class EngagementController extends BaseController
{
    public function toggleLike()
    {
        $userId = Application::$app->session->get('user');
        $data = json_decode(file_get_contents("php://input"), true);
        $postId = $data['post_id'] ?? null;

        if (!$postId) {
            echo json_encode(['success' => false, 'message' => 'Post ID required']);
            return;
        }

        $db = Application::$app->db->getConnection();
        
        // Check if already liked
        $stmt = $db->prepare("SELECT id FROM post_likes WHERE user_id = :user_id AND post_id = :post_id");
        $stmt->execute(['user_id' => $userId, 'post_id' => $postId]);
        
        if ($stmt->fetch()) {
            // Unlike
            $db->prepare("DELETE FROM post_likes WHERE user_id = :user_id AND post_id = :post_id")
               ->execute(['user_id' => $userId, 'post_id' => $postId]);
            $action = 'unliked';
        } else {
            // Like
            $db->prepare("INSERT INTO post_likes (user_id, post_id) VALUES (:user_id, :post_id)")
               ->execute(['user_id' => $userId, 'post_id' => $postId]);
            $action = 'liked';
        }

        // Get new total likes count
        $countStmt = $db->prepare("SELECT COUNT(*) as total FROM post_likes WHERE post_id = :post_id");
        $countStmt->execute(['post_id' => $postId]);
        $totalLikes = $countStmt->fetch()['total'];

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'action' => $action, 'total_likes' => $totalLikes]);
    }

    public function toggleBookmark()
    {
        $userId = Application::$app->session->get('user');
        $data = json_decode(file_get_contents("php://input"), true);
        $postId = $data['post_id'] ?? null;

        if (!$postId) {
            echo json_encode(['success' => false, 'message' => 'Post ID required']);
            return;
        }

        $db = Application::$app->db->getConnection();
        
        // Check if already bookmarked
        $stmt = $db->prepare("SELECT id FROM bookmarks WHERE user_id = :user_id AND post_id = :post_id");
        $stmt->execute(['user_id' => $userId, 'post_id' => $postId]);
        
        if ($stmt->fetch()) {
            // Unbookmark
            $db->prepare("DELETE FROM bookmarks WHERE user_id = :user_id AND post_id = :post_id")
               ->execute(['user_id' => $userId, 'post_id' => $postId]);
            $action = 'unbookmarked';
        } else {
            // Bookmark
            $db->prepare("INSERT INTO bookmarks (user_id, post_id) VALUES (:user_id, :post_id)")
               ->execute(['user_id' => $userId, 'post_id' => $postId]);
            $action = 'bookmarked';
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'action' => $action]);
    }
}
