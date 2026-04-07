<?php
require_once 'config.php';
require_once 'auth.php';
require_auth();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['image']['tmp_name'])) {
    echo json_encode(['error' => 'No image provided.']);
    exit;
}

$file = $_FILES['image'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['error' => 'Upload failed. Please try again.']);
    exit;
}
if ($file['size'] > 10 * 1024 * 1024) {
    echo json_encode(['error' => 'Image exceeds 10 MB limit.']);
    exit;
}
$finfo    = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);
$allowed = ['image/jpeg', 'image/png'];
if (!in_array($mimeType, $allowed, true)) {
    echo json_encode(['error' => 'Only JPG and PNG images are accepted.']);
    exit;
}

$imageData = base64_encode(file_get_contents($file['tmp_name']));

$prompt = 'Extract all text from this article image. '
        . 'Return ONLY a valid JSON object with exactly two fields: '
        . '"title" (the article headline/title text at the top of the image) and '
        . '"content" (the full body text formatted as HTML: use <p> tags for paragraphs, '
        . '<h2> for section headings, <blockquote> for quoted text, <table> for tables). '
        . 'Preserve the original language exactly — Marathi or English. '
        . 'Do not include any explanation, markdown fences, or extra text. Return only the raw JSON object.';

$payload = [
    'contents' => [[
        'parts' => [
            [
                'inline_data' => [
                    'mime_type' => $mimeType,
                    'data'      => $imageData,
                ],
            ],
            [
                'text' => $prompt,
            ],
        ],
    ]],
    'generationConfig' => [
        'maxOutputTokens' => 4096,
    ],
];

$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=' . GEMINI_API_KEY;

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_TIMEOUT        => 60,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false || $httpCode !== 200) {
    echo json_encode(['error' => 'Gemini API error (HTTP ' . $httpCode . '). Check your API key in config.php.']);
    exit;
}

$apiData = json_decode($response, true);
$text    = trim($apiData['candidates'][0]['content']['parts'][0]['text'] ?? '');

// Strip markdown code fences if the model added them despite instructions
$text = preg_replace('/^```(?:json)?\s*/i', '', $text);
$text = preg_replace('/\s*```$/',           '', $text);

$extracted = json_decode($text, true);
if (!is_array($extracted) || !isset($extracted['title'], $extracted['content'])) {
    echo json_encode(['error' => 'Could not parse text from image. Try a higher-resolution image.']);
    exit;
}

echo json_encode([
    'title'   => $extracted['title'],
    'content' => $extracted['content'],
]);
