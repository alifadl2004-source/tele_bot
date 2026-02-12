<?php
require_once __DIR__ . '/../config.php';

$ch = curl_init();
$headers = [
    'Authorization: Bearer ' . OPENROUTER_API_KEY,
    'Content-Type: application/json',
    'HTTP-Referer: https://tele-bot-0ir9.onrender.com',
    'X-Title: Senya AIR-5 Bot Test'
];

$payload = json_encode([
    'model' => OPENROUTER_MODEL,
    'messages' => [
        ['role' => 'user', 'content' => 'قل "مرحباً OpenRouter" فقط']
    ],
    'max_tokens' => 20,
]);

curl_setopt_array($ch, [
    CURLOPT_URL => OPENROUTER_API_URL,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_TIMEOUT => 30,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode<br>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";