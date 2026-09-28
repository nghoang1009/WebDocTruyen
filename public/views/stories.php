<?php
use App\Models\Story;
use App\Models\Category;
use App\Models\Author;

$pageTitle = "Danh sách truyện - " . APP_NAME;

$storyModel = new Story();
$categoryModel = new Category();
$authorModel = new Author();

$allCategories = $categoryModel->all('name ASC');
$allAuthors = $authorModel->all('name ASC');

$params = $_GET;
$page = max(1, (int)($params['page'] ?? 1));
$limit = 18;

$filters = [
    'category' => $params['category'] ?? null,
    'author'   => $params['author'] ?? null,
    'status'   => $params['status'] ?? null,
    'q'        => $params['q'] ?? null,
    'sort'     => $params['sort'] ?? 'updated'
];

$result = $storyModel->listStories($filters, $page, $limit);
$stories = $result['items'];
$totalStories = $result['total'];
$totalPages = ceil($totalStories / $limit);

require_once __DIR__ . '/layouts/header.php';
?>

<main class="main-content">
    <div class="container">

        <!-- Page Header & Filter Form -->
        <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--radius-lg); padding:24px; margin-bottom:32px;">
            <h1 style="font-size:1.5rem; font-weight:800; margin-bottom:16px;">Danh Sách Truyện</h1>

            <form action="<?= APP_URL ?>/stories" method="GET" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:14px; align-items:end;">
                <!-- Category filter -->
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Thể loại</label>
                    <select name="category" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Tất cả thể loại --</option>
                        <?php foreach ($allCategories as $cat): ?>
                            <option value="<?= $cat['slug'] ?>" <?= ($filters['category'] === $cat['slug']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Status filter -->
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="updating" <?= ($filters['status'] === 'updating') ? 'selected' : '' ?>>Đang cập nhật</option>
                        <option value="completed" <?= ($filters['status'] === 'completed') ? 'selected' : '' ?>>Hoàn thành</option>
                        <option value="paused" <?= ($filters['status'] === 'paused') ? 'selected' : '' ?>>Tạm dừng</option>
                    </select>
                </div>

                <!-- Author filter -->
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Tác giả</label>
                    <select name="author" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Tất cả tác giả --</option>
                        <?php foreach ($allAuthors as $author): ?>
                            <option value="<?= $author['slug'] ?>" <?= ($filters['author'] === $author['slug']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($author['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Sort filter -->
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Sắp xếp theo</label>
                    <select name="sort" class="form-control" onchange="this.form.submit()">
                        <option value="updated" <?= ($filters['sort'] === 'updated') ? 'selected' : '' ?>>Mới cập nhật</option>
                        <option value="views" <?= ($filters['sort'] === 'views') ? 'selected' : '' ?>>Lượt xem cao</option>
                        <option value="rating" <?= ($filters['sort'] === 'rating') ? 'selected' : '' ?>>Đánh giá cao</option>
                        <option value="name_az" <?= ($filters['sort'] === 'name_az') ? 'selected' : '' ?>>Tên truyện A - Z</option>
                        <option value="name_za" <?= ($filters['sort'] === 'name_za') ? 'selected' : '' ?>>Tên truyện Z - A</option>
                    </select>
                </div>

                <div>
                    <a href="<?= APP_URL ?>/stories" class="btn btn-outline" style="width:100%;">Đặt lại</a>
                </div>
            </form>
        </div>

        <!-- Stories Grid -->
        <?php if (empty($stories)): ?>
            <div style="text-align:center; padding:60px 20px; background:var(--bg-secondary); border-radius:var(--radius-lg); border:1px solid var(--border-color);">
                <svg style="width:48px;height:48px;color:var(--text-muted);margin-bottom:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 style="font-size:1.15rem; font-weight:700; margin-bottom:8px;">Không tìm thấy truyện phù hợp</h3>
                <p style="color:var(--text-secondary); font-size:0.9rem;">Hãy thử thay đổi bộ lọc hoặc tìm kiếm từ khóa khác.</p>
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
                        <a href="?<?= http_build_query(array_merge($params, ['page' => $page - 1])) ?>" class="page-link">&laquo;</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?<?= http_build_query(array_merge($params, ['page' => $i])) ?>" class="page-link <?= ($i === $page) ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?<?= http_build_query(array_merge($params, ['page' => $page + 1])) ?>" class="page-link">&raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</main>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
