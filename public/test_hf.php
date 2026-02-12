<?php
require_once __DIR__ . '/../config.php';

echo "<h2>اختبار Hugging Face API</h2>";

$prompt = "ما هو سعر سماعة Senya AIR-5؟";

$ch = curl_init();
$headers = [
    'Authorization: Bearer ' . HF_API_TOKEN,
    'Content-Type: application/json'
];
$payload = json_encode([
    'inputs' => $prompt,
    'parameters' => [
        'max_new_tokens' => 100,
        'temperature' => 0.3,
        'repetition_penalty' => 1.1,
        'do_sample' => true
    ]
]);

curl_setopt_array($ch, [
    CURLOPT_URL => HF_API_URL,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_TIMEOUT => 40,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "<h3>معلومات الطلب:</h3>";
echo "<strong>النموذج:</strong> " . HF_MODEL_ID . "<br>";
echo "<strong>التوكن:</strong> " . substr(HF_API_TOKEN, 0, 10) . "...<br>";
echo "<strong>HTTP Code:</strong> " . $httpCode . "<br>";

if ($response === false) {
    echo "<strong>cURL Error:</strong> " . $curlError;
} else {
    echo "<h3>الرد الخام:</h3>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "<h3>الرد بعد التحليل:</h3>";
        echo "<pre>" . print_r($data, true) . "</pre>";
        
        // محاولة استخراج النص المولد
        if (isset($data[0]['generated_text'])) {
            echo "<h3>النص المُولَّد:</h3>";
            echo "<p style='background:#eee; padding:10px;'>" . htmlspecialchars($data[0]['generated_text']) . "</p>";
        } elseif (isset($data['generated_text'])) {
            echo "<h3>النص المُولَّد:</h3>";
            echo "<p style='background:#eee; padding:10px;'>" . htmlspecialchars($data['generated_text']) . "</p>";
        } else {
            echo "<p>لم يتم العثور على generated_text في الرد.</p>";
        }
    } else {
        echo "<p>الرد ليس JSON صحيح: " . json_last_error_msg() . "</p>";
    }
}