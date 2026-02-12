<?php
// ai.php - إصدار OpenAI مع cURL مباشر

require_once __DIR__ . '/config.php';

/**
 * تحميل محتوى JSON كـ نص خام
 */
function load_product_info_raw(): string
{
    if (!file_exists(PRODUCT_INFO_PATH)) {
        return '{}';
    }
    $content = file_get_contents(PRODUCT_INFO_PATH);
    return $content ?: '{}';
}

/**
 * تحميل JSON كمصفوفة
 */
function load_product_info_array(): array
{
    $data = json_decode(load_product_info_raw(), true);
    return is_array($data) ? $data : [];
}

/**
 * استخراج نص الفال باك (يدعم المكانين)
 */
function get_fallback_reply(array $productInfo): string
{
    if (isset($productInfo['fallback_reply'])) {
        return $productInfo['fallback_reply'];
    }
    if (isset($productInfo['ai_rules']['fallback_reply'])) {
        return $productInfo['ai_rules']['fallback_reply'];
    }
    return 'عذراً، لا تتوفر لدي معلومات كافية حالياً.';
}

/**
 * بناء الـ Prompt الذي سيرسل إلى GPT
 */
function build_ai_prompt(string $userMessage, string $productJsonRaw): string
{
    return <<<EOT
أنت مساعد خدمة عملاء ذكي لمتجر يبيع سماعة "Senya AIR-5".
معلومات المنتج وسياسات البيع موجودة في JSON أدناه.
لا تخترع أي معلومات غير موجودة في JSON.
ردودك يجب أن تكون بالعربية، مهذبة، مختصرة، ومفيدة.

محتوى JSON:
$productJsonRaw

العميل يقول: "$userMessage"

الرد:
EOT;
}

/**
 * الاتصال بـ OpenAI API باستخدام cURL
 */
function call_openai_api(string $prompt): ?string
{
    $ch = curl_init();

    $headers = [
        'Authorization: Bearer ' . OPENAI_API_KEY,
        'Content-Type: application/json',
    ];

    $payload = json_encode([
        'model' => OPENAI_MODEL,
        'messages' => [
            ['role' => 'user', 'content' => $prompt],
        ],
        'max_tokens' => 250,
        'temperature' => 0.3,
    ]);

    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://api.openai.com/v1/chat/completions',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_TIMEOUT => 30,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $httpCode !== 200) {
        error_log("OpenAI Error: HTTP $httpCode - " . substr($response, 0, 200));
        return null;
    }

    $data = json_decode($response, true);
    return $data['choices'][0]['message']['content'] ?? null;
}

/**
 * الواجهة الرئيسية: توليد الرد للعميل
 */
function generate_reply_for_user(string $userMessage): string
{
    $productJsonRaw = load_product_info_raw();
    $productInfo    = load_product_info_array();
    $fallback       = get_fallback_reply($productInfo);

    $prompt = build_ai_prompt($userMessage, $productJsonRaw);
    $reply  = call_openai_api($prompt);

    return $reply ?: $fallback;
}