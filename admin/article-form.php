<?php
require_once 'config.php';
require_once 'auth.php';
require_auth();

$editMode = false;
$blog = [
    'id'      => '',
    'title'   => '',
    'author'  => 'Kedar Oak',
    'excerpt' => '',
    'image'   => '',
    'content' => '',
    'date'    => date('Y-m-d'),
];

if (!empty($_GET['id'])) {
    $editId = $_GET['id'];
    $data   = json_decode(file_get_contents(BLOGS_JSON_PATH), true);
    foreach ($data['blogs'] as $b) {
        if ($b['id'] === $editId) {
            $blog     = array_merge($blog, $b);
            $editMode = true;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $editMode ? 'Edit Article' : 'New Article' ?> — Kedar Admin</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.7/quill.snow.css">
</head>
<body>
<?php require 'sidebar.php'; ?>

<div class="main">
    <div class="topbar">
        <span class="topbar__title"><?= $editMode ? 'Edit Article' : 'New Article' ?></span>
        <div class="topbar__actions">
            <a href="dashboard.php" class="btn btn--ghost btn--sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>
    </div>

    <div class="content">
        <form id="articleForm" method="POST" action="save-article.php" enctype="multipart/form-data">
            <?php if ($editMode): ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($blog['id']) ?>">
                <input type="hidden" name="existing_image" value="<?= htmlspecialchars($blog['image']) ?>">
            <?php endif; ?>
            <input type="hidden" name="content" id="contentInput">

            <div class="form-card">

                <!-- Basic info -->
                <div class="form-section">
                    <div class="form-section__title">Article Details</div>

                    <div class="form-group">
                        <label class="form-label" for="titleInput">Title</label>
                        <input type="text" id="titleInput" name="title" class="form-control" required
                               value="<?= htmlspecialchars($blog['title']) ?>"
                               placeholder="Enter article title (Marathi or English)...">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="authorInput">Author / Expert</label>
                            <input type="text" id="authorInput" name="author" class="form-control"
                                   value="<?= htmlspecialchars($blog['author']) ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="excerptInput">
                                Excerpt
                                <span class="form-hint">Shown on article listing</span>
                            </label>
                            <input type="text" id="excerptInput" name="excerpt" class="form-control"
                                   value="<?= htmlspecialchars($blog['excerpt']) ?>"
                                   placeholder="Short 2–3 line summary...">
                        </div>
                    </div>
                </div>

                <!-- Image -->
                <div class="form-section">
                    <div class="form-section__title">Featured Image</div>

                    <?php if ($editMode && !empty($blog['image'])): ?>
                        <div class="current-image">
                            <img src="../<?= htmlspecialchars($blog['image']) ?>" alt="Current image">
                            <span class="current-image-label">Current image — upload a new one to replace it</span>
                        </div>
                    <?php endif; ?>

                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label" for="imageInput">
                            <?= $editMode ? 'Replace Image' : 'Upload Image' ?>
                            <span class="form-hint">JPG or PNG, max 10 MB</span>
                        </label>
                        <input type="file" id="imageInput" name="image"
                               class="form-control form-control--file" accept="image/jpeg,image/png">
                        <img id="imagePreview" alt="Preview">
                    </div>
                </div>

                <!-- Content -->
                <div class="form-section">
                    <div class="form-section__title">Article Content</div>
                    <div class="form-group" style="margin-bottom:0">
                        <div id="editor"><?= $blog['content'] ?></div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="form-footer">
                    <button type="submit" class="btn btn--success">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <?= $editMode ? 'Save Changes' : 'Publish Article' ?>
                    </button>
                    <a href="dashboard.php" class="btn btn--ghost">Cancel</a>
                </div>

            </div>
        </form>
    </div>
</div>

<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
const quill = new Quill('#editor', {
    theme: 'snow',
    placeholder: 'Write the article content here...',
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline'],
            [{ header: 1 }, { header: 2 }],
            [{ list: 'ordered' }, { list: 'bullet' }],
            ['blockquote'],
            ['clean']
        ]
    }
});

document.getElementById('imageInput').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const preview = document.getElementById('imagePreview');
    preview.src = URL.createObjectURL(file);
    preview.style.display = 'block';
});

document.getElementById('articleForm').addEventListener('submit', function () {
    document.getElementById('contentInput').value = quill.root.innerHTML;
});
</script>
</body>
</html>
