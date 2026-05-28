<?php
namespace App\Http\Controllers;
use App\Foundation\Application;

class DashboardController extends BaseController
{
    public function index()
    {
      $userId = Application::$app->session->get('user');
      $fullName = Application::$app->session->get('user_name');

      $posts = []; // We will now fetch posts via AJAX in the view

      // Fetch user's role to determine dashboard structure
      $userStmt = Application::$app->db->getConnection()->prepare("SELECT role FROM users WHERE id = :id");
      $userStmt->execute(['id' => $userId]);
      $userRole = $userStmt->fetch()['role'] ?? 'Primary Audience';

      // Analytics data (Dummy calculated for now if empty)
      $stats = [
          'views' => 0,
          'engagement' => 0,
          'published' => 0
      ];
      
      if ($userRole !== 'Primary Audience' && $userRole !== 'Reader') {
          // If writer/creator, fetch real stats
          $statStmt = Application::$app->db->getConnection()->prepare("
              SELECT SUM(views_count) as total_views, COUNT(id) as total_posts 
              FROM posts WHERE user_id = :id
          ");
          $statStmt->execute(['id' => $userId]);
          $statData = $statStmt->fetch();
          $stats['views'] = $statData['total_views'] ?? 0;
          $stats['published'] = $statData['total_posts'] ?? 0;
          $stats['engagement'] = '5.4%'; // Could be calculated properly
      }

      $this->render('Dashboard',[
        'fullName' => $fullName,
        'posts' => $posts,
        'userRole' => $userRole,
        'stats' => $stats
      ], 'editorial');
    }
}

