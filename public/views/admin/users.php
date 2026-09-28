<?php
use App\Core\Auth;
use App\Models\User;

Auth::requireAdmin();
$pageTitle = "Quản lý Người dùng - " . APP_NAME;

$userModel = new User();
$page = max(1, (int)($_GET['page'] ?? 1));
$search = trim($_GET['search'] ?? '');
$limit = 20;

$result = $userModel->getAllUsersWithRoles($page, $limit, $search);
$users = $result['items'];
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
                <h1 style="font-size:1.6rem; font-weight:800;">Quản Lý Người Dùng (<?= number_format($total) ?>)</h1>
                <p style="color:var(--text-secondary); font-size:0.88rem; margin-top:4px;">Phân quyền vai trò và khóa/mở khóa tài khoản</p>
            </div>

            <form action="<?= APP_URL ?>/admin/users" method="GET" style="display:flex; gap:8px;">
                <input type="text" name="search" class="form-control" placeholder="Tìm username, email..." value="<?= htmlspecialchars($search) ?>" style="width:220px;">
                <button type="submit" class="btn btn-primary">Tìm</button>
            </form>
        </div>

        <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--radius-lg); overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; text-align:left; font-size:0.9rem;">
                <thead>
                    <tr style="border-bottom:1px solid var(--border-color); background:var(--bg-tertiary); color:var(--text-secondary);">
                        <th style="padding:14px 16px;">ID</th>
                        <th style="padding:14px 16px;">Tài khoản</th>
                        <th style="padding:14px 16px;">Email</th>
                        <th style="padding:14px 16px;">Vai trò</th>
                        <th style="padding:14px 16px;">Trạng thái</th>
                        <th style="padding:14px 16px;">Ngày đăng ký</th>
                        <th style="padding:14px 16px; text-align:right;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr style="border-bottom:1px solid var(--border-color);">
                            <td style="padding:12px 16px; font-weight:700; color:var(--text-muted);"><?= $u['id'] ?></td>
                            <td style="padding:12px 16px;">
                                <div style="font-weight:700; color:var(--text-primary);"><?= htmlspecialchars($u['username']) ?></div>
                            </td>
                            <td style="padding:12px 16px; color:var(--text-secondary);"><?= htmlspecialchars($u['email']) ?></td>
                            <td style="padding:12px 16px;">
                                <select class="form-control select-user-role" data-id="<?= $u['id'] ?>" style="width:110px; padding:4px 8px; font-size:0.85rem;">
                                    <option value="2" <?= ((int)$u['role_id'] === 2) ? 'selected' : '' ?>>User</option>
                                    <option value="1" <?= ((int)$u['role_id'] === 1) ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </td>
                            <td style="padding:12px 16px;">
                                <select class="form-control select-user-status" data-id="<?= $u['id'] ?>" style="width:120px; padding:4px 8px; font-size:0.85rem; <?= ($u['status'] === 'banned') ? 'color:var(--danger);' : 'color:var(--success);' ?>">
                                    <option value="active" <?= ($u['status'] === 'active') ? 'selected' : '' ?>>Hoạt động</option>
                                    <option value="banned" <?= ($u['status'] === 'banned') ? 'selected' : '' ?>>Bị khóa</option>
                                </select>
                            </td>
                            <td style="padding:12px 16px; color:var(--text-muted); font-size:0.82rem;">
                                <?= date('d/m/Y', strtotime($u['created_at'])) ?>
                            </td>
                            <td style="padding:12px 16px; text-align:right;">
                                <button class="btn btn-sm btn-danger btn-delete-user" data-id="<?= $u['id'] ?>">
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
