<?php
namespace App\Http\Controllers;

use App\Foundation\Application;
use App\Services\LocalAiService;

class AiController extends BaseController {
    public function generate() {
        // Ensure user is logged in
        $userId = Application::$app->session->get('user');
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        // Read JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        $prompt = $input['prompt'] ?? '';

        if (empty($prompt)) {
            http_response_code(400);
            echo json_encode(['error' => 'Prompt is required']);
            return;
        }

        $aiService = new LocalAiService();
        $generatedText = $aiService->generateText("Write content about: " . $prompt);

        if ($generatedText && strpos($generatedText, 'CURL_ERROR') === false) {
            echo json_encode(['success' => true, 'content' => $generatedText]);
        } else {
            http_response_code(500);
            $errorMsg = $generatedText ?: 'Failed to generate content. Make sure Ollama container is running and model is downloaded.';
            echo json_encode(['error' => $errorMsg]);
        }
    }
}
