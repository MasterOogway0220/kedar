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
    <title><?= $editMode ? 'Edit' : 'New' ?> Article — Kedar Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.7/quill.snow.css">
    <style>
        .ql-editor { min-height:280px; font-size:1rem; }
        #imagePreview { max-width:200px; max-height:150px; border-radius:6px; display:none; margin-top:8px; }
    </style>
</head>
<body class="bg-light">
<div class="container py-4" style="max-width:800px">

    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
        <h1 class="h4 fw-bold mb-0">Kedar Admin Panel</h1>
        <span class="text-muted small">Logged in as kedar &nbsp;|&nbsp;
            <a href="logout.php" class="text-danger text-decoration-none">Logout</a></span>
    </div>

    <h2 class="h5 fw-semibold mb-4"><?= $editMode ? 'Edit Article' : 'New Article' ?></h2>

    <form id="articleForm" method="POST" action="save-article.php" enctype="multipart/form-data">
        <?php if ($editMode): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($blog['id']) ?>">
            <input type="hidden" name="existing_image" value="<?= htmlspecialchars($blog['image']) ?>">
        <?php endif; ?>
        <input type="hidden" name="content" id="contentInput">

        <!-- Title -->
        <div class="mb-3">
            <label class="form-label fw-semibold" for="titleInput">Article Title</label>
            <input type="text" name="title" id="titleInput" class="form-control" required
                   value="<?= htmlspecialchars($blog['title']) ?>"
                   placeholder="e.g. शेअर बाजारातील नवीन संधी...">
        </div>

        <!-- Author -->
        <div class="mb-3">
            <label class="form-label fw-semibold" for="authorInput">Expert / Author</label>
            <input type="text" name="author" id="authorInput" class="form-control"
                   value="<?= htmlspecialchars($blog['author']) ?>">
        </div>

        <!-- Excerpt -->
        <div class="mb-3">
            <label class="form-label fw-semibold" for="excerptInput">
                Short Excerpt
                <span class="text-muted fw-normal">(preview shown on blog listing)</span>
            </label>
            <input type="text" name="excerpt" id="excerptInput" class="form-control"
                   value="<?= htmlspecialchars($blog['excerpt']) ?>"
                   placeholder="2-3 line summary of the article...">
        </div>

        <!-- Image Upload -->
        <div class="mb-3">
            <label class="form-label fw-semibold" for="imageInput">
                Article Image
                <?= $editMode ? '<span class="text-muted fw-normal">(leave empty to keep current)</span>' : '' ?>
            </label>
            <?php if ($editMode && !empty($blog['image'])): ?>
                <div class="mb-2">
                    <img src="../<?= htmlspecialchars($blog['image']) ?>"
                         style="max-height:80px; border-radius:4px;" alt="Current image">
                    <span class="text-muted small ms-2">Current image</span>
                </div>
            <?php endif; ?>
            <input type="file" name="image" id="imageInput"
                   class="form-control" accept="image/jpeg,image/png">
            <img id="imagePreview" alt="Preview">
        </div>

        <!-- OCR -->
        <div class="mb-4">
            <button type="button" id="ocrBtn" class="btn btn-outline-primary">
                🔍 Scan Image &amp; Extract Text
            </button>
            <span id="ocrStatus" class="ms-2 text-muted small"></span>
        </div>

        <!-- Quill Editor -->
        <div class="mb-4">
            <label class="form-label fw-semibold">Article Content</label>
            <div id="editor"><?= $blog['content'] ?></div>
        </div>

        <!-- Submit -->
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success px-4">✅ Save &amp; Publish</button>
            <a href="dashboard.php" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>

<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const quill = new Quill('#editor', {
    theme: 'snow',
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

// Image preview on file select
document.getElementById('imageInput').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const preview = document.getElementById('imagePreview');
    preview.src = URL.createObjectURL(file);
    preview.style.display = 'block';
});

// OCR: send image to ocr.php, populate title + content
document.getElementById('ocrBtn').addEventListener('click', async function () {
    const imageInput = document.getElementById('imageInput');
    if (!imageInput.files.length) {
        alert('Please select an article image first.');
        return;
    }
    const status = document.getElementById('ocrStatus');
    status.textContent = 'Scanning image, please wait...';
    this.disabled = true;

    const fd = new FormData();
    fd.append('image', imageInput.files[0]);

    try {
        const res  = await fetch('ocr.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.error) throw new Error(data.error);
        if (data.title)   document.getElementById('titleInput').value = data.title;
        if (data.content) quill.root.innerHTML = data.content;
        status.textContent = '✅ Text extracted successfully!';
    } catch (e) {
        status.textContent = '❌ ' + e.message;
    } finally {
        this.disabled = false;
    }
});

// Sync Quill HTML into hidden input before form submit
document.getElementById('articleForm').addEventListener('submit', function () {
    document.getElementById('contentInput').value = quill.root.innerHTML;
});
</script>
</body>
</html>
