<?php
$currentUri = $_SERVER['REQUEST_URI'];
?>
<aside class="admin-sidebar" style="width:260px; background:var(--bg-secondary); border-right:1px solid var(--border-color); min-height:calc(100vh - var(--header-height)); padding:24px 16px; flex-shrink:0;">
    <div style="font-weight:800; font-size:1.1rem; color:var(--primary); margin-bottom:24px; padding-left:12px; display:flex; align-items:center; gap:8px;">
        <svg style="width:22px;height:22px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
        </svg>
        <span>ADMIN PANEL</span>
    </div>

    <ul style="list-style:none; display:flex; flex-direction:column; gap:6px;">
        <li>
            <a href="<?= APP_URL ?>/admin" class="nav-link <?= str_ends_with($currentUri, '/admin') ? 'active' : '' ?>">
                <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span>Tổng quan</span>
            </a>
        </li>
        <li>
            <a href="<?= APP_URL ?>/admin/stories" class="nav-link <?= str_contains($currentUri, '/admin/stories') ? 'active' : '' ?>">
                <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span>Quản lý Truyện</span>
            </a>
        </li>
        <li>
            <a href="<?= APP_URL ?>/admin/chapters" class="nav-link <?= str_contains($currentUri, '/admin/chapters') ? 'active' : '' ?>">
                <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Quản lý Chương</span>
            </a>
        </li>
        <li>
            <a href="<?= APP_URL ?>/admin/categories" class="nav-link <?= str_contains($currentUri, '/admin/categories') ? 'active' : '' ?>">
                <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <span>Quản lý Thể loại</span>
            </a>
        </li>
        <li>
            <a href="<?= APP_URL ?>/admin/authors" class="nav-link <?= str_contains($currentUri, '/admin/authors') ? 'active' : '' ?>">
                <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>Quản lý Tác giả</span>
            </a>
        </li>
        <li>
            <a href="<?= APP_URL ?>/admin/users" class="nav-link <?= str_contains($currentUri, '/admin/users') ? 'active' : '' ?>">
                <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>Quản lý Người dùng</span>
            </a>
        </li>
        <li>
            <a href="<?= APP_URL ?>/admin/comments" class="nav-link <?= str_contains($currentUri, '/admin/comments') ? 'active' : '' ?>">
                <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <span>Quản lý Bình luận</span>
            </a>
        </li>
        <li style="margin-top:20px; border-top:1px solid var(--border-color); padding-top:16px;">
            <a href="<?= APP_URL ?>" class="nav-link" style="color:var(--text-muted);">
                <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Trở về Website</span>
            </a>
        </li>
    </ul>
</aside>
