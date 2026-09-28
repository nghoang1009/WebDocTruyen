# WebDocTruyen - Nền Tảng Đọc Truyện Online Hoàn Chỉnh

Hệ thống website đọc truyện online xây dựng bằng **PHP MVC thuần (OOP, PDO, Clean Architecture)** kết hợp giao diện hiện đại **Modern CSS Variables (Dark/Light/Sepia Mode)** và **Vanilla JS ES6+ (Debounce Search, Reader Engine)**.

---

## 1. Cấu Trúc Dự Án

```text
WebDocTruyen/
├── api/                           # REST API Controllers & Endpoints
├── app/
│   ├── core/                      # Core Framework (Database Singleton, Router, Auth, Controller, Model, Request, Response)
│   ├── controllers/               # Auth, Story, Chapter, Category, Author, Interaction, Admin Controllers
│   ├── models/                    # User, Story, Chapter, Category, Author, Comment, Rating, Interaction, Stats
│   └── helpers/                   # Slug generator, Security sanitization, Upload handler
├── database/
│   ├── schema.sql                 # DDL Cơ sở dữ liệu (13 bảng, Khóa ngoại, Index, utf8mb4)
│   ├── seed.sql                   # Dữ liệu mẫu (12+ truyện, 36+ chương, 12 thể loại, 7 tác giả, 6 tài khoản)
│   └── init.php                   # Script khởi tạo Database tự động (CLI / Web)
├── public/
│   ├── index.php                  # Web Entry Point & REST API Router
│   ├── assets/
│   │   ├── css/ (style.css, reader.css)
│   │   ├── js/ (api.js, app.js, reader.js, story.js, admin.js)
│   │   └── images/
│   └── views/
│       ├── layouts/ (header, footer, admin-sidebar)
│       ├── home.php               # Trang chủ (Banner, Section Hot, Mới, Đang đọc, Hoàn thành)
│       ├── stories.php            # Danh sách truyện + Bộ lọc + Sort + Phân trang
│       ├── search.php             # Tìm kiếm truyện + Debounce
│       ├── story-detail.php       # Chi tiết truyện + Đánh giá 5 sao + Bình luận đa cấp
│       ├── reader.php             # Trình đọc truyện tập trung (Tùy biến Font, Size, Theme, Hotkeys)
│       ├── history.php            # Lịch sử đọc & Tiến độ %
│       ├── following.php          # Danh sách truyện theo dõi
│       ├── profile.php            # Trang cá nhân & Đổi mật khẩu
│       ├── auth/ (login, register)
│       └── admin/                 # Dashboard, Stories, Chapters, Categories, Authors, Users, Comments
├── tests/
│   └── run_tests.php              # Automated Test Suite (10 unit & integration tests)
├── config.php                     # Cấu hình hệ thống (DB, App URL, JWT Key)
└── .htaccess                      # URL Rewrite
```

---

## 2. Hướng Dẫn Cài Đặt & Chạy

### Bước 1: Khởi động XAMPP
1. Mở **XAMPP Control Panel**.
2. Khởi động **Apache** và **MySQL**.

### Bước 2: Khởi tạo Database & Dữ liệu mẫu
Mở trình duyệt và truy cập:
```text
http://localhost/WebDocTruyen/database/init.php
```
*(Hoặc chạy lệnh: `php database/init.php` trong terminal)*

### Bước 3: Trải nghiệm Website
- **Trang chủ**: `http://localhost/WebDocTruyen/`
- **Tài khoản mặc định**:
  - **Quản trị viên (Admin)**:
    - Tài khoản: `admin`
    - Mật khẩu: `password123`
    - Truy cập Admin Panel: `http://localhost/WebDocTruyen/admin`
  - **Người dùng (User)**:
    - Tài khoản: `nguyenvana`
    - Mật khẩu: `password123`

---

## 3. Chức Năng Nổi Bật

1. **Reader Engine Chuyên Sâu**:
   - Tùy chỉnh Cỡ chữ (14px - 32px), Dãn dòng (1.4 - 2.4), Phông chữ (Sans-serif, Roboto, Merriweather, Lora).
   - 4 Bộ màu đọc: Tối (Dark), Sáng (Light), Sepia (Vàng ấm dịu mắt), Đen tuyền (Night OLED).
   - Tùy chỉnh Độ rộng khung đọc (Hẹp, Vừa, Rộng, Toàn màn hình).
   - Phím tắt bàn phím: Mũi tên Trái (Chương trước), Mũi tên Phải (Chương sau).
   - Tự động lưu vị trí cuộn & tiến độ đọc % vào LocalStorage và đồng bộ tài khoản máy chủ.
2. **Bộ Lọc & Tìm Kiếm**:
   - Tìm kiếm thời gian thực (Debounce 300ms) ngay trên thanh Header.
   - Lọc đa chiều theo Thể loại, Tác giả, Trạng thái (Đang ra / Hoàn thành), Sắp xếp theo Lượt xem, Đánh giá, Ngày cập nhật, Tên A-Z.
3. **Tương Tác Độc Giả**:
   - Đánh giá 5 sao tức thì (tự động tính điểm trung bình).
   - Bình luận & Phản hồi đa cấp (hỗ trợ xóa/sửa bình luận của chính mình).
   - Theo dõi truyện và lưu danh sách yêu thích.
4. **Admin Dashboard Toàn Diện**:
   - Biểu đồ thống kê lượt xem 7 ngày, Top truyện & chương đọc nhiều nhất.
   - CRUD Truyện (Upload ảnh bìa, tự sinh Slug, gán nhiều thể loại, gán tác giả).
   - Quản lý Chương (Soạn thảo nội dung chương, chế độ Bản nháp / Công khai).
   - Quản lý Thể loại, Tác giả, Khóa/Mở khóa tài khoản người dùng, Kiểm duyệt bình luận.
5. **Bảo Mật Cao**:
   - PDO Prepared Statements chống SQL Injection.
   - Sanitization lọc sạch mã độc XSS trong mô tả và nội dung chương.
   - Password Hashing `PASSWORD_BCRYPT`.
   - Phân quyền nghiêm ngặt Middleware RBAC (User vs Admin).
