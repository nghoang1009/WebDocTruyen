<?php
use App\Core\Auth;
use App\Models\Comment;

Auth::requireAdmin();
$pageTitle = "Quản lý Bình luận - " . APP_NAME;

$commentModel = new Comment();
$page = max(1, (int)($_GET['page'] ?? 1));
$search = trim($_GET['search'] ?? '');
$limit = 20;

$result = $commentModel->getAllAdmin($page, $limit, $search);
$comments = $result['items'];
$total = $result['total'];
$totalPages = ceil($total / $limit);

$extraJs = 'admin.js';
require_once __DIR__ . '/../layouts/header.php';
?>

<div style="display:flex; min-height:calc(100vh - var(--header-height));">
    <?php require_once __DIR__ . '/../layouts/admin-sidebar.php'; ?>

    <main style="flex:1; padding:32px; background:var(--bg-primary); overflow-x:hidden;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; flex-wrap:wrap; gap:16px;">
            <div>
                <h1 style="font-size:1.6rem; font-weight:800;">Quản Lý Bình Luận (<?= number_format($total) ?>)</h1>
                <p style="color:var(--text-secondary); font-size:0.88rem; margin-top:4px;">Kiểm duyệt và xóa bình luận vi phạm tiêu chuẩn cộng đồng</p>
            </div>

            <form action="<?= APP_URL ?>/admin/comments" method="GET" style="display:flex; gap:8px;">
                <input type="text" name="search" class="form-control" placeholder="Tìm nội dung, user..." value="<?= htmlspecialchars($search) ?>" style="width:220px;">
                <button type="submit" class="btn btn-primary">Tìm</button>
            </form>
        </div>

        <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--radius-lg); overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; text-align:left; font-size:0.9rem;">
                <thead>
                    <tr style="border-bottom:1px solid var(--border-color); background:var(--bg-tertiary); color:var(--text-secondary);">
                        <th style="padding:14px 16px;">Người gửi</th>
                        <th style="padding:14px 16px;">Truyện</th>
                        <th style="padding:14px 16px;">Nội dung bình luận</th>
                        <th style="padding:14px 16px;">Thời gian</th>
                        <th style="padding:14px 16px; text-align:right;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($comments as $cmt): ?>
                        <tr style="border-bottom:1px solid var(--border-color);">
                            <td style="padding:12px 16px;">
                                <div style="font-weight:700; color:var(--text-primary);"><?= htmlspecialchars($cmt['username']) ?></div>
                                <div style="font-size:0.75rem; color:var(--text-muted);"><?= htmlspecialchars($cmt['email']) ?></div>
                            </td>
                            <td style="padding:12px 16px;">
                                <a href="<?= APP_URL ?>/stories/<?= $cmt['story_slug'] ?>" target="_blank" style="font-weight:600; color:var(--primary);">
                                    <?= htmlspecialchars($cmt['story_title']) ?>
                                </a>
                            </td>
                            <td style="padding:12px 16px; max-width:400px; color:var(--text-secondary);">
                                <?= htmlspecialchars($cmt['content']) ?>
                            </td>
                            <td style="padding:12px 16px; color:var(--text-muted); font-size:0.82rem; white-space:nowrap;">
                                <?= date('d/m/Y H:i', strtotime($cmt['created_at'])) ?>
                            </td>
                            <td style="padding:12px 16px; text-align:right;">
                                <button class="btn btn-sm btn-danger btn-admin-delete-comment" data-id="<?= $cmt['id'] ?>">
                                    Xóa
                                </button>
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
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="page-link <?= ($i === $page) ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

    </main>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
