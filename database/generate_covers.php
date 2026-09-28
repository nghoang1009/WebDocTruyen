<?php
/**
 * Generate SVG Cover Images for Mock Stories
 */

require_once __DIR__ . '/../config.php';

$coversDir = UPLOAD_PATH . '/covers';
if (!is_dir($coversDir)) {
    mkdir($coversDir, 0777, true);
}

$imagesDir = PUBLIC_PATH . '/assets/images';
if (!is_dir($imagesDir)) {
    mkdir($imagesDir, 0777, true);
}

$stories = [
    ['file' => 'dau-pha-thuong-khung.jpg', 'title' => 'ĐẤU PHÁ', 'sub' => 'THƯƠNG KHUNG', 'bg1' => '#dc2626', 'bg2' => '#7f1d1d', 'author' => 'Thiên Tằm Thổ Đậu'],
    ['file' => 'nhat-niem-vinh-hang.jpg', 'title' => 'NHẤT NIỆM', 'sub' => 'VĨNH HẰNG', 'bg1' => '#0284c7', 'bg2' => '#0c4a6e', 'author' => 'Nhĩ Căn'],
    ['file' => 'thon-phe-tinh-khong.jpg', 'title' => 'THÔN PHỆ', 'sub' => 'TINH KHÔNG', 'bg1' => '#4f46e5', 'bg2' => '#312e81', 'author' => 'Ngã Cật Tây Hồng Thị'],
    ['file' => 'vu-dong-can-khon.jpg', 'title' => 'VŨ ĐỘNG', 'sub' => 'CÀN KHÔN', 'bg1' => '#d97706', 'bg2' => '#78350f', 'author' => 'Thiên Tằm Thổ Đậu'],
    ['file' => 'tien-nghich.jpg', 'title' => 'TIÊN NGHỊCH', 'sub' => 'TU CHÂN', 'bg1' => '#059669', 'bg2' => '#064e3b', 'author' => 'Nhĩ Căn'],
    ['file' => 'ma-dao-to-su.jpg', 'title' => 'MA ĐẠO', 'sub' => 'TỔ SƯ', 'bg1' => '#7c3aed', 'bg2' => '#4c1d95', 'author' => 'Mặc Hương Đồng Khứu'],
    ['file' => 'yeu-em-tu-cai-nhin-dau-tien.jpg', 'title' => 'YÊU EM TỪ CÁI', 'sub' => 'NHÌN ĐẦU TIÊN', 'bg1' => '#db2777', 'bg2' => '#831843', 'author' => 'Cố Mạn'],
    ['file' => 'dao-mo-but-ky.jpg', 'title' => 'ĐẠO MỘ', 'sub' => 'BÚT KÝ', 'bg1' => '#475569', 'bg2' => '#0f172a', 'author' => 'Nam Phái Tam Thúc'],
    ['file' => 'ban-long.jpg', 'title' => 'BÀN LONG', 'sub' => 'CHIẾN THẦN', 'bg1' => '#ca8a04', 'bg2' => '#713f12', 'author' => 'Ngã Cật Tây Hồng Thị'],
    ['file' => 'dai-chua-te.jpg', 'title' => 'ĐẠI CHÚA TỂ', 'sub' => 'BẮC LINH', 'bg1' => '#ea580c', 'bg2' => '#7c2d12', 'author' => 'Thiên Tằm Thổ Đậu'],
    ['file' => 'van-co-than-de.jpg', 'title' => 'VẠN CỔ', 'sub' => 'THẦN ĐẾ', 'bg1' => '#2563eb', 'bg2' => '#1e3a8a', 'author' => 'Thần Đồng'],
    ['file' => 'sam-sam-den-roi.jpg', 'title' => 'SAM SAM', 'sub' => 'ĐẾN RỒI', 'bg1' => '#e11d48', 'bg2' => '#881337', 'author' => 'Cố Mạn'],
    ['file' => 'default-cover.jpg', 'title' => 'TRUYỆN CHỮ', 'sub' => 'WEBDOC TRUYEN', 'bg1' => '#334155', 'bg2' => '#0f172a', 'author' => 'WebDocTruyen']
];

function generateSvgCover($title, $sub, $bg1, $bg2, $author) {
    return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 420" width="300" height="420">
  <defs>
    <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:{$bg1};stop-opacity:1" />
      <stop offset="100%" style="stop-color:{$bg2};stop-opacity:1" />
    </linearGradient>
    <linearGradient id="overlay" x1="0%" y1="100%" x2="0%" y2="0%">
      <stop offset="0%" style="stop-color:#000000;stop-opacity:0.85" />
      <stop offset="50%" style="stop-color:#000000;stop-opacity:0.2" />
      <stop offset="100%" style="stop-color:#000000;stop-opacity:0.5" />
    </linearGradient>
  </defs>
  <rect width="300" height="420" fill="url(#grad)" rx="8" />
  <rect width="300" height="420" fill="url(#overlay)" rx="8" />
  <rect x="15" y="15" width="270" height="390" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="1.5" rx="4" />
  <circle cx="150" cy="140" r="45" fill="rgba(255,255,255,0.1)" />
  <path d="M125 135 L150 115 L175 135 M150 115 L150 165" stroke="#ffffff" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" fill="none" />
  <text x="150" y="240" fill="#ffffff" font-family="system-ui, -apple-system, sans-serif" font-size="24" font-weight="900" text-anchor="middle" letter-spacing="1">{$title}</text>
  <text x="150" y="275" fill="#fcd34d" font-family="system-ui, -apple-system, sans-serif" font-size="20" font-weight="800" text-anchor="middle" letter-spacing="1">{$sub}</text>
  <line x1="80" y1="310" x2="220" y2="310" stroke="rgba(255,255,255,0.3)" stroke-width="1" />
  <text x="150" y="340" fill="#cbd5e1" font-family="system-ui, -apple-system, sans-serif" font-size="13" font-weight="500" text-anchor="middle">Tác giả: {$author}</text>
  <text x="150" y="375" fill="#94a3b8" font-family="system-ui, -apple-system, sans-serif" font-size="11" font-weight="600" text-anchor="middle" letter-spacing="2">WEBDOC TRUYEN</text>
</svg>
SVG;
}

foreach ($stories as $s) {
    $svg = generateSvgCover($s['title'], $s['sub'], $s['bg1'], $s['bg2'], $s['author']);
    file_put_contents($coversDir . '/' . $s['file'], $svg);
}

// Copy default cover to assets/images
$defaultSvg = generateSvgCover('TRUYỆN CHỮ', 'WEBDOC TRUYEN', '#334155', '#0f172a', 'WebDocTruyen');
file_put_contents($imagesDir . '/default-cover.jpg', $defaultSvg);
file_put_contents($imagesDir . '/default-cover.svg', $defaultSvg);
