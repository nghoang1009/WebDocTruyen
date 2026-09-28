<?php
use App\Models\Interaction;
use App\Core\Auth;

$user = Auth::requireAuth();
$pageTitle = "Lịch sử đọc truyện - " . APP_NAME;

$interactionModel = new Interaction();
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 15;

$historyData = $interactionModel->getHistory($user['id'], $page, $limit);
$items = $historyData['items'];
$total = $historyData['total'];
$totalPages = ceil($total / $limit);

require_once __DIR__ . '/layouts/header.php';
?>

<main class="main-content">
    <div class="container">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; flex-wrap:wrap; gap:16px;">
            <div>
                <h1 style="font-size:1.5rem; font-weight:800; display:flex; align-items:center; gap:8px;">
                    <svg style="width:26px;height:26px;color:var(--primary);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Lịch Sử Đọc Truyện (<?= number_format($total) ?>)
                </h1>
                <p style="color:var(--text-secondary); font-size:0.88rem; margin-top:4px;">Danh sách các truyện bạn đã đọc gần đây</p>
            </div>

            <?php if (!empty($items)): ?>
                <button id="btn-clear-all-history" class="btn btn-outline btn-sm" style="color:var(--danger); border-color:var(--danger);">
                    Xóa tất cả lịch sử
                </button>
            <?php endif; ?>
        </div>

        <?php if (empty($items)): ?>
            <div style="text-align:center; padding:60px 20px; background:var(--bg-secondary); border-radius:var(--radius-lg); border:1px solid var(--border-color);">
                <svg style="width:48px;height:48px;color:var(--text-muted);margin-bottom:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 style="font-size:1.15rem; font-weight:700; margin-bottom:8px;">Chưa có lịch sử đọc truyện</h3>
                <p style="color:var(--text-secondary); font-size:0.9rem; margin-bottom:20px;">Hãy khám phá các tác phẩm hấp dẫn ngay!</p>
                <a href="<?= APP_URL ?>/stories" class="btn btn-primary">Khám phá truyện</a>
            </div>
        <?php else: ?>
            <div style="display:grid; grid-template-columns:1fr; gap:12px;">
                <?php foreach ($items as $item): ?>
                    <div id="history-row-<?= $item['story_id'] ?>" style="display:flex; justify-content:space-between; align-items:center; background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--radius-md); padding:16px; gap:16px; flex-wrap:wrap;">
                        <div style="display:flex; gap:16px; align-items:center; flex:1; min-width:260px;">
                            <img src="<?= UPLOAD_URL ?>/covers/<?= $item['cover_image'] ?>" onerror="this.onerror=null;this.src='<?= APP_URL ?>/public/assets/images/default-cover.jpg'" style="width:55px; height:75px; object-fit:cover; border-radius:var(--radius-sm); flex-shrink:0;" alt="<?= htmlspecialchars($item['story_title']) ?>">
                            <div>
                                <a href="<?= APP_URL ?>/stories/<?= $item['story_slug'] ?>" style="font-weight:700; font-size:1rem; color:var(--text-primary); display:block; margin-bottom:4px;">
                                    <?= htmlspecialchars($item['story_title']) ?>
                                </a>
                                <div style="font-size:0.85rem; color:var(--text-secondary); margin-bottom:4px;">
                                    Đang đọc: <strong style="color:var(--primary);">Chương <?= $item['chapter_number'] ?>: <?= htmlspecialchars($item['chapter_title']) ?></strong> (<?= $item['progress_percent'] ?>%)
                                </div>
                                <div style="font-size:0.78rem; color:var(--text-muted);">Đọc lúc: <?= date('d/m/Y H:i', strtotime($item['last_read_at'])) ?></div>
                            </div>
                        </div>

                        <div style="display:flex; align-items:center; gap:10px;">
                            <a href="<?= APP_URL ?>/stories/<?= $item['story_slug'] ?>/chapter/<?= $item['chapter_slug'] ?>" class="btn btn-sm btn-primary">
                                Đọc tiếp
                            </a>
                            <button class="btn btn-sm btn-outline btn-delete-history" data-id="<?= $item['story_id'] ?>" title="Xóa khỏi lịch sử" style="color:var(--danger);">
                                &times;
                            </button>
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Delete single history item
    document.querySelectorAll('.btn-delete-history').forEach(btn => {
        btn.addEventListener('click', async () => {
            const storyId = btn.dataset.id;
            try {
                await API.delete(`/user/history/${storyId}`);
                Toast.success('Đã xóa truyện khỏi lịch sử.');
                document.getElementById(`history-row-${storyId}`)?.remove();
            } catch (err) {
                Toast.error(err.message);
            }
        });
    });

    // Clear all history
    document.getElementById('btn-clear-all-history')?.addEventListener('click', async () => {
        if (!confirm('Bạn có chắc muốn xóa toàn bộ lịch sử đọc?')) return;
        try {
            await API.delete('/user/history');
            Toast.success('Đã xóa toàn bộ lịch sử đọc.');
            setTimeout(() => window.location.reload(), 500);
        } catch (err) {
            Toast.error(err.message);
        }
    });
});
</script>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
