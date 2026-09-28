<?php
use App\Models\Story;
use App\Models\Category;
use App\Models\Interaction;
use App\Core\Auth;

$pageTitle = "Trang chủ - " . APP_NAME;
$storyModel = new Story();
$categoryModel = new Category();

// Fetch stories for sections
$updatedStories = $storyModel->listStories(['sort' => 'updated'], 1, 6)['items'];
$popularStories = $storyModel->listStories(['sort' => 'views'], 1, 6)['items'];
$topRatedStories = $storyModel->listStories(['sort' => 'rating'], 1, 6)['items'];
$completedStories = $storyModel->listStories(['status' => 'completed'], 1, 6)['items'];
$allCategories = $categoryModel->all('name ASC');

// Hero featured story (First top story)
$heroStory = $popularStories[0] ?? null;

// User reading history if logged in
$readingHistory = [];
$userId = Auth::id();
if ($userId) {
    $interactionModel = new Interaction();
    $historyData = $interactionModel->getHistory($userId, 1, 4);
    $readingHistory = $historyData['items'];
}

require_once __DIR__ . '/layouts/header.php';
?>

<main class="main-content">
    <div class="container">

        <!-- Hero Featured Banner -->
        <?php if ($heroStory): ?>
            <div class="hero-banner">
                <div class="hero-grid">
                    <div>
                        <div class="hero-badge">
                            <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                            Truyện Nổi Bật Đang Hot
                        </div>
                        <h1 class="hero-title"><?= htmlspecialchars($heroStory['title']) ?></h1>
                        <p class="hero-desc"><?= strip_tags($heroStory['description']) ?></p>
                        <div class="hero-actions">
                            <a href="<?= APP_URL ?>/stories/<?= $heroStory['slug'] ?>" class="btn btn-primary btn-lg">
                                <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                Đọc Ngay
                            </a>
                            <a href="<?= APP_URL ?>/stories/<?= $heroStory['slug'] ?>" class="btn btn-outline btn-lg">
                                Thông Tin Chi Tiết
                            </a>
                        </div>
                    </div>
                    <div style="text-align:center;">
                        <a href="<?= APP_URL ?>/stories/<?= $heroStory['slug'] ?>">
                            <img src="<?= UPLOAD_URL ?>/covers/<?= $heroStory['cover_image'] ?>" onerror="this.onerror=null;this.src='<?= APP_URL ?>/public/assets/images/default-cover.jpg'" class="hero-cover" alt="<?= htmlspecialchars($heroStory['title']) ?>">
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Category Filter Tags Row -->
        <div class="filter-tags-scroll">
            <a href="<?= APP_URL ?>/stories" class="filter-tag active">Tất cả</a>
            <?php foreach ($allCategories as $cat): ?>
                <a href="<?= APP_URL ?>/stories?category=<?= $cat['slug'] ?>" class="filter-tag">
                    <?= htmlspecialchars($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Section: Đang đọc gần đây (Nếu có) -->
        <?php if (!empty($readingHistory)): ?>
            <section style="margin-bottom: 48px;">
                <div class="section-header">
                    <h2 class="section-title">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Tiếp Tục Đọc
                    </h2>
                    <a href="<?= APP_URL ?>/history" class="section-more">Xem tất cả lịch sử &rarr;</a>
                </div>
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                    <?php foreach ($readingHistory as $item): ?>
                        <div style="display:flex; gap:14px; background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--radius-md); padding:12px; align-items:center;">
                            <img src="<?= UPLOAD_URL ?>/covers/<?= $item['cover_image'] ?>" onerror="this.onerror=null;this.src='<?= APP_URL ?>/public/assets/images/default-cover.jpg'" style="width:50px; height:70px; object-fit:cover; border-radius:var(--radius-sm);" alt="<?= htmlspecialchars($item['story_title']) ?>">
                            <div style="flex:1; overflow:hidden;">
                                <a href="<?= APP_URL ?>/stories/<?= $item['story_slug'] ?>" style="font-weight:700; font-size:0.92rem; color:var(--text-primary); display:block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    <?= htmlspecialchars($item['story_title']) ?>
                                </a>
                                <div style="font-size:0.8rem; color:var(--text-muted); margin:3px 0;">Đang đọc: Chương <?= $item['chapter_number'] ?> (<?= $item['progress_percent'] ?>%)</div>
                                <a href="<?= APP_URL ?>/stories/<?= $item['story_slug'] ?>/chapter/<?= $item['chapter_slug'] ?>" class="btn btn-sm btn-primary" style="padding:3px 10px; font-size:0.75rem;">
                                    Đọc tiếp
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- Section: Truyện Mới Cập Nhật -->
        <section style="margin-bottom: 48px;">
            <div class="section-header">
                <h2 class="section-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Truyện Mới Cập Nhật
                </h2>
                <a href="<?= APP_URL ?>/stories?sort=updated" class="section-more">Xem thêm &rarr;</a>
            </div>
            <div class="story-grid">
                <?php foreach ($updatedStories as $story): ?>
                    <div class="story-card">
                        <a href="<?= APP_URL ?>/stories/<?= $story['slug'] ?>" class="story-card-cover-wrap">
                            <img src="<?= UPLOAD_URL ?>/covers/<?= $story['cover_image'] ?>" onerror="this.onerror=null;this.src='<?= APP_URL ?>/public/assets/images/default-cover.jpg'" class="story-card-cover" alt="<?= htmlspecialchars($story['title']) ?>" loading="lazy">
                            <?php if ($story['status'] === 'completed'): ?>
                                <span class="story-badge completed">Full</span>
                            <?php endif; ?>
                            <span class="story-views-tag">
                                <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <?= number_format($story['views_count']) ?>
                            </span>
                        </a>
                        <div class="story-card-body">
                            <a href="<?= APP_URL ?>/stories/<?= $story['slug'] ?>" class="story-card-title" title="<?= htmlspecialchars($story['title']) ?>">
                                <?= htmlspecialchars($story['title']) ?>
                            </a>
                            <div class="story-card-author"><?= htmlspecialchars($story['author_name'] ?? 'Đang cập nhật') ?></div>
                            <div class="story-card-footer">
                                <?php if ($story['latest_chapter']): ?>
                                    <a href="<?= APP_URL ?>/stories/<?= $story['slug'] ?>/chapter/<?= $story['latest_chapter']['slug'] ?>" class="story-card-chapter" title="Chương <?= $story['latest_chapter']['chapter_number'] ?>">
                                        C.<?= $story['latest_chapter']['chapter_number'] ?>
                                    </a>
                                <?php else: ?>
                                    <span style="color:var(--text-muted);">Chưa có</span>
                                <?php endif; ?>
                                <div class="story-rating-box">
                                    ★ <?= number_format((float)$story['rating_avg'], 1) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Section: Truyện Phổ Biến / Hot -->
        <section style="margin-bottom: 48px;">
            <div class="section-header">
                <h2 class="section-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                    </svg>
                    Truyện Đọc Nhiều Nhất
                </h2>
                <a href="<?= APP_URL ?>/stories?sort=views" class="section-more">Xem thêm &rarr;</a>
            </div>
            <div class="story-grid">
                <?php foreach ($popularStories as $story): ?>
                    <div class="story-card">
                        <a href="<?= APP_URL ?>/stories/<?= $story['slug'] ?>" class="story-card-cover-wrap">
                            <img src="<?= UPLOAD_URL ?>/covers/<?= $story['cover_image'] ?>" onerror="this.onerror=null;this.src='<?= APP_URL ?>/public/assets/images/default-cover.jpg'" class="story-card-cover" alt="<?= htmlspecialchars($story['title']) ?>" loading="lazy">
                            <span class="story-badge hot">HOT</span>
                            <span class="story-views-tag"><?= number_format($story['views_count']) ?></span>
                        </a>
                        <div class="story-card-body">
                            <a href="<?= APP_URL ?>/stories/<?= $story['slug'] ?>" class="story-card-title">
                                <?= htmlspecialchars($story['title']) ?>
                            </a>
                            <div class="story-card-author"><?= htmlspecialchars($story['author_name'] ?? 'Đang cập nhật') ?></div>
                            <div class="story-card-footer">
                                <?php if ($story['latest_chapter']): ?>
                                    <a href="<?= APP_URL ?>/stories/<?= $story['slug'] ?>/chapter/<?= $story['latest_chapter']['slug'] ?>" class="story-card-chapter">
                                        C.<?= $story['latest_chapter']['chapter_number'] ?>
                                    </a>
                                <?php endif; ?>
                                <div class="story-rating-box">★ <?= number_format((float)$story['rating_avg'], 1) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Section: Truyện Hoàn Thành -->
        <section style="margin-bottom: 48px;">
            <div class="section-header">
                <h2 class="section-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Truyện Đã Hoàn Thành (Full)
                </h2>
                <a href="<?= APP_URL ?>/stories?status=completed" class="section-more">Xem thêm &rarr;</a>
            </div>
            <div class="story-grid">
                <?php foreach ($completedStories as $story): ?>
                    <div class="story-card">
                        <a href="<?= APP_URL ?>/stories/<?= $story['slug'] ?>" class="story-card-cover-wrap">
                            <img src="<?= UPLOAD_URL ?>/covers/<?= $story['cover_image'] ?>" onerror="this.onerror=null;this.src='<?= APP_URL ?>/public/assets/images/default-cover.jpg'" class="story-card-cover" alt="<?= htmlspecialchars($story['title']) ?>" loading="lazy">
                            <span class="story-badge completed">Full</span>
                        </a>
                        <div class="story-card-body">
                            <a href="<?= APP_URL ?>/stories/<?= $story['slug'] ?>" class="story-card-title">
                                <?= htmlspecialchars($story['title']) ?>
                            </a>
                            <div class="story-card-author"><?= htmlspecialchars($story['author_name'] ?? 'Đang cập nhật') ?></div>
                            <div class="story-card-footer">
                                <?php if ($story['latest_chapter']): ?>
                                    <a href="<?= APP_URL ?>/stories/<?= $story['slug'] ?>/chapter/<?= $story['latest_chapter']['slug'] ?>" class="story-card-chapter">
                                        C.<?= $story['latest_chapter']['chapter_number'] ?>
                                    </a>
                                <?php endif; ?>
                                <div class="story-rating-box">★ <?= number_format((float)$story['rating_avg'], 1) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

    </div>
</main>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
