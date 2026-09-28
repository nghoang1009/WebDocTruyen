<?php
use App\Core\Auth;
use App\Models\Stats;

Auth::requireAdmin();
$pageTitle = "Admin Dashboard - " . APP_NAME;

$statsModel = new Stats();
$overview = $statsModel->getOverview();
$dailyViews = $statsModel->getDailyViews(7);
$topStories = $statsModel->getTopStories(5);
$topChapters = $statsModel->getTopChapters(5);

$extraJs = 'admin.js';
require_once __DIR__ . '/../layouts/header.php';
?>

<div style="display:flex; min-height:calc(100vh - var(--header-height));">
    <?php require_once __DIR__ . '/../layouts/admin-sidebar.php'; ?>

    <main style="flex:1; padding:32px; background:var(--bg-primary); overflow-x:hidden;">
        <div style="margin-bottom:32px;">
            <h1 style="font-size:1.6rem; font-weight:800;">Tổng Quan Hệ Thống</h1>
            <p style="color:var(--text-secondary); font-size:0.88rem; margin-top:4px;">Thống kê tổng hợp hoạt động của website</p>
        </div>

        <!-- Metric Stat KPI Cards -->
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:20px; margin-bottom:36px;">
            <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--radius-lg); padding:24px; display:flex; align-items:center; gap:16px;">
                <div style="width:50px; height:50px; border-radius:var(--radius-md); background:var(--primary-light); color:var(--primary); display:flex; align-items:center; justify-content:center;">
                    <svg style="width:28px;height:28px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div>
                    <div style="font-size:0.85rem; color:var(--text-secondary); font-weight:600;">Tổng số Truyện</div>
                    <div style="font-size:1.6rem; font-weight:800; color:var(--text-primary);"><?= number_format($overview['total_stories']) ?></div>
                </div>
            </div>

            <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--radius-lg); padding:24px; display:flex; align-items:center; gap:16px;">
                <div style="width:50px; height:50px; border-radius:var(--radius-md); background:rgba(16, 185, 129, 0.15); color:var(--success); display:flex; align-items:center; justify-content:center;">
                    <svg style="width:28px;height:28px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <div style="font-size:0.85rem; color:var(--text-secondary); font-weight:600;">Tổng số Chương</div>
                    <div style="font-size:1.6rem; font-weight:800; color:var(--text-primary);"><?= number_format($overview['total_chapters']) ?></div>
                </div>
            </div>

            <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--radius-lg); padding:24px; display:flex; align-items:center; gap:16px;">
                <div style="width:50px; height:50px; border-radius:var(--radius-md); background:rgba(245, 158, 11, 0.15); color:var(--accent); display:flex; align-items:center; justify-content:center;">
                    <svg style="width:28px;height:28px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div>
                    <div style="font-size:0.85rem; color:var(--text-secondary); font-weight:600;">Người dùng</div>
                    <div style="font-size:1.6rem; font-weight:800; color:var(--text-primary);"><?= number_format($overview['total_users']) ?></div>
                </div>
            </div>

            <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--radius-lg); padding:24px; display:flex; align-items:center; gap:16px;">
                <div style="width:50px; height:50px; border-radius:var(--radius-md); background:rgba(239, 68, 68, 0.15); color:var(--danger); display:flex; align-items:center; justify-content:center;">
                    <svg style="width:28px;height:28px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <div>
                    <div style="font-size:0.85rem; color:var(--text-secondary); font-weight:600;">Lượt xem toàn trang</div>
                    <div style="font-size:1.6rem; font-weight:800; color:var(--text-primary);"><?= number_format($overview['total_views']) ?></div>
                </div>
            </div>
        </div>

        <!-- Views 7-Day Trend Chart Representation -->
        <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--radius-lg); padding:24px; margin-bottom:36px;">
            <h2 style="font-size:1.15rem; font-weight:700; margin-bottom:20px;">Lượt xem 7 ngày gần nhất</h2>
            <div style="display:flex; align-items:flex-end; gap:16px; height:180px; padding:10px 0; border-bottom:1px solid var(--border-color);">
                <?php
                $maxView = 1;
                foreach ($dailyViews as $dv) {
                    if ((int)$dv['total_views'] > $maxView) $maxView = (int)$dv['total_views'];
                }
                foreach ($dailyViews as $dv):
                    $heightPercent = max(10, round(((int)$dv['total_views'] / $maxView) * 100));
                ?>
                    <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:8px; height:100%; justify-content:flex-end;">
                        <span style="font-size:0.75rem; font-weight:700; color:var(--primary);"><?= number_format($dv['total_views']) ?></span>
                        <div style="width:100%; max-width:40px; height:<?= $heightPercent ?>%; background:var(--primary); border-radius:4px 4px 0 0; transition:height 0.3s ease;"></div>
                        <span style="font-size:0.75rem; color:var(--text-muted);"><?= date('d/m', strtotime($dv['view_date'])) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Top Stories & Chapters Grid -->
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(340px, 1fr)); gap:24px;">
            <!-- Top Stories -->
            <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--radius-lg); padding:24px;">
                <h2 style="font-size:1.1rem; font-weight:700; margin-bottom:16px;">Top Truyện Đọc Nhiều</h2>
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <?php foreach ($topStories as $idx => $s): ?>
                        <div style="display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid var(--border-color);">
                            <div style="display:flex; align-items:center; gap:10px; overflow:hidden;">
                                <span style="font-weight:800; font-size:1rem; color:var(--primary); width:20px;"><?= $idx + 1 ?></span>
                                <a href="<?= APP_URL ?>/stories/<?= $s['slug'] ?>" style="font-weight:600; font-size:0.9rem; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    <?= htmlspecialchars($s['title']) ?>
                                </a>
                            </div>
                            <span style="font-size:0.8rem; font-weight:700; color:var(--text-secondary);"><?= number_format($s['views_count']) ?> views</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Top Chapters -->
            <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--radius-lg); padding:24px;">
                <h2 style="font-size:1.1rem; font-weight:700; margin-bottom:16px;">Top Chương Đọc Nhiều</h2>
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <?php foreach ($topChapters as $idx => $c): ?>
                        <div style="display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid var(--border-color);">
                            <div style="display:flex; align-items:center; gap:10px; overflow:hidden;">
                                <span style="font-weight:800; font-size:1rem; color:var(--accent); width:20px;"><?= $idx + 1 ?></span>
                                <div>
                                    <div style="font-weight:600; font-size:0.88rem; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                        <?= htmlspecialchars($c['story_title']) ?> - C.<?= $c['chapter_number'] ?>
                                    </div>
                                </div>
                            </div>
                            <span style="font-size:0.8rem; font-weight:700; color:var(--text-secondary);"><?= number_format($c['views_count']) ?> views</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </main>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
