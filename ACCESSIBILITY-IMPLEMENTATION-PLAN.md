# GIGW 3.0 / WCAG 2.1 AA Accessibility Implementation Plan
## Kesar Securities — Kedar Oak Research Analyst Website
### 50-Checkpoint Audit & Implementation Roadmap

---

## Compliance Context

**Standards:** WCAG 2.1 Level AA + GIGW 3.0 + IS 17802
**SEBI Circular:** July 31, 2025 — All Regulated Entities must comply
**Deadlines:**
- March 31, 2026 — Readiness report due
- April 30, 2026 — Full audit completed
- July 31, 2026 — All issues remediated

**Pages Audited:** index.html, contact.html, blogs.html, blog-detail.html, galler.html
**Tech Stack:** Bootstrap 5, AOS.js, Swiper, Font Awesome, PureCounter, custom CSS/JS

---

## IMPORTANT BUG FOUND DURING AUDIT

Social media icons are SWAPPED on every page — the Instagram icon (`fa-instagram`) links to LinkedIn, and the LinkedIn icon (`fa-linkedin-in`) links to Instagram. This must be fixed regardless of accessibility compliance.

---

## Checkpoint-by-Checkpoint Audit

### Legend
- PASS = Currently compliant
- FAIL = Issues found, fixes listed
- N/A = Not applicable to this site currently
- PARTIAL = Some aspects pass, others fail

---

## PRINCIPLE 1: PERCEIVABLE

### Checkpoint 1 — Text Alternatives for Non-Text Content (WCAG 1.1.1)
**Status: FAIL**

**Issues found across all 5 pages:**

**A) Decorative images have misleading alt text (should be `alt=""`):**

| File | Line | Current | Fix |
|------|------|---------|-----|
| index.html | 146 | `alt="shape icon"` | `alt=""` + `aria-hidden="true"` |
| index.html | 371-372 | `alt="shape-icon"` | `alt=""` + `aria-hidden="true"` |
| index.html | 609 | `alt="shape-icon"` | `alt=""` + `aria-hidden="true"` |
| index.html | 642-649 | `alt="shape icon"` (footer shapes) | `alt=""` + `aria-hidden="true"` |
| All pages | Footer | 4 shape images with `alt="shape icon"` | `alt=""` + `aria-hidden="true"` |
| All pages | Header shape | `alt="shape-icon"` | `alt=""` + `aria-hidden="true"` |

**B) Meaningful images have generic/unhelpful alt text:**

| File | Line | Current | Required |
|------|------|---------|----------|
| All pages | Logo | `alt="logo"` | `alt="Kedar Oak Research Analyst - Home"` |
| index.html | 109 | `alt="coin icon"` | `alt=""` (decorative) or describe purpose |
| index.html | 139 | `alt="banner-thumb"` | Descriptive alt or `alt=""` if decorative |
| index.html | 162 | `alt="about-image"` | `alt="Kedar D. Oak, SEBI Registered Research Analyst"` |
| index.html | 278,300,322,342 | `alt="Feature image"` | Describe each feature (e.g., `alt="Rich market experience illustration"`) |
| index.html | 386 | `alt="about-image"` | `alt="Portfolio review and optimization illustration"` |
| index.html | 404-416 | `alt="about-icon"` | `alt=""` (icon is decorative, text follows) |
| index.html | 487 | `alt="about-image"` | `alt="Investor education session"` |
| index.html | 501 | `alt="about-image"` | `alt="Investment philosophy illustration"` |
| contact.html | 152,165 | `alt="contact-icon"` | `alt=""` (decorative, text follows) |
| galler.html | ALL | `alt="blog Images"` (15 times!) | Each gallery photo needs unique descriptive alt |

**C) Icon-only links have NO accessible name (critical for screen readers):**

| Element | Location | Fix |
|---------|----------|-----|
| Facebook link | All pages footer + contact | Add `aria-label="Visit our Facebook page (opens in new tab)"` |
| Instagram link | All pages footer + contact | Add `aria-label="Visit our Instagram profile (opens in new tab)"` |
| LinkedIn link | All pages footer + contact | Add `aria-label="Visit our LinkedIn profile (opens in new tab)"` |
| Scroll-to-top button | All pages | Add `aria-label="Scroll to top"` |
| Mobile hamburger toggle | All pages | Add `aria-label="Open navigation menu"` + `role="button"` + `tabindex="0"` |

**D) Check icon images used as list bullets:**

| File | Lines | Fix |
|------|-------|-----|
| index.html | 472-479, 526-533 | Add `alt=""` + `aria-hidden="true"` to check.png images (decorative since list text conveys meaning) |

---

### Checkpoint 2 — Pre-recorded Audio/Video Alternatives (WCAG 1.2.1)
**Status: N/A**
No audio-only or video-only content currently on the site. If added in future, text transcripts (audio) or audio tracks/transcripts (video) are required.

### Checkpoint 3 — Captions for Pre-recorded Audio in Synchronized Media (WCAG 1.2.2)
**Status: N/A**
No synchronized media currently. If video with audio is added, closed captions are mandatory.

### Checkpoint 4 — Audio Description or Alternative for Pre-recorded Video (WCAG 1.2.3)
**Status: N/A**
Same as above. Audio description of visual content needed if video is added.

### Checkpoint 5 — Captions for Live Audio (WCAG 1.2.4)
**Status: N/A**
No live audio content. If live webinars are added to the site, real-time captions are required.

### Checkpoint 6 — Audio Description for Pre-recorded Video (WCAG 1.2.5)
**Status: N/A**
Same as Checkpoint 4.

---

### Checkpoint 7 — Info, Structure & Relationships (WCAG 1.3.1)
**Status: FAIL**

**Issues:**

**A) No semantic landmark structure:**
- `<main>` element is MISSING on all 5 pages — screen readers cannot identify the main content area
- `<nav>` element is MISSING — the menu `<ul>` is inside a generic `<div class="menu-area">` with no semantic role
- `<header>` is present (PASS)
- `<footer>` is present (PASS)

**Fix for ALL pages — add after `<header>` close:**
```html
<main id="main-content" role="main">
  <!-- all page content sections go here -->
</main>
```

**Fix for ALL pages — wrap navigation:**
```html
<nav role="navigation" aria-label="Main navigation">
  <ul class="menu menu--style1">...</ul>
</nav>
```

**B) Heading hierarchy is broken:**

| Page | Issue |
|------|-------|
| index.html | No `<h1>` tag at all. First heading is `<h2>`. Has `<h5>` and `<h6>` used for styling rather than structure. |
| contact.html | No `<h1>`. Has `<h3>` as first heading. |
| blogs.html | No `<h1>`. Has `<h2>Article</h2>` as first heading. |
| blog-detail.html | JS-generated `<h1>` for article title (GOOD). But page header has `<h2>` before `<h1>`. |
| galler.html | No `<h1>`. Has `<h2>Gallery</h2>` as first heading. |

**Fix:** Each page needs exactly ONE `<h1>`, and headings must be properly nested (no skipping levels). Add visually hidden `<h1>` if the design doesn't show it:
```html
<h1 class="visually-hidden">Kedar Oak Research Analyst - SEBI Registered</h1>
```

**C) Form fields — contact.html:**
- Labels ARE present with `for` attributes (PARTIAL PASS)
- Missing `aria-required="true"` on required fields
- Missing `role="form"` on the `<form>` element
- No `<fieldset>` and `<legend>` to group the form

**D) Tab panels (index.html):**
- Bootstrap tab panels have ARIA roles via Bootstrap (PARTIAL PASS)
- Tab container needs `aria-label="Strengths and expertise"` on the tab list

**E) Compliance dropdown menu:**
- The "Compliance" link `<a>Compliance</a>` has no `href` and no `role="button"` — it's not programmatically identifiable as a toggle
- Fix: Add `role="button"`, `aria-expanded="false"`, `aria-haspopup="true"`, `tabindex="0"`

---

### Checkpoint 8 — Meaningful Sequence / Reading Order (WCAG 1.3.2)
**Status: PARTIAL**

- DOM order generally follows visual order (PASS)
- Tab panels on index.html: hidden tab content is in DOM but not visible — Bootstrap handles this with `aria-hidden` (PASS)
- Blog detail page dynamically injects content — reading order preserved (PASS)
- **FAIL:** The `<meta http-equiv="content-type">` tag appears BEFORE `<head>` on index.html, contact.html, and galler.html — this is invalid HTML that can affect parsing

---

### Checkpoint 9 — Sensory Characteristics (WCAG 1.3.3)
**Status: PASS**
No instructions rely solely on shape, colour, size, or visual location (e.g., no "click the red button" type instructions).

---

### Checkpoint 10 — Orientation (WCAG 1.3.4)
**Status: PASS**
The site uses Bootstrap's responsive grid and does not lock to any orientation. Content works in both portrait and landscape.

---

### Checkpoint 11 — Identify Input Purpose (WCAG 1.3.5)
**Status: FAIL**

Contact form fields are MISSING `autocomplete` attributes:

| Field | Current | Required |
|-------|---------|----------|
| Name input | No autocomplete | `autocomplete="name"` |
| Email input | No autocomplete | `autocomplete="email"` |

This allows browsers and assistive technologies to auto-fill user data and display purpose icons.

---

### Checkpoint 12 — Use of Colour (WCAG 1.4.1)
**Status: PARTIAL**

- Links in navigation are distinguished by position, not just colour (PASS)
- **FAIL:** Active/current page link may only be distinguished by colour — need underline or other visual indicator
- **CHECK NEEDED:** Form error states (if implemented) must not rely on colour alone — currently no form validation exists

---

### Checkpoint 13 — Audio Control (WCAG 1.4.2)
**Status: PASS**
No auto-playing audio on any page.

---

### Checkpoint 14 — Contrast Ratio — Text (WCAG 1.4.3)
**Status: NEEDS TESTING**

Areas of concern that need contrast ratio testing (must be 4.5:1 for normal text, 3:1 for large text 18pt+):

| Area | Potential Issue |
|------|----------------|
| Banner text on index.html | White text on image/gradient background |
| Navigation links | Light text on potentially light header background |
| Form placeholder text | Grey placeholder on white/light input background (often fails) |
| Footer text/links | Check against dark footer background |
| Blog card text over images | Text overlaid on images may have insufficient contrast |
| `#00d094` green accent colour | Check against both light and dark backgrounds |

**Tool to use:** WebAIM Contrast Checker, axe DevTools, or Chrome Lighthouse

---

### Checkpoint 15 — Resize Text to 200% (WCAG 1.4.4)
**Status: NEEDS TESTING**

- The site uses Bootstrap responsive classes which generally support zoom (likely PASS)
- **Must verify:** All text remains readable and no content is cut off or overlaps at 200% browser zoom
- **Must verify:** No fixed-height containers clip text when enlarged

---

### Checkpoint 16 — Images of Text (WCAG 1.4.5)
**Status: PARTIAL**

- Most text is actual HTML text (PASS)
- **CHECK NEEDED:** Gallery images — if any contain text (event banners, certificates), the text should also be available as actual text or in alt attributes
- The logo `assets/logo.jpeg` contains text but is a logotype (EXEMPTED)

---

### Checkpoint 17 — Reflow at 320px Width (WCAG 1.4.10)
**Status: NEEDS TESTING**

- Bootstrap responsive grid should handle reflow (likely PASS)
- **Must verify:** No horizontal scrollbar appears at 320px CSS width (equivalent to 1280px at 400% zoom)
- **Must verify:** All content remains accessible without horizontal scrolling

---

### Checkpoint 18 — Non-Text Contrast (WCAG 1.4.11)
**Status: NEEDS TESTING**

UI components requiring 3:1 contrast against adjacent colours:

| Component | Check Needed |
|-----------|-------------|
| Form input borders | Border colour vs background |
| Submit button border/background | Against page background |
| Social media icon circles | Icon colour vs circle background |
| Hamburger menu bars | Against header background |
| Tab panel active indicator | Against inactive state |
| Focus indicators | Against page background (if they exist) |

---

### Checkpoint 19 — Text Spacing Override (WCAG 1.4.12)
**Status: NEEDS TESTING**

Content must remain functional when users override these CSS properties:
- Line height: 1.5x font size
- Paragraph spacing: 2x font size
- Letter spacing: 0.12x font size
- Word spacing: 0.16x font size

**Test method:** Apply a browser bookmarklet or user stylesheet with these overrides and verify no content is clipped, overlapped, or hidden.

**Potential issues:** Fixed-height containers, `overflow: hidden` on text containers, cards with strict height constraints.

---

### Checkpoint 20 — Content on Hover or Focus (WCAG 1.4.13)
**Status: FAIL**

**Compliance dropdown submenu:**
- The submenu appears on hover but:
  - **Dismissible?** Can it be closed with Escape key? → Not implemented
  - **Hoverable?** Can user move pointer into the submenu? → Likely yes via CSS
  - **Persistent?** Does it stay visible until hover/focus removed? → Likely yes

**Fix needed:**
- Add keyboard support: Escape key closes submenu
- Ensure focus can enter submenu items
- Submenu must remain visible while any item within it has focus

---

## PRINCIPLE 2: OPERABLE

### Checkpoint 21 — Keyboard Accessible (WCAG 2.1.1)
**Status: FAIL**

**Critical keyboard accessibility failures:**

| Component | Issue | Fix |
|-----------|-------|-----|
| Hamburger menu toggle (`.header-bar`) | Only responds to click. No `tabindex`, no `role`, no keyboard event. | Add `tabindex="0"`, `role="button"`, `aria-label="Toggle navigation menu"`, `aria-expanded="false"`, keydown listener for Enter/Space |
| Compliance dropdown | Parent `<a>Compliance</a>` has no `href` — not focusable on keyboard | Add `href="#"` or `tabindex="0"` + `role="button"` + keyboard events |
| Submenu links | Cannot be reached by keyboard in mobile view | JS menu toggle uses click only; add keyboard support |
| Bootstrap tabs (index.html) | Bootstrap handles Arrow key navigation (likely PASS) | Verify with keyboard testing |
| Scroll-to-top `<a href="#">` | Focusable (PASS), but needs `aria-label` | Add `aria-label="Scroll to top of page"` |
| Gallery images (galler.html) | Not interactive, no keyboard issue (PASS) | — |

---

### Checkpoint 22 — No Keyboard Trap (WCAG 2.1.2)
**Status: PARTIAL**

- No obvious keyboard traps in static content (PASS)
- **Risk:** Mobile menu (when opened) — if no mechanism to close via keyboard, focus may be trapped. Must ensure Escape key closes the menu and returns focus to the toggle button.
- **Risk:** FSlightbox (gallery lightbox) — if used on gallery images, must ensure Escape closes lightbox and Tab doesn't trap inside.

---

### Checkpoint 23 — Character Key Shortcuts (WCAG 2.1.4)
**Status: PASS**
No single-character keyboard shortcuts are implemented on the site.

---

### Checkpoint 24 — Timing Adjustable (WCAG 2.2.1)
**Status: PASS**
No time limits are set on any content or forms. No session timeouts on the static site.

---

### Checkpoint 25 — Pause, Stop, Hide — Moving/Auto-updating Content (WCAG 2.2.2)
**Status: PARTIAL**

- **AOS scroll animations:** Triggered by scroll, not auto-playing. Duration is short. (PASS)
- **PureCounter animations:** Auto-plays once on scroll into view, brief. (PASS)
- **Blog loading indicator:** Auto-disappears once loaded. (PASS)
- **POTENTIAL ISSUE:** If Swiper sliders have `autoplay: true` and run on any page with actual slides visible (blogs page dynamically loads content into a non-slider grid, so likely PASS currently)

**Action:** If any auto-playing slider or animation lasts more than 5 seconds, add a visible Pause button.

**Prefers-reduced-motion:** Add CSS to respect user preferences:
```css
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
}
```

---

### Checkpoint 26 — Three Flashes or Below Threshold (WCAG 2.3.1)
**Status: PASS**
No flashing content found on any page. Animations are smooth transitions.

---

### Checkpoint 27 — Bypass Blocks / Skip Navigation (WCAG 2.4.1)
**Status: FAIL (Critical)**

**There is NO skip navigation link on any page.** A person using a screen reader or keyboard must tab through the entire header, logo, and 5+ navigation links on every single page load before reaching content.

**Fix for ALL 5 pages — add as FIRST element inside `<body>`:**
```html
<a href="#main-content" class="skip-link">Skip to main content</a>
```

**CSS to add to style.css:**
```css
.skip-link {
  position: absolute;
  top: -50px;
  left: 0;
  background: #000;
  color: #fff;
  padding: 12px 24px;
  z-index: 100000;
  font-size: 16px;
  font-weight: 600;
  text-decoration: none;
  transition: top 0.2s ease;
  border-bottom-right-radius: 4px;
}
.skip-link:focus {
  top: 0;
  outline: 3px solid #00d094;
}
```

---

### Checkpoint 28 — Page Titles (WCAG 2.4.2)
**Status: FAIL**

| Page | Current Title | Required Title |
|------|--------------|----------------|
| index.html | `Kedar_Oak Research_Analyst` | `Kedar Oak - SEBI Registered Research Analyst` |
| contact.html | `Kedar_Oak Research_Analyst` | `Contact Us - Kedar Oak Research Analyst` |
| blogs.html | `Kedar Oak Research Analyst - Articles` | PASS (already descriptive) |
| blog-detail.html | Dynamic via JS `${blog.title} - Kedar Oak` | PASS (good implementation) |
| galler.html | `Kedar_Oak Research_Analyst` | `Gallery - Kedar Oak Research Analyst` |

---

### Checkpoint 29 — Focus Order (WCAG 2.4.3)
**Status: PARTIAL**

- DOM order generally matches visual order (PASS)
- **FAIL:** The "Compliance" dropdown `<a>` without `href` breaks tab order — keyboard users skip it entirely
- **CHECK NEEDED:** When mobile menu opens, does focus move into the menu? Does Tab cycle through menu items logically?
- **CHECK NEEDED:** Bootstrap tab panels — Arrow key focus order between tabs

---

### Checkpoint 30 — Link Purpose (WCAG 2.4.4)
**Status: FAIL**

| Link | Issue | Fix |
|------|-------|-----|
| Social media icons (all pages) | No accessible text at all. Screen reader says nothing or just "link". | Add `aria-label` to each `<a>` |
| "Read More" links (blogs.html) | Screen reader user hears multiple "Read More" links with no distinction | Change to `aria-label="Read more about [article title]"` |
| Scroll-to-top | Icon only, no text | Add `aria-label="Scroll to top of page"` |
| PDF links in Compliance dropdown | Text is descriptive (PASS) | — |
| "connect me now" button (contact) | Text is vague but contextually OK | Consider `aria-label="Submit contact form"` |

---

### Checkpoint 31 — Multiple Ways to Navigate (WCAG 2.4.5)
**Status: FAIL**

The site provides navigation via the menu bar only. Missing:
- **Search functionality** — GIGW specifically requires a Search box or link on every page
- **Sitemap page** — A dedicated HTML page listing all pages

**Fix:**
1. Add a Search box in the header (can be a simple search that searches blog articles)
2. Create `sitemap.html` with links to all pages and sections

---

### Checkpoint 32 — Headings and Labels Describe Purpose (WCAG 2.4.6)
**Status: PARTIAL**

- Section headings like "About Kedar D. Oak", "My Strengths and Expertise", "Portfolio Review & Optimization" are descriptive (PASS)
- **FAIL:** Gallery page uses `<h2>Gallery</h2>` with no further context
- **FAIL:** Contact form labels say "Name", "Email", "Message" which is acceptable but the form itself has no heading or `<legend>` explaining its purpose
- **FAIL:** `<h5>` and `<h6>` tags used for styling (feature tab items) rather than as proper headings

---

### Checkpoint 33 — Focus Visible (WCAG 2.4.7)
**Status: FAIL (Critical)**

**No visible focus indicators on interactive elements.** When tabbing through the site, users cannot see which element is currently focused. The template CSS likely uses `outline: none` or relies on browser defaults that may be insufficient.

**Fix — add to style.css:**
```css
/* Visible focus indicator for ALL interactive elements */
a:focus-visible,
button:focus-visible,
input:focus-visible,
textarea:focus-visible,
select:focus-visible,
[tabindex]:focus-visible,
.nav-link:focus-visible {
  outline: 3px solid #00d094 !important;
  outline-offset: 3px !important;
  box-shadow: 0 0 0 6px rgba(0, 208, 148, 0.25) !important;
}

/* Remove default outline ONLY when mouse-clicking (not keyboard) */
:focus:not(:focus-visible) {
  outline: none;
}
```

---

### Checkpoint 34 — Pointer Gestures (WCAG 2.5.1)
**Status: PASS**
No multipoint or path-based gestures are required. All actions use simple clicks/taps.

### Checkpoint 35 — Pointer Cancellation (WCAG 2.5.2)
**Status: PASS**
Standard click/tap behaviour is used throughout. No custom down-event actions.

### Checkpoint 36 — Label in Name (WCAG 2.5.3)
**Status: PARTIAL**

- Form labels match input accessible names (PASS)
- **FAIL:** "connect me now" button text vs accessible name — if speech recognition user says "connect me now" it should work. Currently it's a `<button>` with visible text so it matches (PASS).
- **FAIL:** Social media links have visible icon but NO accessible name — speech users cannot invoke these links

### Checkpoint 37 — Motion Actuation (WCAG 2.5.4)
**Status: PASS**
No device motion or user motion features are used.

---

## PRINCIPLE 3: UNDERSTANDABLE

### Checkpoint 38 — Language of Page (WCAG 3.1.1)
**Status: PARTIAL**

- `lang="en"` is present on all pages (PASS)
- **FAIL:** Missing `dir="ltr"` attribute on `<html>` tag (GIGW requirement)
- Fix: `<html lang="en" dir="ltr" data-bs-theme="light">`

### Checkpoint 39 — Language of Parts (WCAG 3.1.2)
**Status: PASS (currently)**
All content is in English. If Marathi or Hindi content is added (blog-detail.html uses `mr-IN` locale for date formatting), those passages must be wrapped in `<span lang="mr">` or `<span lang="hi">`.

---

### Checkpoint 40 — On Focus (WCAG 3.2.1)
**Status: PASS**
No context changes occur on focus. No auto-redirects or auto-submits.

### Checkpoint 41 — On Input (WCAG 3.2.2)
**Status: PASS**
No form elements trigger context changes on input. The contact form requires explicit button press.

### Checkpoint 42 — Consistent Navigation (WCAG 3.2.3)
**Status: PASS**
The header navigation appears in the same order on all pages. Footer is consistent.

### Checkpoint 43 — Consistent Identification (WCAG 3.2.4)
**Status: PARTIAL**

- Navigation labels are consistent across pages (PASS)
- **FAIL:** Contact page uses "Connect US" (capitalized) while other pages use "Connect Us" — inconsistent labelling
- **FAIL:** Social media icons are SWAPPED (Instagram icon → LinkedIn URL, LinkedIn icon → Instagram URL) on index.html, contact.html, and galler.html. This is both a bug and an accessibility failure — screen readers following the link would announce wrong destination.

---

### Checkpoint 44 — Error Identification (WCAG 3.3.1)
**Status: FAIL**

The contact form has NO error handling whatsoever:
- No HTML5 `required` attributes on mandatory fields
- No JavaScript validation
- No error messages displayed
- No `role="alert"` for announcing errors to screen readers

**Fix:**
```html
<input class="form-control" type="text" id="name" placeholder="Full Name"
       required aria-required="true">
<span class="error-message" id="name-error" role="alert" aria-live="polite"></span>
```

Plus JavaScript validation that populates error messages and focuses the first error field.

---

### Checkpoint 45 — Labels or Instructions (WCAG 3.3.2)
**Status: PARTIAL**

- Contact form has `<label>` elements with `for` attributes (PASS)
- **FAIL:** No instructions about required fields (e.g., "All fields are required")
- **FAIL:** No format hints for email field (e.g., "example@email.com")
- **FAIL:** Form `action=""` — empty, implying no actual submission handling

---

### Checkpoint 46 — Error Suggestion (WCAG 3.3.3)
**Status: FAIL**

No error suggestions are provided. When a user enters invalid email format, no helpful message like "Please enter a valid email address (e.g., name@example.com)" is shown.

---

### Checkpoint 47 — Error Prevention for Legal/Financial (WCAG 3.3.4)
**Status: N/A**
No financial transactions or legal commitments are made through the site. The contact form collects inquiry data only.

---

## PRINCIPLE 4: ROBUST

### Checkpoint 48 — HTML Parsing / Valid Markup (WCAG 4.1.1)
**Status: FAIL**

**Issues found:**

| Page | Issue |
|------|-------|
| index.html, contact.html, galler.html | `<meta http-equiv="content-type">` appears BEFORE `<head>` — invalid HTML |
| galler.html | Has a commented-out `<!-- Mirrored from ... -->` HTTrack comment (minor, but shows the template was scraped) |
| All pages | Need full W3C validation — likely more issues in CSS class usage, unclosed tags, etc. |
| Compliance links | `href="./TERMS & CONDITIONS .pdf"` — ampersand should be `&amp;` and space before `.pdf` may cause issues |

**Fix:**
- Move `<meta http-equiv>` inside `<head>` (or remove it since `<meta charset="UTF-8">` already handles encoding)
- Encode `&` as `&amp;` in all `href` attributes
- Run W3C HTML Validator on all pages and fix reported errors

---

### Checkpoint 49 — Name, Role, Value (WCAG 4.1.2)
**Status: FAIL**

| Component | Issue | Fix |
|-----------|-------|-----|
| Hamburger menu (`.header-bar`) | No role, no state, no name | Add `role="button"`, `aria-label="Toggle navigation menu"`, `aria-expanded="false"`, toggle `aria-expanded` on click |
| Compliance dropdown `<a>` | No role, no state | Add `role="button"`, `aria-expanded="false"`, `aria-haspopup="true"` |
| Mobile submenu | Visibility toggled via JS with `display:block/none` but no ARIA state updates | Update `aria-expanded` when toggled |
| Bootstrap tabs (index.html) | Bootstrap handles roles/states automatically (PASS) | — |
| Scroll-to-top link | No accessible name | Add `aria-label="Scroll to top of page"` |

---

### Checkpoint 50 — Status Messages (WCAG 4.1.3)
**Status: FAIL**

| Status Message | Issue | Fix |
|---------------|-------|-----|
| Blog loading "LOADING BLOGS..." (blogs.html) | Not announced to screen readers | Wrap in `<div role="status" aria-live="polite">` |
| Blog error message | Not announced | Add `role="alert"` to error container |
| Blog detail loading spinner | Not announced | Add `role="status"` + `aria-live="polite"` |
| Blog detail error ("Article not found") | Not announced | Add `role="alert"` to error container |
| Contact form submission result | No feedback exists at all | Add success/error message with `role="alert"` |

---

## GIGW 3.0 — ADDITIONAL INDIAN REQUIREMENTS

Beyond the 50 WCAG checkpoints, GIGW 3.0 requires:

### G1. Accessibility Statement Page
**Status: MISSING**

Create `accessibility.html` with:
- Conformance level targeted (WCAG 2.1 AA)
- Technologies relied upon (HTML5, CSS3, JavaScript, Bootstrap 5)
- Known limitations and alternatives
- Feedback mechanism for accessibility issues
- Contact details for the accessibility coordinator
- Date of last accessibility audit

### G2. Sitemap Page
**Status: MISSING**

Create `sitemap.html` listing all pages in a structured hierarchy.

### G3. Accessibility Toolbar
**Status: MISSING**

GIGW recommends a toolbar with:
- Text size controls (A, A+, A++)
- High contrast toggle
- Screen reader-friendly note

### G4. Search Functionality
**Status: MISSING**

GIGW requires a Search box on every page. Add to header.

### G5. PDF Accessibility
**Status: UNKNOWN — NEEDS AUDIT**

The 4 compliance PDFs must also be accessible:
- `1.Investor Charter.pdf`
- `Investor complaints data for RA.pdf`
- `MITC FORMAT.pdf`
- `TERMS & CONDITIONS .pdf`

Each PDF needs: tagged structure, reading order, alt text for images, bookmarks, language declaration.

### G6. Last Updated Date
**Status: MISSING**

Each page should display when it was last updated.

---

## IMPLEMENTATION PLAN — PRIORITIZED BY PHASE

### Phase 1: Critical Fixes (Week 1-2) — ~25 hours
*These are the highest-impact fixes that address the most severe accessibility barriers*

| # | Task | Checkpoints |
|---|------|-------------|
| 1 | Add skip navigation link to ALL 5 pages + CSS | 27 |
| 2 | Add `<main id="main-content">` to ALL 5 pages | 7 |
| 3 | Wrap navigation in `<nav role="navigation" aria-label="Main navigation">` | 7 |
| 4 | Fix ALL image alt text (decorative → `alt=""`, meaningful → descriptive) | 1 |
| 5 | Add `aria-label` to ALL social media links (and fix the icon/URL swap bug!) | 1, 30, 36 |
| 6 | Add visible focus indicators CSS | 33 |
| 7 | Fix page `<title>` on index.html, contact.html, galler.html | 28 |
| 8 | Fix heading hierarchy on all pages (add `<h1>`, correct nesting) | 7, 32 |
| 9 | Add `aria-label="Scroll to top of page"` to scroll-to-top button | 1, 30, 49 |
| 10 | Add `dir="ltr"` to `<html>` tag on all pages | 38 |
| 11 | Move `<meta http-equiv>` inside `<head>` or remove it | 48 |
| 12 | Fix `&` → `&amp;` in PDF href attributes | 48 |

### Phase 2: Keyboard & Interaction Fixes (Week 3-4) — ~30 hours

| # | Task | Checkpoints |
|---|------|-------------|
| 13 | Make hamburger menu keyboard accessible (`tabindex`, `role="button"`, `aria-expanded`, keydown) | 21, 49 |
| 14 | Make Compliance dropdown keyboard accessible (focusable, Enter/Space/Escape) | 21, 20, 49 |
| 15 | Ensure mobile submenu is keyboard navigable | 21, 22 |
| 16 | Add `autocomplete` attributes to contact form fields | 11 |
| 17 | Add `required` + `aria-required="true"` to contact form fields | 44, 45 |
| 18 | Build form validation with accessible error messages (`role="alert"`) | 44, 46 |
| 19 | Add form submission feedback (success/error status messages) | 50 |
| 20 | Add `role="status"` to blog loading indicators | 50 |
| 21 | Add `role="alert"` to blog error messages | 50 |
| 22 | Add `prefers-reduced-motion` CSS media query | 25 |
| 23 | Fix "Connect US" → "Connect Us" consistency | 43 |

### Phase 3: GIGW-Specific Additions (Week 5-6) — ~30 hours

| # | Task | Checkpoints |
|---|------|-------------|
| 24 | Create Accessibility Statement page (`accessibility.html`) | GIGW |
| 25 | Create Sitemap page (`sitemap.html`) | GIGW, 31 |
| 26 | Add Search box to header (or link to search page) | GIGW, 31 |
| 27 | Add Accessibility Toolbar (text resize A/A+/A++, high contrast) | GIGW |
| 28 | Add print stylesheet for all pages | GIGW |
| 29 | Add breadcrumb navigation to inner pages | 31 |
| 30 | Add "Last Updated" date to pages | GIGW |

### Phase 4: Testing, Audit & Certification (Week 7-8) — ~20 hours

| # | Task | Checkpoints |
|---|------|-------------|
| 31 | Run colour contrast audit on all text/backgrounds | 14, 18 |
| 32 | Test text resize to 200% on all pages | 15 |
| 33 | Test reflow at 320px width | 17 |
| 34 | Test text spacing override | 19 |
| 35 | Test full keyboard navigation on every page | 21, 22, 29, 33 |
| 36 | Test with NVDA screen reader | All |
| 37 | Run W3C HTML Validator on all pages | 48 |
| 38 | Run axe DevTools / WAVE / Lighthouse on all pages | All |
| 39 | Audit compliance PDFs for accessibility | GIGW |
| 40 | Prepare conformance report for SEBI submission | All |

---

## SUMMARY SCORECARD

| Checkpoint | Status | Priority |
|------------|--------|----------|
| 1. Text Alternatives | FAIL | Phase 1 |
| 2. Audio-only Alternatives | N/A | — |
| 3. Captions Pre-recorded | N/A | — |
| 4. Audio Description | N/A | — |
| 5. Live Captions | N/A | — |
| 6. Audio Description Extended | N/A | — |
| 7. Info & Structure | FAIL | Phase 1 |
| 8. Meaningful Sequence | PARTIAL | Phase 1 |
| 9. Sensory Characteristics | PASS | — |
| 10. Orientation | PASS | — |
| 11. Input Purpose | FAIL | Phase 2 |
| 12. Use of Colour | PARTIAL | Phase 4 |
| 13. Audio Control | PASS | — |
| 14. Contrast (Text) | NEEDS TEST | Phase 4 |
| 15. Resize Text | NEEDS TEST | Phase 4 |
| 16. Images of Text | PARTIAL | Phase 4 |
| 17. Reflow | NEEDS TEST | Phase 4 |
| 18. Non-Text Contrast | NEEDS TEST | Phase 4 |
| 19. Text Spacing | NEEDS TEST | Phase 4 |
| 20. Content on Hover/Focus | FAIL | Phase 2 |
| 21. Keyboard Accessible | FAIL | Phase 2 |
| 22. No Keyboard Trap | PARTIAL | Phase 2 |
| 23. Character Key Shortcuts | PASS | — |
| 24. Timing Adjustable | PASS | — |
| 25. Pause/Stop/Hide | PARTIAL | Phase 2 |
| 26. Three Flashes | PASS | — |
| 27. Skip Navigation | FAIL | Phase 1 |
| 28. Page Titles | FAIL | Phase 1 |
| 29. Focus Order | PARTIAL | Phase 2 |
| 30. Link Purpose | FAIL | Phase 1 |
| 31. Multiple Navigation Ways | FAIL | Phase 3 |
| 32. Headings & Labels | PARTIAL | Phase 1 |
| 33. Focus Visible | FAIL | Phase 1 |
| 34. Pointer Gestures | PASS | — |
| 35. Pointer Cancellation | PASS | — |
| 36. Label in Name | PARTIAL | Phase 1 |
| 37. Motion Actuation | PASS | — |
| 38. Language of Page | PARTIAL | Phase 1 |
| 39. Language of Parts | PASS | — |
| 40. On Focus | PASS | — |
| 41. On Input | PASS | — |
| 42. Consistent Navigation | PASS | — |
| 43. Consistent Identification | PARTIAL | Phase 2 |
| 44. Error Identification | FAIL | Phase 2 |
| 45. Labels/Instructions | PARTIAL | Phase 2 |
| 46. Error Suggestion | FAIL | Phase 2 |
| 47. Error Prevention | N/A | — |
| 48. Valid HTML Parsing | FAIL | Phase 1 |
| 49. Name, Role, Value | FAIL | Phase 2 |
| 50. Status Messages | FAIL | Phase 2 |

**Results: 14 PASS | 11 FAIL | 8 PARTIAL | 6 N/A | 6 NEEDS TEST | 5 MISSING (GIGW)**

---

## ESTIMATED TOTAL EFFORT

| Phase | Timeline | Hours |
|-------|----------|-------|
| Phase 1: Critical Fixes | Week 1-2 | ~25 hrs |
| Phase 2: Keyboard & Interaction | Week 3-4 | ~30 hrs |
| Phase 3: GIGW Additions | Week 5-6 | ~30 hrs |
| Phase 4: Testing & Certification | Week 7-8 | ~20 hrs |
| **Total** | **8 weeks** | **~105 hrs** |

---

*Plan prepared: March 29, 2026*
*Target compliance: WCAG 2.1 Level AA + GIGW 3.0 + IS 17802*
*For: Kesar Securities / Kedar Oak (SEBI Reg. No. INH000001055)*
