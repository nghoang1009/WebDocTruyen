<?php
use App\Core\Auth;
use App\Models\Story;
use App\Models\Chapter;

Auth::requireAdmin();
$pageTitle = "Quản lý Chương - " . APP_NAME;

$storyModel = new Story();
$chapterModel = new Chapter();

$stories = $storyModel->all('title ASC');
$selectedStoryId = !empty($_GET['story_id']) ? (int)$_GET['story_id'] : ($stories[0]['id'] ?? 0);

$chapters = [];
$currentStory = null;
if ($selectedStoryId > 0) {
    $currentStory = $storyModel->find($selectedStoryId);
    $chapters = $chapterModel->getByStoryId($selectedStoryId, 'ASC', false);
}

$extraJs = 'admin.js';
require_once __DIR__ . '/../layouts/header.php';
?>

<div style="display:flex; min-height:calc(100vh - var(--header-height));">
    <?php require_once __DIR__ . '/../layouts/admin-sidebar.php'; ?>

    <main style="flex:1; padding:32px; background:var(--bg-primary); overflow-x:hidden;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; flex-wrap:wrap; gap:16px;">
            <div>
                <h1 style="font-size:1.6rem; font-weight:800;">Quản Lý Chương Truyện</h1>
                <p style="color:var(--text-secondary); font-size:0.88rem; margin-top:4px;">Thêm, sửa nội dung chương và duyệt bài</p>
            </div>
            <?php if ($selectedStoryId > 0): ?>
                <button onclick="openCreateChapterModal(<?= $selectedStoryId ?>)" class="btn btn-primary">
                    + Thêm Chương Mới
                </button>
            <?php endif; ?>
        </div>

        <!-- Story Selection Bar -->
        <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--radius-lg); padding:20px; margin-bottom:24px; display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
            <label style="font-weight:700; font-size:0.95rem;">Chọn tác phẩm:</label>
            <select class="form-control" style="max-width:350px;" onchange="window.location.href='?story_id=' + this.value">
                <?php foreach ($stories as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= ((int)$s['id'] === $selectedStoryId) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Chapters Table -->
        <?php if (empty($chapters)): ?>
            <div style="text-align:center; padding:50px 20px; background:var(--bg-secondary); border-radius:var(--radius-lg); border:1px solid var(--border-color);">
                <h3 style="font-size:1.15rem; font-weight:700; margin-bottom:8px;">Truyện này chưa có chương nào</h3>
                <p style="color:var(--text-secondary); font-size:0.9rem; margin-bottom:16px;">Hãy thêm chương đầu tiên để độc giả có thể theo dõi.</p>
                <button onclick="openCreateChapterModal(<?= $selectedStoryId ?>)" class="btn btn-primary">
                    + Thêm Chương Ngay
                </button>
            </div>
        <?php else: ?>
            <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--radius-lg); overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; text-align:left; font-size:0.9rem;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border-color); background:var(--bg-tertiary); color:var(--text-secondary);">
                            <th style="padding:14px 16px;">Số chương</th>
                            <th style="padding:14px 16px;">Tiêu đề</th>
                            <th style="padding:14px 16px;">Slug URL</th>
                            <th style="padding:14px 16px;">Trạng thái</th>
                            <th style="padding:14px 16px;">Lượt xem</th>
                            <th style="padding:14px 16px;">Ngày tạo</th>
                            <th style="padding:14px 16px; text-align:right;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($chapters as $ch): ?>
                            <tr style="border-bottom:1px solid var(--border-color);">
                                <td style="padding:12px 16px; font-weight:700; color:var(--primary);">
                                    <?= $ch['chapter_number'] ?>
                                </td>
                                <td style="padding:12px 16px; font-weight:600; color:var(--text-primary);">
                                    <?= htmlspecialchars($ch['title']) ?>
                                </td>
                                <td style="padding:12px 16px; color:var(--text-muted); font-size:0.8rem;">
                                    <?= htmlspecialchars($ch['slug']) ?>
                                </td>
                                <td style="padding:12px 16px;">
                                    <span class="story-badge <?= ($ch['status'] === 'published') ? 'completed' : '' ?>" style="position:static; font-size:0.75rem;">
                                        <?= ($ch['status'] === 'published') ? 'Công khai' : 'Bản nháp' ?>
                                    </span>
                                </td>
                                <td style="padding:12px 16px; color:var(--text-secondary);">
                                    <?= number_format($ch['views_count']) ?>
                                </td>
                                <td style="padding:12px 16px; color:var(--text-muted); font-size:0.8rem;">
                                    <?= date('d/m/Y', strtotime($ch['created_at'])) ?>
                                </td>
                                <td style="padding:12px 16px; text-align:right;">
                                    <div style="display:inline-flex; gap:6px;">
                                        <a href="<?= APP_URL ?>/stories/<?= $currentStory['slug'] ?>/chapter/<?= $ch['slug'] ?>" target="_blank" class="btn btn-sm btn-outline" title="Xem trên web">
                                            Xem
                                        </a>
                                        <button onclick='openEditChapterModal(<?= json_encode($ch, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="btn btn-sm btn-primary" title="Sửa nội dung">
                                            Sửa
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-delete-chapter" data-id="<?= $ch['id'] ?>" title="Xóa chương">
                                            Xóa
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </main>
</div>

<!-- Modal Chapter Create / Edit -->
<div id="modal-chapter-form" class="modal-overlay">
    <div class="modal-content" style="max-width:800px;">
        <div class="modal-header">
            <h3 id="modal-chapter-title" class="modal-title">Thêm chương mới</h3>
            <button onclick="closeModal('modal-chapter-form')" class="modal-close">&times;</button>
        </div>

        <form id="admin-chapter-form">
            <input type="hidden" id="chapter-id-input" name="id">
            <input type="hidden" id="chapter-story-id-input" name="story_id" value="<?= $selectedStoryId ?>">

            <div style="display:grid; grid-template-columns:120px 1fr; gap:16px;">
                <div class="form-group">
                    <label class="form-label">Số chương (*)</label>
                    <input type="number" step="0.1" id="chapter-number-input" name="chapter_number" class="form-control" required placeholder="1">
                </div>

                <div class="form-group">
                    <label class="form-label">Tiêu đề chương (*)</label>
                    <input type="text" id="chapter-title-input" name="title" class="form-control" required placeholder="Tên chương truyện...">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 180px; gap:16px;">
                <div class="form-group">
                    <label class="form-label">Slug URL (Tự sinh nếu trống)</label>
                    <input type="text" id="chapter-slug-input" name="slug" class="form-control" placeholder="chuong-1-tieu-de">
                </div>

                <div class="form-group">
                    <label class="form-label">Trạng thái</label>
                    <select id="chapter-status-input" name="status" class="form-control">
                        <option value="published">Công khai</option>
                        <option value="draft">Bản nháp</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Nội dung chương (* Hỗ trợ các thẻ HTML cơ bản: &lt;p&gt;, &lt;b&gt;, &lt;i&gt;)</label>
                <textarea id="chapter-content-input" name="content" class="form-control" rows="12" required placeholder="Nhập hoặc dán nội dung chữ của chương..."></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" onclick="closeModal('modal-chapter-form')" class="btn btn-outline">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu chương</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
