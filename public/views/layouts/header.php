<?php
use App\Core\Auth;
use App\Models\Category;

$currentUser = Auth::user();
$categoryModel = new Category();
$navCategories = $categoryModel->getAllWithStoryCount();
?>
<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? APP_NAME) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription ?? 'Đọc truyện online hay nhất, cập nhật liên tục các thể loại tiên hiệp, kiếm hiệp, huyền huyễn, ngôn tình.') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lora:ital,wght@0,400;0,600;1,400&family=Merriweather:ital,wght@0,400;0,700;1,400&family=Roboto:ital,wght@0,400;0,500;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/assets/css/style.css">
    <?php if (isset($extraCss)): ?>
        <link rel="stylesheet" href="<?= APP_URL ?>/public/assets/css/<?= $extraCss ?>">
    <?php endif; ?>
    <script>
        window.APP_URL = "<?= APP_URL ?>";
        window.UPLOAD_URL = "<?= UPLOAD_URL ?>";
        window.IS_LOGGED_IN = <?= $currentUser ? 'true' : 'false' ?>;
        window.IS_ADMIN = <?= Auth::isAdmin() ? 'true' : 'false' ?>;
        window.CURRENT_USER_ID = <?= $currentUser ? (int)$currentUser['id'] : 'null' ?>;
    </script>
</head>
<body>

<header class="site-header">
    <div class="container header-inner">
        <!-- Logo -->
        <a href="<?= APP_URL ?>" class="brand-logo">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <span><?= APP_NAME ?></span>
        </a>

        <!-- Search Bar with Live Debounced Dropdown -->
        <div class="search-box-wrapper">
            <form action="<?= APP_URL ?>/search" method="GET">
                <svg class="search-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" id="header-search-input" name="q" class="search-input" placeholder="Tìm tên truyện, tác giả..." autocomplete="off" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            </form>
            <div id="header-search-results" class="search-results-dropdown"></div>
        </div>

        <!-- Navigation Menu -->
        <nav>
            <ul class="nav-menu">
                <li><a href="<?= APP_URL ?>" class="nav-link <?= empty($_SERVER['REQUEST_URI']) || $_SERVER['REQUEST_URI'] === '/' ? 'active' : '' ?>">Trang chủ</a></li>
                <li><a href="<?= APP_URL ?>/stories" class="nav-link">Danh sách</a></li>
                <li><a href="<?= APP_URL ?>/stories?status=updating" class="nav-link">Mới cập nhật</a></li>
                <li><a href="<?= APP_URL ?>/stories?status=completed" class="nav-link">Hoàn thành</a></li>
                <?php if ($currentUser): ?>
                    <li><a href="<?= APP_URL ?>/history" class="nav-link">Lịch sử</a></li>
                    <li><a href="<?= APP_URL ?>/following" class="nav-link">Theo dõi</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Right Header Actions -->
        <div class="header-actions">
            <!-- Dark / Light Theme Toggle -->
            <button id="theme-toggle-btn" class="btn-icon" title="Chuyển chế độ sáng/tối">
                <svg id="theme-icon" style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>

            <!-- User Authentication Dropdown -->
            <?php if ($currentUser): ?>
                <div style="position:relative;">
                    <button id="user-menu-trigger" class="btn btn-outline" style="padding:6px 12px; gap:8px;">
                        <div style="width:26px; height:26px; border-radius:50%; background:var(--primary); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.75rem;">
                            <?= strtoupper(substr($currentUser['username'], 0, 1)) ?>
                        </div>
                        <span style="font-weight:600; font-size:0.88rem;"><?= htmlspecialchars($currentUser['username']) ?></span>
                        <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="user-menu-dropdown" class="dropdown-menu">
                        <div style="padding:10px 16px; font-size:0.8rem; color:var(--text-muted); border-bottom:1px solid var(--border-color);">
                            Xin chào, <strong style="color:var(--text-primary);"><?= htmlspecialchars($currentUser['username']) ?></strong>
                        </div>
                        <?php if (Auth::isAdmin()): ?>
                            <a href="<?= APP_URL ?>/admin" class="dropdown-item" style="color:var(--accent); font-weight:700;">
                                <svg style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Admin Dashboard</span>
                            </a>
                            <div class="dropdown-divider"></div>
                        <?php endif; ?>
                        <a href="<?= APP_URL ?>/profile" class="dropdown-item">Tài khoản & Hồ sơ</a>
                        <a href="<?= APP_URL ?>/history" class="dropdown-item">Lịch sử đọc</a>
                        <a href="<?= APP_URL ?>/following" class="dropdown-item">Truyện theo dõi</a>
                        <div class="dropdown-divider"></div>
                        <button onclick="handleLogout()" class="dropdown-item" style="width:100%; border:none; background:none; cursor:pointer; color:var(--danger);">
                            Đăng xuất
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?= APP_URL ?>/login" class="btn btn-outline btn-sm">Đăng nhập</a>
                <a href="<?= APP_URL ?>/register" class="btn btn-primary btn-sm">Đăng ký</a>
            <?php endif; ?>

            <!-- Mobile Hamburger Toggle -->
            <button class="mobile-nav-toggle" aria-label="Toggle navigation">
                <svg style="width:24px;height:24px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>
</header>
