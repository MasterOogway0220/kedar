# Admin Panel Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a password-protected PHP admin panel at `/admin/` that lets Kedar upload weekly articles (with AI OCR), storing data in `blogs-data.json` and images in `assets/article/`.

**Architecture:** Multi-page PHP panel with session-based auth. Each page is a standalone PHP file. OCR is a POST endpoint that calls Claude API and returns JSON. The save handler reads/writes `blogs-data.json` directly — no database, changes go live instantly.

**Tech Stack:** PHP (sessions, cURL, file I/O), Bootstrap 5 (CDN), Quill.js 1.3.7 (CDN), Claude API (`claude-haiku-4-5-20251001`)

---

## Key Facts (read before starting)

- JSON file path: `blogs-data.json` at project root. Structure: `{ "blogs": [...] }` (nested, not a flat array).
- Each blog entry has: `id` (e.g. `blog26`), `title`, `author`, `image` (e.g. `assets/article/26.jpeg`), `excerpt`, `content` (HTML string), `date` (YYYY-MM-DD).
- Admin folder is `admin/` at project root. Paths like `../blogs-data.json` and `../assets/article/` work from inside it.
- New articles are **prepended** (array_unshift) so they appear first on the public site.
- ID numbering: find max numeric ID in JSON, add 1. Image filename matches article number (e.g. `blog27` → `27.jpeg`).
- The public `blog-detail.html` already renders `blog.author` and `blog.date` — no frontend changes needed.
- `blogs.html` already fetches and renders from `blogs-data.json` — no changes needed there either.

---

## File Map

| File | Action | Purpose |
|------|--------|---------|
| `.gitignore` | Create | Exclude `admin/config.php` from git |
| `admin/config.php` | Create | Constants: credentials, API key, file paths |
| `admin/auth.php` | Create | `require_auth()` session guard function |
| `admin/index.php` | Create | Login form (kedar / kedar) |
| `admin/logout.php` | Create | Destroy session, redirect to login |
| `admin/dashboard.php` | Create | Article list, newest first, Edit/Delete per row |
| `admin/article-form.php` | Create | Create + edit form with Quill editor, image upload, OCR button |
| `admin/ocr.php` | Create | POST handler: image → Claude API → JSON `{title, content}` |
| `admin/save-article.php` | Create | POST handler: write image + update blogs-data.json |
| `admin/delete-article.php` | Create | POST handler: remove article from blogs-data.json |

---

## Task 1: Project Setup — .gitignore + Config + Auth

**Files:**
- Create: `.gitignore`
- Create: `admin/config.php`
- Create: `admin/auth.php`

- [ ] **Step 1: Create `.gitignore`**

```
admin/config.php
.superpowers/
```

- [ ] **Step 2: Create `admin/config.php`**

```php
<?php
define('ADMIN_USERNAME', 'kedar');
define('ADMIN_PASSWORD', 'kedar');
define('CLAUDE_API_KEY', 'YOUR_CLAUDE_API_KEY_HERE');
define('BLOGS_JSON_PATH', __DIR__ . '/../blogs-data.json');
define('ARTICLES_IMAGE_PATH', __DIR__ . '/../assets/article/');
```

- [ ] **Step 3: Create `admin/auth.php`**

```php
<?php
function require_auth(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (($_SESSION['logged_in'] ?? false) !== true) {
        header('Location: index.php');
        exit;
    }
}
```

- [ ] **Step 4: Commit**

```bash
git add .gitignore admin/auth.php
git commit -m "feat: add admin panel scaffold — gitignore and auth helper"
```

Note: `admin/config.php` is excluded by `.gitignore`. After committing, manually create it on the Hostinger server and fill in the real Claude API key.

---

## Task 2: Login Page + Logout

**Files:**
- Create: `admin/index.php`
- Create: `admin/logout.php`

- [ ] **Step 1: Create `admin/index.php`**

```php
<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Kedar Oak</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background:#f8f9fa; display:flex; align-items:center; justify-content:center; min-height:100vh; }
        .login-card { width:100%; max-width:400px; }
    </style>
</head>
<body>
<div class="login-card card shadow p-4">
    <h2 class="mb-1 fw-bold text-center">Kedar Admin</h2>
    <p class="text-muted text-center mb-4">Sign in to manage articles</p>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label fw-semibold">Username</label>
            <input type="text" name="username" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2">Login</button>
    </form>
</div>
</body>
</html>
```

- [ ] **Step 2: Create `admin/logout.php`**

```php
<?php
session_start();
session_destroy();
header('Location: index.php');
exit;
```

- [ ] **Step 3: Verify login works**

Open `yoursite.com/admin/index.php` in browser.
- Enter wrong password → should show "Invalid username or password."
- Enter `kedar` / `kedar` → should redirect to `dashboard.php` (404 is fine for now)
- Visit `admin/dashboard.php` directly without logging in → should redirect to `index.php`

- [ ] **Step 4: Commit**

```bash
git add admin/index.php admin/logout.php
git commit -m "feat: add admin login and logout pages"
```

---

## Task 3: Dashboard

**Files:**
- Create: `admin/dashboard.php`

- [ ] **Step 1: Create `admin/dashboard.php`**

```php
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
```

- [ ] **Step 2: Verify dashboard**

Log in → should see all existing articles listed, newest first (blog26 at top). Each row should show thumbnail, title, author, ID, Edit and Delete buttons.

- [ ] **Step 3: Commit**

```bash
git add admin/dashboard.php
git commit -m "feat: add admin dashboard with article list"
```

---

## Task 4: Article Form (Create + Edit UI)

**Files:**
- Create: `admin/article-form.php`

- [ ] **Step 1: Create `admin/article-form.php`**

```php
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
            <label class="form-label fw-semibold">Article Title</label>
            <input type="text" name="title" id="titleInput" class="form-control" required
                   value="<?= htmlspecialchars($blog['title']) ?>"
                   placeholder="e.g. शेअर बाजारातील नवीन संधी...">
        </div>

        <!-- Author -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Expert / Author</label>
            <input type="text" name="author" class="form-control"
                   value="<?= htmlspecialchars($blog['author']) ?>">
        </div>

        <!-- Excerpt -->
        <div class="mb-3">
            <label class="form-label fw-semibold">
                Short Excerpt
                <span class="text-muted fw-normal">(preview shown on blog listing)</span>
            </label>
            <input type="text" name="excerpt" class="form-control"
                   value="<?= htmlspecialchars($blog['excerpt']) ?>"
                   placeholder="2-3 line summary of the article...">
        </div>

        <!-- Image Upload -->
        <div class="mb-3">
            <label class="form-label fw-semibold">
                Article Image
                <?= $editMode ? '<span class="text-muted fw-normal">(leave empty to keep current)</span>' : '' ?>
            </label>
            <?php if ($editMode && !empty($blog['image'])): ?>
                <div class="mb-2">
                    <img src="../<?= htmlspecialchars($blog['image']) ?>"
                         style="max-height:80px; border-radius:4px;" alt="Current">
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
```

- [ ] **Step 2: Verify article form (create mode)**

Go to `admin/article-form.php`. Should see empty form with title, author (pre-filled "Kedar Oak"), excerpt, image upload, OCR button, Quill editor, Save button.

- [ ] **Step 3: Verify article form (edit mode)**

Click Edit on any article from dashboard → `article-form.php?id=blog26`. All fields should be pre-populated including existing image preview and content in Quill editor.

- [ ] **Step 4: Commit**

```bash
git add admin/article-form.php
git commit -m "feat: add article create/edit form with Quill editor and OCR button"
```

---

## Task 5: OCR Handler

**Files:**
- Create: `admin/ocr.php`

- [ ] **Step 1: Create `admin/ocr.php`**

```php
<?php
require_once 'config.php';
require_once 'auth.php';
require_auth();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['image']['tmp_name'])) {
    echo json_encode(['error' => 'No image provided.']);
    exit;
}

$file    = $_FILES['image'];
$allowed = ['image/jpeg', 'image/png'];
if (!in_array($file['type'], $allowed, true)) {
    echo json_encode(['error' => 'Only JPG and PNG images are accepted.']);
    exit;
}

$imageData = base64_encode(file_get_contents($file['tmp_name']));
$mediaType = $file['type'];

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
```

- [ ] **Step 2: Get a Claude API key**

Go to https://console.anthropic.com → API Keys → Create Key. Copy the key. On Hostinger, create `admin/config.php` (it is gitignored) and replace `YOUR_CLAUDE_API_KEY_HERE` with the real key.

- [ ] **Step 3: Verify OCR**

On the article form, upload a real article image and click "Scan Image & Extract Text". The title field and Quill editor should auto-fill with extracted Marathi/English text within ~10 seconds.

If it fails:
- Check that `CLAUDE_API_KEY` in `config.php` is correct
- Confirm cURL is available on Hostinger (it is enabled by default on most Hostinger plans)

- [ ] **Step 4: Commit**

```bash
git add admin/ocr.php
git commit -m "feat: add OCR handler via Claude API vision"
```

---

## Task 6: Save Handler

**Files:**
- Create: `admin/save-article.php`

- [ ] **Step 1: Create `admin/save-article.php`**

```php
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
```

- [ ] **Step 2: Verify create flow end-to-end**

On article form, fill in title, excerpt, upload image, click Save & Publish.
- Should redirect to dashboard with green "Article saved successfully!" banner.
- New article should appear at the top of the list.
- Open `blogs.html` on the public site → new article should appear first in the grid.
- Open `blog-detail.html?id=blog27` (or whatever the new ID is) → article should render with correct title, author, date, image, and content.

- [ ] **Step 3: Verify edit flow**

Click Edit on an existing article → change the title → Save & Publish.
- Dashboard should show updated title.
- Public site should reflect the change immediately (next page load).
- Article ID and date should remain unchanged.

- [ ] **Step 4: Commit**

```bash
git add admin/save-article.php
git commit -m "feat: add save handler — writes to blogs-data.json and uploads image"
```

---

## Task 7: Delete Handler

**Files:**
- Create: `admin/delete-article.php`

- [ ] **Step 1: Create `admin/delete-article.php`**

```php
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
```

- [ ] **Step 2: Verify delete flow**

Click Delete on an article → confirm dialog → should redirect to dashboard with yellow "Article deleted." banner. Article should no longer appear in the list or on the public site.

- [ ] **Step 3: Commit**

```bash
git add admin/delete-article.php
git commit -m "feat: add delete handler — removes article from blogs-data.json"
```

---

## Task 8: Final Checks + Hostinger Deployment Notes

- [ ] **Step 1: Verify session protection on all pages**

Without logging in, visit each of these URLs directly — all should redirect to `admin/index.php`:
- `admin/dashboard.php`
- `admin/article-form.php`
- `admin/save-article.php`
- `admin/delete-article.php`
- `admin/ocr.php`

- [ ] **Step 2: Verify Marathi text round-trip**

Add an article with a Marathi title and content. Check `blogs-data.json` directly — Marathi characters should be readable (not escaped as `\u0000` sequences) thanks to `JSON_UNESCAPED_UNICODE`.

- [ ] **Step 3: Note Hostinger deployment steps (for handoff)**

On Hostinger:
1. Upload all files via File Manager or FTP.
2. Manually create `admin/config.php` (it is gitignored) with correct Claude API key.
3. Ensure `blogs-data.json` and `assets/article/` are writable by PHP (chmod 664 / 755 if needed).
4. Visit `yoursite.com/admin/` → login → test full flow.

- [ ] **Step 4: Final commit**

```bash
git add -A
git status  # confirm only expected files
git commit -m "feat: complete admin panel — login, dashboard, article form, OCR, save, delete"
```
