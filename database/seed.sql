-- Mock Seed Data for WebDocTruyen
-- Password hash for 'admin123' and 'password123': $2y$10$e0MYzXyjpJS7Pd0RVvHwHeFOnCvlDRC496DFHD7mOj/92.gY.uomK (or standard bcrypt)

USE `webdoctruyen`;

-- 1. Roles
INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'admin', 'Quản trị viên hệ thống có toàn quyền'),
(2, 'user', 'Người dùng thông thường đọc và tương tác truyện')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- 2. Users (Password: 'admin123' for admin, 'password123' for users)
-- bcrypt hash for 'password123': $2y$10$4n907F6rFvP8g1o2Bw5b9OPyFk9c2aB7J8w1k9yL8mN9v0p1q2r3s
-- We use standard password_hash('password123', PASSWORD_BCRYPT)
INSERT INTO `users` (`id`, `role_id`, `username`, `email`, `password`, `avatar`, `status`, `created_at`) VALUES
(1, 1, 'admin', 'admin@webdoctruyen.vn', '$2y$10$WpA1qLgXhBfxZ2nOfe67Z.s7bWJtWzK.t1mU0d.jD6zE2mB9nZJ5S', 'default-avatar.png', 'active', NOW()),
(2, 2, 'nguyenvana', 'vana@gmail.com', '$2y$10$WpA1qLgXhBfxZ2nOfe67Z.s7bWJtWzK.t1mU0d.jD6zE2mB9nZJ5S', 'default-avatar.png', 'active', NOW()),
(3, 2, 'tranthib', 'thib@gmail.com', '$2y$10$WpA1qLgXhBfxZ2nOfe67Z.s7bWJtWzK.t1mU0d.jD6zE2mB9nZJ5S', 'default-avatar.png', 'active', NOW()),
(4, 2, 'lequangc', 'quangc@gmail.com', '$2y$10$WpA1qLgXhBfxZ2nOfe67Z.s7bWJtWzK.t1mU0d.jD6zE2mB9nZJ5S', 'default-avatar.png', 'active', NOW()),
(5, 2, 'phamminhd', 'minhd@gmail.com', '$2y$10$WpA1qLgXhBfxZ2nOfe67Z.s7bWJtWzK.t1mU0d.jD6zE2mB9nZJ5S', 'default-avatar.png', 'active', NOW()),
(6, 2, 'hoanglan', 'hoanglan@gmail.com', '$2y$10$WpA1qLgXhBfxZ2nOfe67Z.s7bWJtWzK.t1mU0d.jD6zE2mB9nZJ5S', 'default-avatar.png', 'active', NOW())
ON DUPLICATE KEY UPDATE `username`=VALUES(`username`);

-- 3. Authors
INSERT INTO `authors` (`id`, `name`, `slug`, `bio`) VALUES
(1, 'Thiên Tằm Thổ Đậu', 'thien-tam-tho-dau', 'Tác giả nổi tiếng với các tác phẩm Đấu Phá Thương Khung, Vũ Động Càn Khôn, Đại Chúa Tể.'),
(2, 'Nhĩ Căn', 'nhi-can', 'Cây bút đại thụ của dòng tiên hiệp: Tiên Nghịch, Cầu Ma, Nhất Niệm Vĩnh Hằng.'),
(3, 'Ngã Cật Tây Hồng Thị', 'nga-cat-tay-hong-thi', 'Tác giả của Tinh Thần Biến, Bàn Long, Thôn Phệ Tinh Không.'),
(4, 'Mặc Hương Đồng Khứu', 'mac-huong-dong-khuu', 'Nữ tác giả nổi danh với Ma Đạo Tổ Sư, Thiên Quan Tứ Phúc.'),
(5, 'Cố Mạn', 'co-man', 'Tác giả ngôn tình hiện đại hàng đầu: Sam Sam Đến Rồi, Yêu Em Từ Cái Nhìn Đầu Tiên.'),
(6, 'Nam Phái Tam Thúc', 'nam-phai-tam-thuc', 'Tác giả bộ tiểu thuyết thám hiểm kinh điển Đạo Mộ Bút Ký.'),
(7, 'Thần Đồng', 'than-dong', 'Tác giả truyện võ hiệp, kỳ ảo đặc sắc.')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- 4. Categories
INSERT INTO `categories` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Tiên Hiệp', 'tien-hiep', 'Truyện tu tiên, tu chân cầu đạo trường sinh bất tử.'),
(2, 'Kiếm Hiệp', 'kiem-hiep', 'Võ lâm giang hồ, bang phái tranh hùng, ân oán tình thù.'),
(3, 'Huyền Huyễn', 'huyen-huyen', 'Thế giới huyền ảo, ma pháp, dị giới thần thoại.'),
(4, 'Ngôn Tình', 'ngon-tinh', 'Tình yêu lãng mạn, thanh xuân vườn trường, hào môn thế gia.'),
(5, 'Đô Thị', 'do-thi', 'Bối cảnh thành phố hiện đại, lập nghiệp, thăng tiến, siêu năng.'),
(6, 'Trinh Thám', 'trinh-tham', 'Phá án, suy luận logic, giải mã bí ẩn.'),
(7, 'Khoa Huyễn', 'khoa-huyen', 'Khoa học viễn tưởng, không gian vũ trụ, công nghệ tương lai.'),
(8, 'Võng Du', 'vong-du', 'Trò chơi thực tế ảo, game online, esport.'),
(9, 'Trọng Sinh', 'trong-sinh', 'Được sống lại kiếp trước làm lại cuộc đời, nghịch thiên cải mệnh.'),
(10, 'Hài Hước', 'hai-huoc', 'Cười sảng khoái, cốt truyện dí dỏm, thư giãn nhẹ nhàng.'),
(11, 'Dị Năng', 'di-nang', 'Năng lực đặc biệt, siêu anh hùng thế giới ngầm.'),
(12, 'Lịch Sử - Quân Sự', 'lich-su-quan-su', 'Chiến tranh, binh thư mưu lược, xuyên không về cổ đại.')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- 5. Stories (12 stories)
INSERT INTO `stories` (`id`, `author_id`, `title`, `slug`, `description`, `cover_image`, `status`, `views_count`, `rating_avg`, `rating_count`, `created_at`, `updated_at`) VALUES
(1, 1, 'Đấu Phá Thương Khung', 'dau-pha-thuong-khung', 'Tại một thế giới thuộc về Đấu Khí, không hề có ma pháp hoa lệ hay võ thuật huyền ảo, chỉ có Đấu Khí phồn thịnh tới đỉnh phong! Tiêu Viêm - một thiên tài từ đỉnh cao bỗng chốc trở thành phế vật, trải qua vô vàn cay đắng để rồi bước lên con đường chinh phục đỉnh cao cường giả.', 'dau-pha-thuong-khung.jpg', 'completed', 154200, 4.85, 120, NOW() - INTERVAL 30 DAY, NOW()),
(2, 2, 'Nhất Niệm Vĩnh Hằng', 'nhat-niem-vinh-hang', 'Một niệm thành biển cả, một niệm hóa nương dâu. Một niệm trảm nghìn ma, một niệm giết vạn tiên. Duy ta niệm... vĩnh hằng! Nhân vật chính Bạch Tiểu Thuần, tính cách hài hước nhưng vô cùng sợ chết, luôn khát khao trường sinh bất lão.', 'nhat-niem-vinh-hang.jpg', 'completed', 128500, 4.90, 98, NOW() - INTERVAL 25 DAY, NOW()),
(3, 3, 'Thôn Phệ Tinh Không', 'thon-phe-tinh-khong', 'Sau thảm họa biến đổi gen RR, Trái Đất bước vào kỷ nguyên quái thú hoành hành. Thiếu niên La Phong từng bước vượt qua khảo nghiệm sinh tử, trở thành cường giả tối cao bước ra vũ trụ bao la.', 'thon-phe-tinh-khong.jpg', 'completed', 98700, 4.75, 75, NOW() - INTERVAL 20 DAY, NOW()),
(4, 1, 'Vũ Động Càn Khôn', 'vu-dong-can-khon', 'Thiếu niên Lâm Động xuất thân bình thường, tình cờ nhặt được Thạch Phù thần bí. Từ một thiếu niên yếu thế của Lâm gia, chàng bắt đầu hành trình nghịch thiên bảo vệ gia đình và người thương.', 'vu-dong-can-khon.jpg', 'completed', 85400, 4.70, 64, NOW() - INTERVAL 18 DAY, NOW()),
(5, 2, 'Tiên Nghịch', 'tien-nghich', 'Thuận vi phàm, nghịch vi tiên. Vương Lâm - một thiếu niên bình phàm bước vào con đường tu chân tàn khốc, không có thiên phú dị bẩm, chỉ dựa vào một tấm lòng kiên định và Thiết Hạt Chu Châu.', 'tien-nghich.jpg', 'completed', 112000, 4.95, 140, NOW() - INTERVAL 15 DAY, NOW()),
(6, 4, 'Ma Đạo Tổ Sư', 'ma-dao-to-su', 'Di Lăng Lão Tổ Ngụy Vô Tiện kiếp trước vạn người phỉ nhổ, bị chúng môn phái vây đánh đến tan xương nát thịt. Mười ba năm sau sống lại trong thân xác Mạc Huyền Vũ, cùng Lam Vong Cơ vén màn bí mật đen tối.', 'ma-dao-to-su.jpg', 'completed', 145000, 4.92, 180, NOW() - INTERVAL 12 DAY, NOW()),
(7, 5, 'Yêu Em Từ Cái Nhìn Đầu Tiên', 'yeu-em-tu-cai-nhin-dau-tien', 'Câu chuyện tình yêu lãng mạn ngọt ngào giữa đệ nhất cao thủ máy chủ Tiêu Nại và hoa khôi khoa công nghệ thông tin Bối Vi Vi, bắt đầu từ trò chơi trực tuyến Mộng Du Giang Hồ.', 'yeu-em-tu-cai-nhin-dau-tien.jpg', 'completed', 73200, 4.80, 55, NOW() - INTERVAL 10 DAY, NOW()),
(8, 6, 'Đạo Mộ Bút Ký', 'dao-mo-but-ky', 'Năm mươi năm trước, một nhóm trộm mộ Trường Sa đào được một quyển chiến quốc lụa sách ghi chép vị trí cổ mộ bí ẩn. Năm mươi năm sau, Ngô Tà tìm được di vật của ông nội, bắt đầu cuộc hành trình phiêu lưu rùng rợn.', 'dao-mo-but-ky.jpg', 'updating', 89300, 4.88, 90, NOW() - INTERVAL 8 DAY, NOW()),
(9, 3, 'Bàn Long', 'ban-long', 'Đại lục Ngọc Lan, cường giả vi tôn. Lâm Lôi Ba Lỗ Khắc vô tình tìm thấy Bàn Long giới chỉ, mở ra con đường tu luyện trở thành Hồn Thạch Chiến Thần rung chuyển các vị diện.', 'ban-long.jpg', 'completed', 67800, 4.65, 48, NOW() - INTERVAL 7 DAY, NOW()),
(10, 1, 'Đại Chúa Tể', 'dai-chua-te', 'Đại Thiên Thế Giới, nơi các vị diện giao thoa, vạn tộc hội tụ. Mục Trần - thiếu niên Bắc Linh cảnh cưỡi Cửu U Tước, bước ra khỏi thế giới nhỏ bé để tranh đoạt vị trí Chúa Tể.', 'dai-chua-te.jpg', 'updating', 61200, 4.60, 42, NOW() - INTERVAL 5 DAY, NOW()),
(11, 7, 'Vạn Cổ Thần Đế', 'van-co-than-de', 'Tám trăm năm trước, Minh Đế chi tử Trương Nhược Trần bị vị hôn thê Trì Dao công chúa sát hại. Tám trăm năm sau, hắn tái sinh, mở ra con đường báo thù và cứu rỗi càn khôn.', 'van-co-than-de.jpg', 'updating', 53400, 4.55, 38, NOW() - INTERVAL 3 DAY, NOW()),
(12, 5, 'Sam Sam Đến Rồi', 'sam-sam-den-roi', 'Tiết Sam Sam - cô gái ngây thơ có nhóm máu hiếm tình cờ cứu mạng em gái Tổng tài Phong Đằng. Từ đó bắt đầu chuỗi ngày làm việc và câu chuyện tình hài hước ấm áp.', 'sam-sam-den-roi.jpg', 'completed', 45100, 4.70, 31, NOW() - INTERVAL 2 DAY, NOW())
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);

-- 6. Story Categories Mapping
INSERT INTO `story_categories` (`story_id`, `category_id`) VALUES
(1, 1), (1, 3), (1, 9), -- Đấu Phá Thương Khung: Tiên Hiệp, Huyền Huyễn, Trọng Sinh
(2, 1), (2, 10), (2, 3), -- Nhất Niệm Vĩnh Hằng: Tiên Hiệp, Hài Hước, Huyền Huyễn
(3, 7), (3, 11), (3, 5), -- Thôn Phệ Tinh Không: Khoa Huyễn, Dị Năng, Đô Thị
(4, 3), (4, 2), -- Vũ Động Càn Khôn: Huyền Huyễn, Kiếm Hiệp
(5, 1), (5, 3), -- Tiên Nghịch: Tiên Hiệp, Huyền Huyễn
(6, 1), (6, 4), (6, 2), -- Ma Đạo Tổ Sư: Tiên Hiệp, Ngôn Tình, Kiếm Hiệp
(7, 4), (7, 8), (7, 5), -- Yêu Em Từ Cái Nhìn Đầu Tiên: Ngôn Tình, Võng Du, Đô Thị
(8, 6), (8, 3), -- Đạo Mộ Bút Ký: Trinh Thám, Huyền Huyễn
(9, 3), (9, 1), -- Bàn Long: Huyền Huyễn, Tiên Hiệp
(10, 3), (10, 1), -- Đại Chúa Tể: Huyền Huyễn, Tiên Hiệp
(11, 1), (11, 9), -- Vạn Cổ Thần Đế: Tiên Hiệp, Trọng Sinh
(12, 4), (12, 5), (12, 10) -- Sam Sam Đến Rồi: Ngôn Tình, Đô Thị, Hài Hước
ON DUPLICATE KEY UPDATE `category_id`=VALUES(`category_id`);

-- 7. Chapters (36+ chapters, 3 chapters for each of the 12 stories)
INSERT INTO `chapters` (`id`, `story_id`, `chapter_number`, `title`, `slug`, `content`, `views_count`, `status`, `created_at`) VALUES
-- Story 1: Đấu Phá Thương Khung
(1, 1, 1, 'Chương 1: Phế vật Tiêu Viêm', 'chuong-1-phe-vat-tieu-viem',
'<p>"Đấu lực, tam đoạn!"</p><p>Nhìn vào năm chữ to lớn có chút chói mắt trên trắc nghiệm ma thạch bi, thiếu niên mặt không chút thay đổi, khóe môi hiện lên một tia tự giễu, nắm chặt bàn tay, bởi vì lực đạo quá lớn, khiến cho móng tay hơi nhọn đâm sâu vào lòng bàn tay, mang lại từng cơn đau nhức nhối...</p><p>"Tiêu Viêm, Đấu Chi Khí: Tam Đoạn! Cấp bậc: Hạ đẳng!" Bên cạnh trắc nghiệm ma thạch bi, một vị trung niên nhân nhìn lướt qua kết quả hiển thị trên mặt bia, ngữ khí đạm mạc công bố kết quả.</p><p>Vừa nghe trung niên nhân nói xong, trên quảng trường rộng lớn liền không bất ngờ dấy lên một hồi châm chọc cùng cười nhạo xôn xao.</p><p>"Tam đoạn? Quả nhiên lại là tam đoạn! Ai, phế vật này thật sự làm mất hết mặt mũi của Tiêu gia chúng ta."</p><p>"Nếu như phụ thân hắn không phải là tộc trưởng, loại rác rưởi này đã sớm bị trục xuất khỏi gia tộc rồi, đâu có tư cách ở lại đây hưởng thụ tài nguyên tu luyện."</p><p>Tiêu Viêm lẳng lặng lắng nghe những lời bàn tán xung quanh, ánh mắt đảo qua đám thiếu niên đồng lứa từng vây quanh tâng bốc mình năm xưa, nay lại dùng ánh mắt khinh miệt nhìn mình. Hắn hít sâu một hơi, thầm tự nhủ: "Ba mươi năm Hà Đông, ba mươi năm Hà Tây, đừng khinh thiếu niên nghèo!"</p>', 12500, 'published', NOW() - INTERVAL 30 DAY),

(2, 1, 2, 'Chương 2: Đấu Khí Đại Lục', 'chuong-2-dau-khi-dai-luc',
'<p>Đêm khuya, thanh phong phất qua ngọn cây, mang theo hương thơm cỏ cây nhè nhẹ.</p><p>Tiêu Viêm ngồi xếp bằng trên đỉnh núi sau gia tộc, nhìn lên bầu trời đêm đầy sao lấp lánh. Nơi này là Đấu Khí Đại Lục, một thế giới tràn ngập sự kỳ diệu và tàn khốc. Tại nơi này, không có hoa lệ ma pháp, cũng không có võ kỹ lòe loẹt, thứ duy nhất tồn tại và quyết định tất cả chính là Đấu Khí!</p><p>Cấp bậc Đấu Khí chia làm: Đấu Chi Khí, Đấu Giả, Đấu Sư, Đại Đấu Sư, Đấu Linh, Đấu Vương, Đấu Hoàng, Đấu Tông, Đấu Tôn, Đấu Thánh, và trong truyền thuyết: Đấu Đế!</p><p>Năm bốn tuổi bắt đầu luyện khí, mười tuổi ngưng tụ Đấu Chi Khí cửu đoạn, mười một tuổi đột phá Đấu Giả trở thành thiên tài trẻ tuổi nhất gia tộc trăm năm qua. Thế nhưng, bi kịch bắt đầu từ năm mười hai tuổi, đấu khí trong cơ thể hắn không hiểu vì sao cứ liên tục tiêu tán không ngừng...</p><p>Tiêu Viêm cúi đầu, ánh mắt dừng lại ở chiếc nhẫn đen cũ kỹ đeo trên ngón tay trỏ. Chiếc nhẫn này là di vật duy nhất mà mẫu thân để lại trước khi qua đời.</p>', 11200, 'published', NOW() - INTERVAL 29 DAY),

(3, 1, 3, 'Chương 3: Khách quý Vân Lam Tông', 'chuong-3-khach-quy-van-lam-tong',
'<p>Sáng sớm hôm sau, trong đại sảnh của Tiêu gia, không khí vô cùng trang trọng.</p><p>Tộc trưởng Tiêu Chiến cùng ba vị đại trưởng lão ngồi ngay ngắn trên ghế chủ vị. Phía dưới khách quý ngồi có ba người mặc nguyệt bạch bào phục, trước ngực thêu một đồ án hình kiếm nhỏ thanh lịch cùng ba đám mây trôi bồng bềnh.</p><p>Đó là biểu tượng của đại thế lực đứng đầu Gia Mã Đế Quốc - Vân Lam Tông!</p><p>Dẫn đầu là một vị lão giả sắc mặt hồng nhuận, bên cạnh là một thiếu nữ thanh tú tuyệt lệ, dung nhan thanh lệ thoát tục, chính là nạp Lan Yên Nhiên - người đã được định ước thông gia với Tiêu Viêm từ thuở nhỏ.</p><p>"Tiêu tộc trưởng, hôm nay lão phu phụng mệnh tông chủ tới đây, thật sự có một thỉnh cầu bất tiện..." Lão giả áo trắng họ Cát mở lời, trong mắt lóe lên một tia áy náy nhưng kiên quyết.</p><p>Tiêu Chiến khẽ nhíu mày, chén trà trong tay hơi chao đảo: "Cát Lăng tiên sinh, ngài có điều gì cứ nói thẳng."</p>', 10300, 'published', NOW() - INTERVAL 28 DAY),

-- Story 2: Nhất Niệm Vĩnh Hằng
(4, 2, 1, 'Chương 1: Ta là Bạch Tiểu Thuần', 'chuong-1-ta-la-bach-tieu-thuan',
'<p>Mao Sơn dưới chân núi, có một thôn trang nhỏ tên là Thập Lý Thôn.</p><p>Lúc này, một thiếu niên chừng mười lăm mười sáu tuổi, dáng người gầy yếu, mặc một bộ áo dài vải thô giặt đến bạc màu, đang cầm trên tay một cây hương màu đen đã dính đầy bụi bặm.</p><p>"Tiên nhân gia gia, người mau xuất hiện đi... Thôn dân họ sắp đuổi theo đánh chết ta rồi!"</p><p>Thiếu niên này tên là Bạch Tiểu Thuần. Hắn từ nhỏ đã mồ côi, tính tình chất phác... theo lời hắn tự nhận, nhưng trong mắt người trong thôn thì hắn chính là một tên tiểu ma vương chuyên trộm gà bắt chó.</p><p>Quan trọng nhất là, Bạch Tiểu Thuần vô cùng sợ chết! Nguyện vọng lớn nhất đời hắn chính là trường sinh bất lão, sống mãi cùng trời đất.</p><p>Phụt!</p><p>Cây hương đen bỗng bốc lên một làn khói tím mờ ảo, bay thẳng lên chín tầng mây xanh.</p>', 9800, 'published', NOW() - INTERVAL 25 DAY),

(5, 2, 2, 'Chương 2: Linh Khê Tông', 'chuong-2-linh-khe-tong',
'<p>Một đạo cầu vồng xé toạc tầng mây hạ xuống. Từ trong ánh sáng bước ra một vị trung niên đạo nhân râu dài bay bay, tiên phong đạo cốt.</p><p>"Ngươi là hậu nhân của Bạch gia? Cây hương dẫn tiên này rốt cuộc cũng được thắp lên rồi."</p><p>Bạch Tiểu Thuần trừng lớn mắt, lập tức quỳ sụp xuống: "Tiên nhân sư thúc! Người nhận đồ nhi đi! Đồ nhi muốn tu tiên, muốn trường sinh bất tử!"</p><p>Đạo nhân Lý Thanh Hậu nhìn thiếu niên trước mắt, trong lòng cảm thán duyên phận năm xưa với tổ phụ Bạch gia, phất tay áo cuốn lấy Bạch Tiểu Thuần bay thẳng vào hư không, hướng về phía tông môn Tiên Đạo khổng lồ - Linh Khê Tông.</p>', 8900, 'published', NOW() - INTERVAL 24 DAY),

(6, 2, 3, 'Chương 3: Hỏa Táo Phòng thần kỳ', 'chuong-3-hoa-tao-phong-than-ky',
'<p>Linh Khê Tông phân làm Nam Ngạn và Bắc Ngạn, dưới chân núi là khu vực tạp dịch.</p><p>Bạch Tiểu Thuần được phân phối tới một nơi mà tất cả đệ tử tạp dịch đều tha thiết ước mơ - Hỏa Táo Phòng!</p><p>"Đại sư huynh, Hỏa Táo Phòng này có gì tốt vậy?" Bạch Tiểu Thuần nhìn vị sư huynh béo tròn như quả bóng trước mặt tò mò hỏi.</p><p>Trương Đại Bàn cười híp cả mắt, vỗ vào chiếc bụng tròn vo: "Tiểu đệ, ngươi chưa biết đấy thôi. Ở Hỏa Táo Phòng chúng ta ngày ngày nấu linh thực, linh đan bồi bổ, đệ tử nơi này ai ai cũng béo tốt, thọ mệnh dài hơn người ngoài ít nhất mấy chục năm!"</p><p>Bạch Tiểu Thuần nghe hai chữ "thọ mệnh", hai mắt lập tức sáng rực như đèn pha.</p>', 8100, 'published', NOW() - INTERVAL 23 DAY),

-- Story 3: Thôn Phệ Tinh Không
(7, 3, 1, 'Chương 1: La Phong', 'chuong-1-la-phong',
'<p>Năm 2056, Giang Nam Thị.</p><p>Trên sân thượng của một khu chung cư cũ kỹ giá rẻ, thiếu niên La Phong mặc bộ đồ thể thao đơn giản, đang thực hiện những động tác rèn luyện thể lực cơ bản.</p><p>Quyền lực trắc nghiệm: 950kg! Vận tốc: 26m/s! Thần kinh phản ứng: Ưu đẳng!</p><p>"Chỉ còn một bước nữa là mình có thể vượt qua kỳ thi sát hạch Chuẩn Võ Giả rồi." La Phong lau mồ hôi trên trán, ánh mắt kiên định nhìn về phía trung tâm thành phố với những tòa nhà chọc trời hào nhoáng.</p>', 7500, 'published', NOW() - INTERVAL 20 DAY),

(8, 3, 2, 'Chương 2: Sát hạch Chuẩn Võ Giả', 'chuong-2-sat-hach-chuan-vo-gia',
'<p>Cực Hạn Vũ Quán, hội quán lớn nhất toàn cầu do đệ nhất cường giả Hồng sáng lập.</p><p>La Phong đứng trong hàng ngũ những học viên tham gia khảo hạch. Khí áp trong phòng huấn luyện vô cùng ngột ngạt.</p><p>"Người tiếp theo: La Phong!" Giọng nói của giám khảo vang lên.</p><p>La Phong bước lên trước máy đo quyền lực, hít sâu, eo xoay chuyển, phát lực từ gót chân truyền thẳng lên nắm đấm phát ra tiếng nổ giòn tan trong không khí. Bùm!</p><p>Màn hình điện tử lập tức nhảy số: 1021 kg!</p>', 6900, 'published', NOW() - INTERVAL 19 DAY),

(9, 3, 3, 'Chương 3: Tinh thần niệm sư thức tỉnh', 'chuong-3-tinh-than-niem-su-thuc-tinh',
'<p>Đêm sát hạch thực chiến quái thú tại khu vực hoang dã 0231.</p><p>Đối mặt với bầy Độc Giác Trư hung hãn, trong giây phút hiểm nghèo nhất, đầu óc La Phong bỗng truyền đến một cơn đau nhức xé rách linh hồn. Một quả cầu màu bạc trong thức hải của hắn bắt đầu xoay chuyển dữ dội...</p><p>Vút! Vút! Lưỡi phi đao bay lơ lửng trên không trung theo ý niệm của hắn, trong nháy mắt xuyên thủng đầu thủ lĩnh bầy quái thú!</p><p>"Tinh Thần Niệm Sư! Mình đã thức tỉnh trở thành Tinh Thần Niệm Sư!"</p>', 6400, 'published', NOW() - INTERVAL 18 DAY),

-- Story 4: Vũ Động Càn Khôn
(10, 4, 1, 'Chương 1: Lâm Động', 'chuong-1-lam-dong', '<p>Thanh Dương Trấn, Lâm gia hậu sơn.</p><p>Một thiếu niên trên người đầy vết bầm tím đang cắn răng đấm vào bao cát. Dù máu đã rỉ ra từ kẽ ngón tay, hắn vẫn không dừng lại một giây.</p><p>"Phụ thân bị trọng thương, gia tộc ngày một suy tàn, mình nhất định phải mạnh lên!"</p>', 5400, 'published', NOW() - INTERVAL 18 DAY),
(11, 4, 2, 'Chương 2: Thạch Phù thần bí', 'chuong-2-thach-phu-than-by', '<p>Trong sơn động sau núi, Lâm Động tình cờ rơi xuống một hồ nước ngầm phát sáng. Dưới đáy hồ, một khối đá hình thù kỳ lạ phát ra ánh sáng ôn hòa bay vào lòng bàn tay hắn, thẩm thấu trực tiếp vào trong máu huyết.</p>', 5100, 'published', NOW() - INTERVAL 17 DAY),
(12, 4, 3, 'Chương 3: Tôi Thể Thất Trọng', 'chuong-3-toi-the-that-trong', '<p>Nhờ có dịch thể thần kỳ ngưng tụ từ Thạch Phù, tốc độ tu luyện của Lâm Động tăng vọt gấp mười lần, trực tiếp đột phá Tôi Thể cảnh tầng thứ bảy!</p>', 4800, 'published', NOW() - INTERVAL 16 DAY),

-- Story 5: Tiên Nghịch
(13, 5, 1, 'Chương 1: Ly gia cầu đạo', 'chuong-1-ly-gia-cau-dao', '<p>Triệu Quốc, một thôn xóm bình dị vùng biên cương. Thiếu niên Vương Lâm ôm tay nải, từ biệt cha mẹ già để lên núi tham gia kỳ sát hạch đệ tử Hằng Nhạc Phái.</p>', 6200, 'published', NOW() - INTERVAL 15 DAY),
(14, 5, 2, 'Chương 2: Thiên phú bình phàm', 'chuong-2-thien-phu-binh-pham', '<p>"Tư chất hạ đẳng, linh căn tàn khuyết, không thể tu tiên!" Lời phán xét của trưởng lão như sét đánh ngang tai thiếu niên tràn đầy nhiệt huyết.</p>', 5800, 'published', NOW() - INTERVAL 14 DAY),
(15, 5, 3, 'Chương 3: Hạt châu thần bí', 'chuong-3-hat-chau-than-by', '<p>Tuyệt vọng nhảy xuống vách đá, Vương Lâm không chết mà rơi vào tổ chim ưng, phát hiện một hạt châu kỳ lạ có khắc hình chín con chim thần bí...</p>', 5500, 'published', NOW() - INTERVAL 13 DAY),

-- Story 6: Ma Đạo Tổ Sư
(16, 6, 1, 'Chương 1: Trọng sinh hiến xá', 'chuong-1-trong-sinh-hien-xa', '<p>Ngụy Vô Tiện vừa mở mắt, đã bị một cái tát trời giáng cùng tiếng mắng chửi thậm tệ: "Đồ điên họ Mạc, dám giành đồ của thiếu gia!"</p><p>Hắn sờ lên mặt, phát hiện mình được hiến xá sống lại trong thân xác của một kẻ đoạn tụ bị ức hiếp.</p>', 8900, 'published', NOW() - INTERVAL 12 DAY),
(17, 6, 2, 'Chương 2: Mạc gia trang biến cố', 'chuong-2-mac-gia-trang-bien-co', '<p>Đêm khuya tại Mạc gia trang, quỷ thủ hung tàn xuất hiện cắn nuốt sinh mạng. Đệ tử Lam gia cầu viện, tiếng đàn Cổ Cầm quen thuộc vang lên giữa màn đêm...</p>', 8300, 'published', NOW() - INTERVAL 11 DAY),
(18, 6, 3, 'Chương 3: Lam Vong Cơ', 'chuong-3-lam-vong-co', '<p>Bạch y như tuyết, mạt ngạch đoan trang, Tị Trần kiếm quang lạnh lẽo. Người đó cuối cùng cũng xuất hiện, ánh mắt chạm nhau sau mười ba năm cách biệt sinh tử.</p>', 7900, 'published', NOW() - INTERVAL 10 DAY),

-- Story 7: Yêu Em Từ Cái Nhìn Đầu Tiên
(19, 7, 1, 'Chương 1: Bị đá trong game', 'chuong-1-bi-da-trong-game', '<p>Lô Vĩ Vi Vi - hồng y nữ hiệp cấp độ cao nhất bang hội bỗng nhận được lời đề nghị ly hôn từ phu quân Chân Thủy Vô Hương trong game Mộng Du Giang Hồ.</p>', 4500, 'published', NOW() - INTERVAL 10 DAY),
(20, 7, 2, 'Chương 2: Lời cầu hôn của Nhất Tiếu Nại Hà', 'chuong-2-loi-cau-hon-cua-nhat-tieu-nai-ha', '<p>Dưới gốc cây liễu bên bờ sông Lạc Dương, đệ nhất cao thủ toàn server Nhất Tiếu Nại Hà gửi tin nhắn thoại thanh lãnh: "Vi Vi, chúng ta kết hôn đi."</p>', 4300, 'published', NOW() - INTERVAL 9 DAY),
(21, 7, 3, 'Chương 3: Hôn lễ thế kỷ', 'chuong-3-hon-le-the-ky', '<p>Pháo hoa rực rỡ thắp sáng thành Lạc Dương, đoàn kiệu hoa tám người khiêng diễu hành quanh bản đồ trong sự ngỡ ngàng của toàn thể người chơi máy chủ.</p>', 4100, 'published', NOW() - INTERVAL 8 DAY),

-- Story 8: Đạo Mộ Bút Ký
(22, 8, 1, 'Chương 1: Quyển lụa Chiến Quốc', 'chuong-1-quyen-lua-chien-quoc', '<p>Tại cửa hàng đồ cổ Hàng Châu, Ngô Tà nhận được một bức ảnh chụp bản thảo cổ văn từ người bạn cũ, mở ra manh mối về một ngôi mộ cổ thời Thất Tinh Lỗ Vương.</p>', 4900, 'published', NOW() - INTERVAL 8 DAY),
(23, 8, 2, 'Chương 2: Xuất phát vào núi', 'chuong-2-xuat-phat-vao-nui', '<p>Đoàn người của Tam Thúc cùng chàng trai bí ẩn mang hắc đao Trương Khởi Linh bắt đầu tiến sâu vào khu rừng nguyên sinh Sơn Đông.</p>', 4600, 'published', NOW() - INTERVAL 7 DAY),
(24, 8, 3, 'Chương 3: Thủy động kinh hoàng', 'chuong-3-thuy-dong-kinh-hoang', '<p>Con thuyền nhỏ trôi vào hang động ngập nước tối đen, xung quanh xuất hiện hàng vạn con thi thiềm bò lổm ngổm trên vách đá...</p>', 4400, 'published', NOW() - INTERVAL 6 DAY),

-- Story 9: Bàn Long
(25, 9, 1, 'Chương 1: Ô Sơn trấn thiếu niên', 'chuong-1-o-son-tran-thieu-nien', '<p>Lâm Lôi ngắm nhìn bức tượng Long Huyết Chiến Sĩ khổng lồ, thầm mơ ước một ngày khôi phục lại vinh quang của gia tộc Ba Lỗ Khắc.</p>', 3800, 'published', NOW() - INTERVAL 7 DAY),
(26, 9, 2, 'Chương 2: Bàn Long giới chỉ', 'chuong-2-ban-long-gioi-chi', '<p>Trong đống đổ nát của phủ đệ cũ, Lâm Lôi nhặt được một chiếc nhẫn kỳ lạ dính bùn đất, bên trong phong ấn linh hồn Đại Ma Đạo Sư Đức Lâm Kha Đặc.</p>', 3600, 'published', NOW() - INTERVAL 6 DAY),
(27, 9, 3, 'Chương 3: Ma pháp thiên tài', 'chuong-3-ma-phap-thien-tai', '<p>Kỳ kiểm tra ma pháp học viện Ân Tư Đặc, Lâm Lôi biểu hiện song hệ Địa - Phong siêu đẳng làm chấn động toàn thể viện trưởng các phân khoa.</p>', 3400, 'published', NOW() - INTERVAL 5 DAY),

-- Story 10: Đại Chúa Tể
(28, 10, 1, 'Chương 1: Bắc Linh thiếu niên', 'chuong-1-bac-linh-thieu-nien', '<p>Mục Trần ngồi dưới gốc cây đại thụ, nhìn thiếu nữ Lạc Ly tóc bạc dung nhan tuyệt mỹ, trong lòng dấy lên quyết tâm bảo vệ nàng trước mọi phong ba.</p>', 3200, 'published', NOW() - INTERVAL 5 DAY),
(29, 10, 2, 'Chương 2: Linh Lộ sát thần', 'chuong-2-linh-lo-sat-than', '<p>Danh hiệu Huyết Họa Giả của Mục Trần trên Linh Lộ khiến các thiên kiêu thế gia mỗi khi nghe đến tên đều biến sắc kinh sợ.</p>', 3000, 'published', NOW() - INTERVAL 4 DAY),
(30, 10, 3, 'Chương 3: Cửu U Tước phong ấn', 'chuong-3-cuu-u-tuoc-phong-an', '<p>Hắc ám hỏa diễm bùng cháy trong cơ thể, Mục Trần cùng Cửu U Tước đạt thành huyết thệ cộng sinh thần thánh.</p>', 2900, 'published', NOW() - INTERVAL 3 DAY),

-- Story 11: Vạn Cổ Thần Đế
(31, 11, 1, 'Chương 1: Bát bách niên hậu', 'chuong-1-bat-bach-nien-hau', '<p>Tỉnh lại sau giấc mộng tám trăm năm, Trương Nhược Trần phát hiện vị hôn thê giết mình năm xưa giờ đã trở thành Nữ Hoàng thống trị Côn Lôn Giới.</p>', 2700, 'published', NOW() - INTERVAL 3 DAY),
(32, 11, 2, 'Chương 2: Cửu Thiên Huyền Thể', 'chuong-2-cuu-thien-huyen-the', '<p>Mở ra Cửu Thiên Thần Thể Lạc Ấn, kinh mạch thông suốt hấp thu thiên địa linh khí với tốc độ kinh hồn bạt vía.</p>', 2500, 'published', NOW() - INTERVAL 2 DAY),
(33, 11, 3, 'Chương 3: Thời Không Bí Điển', 'chuong-3-thoi-khong-bi-dien', '<p>Bí mật của Thần Khí Thời Không Truyền Thừa Đồ được kích hoạt, một ngày tu luyện trong không gian này bằng mười ngày thế giới bên ngoài.</p>', 2400, 'published', NOW() - INTERVAL 1 DAY),

-- Story 12: Sam Sam Đến Rồi
(34, 12, 1, 'Chương 1: Máu hiếm Panda', 'chuong-1-mau-hiem-panda', '<p>Nửa đêm nhận được cuộc gọi khẩn cấp từ bệnh viện, Tiết Sam Sam vội vàng tới hiến máu cứu em gái Chủ tịch tập đoàn Phong Đằng.</p>', 2200, 'published', NOW() - INTERVAL 2 DAY),
(35, 12, 2, 'Chương 2: Hộp cơm trưa tổng tài', 'chuong-2-hop-com-trua-tong-tai', '<p>Từ hôm đó, mỗi trưa Sam Sam đều được "triệu hồi" lên văn phòng Chủ tịch tầng cao nhất để ăn trọn vẹn suất cơm dinh dưỡng cùng Phong Đằng.</p>', 2100, 'published', NOW() - INTERVAL 1 DAY),
(36, 12, 3, 'Chương 3: Tiết Sam Sam cố lên', 'chuong-3-tiet-sam-sam-co-len', '<p>Đối mặt với ánh nhìn lạnh như băng nhưng chất chứa cưng chiều của Đại boss, Sam Sam chỉ biết thầm nhủ: Ăn cơm mới là việc quan trọng nhất!</p>', 2000, 'published', NOW())
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);

-- 8. Ratings
INSERT INTO `ratings` (`user_id`, `story_id`, `rating`, `created_at`) VALUES
(2, 1, 5, NOW() - INTERVAL 10 DAY),
(3, 1, 5, NOW() - INTERVAL 9 DAY),
(4, 1, 4, NOW() - INTERVAL 8 DAY),
(2, 2, 5, NOW() - INTERVAL 7 DAY),
(3, 2, 5, NOW() - INTERVAL 6 DAY),
(5, 2, 5, NOW() - INTERVAL 5 DAY),
(3, 6, 5, NOW() - INTERVAL 4 DAY),
(6, 6, 5, NOW() - INTERVAL 3 DAY),
(2, 7, 5, NOW() - INTERVAL 2 DAY),
(6, 7, 4, NOW() - INTERVAL 1 DAY)
ON DUPLICATE KEY UPDATE `rating`=VALUES(`rating`);

-- 9. Follows & Favorites
INSERT INTO `follows` (`user_id`, `story_id`, `created_at`) VALUES
(2, 1, NOW() - INTERVAL 10 DAY),
(2, 2, NOW() - INTERVAL 9 DAY),
(2, 6, NOW() - INTERVAL 8 DAY),
(3, 1, NOW() - INTERVAL 7 DAY),
(3, 7, NOW() - INTERVAL 6 DAY),
(4, 3, NOW() - INTERVAL 5 DAY),
(5, 5, NOW() - INTERVAL 4 DAY),
(6, 6, NOW() - INTERVAL 3 DAY)
ON DUPLICATE KEY UPDATE `created_at`=VALUES(`created_at`);

INSERT INTO `favorites` (`user_id`, `story_id`, `created_at`) VALUES
(2, 1, NOW() - INTERVAL 10 DAY),
(2, 6, NOW() - INTERVAL 9 DAY),
(3, 2, NOW() - INTERVAL 8 DAY),
(6, 7, NOW() - INTERVAL 7 DAY)
ON DUPLICATE KEY UPDATE `created_at`=VALUES(`created_at`);

-- 10. Reading History
INSERT INTO `reading_history` (`user_id`, `story_id`, `chapter_id`, `progress_percent`, `last_read_at`) VALUES
(2, 1, 2, 65, NOW() - INTERVAL 1 HOUR),
(2, 2, 4, 100, NOW() - INTERVAL 5 HOUR),
(3, 6, 17, 40, NOW() - INTERVAL 2 HOUR),
(4, 3, 8, 80, NOW() - INTERVAL 1 DAY)
ON DUPLICATE KEY UPDATE `progress_percent`=VALUES(`progress_percent`);

-- 11. Comments & Replies
INSERT INTO `comments` (`id`, `user_id`, `story_id`, `chapter_id`, `parent_id`, `content`, `created_at`) VALUES
(1, 2, 1, 1, NULL, 'Truyện đọc rất cuốn, mở đầu rất cảm xúc và nghẹt thở!', NOW() - INTERVAL 5 DAY),
(2, 3, 1, 1, 1, 'Đồng ý với bác, đoạn ba mươi năm Hà Đông đọc nổi da gà.', NOW() - INTERVAL 4 DAY),
(3, 4, 1, NULL, NULL, 'Tiêu Viêm quá đỉnh, tuyệt tác tiên hiệp kinh điển!', NOW() - INTERVAL 3 DAY),
(4, 5, 2, 4, NULL, 'Bạch Tiểu Thuần hài hước quá, cười đau cả ruột đoạn Hỏa Táo Phòng.', NOW() - INTERVAL 2 DAY),
(5, 6, 6, 16, NULL, 'Văn phong Mặc Hương Đồng Khứu sâu sắc và cảm động vô cùng.', NOW() - INTERVAL 1 DAY)
ON DUPLICATE KEY UPDATE `content`=VALUES(`content`);

-- 12. Views Log (for dashboard chart statistics)
INSERT INTO `views_log` (`story_id`, `chapter_id`, `view_date`, `views_count`) VALUES
(1, 1, CURDATE() - INTERVAL 6 DAY, 150),
(1, 1, CURDATE() - INTERVAL 5 DAY, 220),
(1, 1, CURDATE() - INTERVAL 4 DAY, 310),
(1, 1, CURDATE() - INTERVAL 3 DAY, 450),
(1, 1, CURDATE() - INTERVAL 2 DAY, 520),
(1, 1, CURDATE() - INTERVAL 1 DAY, 680),
(1, 1, CURDATE(), 750),
(2, 4, CURDATE() - INTERVAL 6 DAY, 120),
(2, 4, CURDATE() - INTERVAL 5 DAY, 180),
(2, 4, CURDATE() - INTERVAL 4 DAY, 290),
(2, 4, CURDATE() - INTERVAL 3 DAY, 380),
(2, 4, CURDATE() - INTERVAL 2 DAY, 490),
(2, 4, CURDATE() - INTERVAL 1 DAY, 580),
(2, 4, CURDATE(), 620)
ON DUPLICATE KEY UPDATE `views_count`=VALUES(`views_count`);
