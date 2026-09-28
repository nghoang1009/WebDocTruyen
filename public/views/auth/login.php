<?php
use App\Core\Auth;

if (Auth::check()) {
    header('Location: ' . APP_URL);
    exit;
}

$pageTitle = "Đăng nhập - " . APP_NAME;
require_once __DIR__ . '/../layouts/header.php';
?>

<main class="main-content" style="display:flex; align-items:center; justify-content:center; min-height:calc(100vh - var(--header-height) - 150px);">
    <div class="container" style="max-width:440px;">

        <div style="background:var(--bg-secondary); border:1px solid var(--border-color); border-radius:var(--radius-lg); padding:36px; box-shadow:var(--shadow-lg);">
            <div style="text-align:center; margin-bottom:28px;">
                <h1 style="font-size:1.6rem; font-weight:800; margin-bottom:8px;">Đăng Nhập</h1>
                <p style="color:var(--text-secondary); font-size:0.88rem;">Chào mừng bạn trở lại với <?= APP_NAME ?></p>
            </div>

            <form id="login-form">
                <div class="form-group">
                    <label class="form-label">Tài khoản hoặc Email</label>
                    <input type="text" id="login-account" class="form-control" placeholder="Tên đăng nhập hoặc email..." required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label">Mật khẩu</label>
                    <input type="password" id="login-password" class="form-control" placeholder="Nhập mật khẩu..." required>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; padding:12px; margin-top:8px;">
                    Đăng Nhập
                </button>
            </form>

            <div style="text-align:center; margin-top:24px; font-size:0.88rem; color:var(--text-secondary);">
                Chưa có tài khoản? <a href="<?= APP_URL ?>/register" style="color:var(--primary); font-weight:700;">Đăng ký ngay</a>
            </div>

            <div style="margin-top:20px; padding:12px; background:var(--bg-primary); border-radius:var(--radius-md); font-size:0.8rem; color:var(--text-muted); line-height:1.5;">
                <strong>Tài khoản thử nghiệm:</strong><br>
                - Admin: <code>admin</code> / <code>password123</code><br>
                - User: <code>nguyenvana</code> / <code>password123</code>
            </div>
        </div>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('login-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const account = document.getElementById('login-account').value.trim();
        const password = document.getElementById('login-password').value;

        try {
            const res = await API.post('/auth/login', { account, password });
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
