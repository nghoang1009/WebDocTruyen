<?php
use App\Core\Auth;

if (Auth::check()) {
    header('Location: ' . APP_URL);
    exit;
}

$pageTitle = "Đăng ký tài khoản - " . APP_NAME;
require_once __DIR__ . '/../layouts/header.php';
?>

<main class="main-content" style="display:flex; align-items:center; justify-content:center; min-height:calc(100vh - var(--header-height) - 150px);">
    <div class="container" style="max-width:460px;">

        <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--radius-lg); padding:36px; box-shadow:var(--shadow-lg);">
            <div style="text-align:center; margin-bottom:28px;">
                <h1 style="font-size:1.6rem; font-weight:800; margin-bottom:8px;">Tạo Tài Khoản</h1>
                <p style="color:var(--text-secondary); font-size:0.88rem;">Tham gia cộng đồng đọc truyện tại <?= APP_NAME ?></p>
            </div>

            <form id="register-form">
                <div class="form-group">
                    <label class="form-label">Tên tài khoản (Username)</label>
                    <input type="text" id="reg-username" class="form-control" placeholder="Từ 3-30 ký tự, không dấu..." required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" id="reg-email" class="form-control" placeholder="email@example.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Mật khẩu</label>
                    <input type="password" id="reg-password" class="form-control" placeholder="Tối thiểu 6 ký tự..." required>
                </div>

                <div class="form-group">
                    <label class="form-label">Xác nhận mật khẩu</label>
                    <input type="password" id="reg-confirm-password" class="form-control" placeholder="Nhập lại mật khẩu..." required>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; padding:12px; margin-top:8px;">
                    Đăng Ký Tài Khoản
                </button>
            </form>

            <div style="text-align:center; margin-top:24px; font-size:0.88rem; color:var(--text-secondary);">
                Đã có tài khoản? <a href="<?= APP_URL ?>/login" style="color:var(--primary); font-weight:700;">Đăng nhập</a>
            </div>
        </div>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('register-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const username = document.getElementById('reg-username').value.trim();
        const email = document.getElementById('reg-email').value.trim();
        const password = document.getElementById('reg-password').value;
        const confirm_password = document.getElementById('reg-confirm-password').value;

        if (password !== confirm_password) {
            Toast.error('Xác nhận mật khẩu không khớp.');
            return;
        }

        try {
            const res = await API.post('/auth/register', {
                username,
                email,
                password,
                confirm_password
            });
            API.setToken(res.data.token);
            Toast.success(res.message);
            setTimeout(() => {
                window.location.href = window.APP_URL || '/';
            }, 600);
        } catch (err) {
            Toast.error(err.message);
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
