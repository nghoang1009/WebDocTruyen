<?php
use App\Models\Interaction;
use App\Core\Auth;

$user = Auth::requireAuth();
$pageTitle = "Truyện đang theo dõi - " . APP_NAME;

$interactionModel = new Interaction();
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 18;

$followData = $interactionModel->getFollowing($user['id'], $page, $limit);
$stories = $followData['items'];
$total = $followData['total'];
$totalPages = ceil($total / $limit);

require_once __DIR__ . '/layouts/header.php';
?>

<main class="main-content">
    <div class="container">

        <div style="margin-bottom:28px;">
            <h1 style="font-size:1.5rem; font-weight:800; display:flex; align-items:center; gap:8px;">
                <svg style="width:26px;height:26px;color:var(--primary);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                </svg>
                Truyện Đang Theo Dõi (<?= number_format($total) ?>)
            </h1>
            <p style="color:var(--text-secondary); font-size:0.88rem; margin-top:4px;">Cập nhật chương mới nhanh nhất cho các truyện yêu thích của bạn</p>
        </div>

        <?php if (empty($stories)): ?>
            <div style="text-align:center; padding:60px 20px; background:var(--bg-secondary); border-radius:var(--radius-lg); border:1px solid var(--border-color);">
                <svg style="width:48px;height:48px;color:var(--text-muted);margin-bottom:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                </svg>
                <h3 style="font-size:1.15rem; font-weight:700; margin-bottom:8px;">Bạn chưa theo dõi truyện nào</h3>
                <p style="color:var(--text-secondary); font-size:0.9rem; margin-bottom:20px;">Hãy bấm nút "Theo dõi" ở trang truyện để nhận thông báo chương mới nhất.</p>
                <a href="<?= APP_URL ?>/stories" class="btn btn-primary">Khám phá truyện</a>
            </div>
        <?php else: ?>
            <div class="story-grid">
                <?php foreach ($stories as $story): ?>
                    <div class="story-card">
                        <a href="<?= APP_URL ?>/stories/<?= $story['slug'] ?>" class="story-card-cover-wrap">
                            <img src="<?= UPLOAD_URL ?>/covers/<?= $story['cover_image'] ?>" onerror="this.onerror=null;this.src='<?= APP_URL ?>/public/assets/images/default-cover.jpg'" class="story-card-cover" alt="<?= htmlspecialchars($story['title']) ?>" loading="lazy">
                            <?php if ($story['status'] === 'completed'): ?>
                                <span class="story-badge completed">Full</span>
                            <?php endif; ?>
                            <span class="story-views-tag"><?= number_format($story['views_count']) ?> lượt xem</span>
                        </a>
                        <div class="story-card-body">
                            <a href="<?= APP_URL ?>/stories/<?= $story['slug'] ?>" class="story-card-title" title="<?= htmlspecialchars($story['title']) ?>">
                                <?= htmlspecialchars($story['title']) ?>
                            </a>
                            <div class="story-card-author"><?= htmlspecialchars($story['author_name'] ?? 'Đang cập nhật') ?></div>
                            <div class="story-card-footer">
                                <?php if ($story['latest_chapter']): ?>
                                    <a href="<?= APP_URL ?>/stories/<?= $story['slug'] ?>/chapter/<?= $story['latest_chapter']['slug'] ?>" class="story-card-chapter">
                                        C.<?= $story['latest_chapter']['chapter_number'] ?>
                                    </a>
                                <?php else: ?>
                                    <span style="color:var(--text-muted);">Chưa có</span>
                                <?php endif; ?>
                                <div class="story-rating-box">★ <?= number_format((float)$story['rating_avg'], 1) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>" class="page-link <?= ($i === $page) ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</main>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
