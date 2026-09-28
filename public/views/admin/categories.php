<?php
use App\Core\Auth;
use App\Models\Category;

Auth::requireAdmin();
$pageTitle = "Quản lý Thể loại - " . APP_NAME;

$categoryModel = new Category();
$categories = $categoryModel->getAllWithStoryCount();

$extraJs = 'admin.js';
require_once __DIR__ . '/../layouts/header.php';
?>

<div style="display:flex; min-height:calc(100vh - var(--header-height));">
    <?php require_once __DIR__ . '/../layouts/admin-sidebar.php'; ?>

    <main style="flex:1; padding:32px; background:var(--bg-primary); overflow-x:hidden;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; flex-wrap:wrap; gap:16px;">
            <div>
                <h1 style="font-size:1.6rem; font-weight:800;">Quản Lý Thể Loại (<?= count($categories) ?>)</h1>
                <p style="color:var(--text-secondary); font-size:0.88rem; margin-top:4px;">Danh mục phân loại truyện</p>
            </div>
            <button onclick="openCreateCategoryModal()" class="btn btn-primary">
                + Thêm Thể Loại
            </button>
        </div>

        <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--radius-lg); overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; text-align:left; font-size:0.9rem;">
                <thead>
                    <tr style="border-bottom:1px solid var(--border-color); background:var(--bg-tertiary); color:var(--text-secondary);">
                        <th style="padding:14px 16px;">ID</th>
                        <th style="padding:14px 16px;">Tên thể loại</th>
                        <th style="padding:14px 16px;">Slug URL</th>
                        <th style="padding:14px 16px;">Mô tả</th>
                        <th style="padding:14px 16px;">Số lượng truyện</th>
                        <th style="padding:14px 16px; text-align:right;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr style="border-bottom:1px solid var(--border-color);">
                            <td style="padding:12px 16px; font-weight:700; color:var(--text-muted);"><?= $cat['id'] ?></td>
                            <td style="padding:12px 16px; font-weight:700; color:var(--text-primary);"><?= htmlspecialchars($cat['name']) ?></td>
                            <td style="padding:12px 16px; color:var(--text-muted); font-size:0.85rem;"><?= htmlspecialchars($cat['slug']) ?></td>
                            <td style="padding:12px 16px; color:var(--text-secondary); font-size:0.85rem;"><?= htmlspecialchars($cat['description'] ?? 'Chưa có') ?></td>
                            <td style="padding:12px 16px; font-weight:600; color:var(--primary);"><?= number_format($cat['story_count']) ?></td>
                            <td style="padding:12px 16px; text-align:right;">
                                <div style="display:inline-flex; gap:6px;">
                                    <button onclick='openEditCategoryModal(<?= json_encode($cat, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="btn btn-sm btn-primary">
                                        Sửa
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-delete-cat" data-id="<?= $cat['id'] ?>">
                                        Xóa
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<!-- Modal Category Form -->
<div id="modal-category-form" class="modal-overlay">
    <div class="modal-content" style="max-width:500px;">
        <div class="modal-header">
            <h3 id="modal-cat-title" class="modal-title">Thêm thể loại</h3>
            <button onclick="closeModal('modal-category-form')" class="modal-close">&times;</button>
        </div>

        <form id="admin-category-form">
            <input type="hidden" id="cat-id-input">

            <div class="form-group">
                <label class="form-label">Tên thể loại (*)</label>
                <input type="text" id="cat-name-input" class="form-control" required placeholder="Ví dụ: Tiên Hiệp, Đô Thị...">
            </div>

            <div class="form-group">
                <label class="form-label">Slug URL (Tùy chọn)</label>
                <input type="text" id="cat-slug-input" class="form-control" placeholder="tien-hiep">
            </div>

            <div class="form-group">
                <label class="form-label">Mô tả ngắn</label>
                <textarea id="cat-desc-input" class="form-control" rows="3" placeholder="Mô tả đặc điểm thể loại này..."></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" onclick="closeModal('modal-category-form')" class="btn btn-outline">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu thể loại</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateCategoryModal() {
    document.getElementById('modal-cat-title').innerText = 'Thêm thể loại mới';
    document.getElementById('admin-category-form').reset();
    document.getElementById('cat-id-input').value = '';
    openModal('modal-category-form');
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
