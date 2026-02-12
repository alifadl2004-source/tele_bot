<?php
// telegram.php
// دوال مساعدة للتعامل مع Telegram Bot API

require_once __DIR__ . '/config.php';

/**
 * إرسال رسالة نصية إلى مستخدم/شات معيّن
 *
 * @param int|string $chatId
 * @param string     $text
 * @return bool      نجاح أو فشل الطلب
 */
function telegram_send_message($chatId, string $text): bool
{
    $url = 'https://api.telegram.org/bot' . TELEGRAM_BOT_TOKEN . '/sendMessage';

    $payload = [
        'chat_id'    => $chatId,
        'text'       => $text,
        'parse_mode' => 'HTML',
    ];

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_TIMEOUT        => 15,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ($httpCode >= 200 && $httpCode < 300);
}