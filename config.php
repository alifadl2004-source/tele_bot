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