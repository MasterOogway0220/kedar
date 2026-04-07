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
$mediaType = $mimeType;

$prompt = 'Extract all text from this article image. '
        . 'Return ONLY a valid JSON object with exactly two fields: '
        . '"title" (the article headline/title text at the top of the image) and '
        . '"content" (the full body text formatted as HTML: use <p> tags for paragraphs, '
        . '<h2> for section headings, <blockquote> for quoted text, <table> for tables). '
        . 'Preserve the original language exactly — Marathi or English. '
        . 'Do not include any explanation, markdown fences, or extra text. Return only the raw JSON object.';

$payload = [
    'model'      => 'claude-haiku-4-5-20251001',
    'max_tokens' => 4096,
    'messages'   => [[
        'role'    => 'user',
        'content' => [
            [
                'type'   => 'image',
                'source' => [
                    'type'       => 'base64',
                    'media_type' => $mediaType,
                    'data'       => $imageData,
                ],
            ],
            [
                'type' => 'text',
                'text' => $prompt,
            ],
        ],
    ]],
];

$ch = curl_init('https://api.anthropic.com/v1/messages');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'x-api-key: '          . CLAUDE_API_KEY,
        'anthropic-version: 2023-06-01',
    ],
    CURLOPT_TIMEOUT        => 60,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false || $httpCode !== 200) {
    echo json_encode(['error' => 'Claude API error (HTTP ' . $httpCode . '). Check your API key in config.php.']);
    exit;
}

$apiData = json_decode($response, true);
$text    = trim($apiData['content'][0]['text'] ?? '');

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
