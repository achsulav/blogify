<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Category;
use App\Foundation\Application;

class OnboardingController extends BaseController
{
    public function show()
    {
        $userId = Application::$app->session->get('user');
        if (!$userId) {
            $this->redirect('/login');
            return;
        }

        $userModel = new User(Application::$app->db);
        if ($userModel->isOnboarded($userId)) {
            $this->redirect('/dashboard');
            return;
        }

        $categoryModel = new Category(Application::$app->db);
        $categories = $categoryModel->getAll();

        $this->render('Onboarding', [
            'categories' => $categories,
            'isSettings' => false,
            'userInterests' => []
        ], 'root'); // Assuming root layout is appropriate or we can use a custom one
    }

    public function settings()
    {
        $userId = Application::$app->session->get('user');
        if (!$userId) {
            $this->redirect('/login');
            return;
        }

        $categoryModel = new Category(Application::$app->db);
        $categories = $categoryModel->getAll();
        
        $userModel = new User(Application::$app->db);
        $userInterestsArray = $userModel->getInterests($userId);
        $userInterests = array_column($userInterestsArray, 'id');

        // Fetch user role
        $stmt = Application::$app->db->getConnection()->prepare("SELECT role FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $userRole = $stmt->fetch()['role'] ?? 'Primary Audience';

        $this->render('Onboarding', [
            'categories' => $categories,
            'isSettings' => true,
            'userInterests' => $userInterests,
            'userRole' => $userRole
        ], 'root');
    }

    public function save()
    {
        $userId = Application::$app->session->get('user');
        if (!$userId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        // Read JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        $categoryIds = $input['categories'] ?? [];
        $role = $input['role'] ?? 'Primary Audience';

        $userModel = new User(Application::$app->db);
        
        // Save role
        $stmt = Application::$app->db->getConnection()->prepare("UPDATE users SET role = :role WHERE id = :id");
        $stmt->execute(['role' => $role, 'id' => $userId]);

        if ($userModel->saveInterests($userId, $categoryIds)) {
            $userModel->markOnboarded($userId);
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'redirect' => '/dashboard']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to save interests']);
        }
    }
}
