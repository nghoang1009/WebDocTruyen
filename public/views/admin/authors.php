<?php
use App\Core\Auth;
use App\Models\Author;

Auth::requireAdmin();
$pageTitle = "Quản lý Tác giả - " . APP_NAME;

$authorModel = new Author();
$authors = $authorModel->getAllWithStoryCount();

$extraJs = 'admin.js';
require_once __DIR__ . '/../layouts/header.php';
?>

<div style="display:flex; min-height:calc(100vh - var(--header-height));">
    <?php require_once __DIR__ . '/../layouts/admin-sidebar.php'; ?>

    <main style="flex:1; padding:32px; background:var(--bg-primary); overflow-x:hidden;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; flex-wrap:wrap; gap:16px;">
            <div>
                <h1 style="font-size:1.6rem; font-weight:800;">Quản Lý Tác Giả (<?= count($authors) ?>)</h1>
                <p style="color:var(--text-secondary); font-size:0.88rem; margin-top:4px;">Danh sách các tác giả trên website</p>
            </div>
            <button onclick="openCreateAuthorModal()" class="btn btn-primary">
                + Thêm Tác Giả
            </button>
        </div>

        <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--radius-lg); overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; text-align:left; font-size:0.9rem;">
                <thead>
                    <tr style="border-bottom:1px solid var(--border-color); background:var(--bg-tertiary); color:var(--text-secondary);">
                        <th style="padding:14px 16px;">ID</th>
                        <th style="padding:14px 16px;">Tên tác giả</th>
                        <th style="padding:14px 16px;">Slug URL</th>
                        <th style="padding:14px 16px;">Tiểu sử / Giới thiệu</th>
                        <th style="padding:14px 16px;">Số tác phẩm</th>
                        <th style="padding:14px 16px; text-align:right;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($authors as $author): ?>
                        <tr style="border-bottom:1px solid var(--border-color);">
                            <td style="padding:12px 16px; font-weight:700; color:var(--text-muted);"><?= $author['id'] ?></td>
                            <td style="padding:12px 16px; font-weight:700; color:var(--text-primary);"><?= htmlspecialchars($author['name']) ?></td>
                            <td style="padding:12px 16px; color:var(--text-muted); font-size:0.85rem;"><?= htmlspecialchars($author['slug']) ?></td>
                            <td style="padding:12px 16px; color:var(--text-secondary); font-size:0.85rem;"><?= htmlspecialchars($author['bio'] ?? 'Chưa có') ?></td>
                            <td style="padding:12px 16px; font-weight:600; color:var(--primary);"><?= number_format($author['story_count']) ?></td>
                            <td style="padding:12px 16px; text-align:right;">
                                <div style="display:inline-flex; gap:6px;">
                                    <button onclick='openEditAuthorModal(<?= json_encode($author, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="btn btn-sm btn-primary">
                                        Sửa
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-delete-author" data-id="<?= $author['id'] ?>">
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

<!-- Modal Author Form -->
<div id="modal-author-form" class="modal-overlay">
    <div class="modal-content" style="max-width:500px;">
        <div class="modal-header">
            <h3 id="modal-author-title" class="modal-title">Thêm tác giả</h3>
            <button onclick="closeModal('modal-author-form')" class="modal-close">&times;</button>
        </div>

        <form id="admin-author-form">
            <input type="hidden" id="author-id-input">

            <div class="form-group">
                <label class="form-label">Tên tác giả (*)</label>
                <input type="text" id="author-name-input" class="form-control" required placeholder="Ví dụ: Thiên Tằm Thổ Đậu...">
            </div>

            <div class="form-group">
                <label class="form-label">Slug URL (Tùy chọn)</label>
                <input type="text" id="author-slug-input" class="form-control" placeholder="thien-tam-tho-dau">
            </div>

            <div class="form-group">
                <label class="form-label">Tiểu sử tác giả</label>
                <textarea id="author-bio-input" class="form-control" rows="3" placeholder="Các tác phẩm tiêu biểu hoặc thông tin tác giả..."></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" onclick="closeModal('modal-author-form')" class="btn btn-outline">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu tác giả</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateAuthorModal() {
    document.getElementById('modal-author-title').innerText = 'Thêm tác giả mới';
    document.getElementById('admin-author-form').reset();
    document.getElementById('author-id-input').value = '';
    openModal('modal-author-form');
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
