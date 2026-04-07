<?php
require_once 'config.php';
require_once 'auth.php';
require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    header('Location: dashboard.php');
    exit;
}

$deleteId = trim($_POST['id']);

$data          = json_decode(file_get_contents(BLOGS_JSON_PATH), true);
$data['blogs'] = array_values(
    array_filter($data['blogs'], fn($b) => $b['id'] !== $deleteId)
);

file_put_contents(
    BLOGS_JSON_PATH,
    json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    LOCK_EX
);

header('Location: dashboard.php?msg=deleted');
exit;
