<?php
// bot.php
// ملف الـ webhook الرئيسي لبوت تيليجرام

ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error_log.txt'); // سجل الأخطاء في الجذر

require_once __DIR__ . '/../config.php';    // ارجع مجلداً للخلف

require_once __DIR__ . '/../ai.php';        // ارجع مجلداً للخلف
require_once __DIR__ . '/../telegram.php';  // ارجع مجلداً للخلف

// ... باقي الكود كما هو ...
// قراءة البيانات الواردة من تيليجرام
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

// التأكد من وجود رسالة
if (!isset($update['message']) || !isset($update['message']['chat']['id'])) {
    http_response_code(200);
    exit('No message');
}

$chatId = $update['message']['chat']['id'];
$userText = '';

// استخراج النص إن وجد
if (isset($update['message']['text'])) {
    $userText = trim($update['message']['text']);
}

// تحميل معلومات المنتج واستخراج الفال باك (لجميع الحالات)
$productInfo = load_product_info_array();
$fallback = get_fallback_reply($productInfo); // الدالة موجودة في ai.php

// إذا كانت الرسالة فارغة (غير نصية)
if ($userText === '') {
    telegram_send_message($chatId, $fallback);
    http_response_code(200);
    exit;
}

// توليد الرد عبر الذكاء الاصطناعي
$reply = generate_reply_for_user($userText);

// إرسال الرد
telegram_send_message($chatId, $reply);

// إنهاء الطلب بنجاح
http_response_code(200);
echo 'OK';

