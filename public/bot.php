<?php
// bot.php - Webhook الرئيسي

ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error_log.txt');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../ai.php';
require_once __DIR__ . '/../telegram.php';

$rawInput = file_get_contents('php://input');
if (!$rawInput) {
    http_response_code(400);
    exit('No input');
}

$update = json_decode($rawInput, true);
if (!is_array($update)) {
    http_response_code(400);
    exit('Invalid JSON');
}

if (!isset($update['message']) || !isset($update['message']['chat']['id'])) {
    http_response_code(200);
    exit('No message');
}

$chatId = $update['message']['chat']['id'];
$userText = '';

if (isset($update['message']['text'])) {
    $userText = trim($update['message']['text']);
}

$productInfo = load_product_info_array();
$fallback = get_fallback_reply($productInfo);

if ($userText === '') {
    telegram_send_message($chatId, $fallback);
    http_response_code(200);
    exit;
}

$reply = generate_reply_for_user($userText);
telegram_send_message($chatId, $reply);

http_response_code(200);
echo 'OK';