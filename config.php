<?php
// config.php - نسخة OpenRouter (مجانية)

// توكن بوت تيليجرام
define('TELEGRAM_BOT_TOKEN', '8575333274:AAHaRmOxcpt0QDLFZNqLKWAIPGW64j1iMbI');

// 🟢 مفتاح OpenRouter API (مجاني)
define('OPENROUTER_API_KEY', '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');

// النموذج المستخدم: DeepSeek مجاني بالكامل عبر OpenRouter
define('OPENROUTER_MODEL', 'deepseek/deepseek-chat:free');

// رابط OpenRouter API
define('OPENROUTER_API_URL', 'https://openrouter.ai/api/v1/chat/completions');

// مسار ملف JSON (لا تغيره)
define('PRODUCT_INFO_PATH', __DIR__ . '/product_info.json');

// تفعيل سجل الأخطاء
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');