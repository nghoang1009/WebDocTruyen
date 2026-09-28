<?php
use App\Models\Chapter;
use App\Models\Story;
use App\Core\Auth;

$storySlug = $storySlug ?? ($_GET['storySlug'] ?? '');
$chapterSlug = $chapterSlug ?? ($_GET['chapterSlug'] ?? '');

$chapterModel = new Chapter();
$chapter = $chapterModel->getChapterForReading($storySlug, $chapterSlug);

if (!$chapter) {
    http_response_code(404);
    require_once __DIR__ . '/404.php';
    exit;
}

$pageTitle = "Chương " . $chapter['chapter_number'] . ": " . htmlspecialchars($chapter['title']) . " - " . htmlspecialchars($chapter['story_title']);
$pageDescription = "Đọc truyện " . htmlspecialchars($chapter['story_title']) . " chương " . $chapter['chapter_number'] . " online.";

// Track view & history
$storyModel = new Story();
$storyModel->incrementViews($chapter['story_id'], $chapter['id']);

$extraCss = 'reader.css';
$extraJs = 'reader.js';

require_once __DIR__ . '/layouts/header.php';
?>

<!-- Reading Progress Bar -->
<div id="reading-progress-bar" class="reading-progress-bar"></div>

<script>
    window.READER_DATA = {
        storyId: <?= (int)$chapter['story_id'] ?>,
        chapterId: <?= (int)$chapter['id'] ?>,
        storySlug: "<?= htmlspecialchars($chapter['story_slug']) ?>",
        chapterSlug: "<?= htmlspecialchars($chapter['slug']) ?>"
    };
</script>

<div class="reader-page">

    <!-- Reader Controls Bar -->
    <div class="reader-controls-bar">
        <!-- Back to Story -->
        <a href="<?= APP_URL ?>/stories/<?= $chapter['story_slug'] ?>" class="btn btn-sm btn-outline" title="Về trang giới thiệu truyện">
            <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span style="display:none; @media(min-width:640px){display:inline;}">Truyện</span>
        </a>

        <!-- Prev Chapter -->
        <?php if ($chapter['prev']): ?>
            <a id="prev-chapter-btn" href="<?= APP_URL ?>/stories/<?= $chapter['story_slug'] ?>/chapter/<?= $chapter['prev']['slug'] ?>" class="btn btn-sm btn-primary" title="Phím tắt: Mũi tên trái">
                &larr; <span style="display:none; @media(min-width:640px){display:inline;}">Chương trước</span>
            </a>
        <?php else: ?>
            <button class="btn btn-sm btn-outline" disabled style="opacity:0.5; cursor:not-allowed;">&larr;</button>
        <?php endif; ?>

        <!-- Chapter Selector Dropdown -->
        <select class="chapter-select">
            <?php foreach ($chapter['all_chapters'] as $ch): ?>
                <option value="<?= APP_URL ?>/stories/<?= $chapter['story_slug'] ?>/chapter/<?= $ch['slug'] ?>" <?= ((int)$ch['id'] === (int)$chapter['id']) ? 'selected' : '' ?>>
                    Chương <?= $ch['chapter_number'] ?>: <?= htmlspecialchars($ch['title']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Next Chapter -->
        <?php if ($chapter['next']): ?>
            <a id="next-chapter-btn" href="<?= APP_URL ?>/stories/<?= $chapter['story_slug'] ?>/chapter/<?= $chapter['next']['slug'] ?>" class="btn btn-sm btn-primary" title="Phím tắt: Mũi tên phải">
                <span style="display:none; @media(min-width:640px){display:inline;}">Chương sau</span> &rarr;
            </a>
        <?php else: ?>
            <button class="btn btn-sm btn-outline" disabled style="opacity:0.5; cursor:not-allowed;">&rarr;</button>
        <?php endif; ?>

        <!-- Reader Settings Toggle Button -->
        <button id="reader-settings-toggle" class="btn btn-sm btn-outline" title="Tùy chỉnh đọc">
            <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
            </svg>
        </button>
    </div>

    <!-- Reader Settings Drawer -->
    <div id="reader-settings-panel" class="reader-settings-panel">
        <div style="font-weight:700; font-size:1rem; margin-bottom:16px; border-bottom:1px solid var(--border-color); padding-bottom:8px;">
            Cài đặt giao diện đọc
        </div>

        <!-- Theme Choice -->
        <div class="settings-row">
            <span class="settings-label">Màu nền</span>
            <div style="display:flex; gap:10px;">
                <div class="theme-circle circle-dark" data-theme="theme-reader-dark" title="Tối (Mặc định)"></div>
                <div class="theme-circle circle-light" data-theme="theme-reader-light" title="Sáng"></div>
                <div class="theme-circle circle-sepia" data-theme="theme-reader-sepia" title="Sepia / Vàng ấm"></div>
                <div class="theme-circle circle-night" data-theme="theme-reader-night" title="Đen tuyền (OLED)"></div>
            </div>
        </div>

        <!-- Font Family -->
        <div class="settings-row">
            <span class="settings-label">Phông chữ</span>
            <select id="reader-font-select" class="form-control" style="width:140px; padding:4px 8px; font-size:0.85rem;">
                <option value="font-sans-serif">Sans-serif</option>
                <option value="font-roboto">Roboto</option>
                <option value="font-merriweather">Merriweather</option>
                <option value="font-lora">Lora Serif</option>
            </select>
        </div>

        <!-- Font Size -->
        <div class="settings-row">
            <span class="settings-label">Cỡ chữ</span>
            <div class="settings-btn-group">
                <button id="btn-font-dec" class="settings-btn">-</button>
                <span id="display-font-size" style="font-size:0.85rem; font-weight:700; min-width:40px; text-align:center;">18px</span>
                <button id="btn-font-inc" class="settings-btn">+</button>
            </div>
        </div>

        <!-- Line Height -->
        <div class="settings-row">
            <span class="settings-label">Dãn dòng</span>
            <div class="settings-btn-group">
                <button id="btn-line-dec" class="settings-btn">-</button>
                <span id="display-line-height" style="font-size:0.85rem; font-weight:700; min-width:40px; text-align:center;">1.8</span>
                <button id="btn-line-inc" class="settings-btn">+</button>
            </div>
        </div>

        <!-- Reader Container Width -->
        <div class="settings-row" style="margin-bottom:0;">
            <span class="settings-label">Khung đọc</span>
            <div class="settings-btn-group">
                <button class="settings-btn btn-width-select" data-width="reader-width-small">Hẹp</button>
                <button class="settings-btn btn-width-select" data-width="reader-width-medium">Vừa</button>
                <button class="settings-btn btn-width-select" data-width="reader-width-large">Rộng</button>
            </div>
        </div>
    </div>

    <!-- Main Reading Area Container -->
    <main class="reader-container reader-width-medium">

        <!-- Chapter Heading -->
        <header class="reader-chapter-title-heading">
            <a href="<?= APP_URL ?>/stories/<?= $chapter['story_slug'] ?>" style="font-size:0.95rem; font-weight:600; color:var(--primary); text-transform:uppercase; letter-spacing:0.5px; display:inline-block; margin-bottom:8px;">
                <?= htmlspecialchars($chapter['story_title']) ?>
            </a>
            <h1>Chương <?= $chapter['chapter_number'] ?>: <?= htmlspecialchars($chapter['title']) ?></h1>
            <div class="reader-meta">
                Cập nhật: <?= date('d/m/Y H:i', strtotime($chapter['created_at'])) ?> &bull; Lượt xem: <?= number_format($chapter['views_count'] + 1) ?>
            </div>
        </header>

        <!-- Chapter Text Content -->
        <article class="reader-content font-sans-serif" style="font-size: 18px; line-height: 1.8;">
            <?= $chapter['content'] ?>
        </article>

        <!-- Bottom Navigation Buttons -->
        <div class="reader-footer-nav">
            <?php if ($chapter['prev']): ?>
                <a href="<?= APP_URL ?>/stories/<?= $chapter['story_slug'] ?>/chapter/<?= $chapter['prev']['slug'] ?>" class="btn btn-primary btn-lg">
                    &larr; Chương trước
                </a>
            <?php endif; ?>

            <select class="chapter-select" style="max-width:200px;">
                <?php foreach ($chapter['all_chapters'] as $ch): ?>
                    <option value="<?= APP_URL ?>/stories/<?= $chapter['story_slug'] ?>/chapter/<?= $ch['slug'] ?>" <?= ((int)$ch['id'] === (int)$chapter['id']) ? 'selected' : '' ?>>
                        Chương <?= $ch['chapter_number'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if ($chapter['next']): ?>
                <a href="<?= APP_URL ?>/stories/<?= $chapter['story_slug'] ?>/chapter/<?= $chapter['next']['slug'] ?>" class="btn btn-primary btn-lg">
                    Chương sau &rarr;
                </a>
            <?php endif; ?>
        </div>

    </main>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
