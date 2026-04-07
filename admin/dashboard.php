<?php
require_once 'config.php';
require_once 'auth.php';
require_auth();

$data   = json_decode(file_get_contents(BLOGS_JSON_PATH), true);
$blogs  = $data['blogs'] ?? [];
$total  = count($blogs);
$flash  = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Kedar Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .thumb { width:56px; height:44px; object-fit:cover; border-radius:4px; }
        .thumb-ph { width:56px; height:44px; background:#e9ecef; border-radius:4px;
                    display:flex; align-items:center; justify-content:center; color:#aaa; font-size:11px; }
    </style>
</head>
<body class="bg-light">
<div class="container py-4" style="max-width:900px">

    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
        <h1 class="h4 fw-bold mb-0">Kedar Admin Panel</h1>
        <span class="text-muted small">Logged in as kedar &nbsp;|&nbsp;
            <a href="logout.php" class="text-danger text-decoration-none">Logout</a></span>
    </div>

    <?php if ($flash === 'saved'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            Article saved successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($flash === 'deleted'): ?>
        <div class="alert alert-warning alert-dismissible fade show">
            Article deleted.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h6 fw-semibold mb-0">All Articles
            <span class="text-muted fw-normal">(<?= $total ?> total)</span>
        </h2>
        <a href="article-form.php" class="btn btn-primary btn-sm">+ Add New Article</a>
    </div>

    <div class="card shadow-sm">
        <ul class="list-group list-group-flush">
            <?php foreach ($blogs as $blog): ?>
            <li class="list-group-item d-flex align-items-center gap-3 py-3">
                <?php
                $imgPath = __DIR__ . '/../' . ($blog['image'] ?? '');
                if (!empty($blog['image']) && file_exists($imgPath)):
                ?>
                    <img src="../<?= htmlspecialchars($blog['image']) ?>" class="thumb" alt="">
                <?php else: ?>
                    <div class="thumb-ph">IMG</div>
                <?php endif; ?>

                <div class="flex-grow-1 overflow-hidden">
                    <div class="fw-semibold text-truncate">
                        <?= htmlspecialchars($blog['title']) ?>
                    </div>
                    <div class="text-muted small">
                        <?= htmlspecialchars($blog['author'] ?? 'Kedar Oak') ?>
                        &nbsp;·&nbsp;
                        <?= htmlspecialchars($blog['id']) ?>
                    </div>
                </div>

                <div class="d-flex gap-2 flex-shrink-0">
                    <a href="article-form.php?id=<?= urlencode($blog['id']) ?>"
                       class="btn btn-warning btn-sm">Edit</a>
                    <form method="POST" action="delete-article.php"
                          onsubmit="return confirm('Delete this article? This cannot be undone.')">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($blog['id']) ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </div>
            </li>
            <?php endforeach; ?>

            <?php if ($total === 0): ?>
            <li class="list-group-item text-center text-muted py-5">
                No articles yet. <a href="article-form.php">Add the first one.</a>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
