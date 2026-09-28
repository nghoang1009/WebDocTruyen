<?php
$pageTitle = "404 - Không tìm thấy trang - " . APP_NAME;
require_once __DIR__ . '/layouts/header.php';
?>

<main class="main-content" style="display:flex; align-items:center; justify-content:center; min-height:calc(100vh - var(--header-height) - 150px); text-align:center;">
    <div class="container" style="max-width:500px; padding:40px 20px;">
        <div style="font-size:6rem; font-weight:900; color:var(--primary); line-height:1; margin-bottom:16px;">404</div>
        <h1 style="font-size:1.6rem; font-weight:800; margin-bottom:12px;">Trang không tồn tại</h1>
        <p style="color:var(--text-secondary); font-size:0.95rem; margin-bottom:28px; line-height:1.6;">
            Trang bạn đang tìm kiếm có thể đã bị xóa, thay đổi tên hoặc tạm thời không khả dụng.
        </p>
        <a href="<?= APP_URL ?>" class="btn btn-primary btn-lg">
            Về Trang Chủ
        </a>
    </div>
</main>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
