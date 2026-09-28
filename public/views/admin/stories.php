<?php
use App\Core\Auth;
use App\Models\Story;
use App\Models\Author;
use App\Models\Category;

Auth::requireAdmin();
$pageTitle = "Quản lý Truyện - " . APP_NAME;

$storyModel = new Story();
$authorModel = new Author();
$categoryModel = new Category();

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$result = $storyModel->listStories($_GET, $page, $limit);
$stories = $result['items'];
$total = $result['total'];
$totalPages = ceil($total / $limit);

$authors = $authorModel->all('name ASC');
$categories = $categoryModel->all('name ASC');

$extraJs = 'admin.js';
require_once __DIR__ . '/../layouts/header.php';
?>

<div style="display:flex; min-height:calc(100vh - var(--header-height));">
    <?php require_once __DIR__ . '/../layouts/admin-sidebar.php'; ?>

    <main style="flex:1; padding:32px; background:var(--bg-primary); overflow-x:hidden;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; flex-wrap:wrap; gap:16px;">
            <div>
                <h1 style="font-size:1.6rem; font-weight:800;">Quản Lý Truyện (<?= number_format($total) ?>)</h1>
                <p style="color:var(--text-secondary); font-size:0.88rem; margin-top:4px;">Thêm mới, chỉnh sửa thông tin tác phẩm và quản lý chương</p>
            </div>
            <button onclick="openCreateStoryModal()" class="btn btn-primary">
                + Thêm Truyện Mới
            </button>
        </div>

        <!-- Stories Table -->
        <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--radius-lg); overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; text-align:left; font-size:0.9rem;">
                <thead>
                    <tr style="border-bottom:1px solid var(--border-color); background:var(--bg-tertiary); color:var(--text-secondary);">
                        <th style="padding:14px 16px;">Bìa</th>
                        <th style="padding:14px 16px;">Tên truyện</th>
                        <th style="padding:14px 16px;">Tác giả</th>
                        <th style="padding:14px 16px;">Thể loại</th>
                        <th style="padding:14px 16px;">Trạng thái</th>
                        <th style="padding:14px 16px;">Lượt xem</th>
                        <th style="padding:14px 16px; text-align:right;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stories as $story): ?>
                        <tr style="border-bottom:1px solid var(--border-color);">
                            <td style="padding:12px 16px;">
                                <img src="<?= UPLOAD_URL ?>/covers/<?= $story['cover_image'] ?>" onerror="this.onerror=null;this.src='<?= APP_URL ?>/public/assets/images/default-cover.jpg'" style="width:40px; height:55px; object-fit:cover; border-radius:4px;" alt="">
                            </td>
                            <td style="padding:12px 16px;">
                                <a href="<?= APP_URL ?>/stories/<?= $story['slug'] ?>" target="_blank" style="font-weight:700; color:var(--text-primary);">
                                    <?= htmlspecialchars($story['title']) ?>
                                </a>
                                <div style="font-size:0.75rem; color:var(--text-muted);">Slug: <?= htmlspecialchars($story['slug']) ?></div>
                            </td>
                            <td style="padding:12px 16px; color:var(--text-secondary);">
                                <?= htmlspecialchars($story['author_name'] ?? 'Chưa rõ') ?>
                            </td>
                            <td style="padding:12px 16px;">
                                <div style="display:flex; flex-wrap:wrap; gap:4px;">
                                    <?php foreach ($story['categories'] as $c): ?>
                                        <span style="font-size:0.72rem; background:var(--bg-primary); padding:2px 6px; border-radius:4px; border:1px solid var(--border-color);">
                                            <?= htmlspecialchars($c['name']) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td style="padding:12px 16px;">
                                <span class="story-badge <?= ($story['status'] === 'completed') ? 'completed' : '' ?>" style="position:static;">
                                    <?= ($story['status'] === 'completed') ? 'Full' : (($story['status'] === 'paused') ? 'Tạm dừng' : 'Đang ra') ?>
                                </span>
                            </td>
                            <td style="padding:12px 16px; font-weight:600; color:var(--text-secondary);">
                                <?= number_format($story['views_count']) ?>
                            </td>
                            <td style="padding:12px 16px; text-align:right;">
                                <div style="display:inline-flex; gap:6px;">
                                    <a href="<?= APP_URL ?>/admin/chapters?story_id=<?= $story['id'] ?>" class="btn btn-sm btn-outline" title="Quản lý chương">
                                        Chương
                                    </a>
                                    <button onclick='openEditStoryModal(<?= json_encode($story, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="btn btn-sm btn-primary" title="Sửa truyện">
                                        Sửa
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-delete-story" data-id="<?= $story['id'] ?>" data-title="<?= htmlspecialchars($story['title']) ?>" title="Xóa truyện">
                                        Xóa
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="page-link <?= ($i === $page) ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

    </main>
</div>

<!-- Modal Create / Edit Story -->
<div id="modal-story-form" class="modal-overlay">
    <div class="modal-content" style="max-width:650px;">
        <div class="modal-header">
            <h3 id="modal-story-title" class="modal-title">Thêm truyện mới</h3>
            <button onclick="closeModal('modal-story-form')" class="modal-close">&times;</button>
        </div>

        <form id="admin-story-form">
            <input type="hidden" id="story-id-input" name="id">

            <div class="form-group">
                <label class="form-label">Tên truyện (*)</label>
                <input type="text" id="story-title-input" name="title" class="form-control" required placeholder="Nhập tên truyện...">
            </div>

            <div class="form-group">
                <label class="form-label">Slug URL (Tùy chọn, tự sinh nếu để trống)</label>
                <input type="text" id="story-slug-input" name="slug" class="form-control" placeholder="ten-truyen-tuy-chinh">
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div class="form-group">
                    <label class="form-label">Tác giả</label>
                    <select id="story-author-input" name="author_id" class="form-control">
                        <option value="">-- Chọn tác giả --</option>
                        <?php foreach ($authors as $auth): ?>
                            <option value="<?= $auth['id'] ?>"><?= htmlspecialchars($auth['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Trạng thái</label>
                    <select id="story-status-input" name="status" class="form-control">
                        <option value="updating">Đang cập nhật</option>
                        <option value="completed">Hoàn thành</option>
                        <option value="paused">Tạm dừng</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Thể loại</label>
                <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:8px; max-height:120px; overflow-y:auto; padding:10px; background:var(--bg-primary); border-radius:var(--radius-md); border:1px solid var(--border-color);">
                    <?php foreach ($categories as $cat): ?>
                        <label style="display:flex; align-items:center; gap:6px; font-size:0.85rem; cursor:pointer;">
                            <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>" class="story-cat-checkbox">
                            <span><?= htmlspecialchars($cat['name']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Ảnh bìa (Upload file hoặc để trống dùng mặc định)</label>
                <input type="file" name="cover" accept="image/*" class="form-control">
            </div>

            <div class="form-group">
                <label class="form-label">Giới thiệu / Tóm tắt truyện</label>
                <textarea id="story-desc-input" name="description" class="form-control" rows="5" placeholder="Nội dung giới thiệu tác phẩm..."></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:24px;">
                <button type="button" onclick="closeModal('modal-story-form')" class="btn btn-outline">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu truyện</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
