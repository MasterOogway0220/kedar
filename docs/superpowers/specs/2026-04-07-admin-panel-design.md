# Admin Panel Design Spec
**Date:** 2026-04-07  
**Project:** Kedar Oak Research Analyst Website  
**Feature:** Weekly Article Upload Admin Panel

---

## Overview

A password-protected PHP admin panel hosted in an `admin/` folder on Hostinger. Allows Kedar Oak to upload weekly articles, with AI-powered OCR to extract text from article images. Articles are stored in `blogs-data.json` and images in `assets/article/`. No database required. Changes are live on the public website instantly after saving.

---

## Architecture

- **Hosting:** Hostinger shared hosting (PHP support available)
- **Storage:** `blogs-data.json` at project root (existing file, no schema changes except adding `expert` field)
- **Images:** `assets/article/` — auto-numbered (e.g., `27.jpeg`, `28.jpeg`)
- **Auth:** PHP session-based, single user (`kedar` / `kedar`)
- **OCR:** Claude API (vision) called from PHP via HTTP — extracts title + content from uploaded image
- **Editor:** Quill.js WYSIWYG — handles Marathi (Devanagari) and English
- **Article ordering:** New articles prepended to top of `blogs-data.json` array so they appear first on the public site

---

## JSON Data Structure

New articles will follow this structure (adds `expert` field to existing schema):

```json
{
  "id": "blog27",
  "title": "शेअर बाजारातील नवीन संधी",
  "expert": "Kedar Oak",
  "image": "assets/article/27.jpeg",
  "excerpt": "Short 2-3 line summary shown on the blog listing page.",
  "content": "<p>Full article HTML content...</p>"
}
```

**ID generation:** Find the highest existing numeric ID in the JSON array, increment by 1.  
**Image naming:** Matches the article number (e.g., `blog27` → `27.jpeg`).  
**Expert field:** Pre-filled with "Kedar Oak", editable if needed.

---

## Pages & Files

```
admin/
├── index.php           Login page (kedar / kedar)
├── dashboard.php       Article list — newest first, Edit + Delete per row
├── article-form.php    Create or edit article (shared form, ?id= param for edit mode)
├── save-article.php    POST handler — writes to blogs-data.json, saves image
├── delete-article.php  POST handler — removes article from blogs-data.json
├── ocr.php             POST handler — receives image, calls Claude API, returns text
└── logout.php          Clears session, redirects to login
```

---

## Page Designs

### Login (`admin/index.php`)
- Simple centered form: Username + Password fields + Login button
- On success: redirects to `dashboard.php`
- On failure: shows inline error message
- All other admin pages redirect to login if session is not set

### Dashboard (`admin/dashboard.php`)
- Header: "Kedar Admin Panel" + "Logged in as kedar | Logout"
- "Add New Article" button (top right) → links to `article-form.php`
- Table of all articles, newest first:
  - Thumbnail (56×44px)
  - Title (truncated if long)
  - Expert name + Article ID (subtitle)
  - Edit button → `article-form.php?id=blog27`
  - Delete button → POST to `delete-article.php` (confirm before submitting)

### Article Form (`admin/article-form.php`)
Handles both **create** (no `?id`) and **edit** (`?id=blog27`) modes.

Fields:
1. **Title** — text input, auto-filled by OCR
2. **Expert** — text input, pre-filled "Kedar Oak"
3. **Excerpt** — text input, short summary
4. **Article Image** — file upload (JPG/PNG), shows preview thumbnail after selection
5. **Scan Image & Extract Text** button — calls `ocr.php`, populates Title + Content
6. **Article Content** — Quill.js editor (toolbar: Bold, Italic, Underline, H1, H2, List, Blockquote)
7. **Save & Publish** button → POST to `save-article.php`
8. **Cancel** button → back to dashboard

### Save Handler (`admin/save-article.php`)
- Validates session
- Reads uploaded image → saves to `assets/article/{n}.jpeg`
- Reads `blogs-data.json`
- **Create mode:** builds new article object, prepends to array
- **Edit mode:** finds article by ID, updates fields in place (image only replaced if new file uploaded)
- Writes updated array back to `blogs-data.json`
- Redirects to dashboard with success message

### Delete Handler (`admin/delete-article.php`)
- Validates session + POST request
- Reads `blogs-data.json`, filters out article by ID
- Writes updated array back to file
- Does NOT delete the image file (kept as backup)
- Redirects to dashboard

### OCR Handler (`admin/ocr.php`)
- Accepts POST with image file
- Base64-encodes the image
- Sends to Claude API (`claude-haiku-4-5-20251001`) with vision prompt:
  > "Extract all text from this article image. Return a JSON object with two fields: 'title' (the article headline/title) and 'content' (the full body text formatted as HTML paragraphs using `<p>` tags). Preserve the original language — Marathi or English."
- Returns JSON `{ "title": "...", "content": "<p>...</p>" }` to the browser
- JavaScript on `article-form.php` reads the response and populates the Title field and Quill editor

---

## Security

- All admin pages check `$_SESSION['logged_in'] === true` and redirect if not set
- Login compares against hardcoded credentials (username: `kedar`, password: `kedar`)
- File uploads validated: only JPG/PNG accepted, max size enforced
- `blogs-data.json` write uses `file_put_contents` with `LOCK_EX` flag to prevent race conditions
- Claude API key stored in a `admin/config.php` file (not publicly accessible)

---

## Frontend Impact

- **No changes to `blogs.html` or `blog-detail.html`** — they already read from `blogs-data.json` via `fetch()`
- **One addition:** the `expert` field should be displayed on the blog detail page. This is a small update to `blog-detail.html` to render the expert name.
- The public website updates instantly — no redeployment needed

---

## Out of Scope

- Draft / scheduled publishing (articles publish immediately on save)
- Multiple admin users
- Image resizing or compression
- Search within admin panel
- Article categories or tags
