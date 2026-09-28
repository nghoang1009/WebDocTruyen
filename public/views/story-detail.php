<?php
use App\Models\Story;
use App\Models\Chapter;
use App\Models\Interaction;
use App\Models\Rating;
use App\Core\Auth;

$slug = $slug ?? ($_GET['slug'] ?? '');
$storyModel = new Story();
$chapterModel = new Chapter();
$ratingModel = new Rating();
$interactionModel = new Interaction();

$story = $storyModel->findBySlug($slug);
if (!$story) {
    http_response_code(404);
    require_once __DIR__ . '/404.php';
    exit;
}

$pageTitle = htmlspecialchars($story['title']) . " - " . APP_NAME;
$pageDescription = strip_tags(substr($story['description'], 0, 160));

$chapters = $chapterModel->getByStoryId($story['id'], 'ASC', true);

// User interaction state
$userId = Auth::id();
$userRating = 0;
$isFollowing = false;
$isFavorited = false;

if ($userId) {
    $userRating = $ratingModel->getUserRating($userId, $story['id']) ?? 0;
    $isFollowing = $interactionModel->isFollowing($userId, $story['id']);
    $isFavorited = $interactionModel->isFavorited($userId, $story['id']);
}

$extraJs = 'story.js';
require_once __DIR__ . '/layouts/header.php';
?>

<script>
    window.STORY_ID = <?= (int)$story['id'] ?>;
</script>

<main class="main-content">
    <div class="container">

        <!-- Story Main Info Banner -->
        <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--radius-lg); padding:32px; margin-bottom:36px;">
            <div style="display:grid; grid-template-columns:1fr; gap:32px;" class="story-detail-grid">
                <style>
                    @media (min-width: 768px) {
                        .story-detail-grid {
                            grid-template-columns: 240px 1fr !important;
                        }
                    }
                </style>

                <!-- Cover Column -->
                <div style="text-align:center;">
                    <img src="<?= UPLOAD_URL ?>/covers/<?= $story['cover_image'] ?>" onerror="this.onerror=null;this.src='<?= APP_URL ?>/public/assets/images/default-cover.jpg'" style="width:100%; max-width:240px; height:330px; object-fit:cover; border-radius:var(--radius-md); box-shadow:var(--shadow-lg); margin:0 auto;" alt="<?= htmlspecialchars($story['title']) ?>">

                    <!-- Rating Stars Widget -->
                    <div style="margin-top:16px; background:var(--bg-primary); padding:12px; border-radius:var(--radius-md); border:1px solid var(--border-color);">
                        <div style="font-size:0.85rem; font-weight:700; margin-bottom:6px; color:var(--text-secondary);">Đánh giá truyện</div>
                        <div id="star-rating-container" data-user-rating="<?= $userRating ?>" style="display:flex; justify-content:center; gap:6px; cursor:pointer;">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <button class="star-rating-btn" data-value="<?= $i ?>" style="background:none; border:none; font-size:1.4rem; color:<?= ($i <= $userRating) ? '#f59e0b' : '#64748b' ?>; cursor:pointer; padding:2px;">★</button>
                            <?php endfor; ?>
                        </div>
                        <div style="font-size:0.8rem; color:var(--text-muted); margin-top:4px;">
                            <strong id="story-rating-avg" style="color:var(--accent);"><?= number_format((float)$story['rating_avg'], 1) ?></strong>/5 (<span id="story-rating-count"><?= $story['rating_count'] ?></span> đánh giá)
                        </div>
                    </div>
                </div>

                <!-- Info Column -->
                <div>
                    <div style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:12px;">
                        <span class="story-badge <?= ($story['status'] === 'completed') ? 'completed' : '' ?>" style="position:static;">
                            <?= ($story['status'] === 'completed') ? 'Hoàn thành' : (($story['status'] === 'paused') ? 'Tạm dừng' : 'Đang cập nhật') ?>
                        </span>
                        <?php foreach ($story['categories'] as $cat): ?>
                            <a href="<?= APP_URL ?>/stories?category=<?= $cat['slug'] ?>" class="filter-tag" style="font-size:0.78rem; padding:3px 10px;">
                                <?= htmlspecialchars($cat['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <h1 style="font-size:1.85rem; font-weight:800; line-height:1.25; margin-bottom:14px;"><?= htmlspecialchars($story['title']) ?></h1>

                    <div style="display:flex; flex-wrap:wrap; gap:20px; font-size:0.9rem; color:var(--text-secondary); margin-bottom:20px; border-bottom:1px solid var(--border-color); padding-bottom:16px;">
                        <div>Tác giả: <strong style="color:var(--text-primary);"><?= htmlspecialchars($story['author_name'] ?? 'Đang cập nhật') ?></strong></div>
                        <div>Số chương: <strong style="color:var(--text-primary);"><?= count($chapters) ?></strong></div>
                        <div>Lượt xem: <strong style="color:var(--text-primary);"><?= number_format($story['views_count']) ?></strong></div>
                        <div>Cập nhật: <strong style="color:var(--text-primary);"><?= date('d/m/Y', strtotime($story['updated_at'])) ?></strong></div>
                    </div>

                    <!-- Action Buttons -->
                    <div style="display:flex; flex-wrap:wrap; gap:12px; margin-bottom:24px;">
                        <?php if (!empty($chapters)): ?>
                            <a href="<?= APP_URL ?>/stories/<?= $story['slug'] ?>/chapter/<?= $chapters[0]['slug'] ?>" class="btn btn-primary btn-lg">
                                <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                Đọc Từ Đầu
                            </a>
                            <a href="<?= APP_URL ?>/stories/<?= $story['slug'] ?>/chapter/<?= end($chapters)['slug'] ?>" class="btn btn-outline btn-lg">
                                Đọc Mới Nhất
                            </a>
                        <?php endif; ?>

                        <button id="btn-toggle-follow" class="btn <?= $isFollowing ? 'btn-outline' : 'btn-primary' ?> btn-lg">
                            <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                            </svg>
                            <span class="btn-text"><?= $isFollowing ? 'Đang theo dõi' : 'Theo dõi' ?></span>
                        </button>

                        <button id="btn-toggle-favorite" class="btn <?= $isFavorited ? 'btn-accent' : 'btn-outline' ?> btn-lg">
                            <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span class="btn-text"><?= $isFavorited ? 'Đã thích' : 'Yêu thích' ?></span>
                        </button>
                    </div>

                    <!-- Description -->
                    <div style="font-size:0.95rem; color:var(--text-secondary); line-height:1.75; max-height:220px; overflow-y:auto; padding-right:10px;">
                        <?= $story['description'] ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chapter List Section -->
        <section style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--radius-lg); padding:28px; margin-bottom:36px;">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:20px; border-bottom:1px solid var(--border-color); padding-bottom:16px;">
                <h2 style="font-size:1.3rem; font-weight:800; display:flex; align-items:center; gap:8px;">
                    <svg style="width:22px;height:22px;color:var(--primary);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    Danh Sách Chương (<?= count($chapters) ?>)
                </h2>

                <div style="display:flex; gap:10px; align-items:center;">
                    <input type="text" id="chapter-search-filter" class="form-control" placeholder="Tìm số chương..." style="width:160px; padding:6px 12px; font-size:0.85rem;">
                    <button id="btn-sort-chapters" class="btn btn-sm btn-outline">Cũ nhất trước</button>
                </div>
            </div>

            <?php if (empty($chapters)): ?>
                <div style="text-align:center; padding:30px; color:var(--text-muted);">Hiện chưa có chương nào được đăng tải.</div>
            <?php else: ?>
                <div id="chapters-grid-container" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:10px; max-height:480px; overflow-y:auto; padding-right:8px;">
                    <?php foreach ($chapters as $ch): ?>
                        <a href="<?= APP_URL ?>/stories/<?= $story['slug'] ?>/chapter/<?= $ch['slug'] ?>" class="chapter-list-item" style="padding:10px 14px; background:var(--bg-primary); border:1px solid var(--border-color); border-radius:var(--radius-sm); font-size:0.88rem; color:var(--text-secondary); display:flex; justify-content:space-between; align-items:center; transition:all 0.15s ease;">
                            <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:80%; font-weight:500;">
                                Chương <?= $ch['chapter_number'] ?>: <?= htmlspecialchars($ch['title']) ?>
                            </span>
                            <span style="font-size:0.75rem; color:var(--text-muted); flex-shrink:0;"><?= date('d/m', strtotime($ch['created_at'])) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Comments Section -->
        <section style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--radius-lg); padding:28px;">
            <h2 style="font-size:1.3rem; font-weight:800; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                <svg style="width:22px;height:22px;color:var(--primary);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                Bình Luận & Thảo Luận
            </h2>

            <!-- Add Comment Form -->
            <form id="comment-form" style="margin-bottom:28px;">
                <div class="form-group">
                    <textarea id="comment-input" class="form-control" rows="3" placeholder="Chia sẻ suy nghĩ của bạn về bộ truyện này..." required></textarea>
                </div>
                <div style="display:flex; justify-content:flex-end;">
                    <button type="submit" class="btn btn-primary">Gửi bình luận</button>
                </div>
            </form>

            <!-- Comments List -->
            <div id="comments-list-container">
                <div style="text-align:center; padding:30px; color:var(--text-muted);">Đang tải bình luận...</div>
            </div>
        </section>

    </div>
</main>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
