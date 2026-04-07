<?php
require_once 'config.php';
require_once 'auth.php';
require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$editId  = trim($_POST['id'] ?? '');
$isEdit  = $editId !== '';

// Read JSON
$data  = json_decode(file_get_contents(BLOGS_JSON_PATH), true);
$blogs = $data['blogs'] ?? [];

// Find max article number for new ID / image filename
$maxNum = 0;
foreach ($blogs as $b) {
    $num = (int) preg_replace('/[^0-9]/', '', $b['id'] ?? '');
    if ($num > $maxNum) $maxNum = $num;
}

// Handle image upload (optional on edit)
$imagePath = trim($_POST['existing_image'] ?? '');
if (!empty($_FILES['image']['tmp_name'])) {
    $allowed = ['image/jpeg', 'image/png'];
    if (!in_array($_FILES['image']['type'], $allowed, true)) {
        header('Location: dashboard.php?msg=image_error');
        exit;
    }
    if ($_FILES['image']['size'] > 10 * 1024 * 1024) {
        header('Location: dashboard.php?msg=image_too_large');
        exit;
    }
    $articleNum = $isEdit
        ? (int) preg_replace('/[^0-9]/', '', $editId)
        : $maxNum + 1;
    $ext      = ($_FILES['image']['type'] === 'image/png') ? 'png' : 'jpeg';
    $filename = $articleNum . '.' . $ext;
    move_uploaded_file($_FILES['image']['tmp_name'], ARTICLES_IMAGE_PATH . $filename);
    $imagePath = 'assets/article/' . $filename;
}

// Preserve existing date on edit
$existingDate = date('Y-m-d');
if ($isEdit) {
    foreach ($blogs as $b) {
        if ($b['id'] === $editId) {
            $existingDate = $b['date'] ?? date('Y-m-d');
            break;
        }
    }
}

$article = [
    'id'      => $isEdit ? $editId : 'blog' . ($maxNum + 1),
    'title'   => trim($_POST['title']   ?? ''),
    'author'  => trim($_POST['author']  ?? 'Kedar Oak'),
    'image'   => $imagePath,
    'excerpt' => trim($_POST['excerpt'] ?? ''),
    'content' => $_POST['content']      ?? '',
    'date'    => $isEdit ? $existingDate : date('Y-m-d'),
];

if ($isEdit) {
    foreach ($blogs as &$b) {
        if ($b['id'] === $editId) {
            $b = $article;
            break;
        }
    }
    unset($b);
} else {
    array_unshift($blogs, $article); // prepend — newest first
}

$data['blogs'] = $blogs;
file_put_contents(
    BLOGS_JSON_PATH,
    json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    LOCK_EX
);

header('Location: dashboard.php?msg=saved');
exit;
