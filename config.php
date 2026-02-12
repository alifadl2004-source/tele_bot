<?php
// config.php - نسخة OpenRouter (نموذج مجاني محدث)

define('TELEGRAM_BOT_TOKEN', '8575333274:AAHaRmOxcpt0QDLFZNqLKWAIPGW64j1iMbI');
define('OPENROUTER_API_KEY', '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');

// ✅ تم تغيير النموذج إلى إصدار مجاني متاح
define('OPENROUTER_MODEL', 'deepseek/deepseek-chat-v3-0324:free');

define('OPENROUTER_API_URL', 'https://openrouter.ai/api/v1/chat/completions');
define('PRODUCT_INFO_PATH', __DIR__ . '/product_info.json');

ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');
