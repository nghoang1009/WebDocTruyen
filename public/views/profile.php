<?php
use App\Core\Auth;
use App\Models\User;

$user = Auth::requireAuth();
$pageTitle = "Hồ sơ cá nhân - " . APP_NAME;

$userModel = new User();
$userData = $userModel->getUserWithRole($user['id']);

require_once __DIR__ . '/layouts/header.php';
?>

<main class="main-content">
    <div class="container" style="max-width:800px;">

        <div style="margin-bottom:28px;">
            <h1 style="font-size:1.5rem; font-weight:800;">Tài Khoản & Hồ Sơ</h1>
            <p style="color:var(--text-secondary); font-size:0.88rem; margin-top:4px;">Quản lý thông tin cá nhân và bảo mật tài khoản của bạn</p>
        </div>

        <div style="display:grid; grid-template-columns:1fr; gap:28px;">

            <!-- Basic Info Form -->
            <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--radius-lg); padding:28px;">
                <h2 style="font-size:1.15rem; font-weight:700; margin-bottom:20px; border-bottom:1px solid var(--border-color); padding-bottom:10px;">
                    Thông tin cơ bản
                </h2>

                <form id="profile-info-form">
                    <div style="display:flex; align-items:center; gap:20px; margin-bottom:24px;">
                        <div style="width:64px; height:64px; border-radius:50%; background:var(--primary); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.5rem;">
                            <?= strtoupper(substr($userData['username'], 0, 1)) ?>
                        </div>
                        <div>
                            <div style="font-weight:700; font-size:1.1rem; color:var(--text-primary);"><?= htmlspecialchars($userData['username']) ?></div>
                            <div style="font-size:0.82rem; color:var(--text-muted);">
                                Vai trò: <span style="color:var(--primary); font-weight:600; text-transform:uppercase;"><?= htmlspecialchars($userData['role_name']) ?></span> &bull; Tham gia: <?= date('d/m/Y', strtotime($userData['created_at'])) ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tên tài khoản</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($userData['username']) ?>" disabled style="opacity:0.7; cursor:not-allowed;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" id="profile-email-input" name="email" class="form-control" value="<?= htmlspecialchars($userData['email']) ?>" required>
                    </div>

                    <div style="display:flex; justify-content:flex-end;">
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>

            <!-- Change Password Form -->
            <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--radius-lg); padding:28px;">
                <h2 style="font-size:1.15rem; font-weight:700; margin-bottom:20px; border-bottom:1px solid var(--border-color); padding-bottom:10px;">
                    Đổi mật khẩu
                </h2>

                <form id="profile-password-form">
                    <div class="form-group">
                        <label class="form-label">Mật khẩu hiện tại</label>
                        <input type="password" id="current-password-input" class="form-control" required placeholder="Nhập mật khẩu cũ...">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mật khẩu mới</label>
                        <input type="password" id="new-password-input" class="form-control" required placeholder="Tối thiểu 6 ký tự...">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Xác nhận mật khẩu mới</label>
                        <input type="password" id="confirm-password-input" class="form-control" required placeholder="Nhập lại mật khẩu mới...">
                    </div>

                    <div style="display:flex; justify-content:flex-end;">
                        <button type="submit" class="btn btn-primary">Cập nhật mật khẩu</button>
                    </div>
                </form>
            </div>

        </div>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Update profile
    document.getElementById('profile-info-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const email = document.getElementById('profile-email-input').value.trim();
        try {
            const res = await API.post('/auth/profile', { email });
            Toast.success(res.message);
        } catch (err) {
            Toast.error(err.message);
        }
    });

    // Change password
    document.getElementById('profile-password-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const current_password = document.getElementById('current-password-input').value;
        const new_password = document.getElementById('new-password-input').value;
        const confirm_password = document.getElementById('confirm-password-input').value;

        if (new_password !== confirm_password) {
            Toast.error('Xác nhận mật khẩu mới không khớp.');
            return;
        }

        try {
            const res = await API.post('/auth/change-password', {
                current_password,
                new_password,
                confirm_password
            });
            Toast.success(res.message);
            document.getElementById('profile-password-form').reset();
        } catch (err) {
            Toast.error(err.message);
        }
    });
});
</script>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
