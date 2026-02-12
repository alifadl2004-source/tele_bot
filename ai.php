<?php
// ai.php
// مسؤول عن الاتصال بـ Hugging Face وصياغة الطلب (prompt)

require_once __DIR__ . '/config.php';

/**
 * تحميل محتوى ملف JSON كـ string خام
 */
function load_product_info_raw(): string
{
    if (!file_exists(PRODUCT_INFO_PATH)) {
        error_log("ai.php: product_info.json غير موجود");
        return '{}';
    }

    $jsonContent = file_get_contents(PRODUCT_INFO_PATH);
    if ($jsonContent === false || trim($jsonContent) === '') {
        error_log("ai.php: product_info.json فارغ أو غير قابل للقراءة");
        return '{}';
    }

    return $jsonContent;
}

/**
 * تحميل ملف JSON كـ مصفوفة PHP
 */
function load_product_info_array(): array
{
    $raw = load_product_info_raw();
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        error_log("ai.php: فشل تحويل JSON إلى مصفوفة");
        return [];
    }
    return $data;
}

/**
 * استخراج نص fallback_reply من JSON مع دعم البنية الجديدة
 */
function get_fallback_reply(array $productInfo): string
{
    // 1. البحث في الجذر
    if (isset($productInfo['fallback_reply']) && is_string($productInfo['fallback_reply'])) {
        return $productInfo['fallback_reply'];
    }
    // 2. البحث داخل ai_rules
    if (isset($productInfo['ai_rules']['fallback_reply']) && is_string($productInfo['ai_rules']['fallback_reply'])) {
        return $productInfo['ai_rules']['fallback_reply'];
    }
    // 3. نص افتراضي
    return 'عذرًا، ما عندي معلومات كافية للإجابة على هذا السؤال حاليًا. يرجى التواصل مع الدعم الفني.';
}

/**
 * بناء الـ Prompt المرسل للذكاء الاصطناعي
 */
function build_ai_prompt(string $userMessage, string $productJsonRaw): string
{
    $prompt = <<<EOT
أنت مساعد خدمة عملاء عربي لبوت تيليجرام، وظيفتك الرد على استفسارات العملاء عن منتج واحد فقط اسمه "Senya AIR-5".

القواعد الصارمة:
- استخدم فقط المعلومات الموجودة في ملف JSON المرفق.
- لا تخترع أي معلومة غير موجودة في JSON.
- لا تضيف أسعاراً أو مواصفات أو سياسة ضمان أو توصيل غير مذكورة.
- إذا لم تجد إجابة، استخدم نص "fallback_reply" الموجود داخل JSON حرفياً.
- الرد يجب أن يكون:
  * بالعربية الفصحى أو العامية الخفيفة
  * مهذباً وواضحاً ومختصراً
  * مناسباً لخدمة العملاء

محتوى ملف JSON (جميع معلومات المنتج وسياساته):
---
$productJsonRaw
---

رسالة العميل:
"$userMessage"

الآن أجب برسالة واحدة جاهزة للإرسال للعميل، بدون أي شروحات إضافية.
EOT;

    return $prompt;
}

/**
 * إرسال الطلب إلى Hugging Face Inference API
 */
function call_hf_api(string $prompt): ?string
{
    $ch = curl_init();

    $headers = [
        'Authorization: Bearer ' . HF_API_TOKEN,
        'Content-Type: application/json'
    ];

    // بعض النماذج العربية تحتاج إلى max_length بدلاً من max_new_tokens
    $payload = json_encode([
        'inputs' => $prompt,
        'parameters' => [
            'max_new_tokens' => 250,
            'temperature'    => 0.3,
            'repetition_penalty' => 1.1, // لتجنب التكرار
            'do_sample'      => true,
        ]
    ]);

    curl_setopt_array($ch, [
        CURLOPT_URL            => HF_API_URL,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_TIMEOUT        => 40,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $httpCode < 200 || $httpCode >= 300) {
        error_log("ai.php: فشل الاتصال بـ Hugging Face - HTTP $httpCode");
        return null;
    }

    $data = json_decode($response, true);
    if (!is_array($data)) {
        error_log("ai.php: رد Hugging Face غير صالح - " . substr($response, 0, 200));
        return null;
    }

    // استخراج النص المُولَّد
    if (isset($data[0]['generated_text'])) {
        return trim($data[0]['generated_text']);
    }
    if (isset($data['generated_text'])) {
        return trim($data['generated_text']);
    }

    error_log("ai.php: لم يتم العثور على generated_text في الرد");
    return null;
}

/**
 * الواجهة العليا: توليد الرد للعميل
 */
function generate_reply_for_user(string $userMessage): string
{
    $productJsonRaw = load_product_info_raw();
    $productInfo    = load_product_info_array();

    // الحصول على نص الفال باك المناسب
    $fallback = get_fallback_reply($productInfo);

    // بناء الـ prompt
    $prompt = build_ai_prompt($userMessage, $productJsonRaw);

    // استدعاء Hugging Face
    $aiReply = call_hf_api($prompt);

    if ($aiReply === null || trim($aiReply) === '') {
        error_log("ai.php: الرد من HF فارغ أو فاشل، استخدام الفال باك");
        return $fallback;
    }

    // إزالة أي تكرار للـ prompt (إذا رد النموذج بالكامل)
    if (strpos($aiReply, $prompt) === 0) {
        $aiReply = substr($aiReply, strlen($prompt));
    }

    // قص الرد إذا كان طويلاً جداً
    if (mb_strlen($aiReply) > 1000) {
        $aiReply = mb_substr($aiReply, 0, 1000) . '...';
    }

    return trim($aiReply);
}