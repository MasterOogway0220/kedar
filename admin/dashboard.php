<?php
require_once 'config.php';
require_once 'auth.php';
require_auth();

$data  = json_decode(file_get_contents(BLOGS_JSON_PATH), true);
$blogs = $data['blogs'] ?? [];
$total = count($blogs);
$flash = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles — Kedar Admin</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<?php require 'sidebar.php'; ?>

<div class="main">
    <div class="topbar">
        <span class="topbar__title">Articles</span>
        <div class="topbar__actions">
            <a href="article-form.php" class="btn btn--primary btn--sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                New Article
            </a>
        </div>
    </div>

    <div class="content">

        <?php if ($flash === 'saved'): ?>
            <div class="alert alert--success">
                <span>✓ Article saved and published successfully.</span>
                <button class="alert__close" onclick="this.parentElement.remove()">×</button>
            </div>
        <?php elseif ($flash === 'deleted'): ?>
            <div class="alert alert--warning">
                <span>Article deleted.</span>
                <button class="alert__close" onclick="this.parentElement.remove()">×</button>
            </div>
        <?php elseif ($flash === 'image_error'): ?>
            <div class="alert alert--danger">
                <span>Invalid image type. Only JPG and PNG are accepted.</span>
                <button class="alert__close" onclick="this.parentElement.remove()">×</button>
            </div>
        <?php elseif ($flash === 'image_too_large'): ?>
            <div class="alert alert--danger">
                <span>Image exceeds the 10 MB size limit.</span>
                <button class="alert__close" onclick="this.parentElement.remove()">×</button>
            </div>
        <?php elseif ($flash === 'upload_error'): ?>
            <div class="alert alert--danger">
                <span>Image upload failed. Please try again.</span>
                <button class="alert__close" onclick="this.parentElement.remove()">×</button>
            </div>
        <?php endif; ?>

        <div class="stat-strip">
            <div class="stat">
                <div class="stat__value"><?= $total ?></div>
                <div class="stat__label">Total Articles</div>
            </div>
        </div>

        <div class="card">
            <div class="card__header">
                <span class="card__title">All Articles <span class="card__count"><?= $total ?> total</span></span>
            </div>

            <?php if ($total === 0): ?>
                <div class="empty-state">
                    <div class="empty-state__icon">📄</div>
                    <div class="empty-state__title">No articles yet</div>
                    <div class="empty-state__sub"><a href="article-form.php" style="color:var(--sidebar-accent)">Create your first article</a></div>
                </div>
            <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:64px"></th>
                        <th>Article</th>
                        <th style="width:100px">ID</th>
                        <th style="width:130px">Date</th>
                        <th style="width:120px"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($blogs as $blog): ?>
                    <tr>
                        <td>
                            <?php
                            $imgPath = __DIR__ . '/../' . ($blog['image'] ?? '');
                            if (!empty($blog['image']) && file_exists($imgPath)): ?>
                                <img src="../<?= htmlspecialchars($blog['image']) ?>" class="article-thumb" alt="">
                            <?php else: ?>
                                <div class="article-thumb-ph">IMG</div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="article-title"><?= htmlspecialchars($blog['title']) ?></span>
                            <span class="article-meta"><?= htmlspecialchars($blog['author'] ?? 'Kedar Oak') ?></span>
                        </td>
                        <td>
                            <span class="article-id"><?= htmlspecialchars($blog['id']) ?></span>
                        </td>
                        <td style="font-size:12px;color:var(--text-muted)">
                            <?= htmlspecialchars($blog['date'] ?? '') ?>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;justify-content:flex-end">
                                <a href="article-form.php?id=<?= urlencode($blog['id']) ?>" class="btn btn--warn-soft btn--sm">Edit</a>
                                <form method="POST" action="delete-article.php" onsubmit="return confirm('Delete this article? This cannot be undone.')">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($blog['id']) ?>">
                                    <button type="submit" class="btn btn--danger-soft btn--sm">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

    </div>
</div>
</body>
</html>
