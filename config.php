<?php
// config.php
// إعدادات التوكنات وروابط الـ API

// توكن بوت تيليجرام
define('TELEGRAM_BOT_TOKEN', '8575333274:AAHaRmOxcpt0QDLFZNqLKWAIPGW64j1iMbI');

// توكن Hugging Face Inference API (يُفضل استبداله بآخر صالح)
define('HF_API_TOKEN', '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');

// نموذج عربي متخصص (أفضل من gpt2)
define('HF_MODEL_ID', 'akhooli/gpt2');

// رابط API الخاص بالنموذج

define('HF_API_URL', 'https://router.huggingface.co/models/' . HF_MODEL_ID);

// مسار ملف JSON (نفس مجلد المشروع)
define('PRODUCT_INFO_PATH', __DIR__ . '/product_info.json');

// تفعيل تسجيل الأخطاء (اختياري، مفيد جداً للتتبع)
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');