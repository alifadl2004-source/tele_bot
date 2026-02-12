<?php
// config.php - نسخة DeepSeek API

// توكن بوت تيليجرام
define('TELEGRAM_BOT_TOKEN', '8575333274:AAHaRmOxcpt0QDLFZNqLKWAIPGW64j1iMbI');

// 🟢 مفتاح DeepSeek API (مجاني بالكامل)
define('DEEPSEEK_API_KEY', 'sk-bacd69d56c114420afa08c70f7ca88c9');

// النموذج المستخدم (deepseek-chat هو الأنسب)
define('DEEPSEEK_MODEL', 'deepseek-chat');

// مسار ملف JSON (لا تغيره)
define('PRODUCT_INFO_PATH', __DIR__ . '/product_info.json');

// تفعيل سجل الأخطاء
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');