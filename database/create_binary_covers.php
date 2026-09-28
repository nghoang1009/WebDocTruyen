<?php
/**
 * Create 100% Valid Binary JPEG Covers using PHP GD library
 */

require_once __DIR__ . '/../config.php';

$coversDir = UPLOAD_PATH . '/covers';
if (!is_dir($coversDir)) {
    @mkdir($coversDir, 0777, true);
}

$imagesDir = PUBLIC_PATH . '/assets/images';
if (!is_dir($imagesDir)) {
    @mkdir($imagesDir, 0777, true);
}

$stories = [
    ['file' => 'dau-pha-thuong-khung.jpg', 'title' => 'DAU PHA', 'sub' => 'THUONG KHUNG', 'r1' => 220, 'g1' => 38, 'b1' => 38],
    ['file' => 'nhat-niem-vinh-hang.jpg', 'title' => 'NHAT NIEM', 'sub' => 'VINH HANG', 'r1' => 2, 'g1' => 132, 'b1' => 199],
    ['file' => 'thon-phe-tinh-khong.jpg', 'title' => 'THON PHE', 'sub' => 'TINH KHONG', 'r1' => 79, 'g1' => 70, 'b1' => 229],
    ['file' => 'vu-dong-can-khon.jpg', 'title' => 'VU DONG', 'sub' => 'CAN KHON', 'r1' => 217, 'g1' => 119, 'b1' => 6],
    ['file' => 'tien-nghich.jpg', 'title' => 'TIEN NGHICH', 'sub' => 'TU CHAN', 'r1' => 5, 'g1' => 150, 'b1' => 105],
    ['file' => 'ma-dao-to-su.jpg', 'title' => 'MA DAO', 'sub' => 'TO SU', 'r1' => 124, 'g1' => 58, 'b1' => 237],
    ['file' => 'yeu-em-tu-cai-nhin-dau-tien.jpg', 'title' => 'YEU EM', 'sub' => 'TU CAI NHIN DAU TIEN', 'r1' => 219, 'g1' => 39, 'b1' => 119],
    ['file' => 'dao-mo-but-ky.jpg', 'title' => 'DAO MO', 'sub' => 'BUT KY', 'r1' => 71, 'g1' => 85, 'b1' => 105],
    ['file' => 'ban-long.jpg', 'title' => 'BAN LONG', 'sub' => 'CHIEN THAN', 'r1' => 202, 'g1' => 138, 'b1' => 4],
    ['file' => 'dai-chua-te.jpg', 'title' => 'DAI CHUA TE', 'sub' => 'BAC LINH', 'r1' => 234, 'g1' => 88, 'b1' => 12],
    ['file' => 'van-co-than-de.jpg', 'title' => 'VAN CO', 'sub' => 'THAN DE', 'r1' => 37, 'g1' => 99, 'b1' => 235],
    ['file' => 'sam-sam-den-roi.jpg', 'title' => 'SAM SAM', 'sub' => 'DEN ROI', 'r1' => 225, 'g1' => 29, 'b1' => 72],
    ['file' => 'default-cover.jpg', 'title' => 'WEBDOC', 'sub' => 'TRUYEN', 'r1' => 51, 'g1' => 65, 'b1' => 85]
];

foreach ($stories as $s) {
    $w = 300;
    $h = 420;
    $im = @imagecreatetruecolor($w, $h);
    if (!$im) continue;

    // Background gradient effect
    $bgTop = imagecolorallocate($im, $s['r1'], $s['g1'], $s['b1']);
    $bgBottom = imagecolorallocate($im, max(0, $s['r1'] - 100), max(0, $s['g1'] - 100), max(0, $s['b1'] - 100));

    for ($y = 0; $y < $h; $y++) {
        $ratio = $y / $h;
        $r = (int)($s['r1'] * (1 - $ratio) + max(0, $s['r1'] - 120) * $ratio);
        $g = (int)($s['g1'] * (1 - $ratio) + max(0, $s['g1'] - 120) * $ratio);
        $b = (int)($s['b1'] * (1 - $ratio) + max(0, $s['b1'] - 120) * $ratio);
        $color = imagecolorallocate($im, $r, $g, $b);
        imageline($im, 0, $y, $w, $y, $color);
    }

    // Border
    $white = imagecolorallocate($im, 255, 255, 255);
    $gold = imagecolorallocate($im, 252, 211, 77);
    $gray = imagecolorallocate($im, 203, 213, 225);
    $darkBorder = imagecolorallocate($im, 255, 255, 255);

    imagerectangle($im, 15, 15, $w - 15, $h - 15, $gray);
    imagerectangle($im, 18, 18, $w - 18, $h - 18, $gray);

    // Text rendering
    $font = 5; // Built-in large font
    $titleX = max(20, (int)(($w - strlen($s['title']) * imagefontwidth($font)) / 2));
    $subX = max(20, (int)(($w - strlen($s['sub']) * imagefontwidth($font)) / 2));

    imagestring($im, $font, $titleX, 160, $s['title'], $white);
    imagestring($im, $font, $subX, 200, $s['sub'], $gold);

    $footerText = "- WEBDOC TRUYEN -";
    $footX = (int)(($w - strlen($footerText) * imagefontwidth(3)) / 2);
    imagestring($im, 3, $footX, 350, $footerText, $gray);

    imagejpeg($im, $coversDir . '/' . $s['file'], 90);
    if ($s['file'] === 'default-cover.jpg') {
        imagejpeg($im, $imagesDir . '/default-cover.jpg', 90);
    }
    imagedestroy($im);
}
