<?php
namespace App\Services;

class LocalAiService {
    public function generateText(string $prompt): ?string {
        // Prevent PHP from timing out while waiting for AI
        ini_set('max_execution_time', 300);

        // Pointing to the local Ollama server container on the Docker network
        $ch = curl_init('http://ollama:11434/api/chat');
        
        $data = [
            'model' => 'phi3', // Much faster and lighter than llama3
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful blog writing assistant.  Limit your response to exactly 1 short paragraph (around 4-5 sentences max). Do not write more than that. Write only the generated content without any introduction.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'stream' => false 
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        $response = curl_exec($ch);
        
        if (!$response) {
            $error = curl_error($ch);
            curl_close($ch);
            return "CURL_ERROR: " . $error;
        }
        curl_close($ch);

        $result = json_decode($response, true);
        
        return $result['message']['content'] ?? null;
    }
}
