<?php
$ch = curl_init('http://127.0.0.1:11434/api/chat');
$data = [
    'model' => 'phi3',
    'messages' => [
        ['role' => 'user', 'content' => 'hello']
    ],
    'stream' => false 
];
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
if (!$response) {
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    echo "Response: " . $response . "\n";
}
curl_close($ch);
