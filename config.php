<?php
// config.php - نسخة OpenAI (cURL)

// توكن بوت تيليجرام (خاص بك)
define('TELEGRAM_BOT_TOKEN', '8575333274:AAHaRmOxcpt0QDLFZNqLKWAIPGW64j1iMbI');

// 🟢 مفتاح OpenAI API - ضع المفتاح الذي حصلت عليه هنا
define('OPENAI_API_KEY', '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'); // غيّره

// النموذج المستخدم (gpt-3.5-turbo = أرخص وأسرع، gpt-4 = أدق)
define('OPENAI_MODEL', 'gpt-3.5-turbo');

// مسار ملف JSON (لا تغيره)
define('PRODUCT_INFO_PATH', __DIR__ . '/product_info.json');

// تفعيل سجل الأخطاء (مفيد للتتبع)
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');