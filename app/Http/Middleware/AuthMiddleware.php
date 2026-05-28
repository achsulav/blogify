<?php
namespace App\Http\Middleware;

use App\Foundation\Application;

class AuthMiddleware implements Middleware{
  public function handle():void
  {
    if(!Application::$app->session->get('user')){
      header('Location: /login');
      exit;
    }

    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $userId = Application::$app->session->get('user');
    
    // Onboarding check
    if ($uri !== '/onboarding' && strpos($uri, '/api/onboarding/save') !== 0 && $uri !== '/logout') {
        $userModel = new \App\Models\User(Application::$app->db);
        if (!$userModel->isOnboarded($userId)) {
            header('Location: /onboarding');
            exit;
        }
    }

    $subdomain = Application::$app->getSubdomain();
    $loggedInUsername = Application::$app->session->get('username');

    if ($subdomain && $subdomain !== 'blogify' && $subdomain !== 'www') {
        // Only enforce subdomain matching for non-comment routes
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if (strpos($uri, '/comment/') !== 0) {
            if ($subdomain !== $loggedInUsername) {
                Application::$app->session->setFlash('error', 'Unauthorized access to this subdomain.');
                if (is_production()) {
                    header("Location: /dashboard");
                } else {
                    header("Location: http://{$loggedInUsername}.blogify.dev/dashboard");
                }
                exit;
            }
        }
    }

  }
}
