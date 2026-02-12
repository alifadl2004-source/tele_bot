<?php
require_once __DIR__ . '/../config.php';

$ch = curl_init();
$headers = [
    'Authorization: Bearer ' . OPENAI_API_KEY,
    'Content-Type: application/json',
];
$payload = json_encode([
    'model' => OPENAI_MODEL,
    'messages' => [
        ['role' => 'user', 'content' => 'قل "مرحباً" فقط']
    ],
    'max_tokens' => 10,
]);

curl_setopt_array($ch, [
    CURLOPT_URL => 'https://api.openai.com/v1/chat/completions',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_POSTFIELDS => $payload,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode<br>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";