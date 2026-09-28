<?php
use App\Models\Category;
$footerCategories = (new Category())->all('id ASC');
?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <div class="footer-brand"><?= APP_NAME ?></div>
                <p class="footer-desc">
                    Nền tảng đọc truyện chữ online miễn phí hàng đầu. Trải nghiệm đọc truyện mượt mà, giao diện hiện đại tối ưu cho mọi thiết bị máy tính và điện thoại.
                </p>
            </div>

            <div>
                <div class="footer-title">Thể loại nổi bật</div>
                <div style="display:flex; flex-wrap:wrap; gap:6px;">
                    <?php foreach (array_slice($footerCategories, 0, 10) as $cat): ?>
                        <a href="<?= APP_URL ?>/stories?category=<?= $cat['slug'] ?>" class="filter-tag" style="font-size:0.78rem; padding:4px 10px;">
                            <?= htmlspecialchars($cat['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <div class="footer-title">Liên kết nhanh</div>
                <ul class="footer-links">
                    <li><a href="<?= APP_URL ?>" class="footer-link">Trang chủ</a></li>
                    <li><a href="<?= APP_URL ?>/stories" class="footer-link">Tất cả truyện</a></li>
                    <li><a href="<?= APP_URL ?>/stories?sort=views" class="footer-link">Truyện đọc nhiều</a></li>
                    <li><a href="<?= APP_URL ?>/stories?sort=rating" class="footer-link">Truyện đánh giá cao</a></li>
                    <li><a href="<?= APP_URL ?>/stories?status=completed" class="footer-link">Truyện full hoàn thành</a></li>
                </ul>
            </div>

            <div>
                <div class="footer-title">Chính sách & Hỗ trợ</div>
                <ul class="footer-links">
                    <li><a href="#" class="footer-link">Điều khoản sử dụng</a></li>
                    <li><a href="#" class="footer-link">Chính sách bảo mật</a></li>
                    <li><a href="#" class="footer-link">Vấn đề bản quyền</a></li>
                    <li><a href="#" class="footer-link">Liên hệ quản trị viên</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. Tất cả quyền được bảo lưu.</p>
        </div>
    </div>
</footer>

<div id="toast-container"></div>

<script src="<?= APP_URL ?>/public/assets/js/api.js"></script>
<script src="<?= APP_URL ?>/public/assets/js/app.js"></script>
<?php if (isset($extraJs)): ?>
    <script src="<?= APP_URL ?>/public/assets/js/<?= $extraJs ?>"></script>
<?php endif; ?>

</body>
</html>
