<?php
use App\Models\Story;

$query = trim($_GET['q'] ?? '');
$pageTitle = "Tìm kiếm: " . ($query ? htmlspecialchars($query) : 'Tất cả') . " - " . APP_NAME;

$storyModel = new Story();
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 18;

$result = $storyModel->listStories(['q' => $query], $page, $limit);
$stories = $result['items'];
$totalStories = $result['total'];
$totalPages = ceil($totalStories / $limit);

require_once __DIR__ . '/layouts/header.php';
?>

<main class="main-content">
    <div class="container">

        <!-- Search Header -->
        <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--radius-lg); padding:28px; margin-bottom:32px;">
            <h1 style="font-size:1.4rem; font-weight:800; margin-bottom:12px;">
                Kết quả tìm kiếm cho: <span style="color:var(--primary);">"<?= htmlspecialchars($query) ?>"</span>
            </h1>
            <p style="color:var(--text-secondary); font-size:0.9rem;">
                Tìm thấy <strong><?= number_format($totalStories) ?></strong> tác phẩm phù hợp
            </p>

            <form action="<?= APP_URL ?>/search" method="GET" style="margin-top:16px; display:flex; gap:10px; max-width:600px;">
                <input type="text" name="q" class="form-control" placeholder="Nhập tên truyện, tác giả..." value="<?= htmlspecialchars($query) ?>" required>
                <button type="submit" class="btn btn-primary">Tìm kiếm</button>
            </form>
        </div>

        <?php if (empty($stories)): ?>
            <div style="text-align:center; padding:60px 20px; background:var(--bg-secondary); border-radius:var(--radius-lg); border:1px solid var(--border-color);">
                <svg style="width:48px;height:48px;color:var(--text-muted);margin-bottom:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <h3 style="font-size:1.15rem; font-weight:700; margin-bottom:8px;">Không tìm thấy truyện nào với từ khóa trên</h3>
                <p style="color:var(--text-secondary); font-size:0.9rem;">Vui lòng thử tìm kiếm bằng từ khóa ngắn hơn hoặc kiểm tra chính tả.</p>
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
                            <span class="story-views-tag">
                                <?= number_format($story['views_count']) ?> lượt xem
                            </span>
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
                    <?php if ($page > 1): ?>
                        <a href="?q=<?= urlencode($query) ?>&page=<?= $page - 1 ?>" class="page-link">&laquo;</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?q=<?= urlencode($query) ?>&page=<?= $i ?>" class="page-link <?= ($i === $page) ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?q=<?= urlencode($query) ?>&page=<?= $page + 1 ?>" class="page-link">&raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</main>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
