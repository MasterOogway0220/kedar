<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
<aside class="sidebar">
    <div class="sidebar__brand">
        <div class="sidebar__brand-name">Kedar Admin</div>
        <div class="sidebar__brand-sub">Research Analyst Panel</div>
    </div>
    <nav class="sidebar__nav">
        <div class="sidebar__label">Content</div>
        <a href="dashboard.php" class="sidebar__item <?= $currentPage === 'dashboard.php' ? 'sidebar__item--active' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            All Articles
        </a>
        <a href="article-form.php" class="sidebar__item <?= $currentPage === 'article-form.php' && empty($_GET['id']) ? 'sidebar__item--active' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            New Article
        </a>
    </nav>
    <div class="sidebar__footer">
        <div class="sidebar__user">Signed in as <strong>kedar</strong></div>
        <a href="logout.php" class="sidebar__logout">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Sign out
        </a>
    </div>
</aside>
