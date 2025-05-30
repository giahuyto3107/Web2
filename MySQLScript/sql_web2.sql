DROP DATABASE IF EXISTS web2_sql;

CREATE DATABASE IF NOT EXISTS web2_sql;
USE web2_sql;

DROP TABLE IF EXISTS cart_items, order_items, orders, purchase_order_items, 
					 purchase_order, supplier, user, product, category, 
                     account, role, status, review, permission, role_permission;

CREATE TABLE IF NOT EXISTS status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    status_name VARCHAR(50) NOT NULL,
    status_description VARCHAR(255)
);

INSERT INTO status (id, status_name, status_description) VALUES
    (1, 'Hoạt động', 'Bản ghi hiện đang hoạt động và sử dụng được'),
    (2, 'Không hoạt động', 'Bản ghi hiện không hoạt động và không được sử dụng'),
    (3, 'Đang chờ xử lý', 'Bản ghi đang chờ xử lý hoặc phê duyệt'),
    (4, 'Đang giao hàng', 'Bản ghi đang được giao và trong quá trình vận chuyển'),
    (5, 'Hoàn thành', 'Bản ghi đã được hoàn thành thành công'),
    (6, 'Đã xóa', 'Bản ghi đã bị xóa'),
    (7, 'Đã hủy', 'Bản ghi đã bị hủy');


CREATE TABLE IF NOT EXISTS role (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    role_description VARCHAR(255),
    status_id INT,
        FOREIGN KEY (status_id) REFERENCES status(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

INSERT INTO `role` (`id`, `role_name`, `role_description`, `status_id`) 
VALUES
    (1, 'Quản trị viên', 'Quản trị viên có toàn quyền truy cập', 1),
    (2, 'Khách hàng', 'Khách hàng thông thường', 1),
    (3, 'Người cung cấp', 'Nhà cung cấp sản phẩm', 1),
    (4, 'Quản lý', 'Quản lý với quyền truy cập hạn chế', 1);

CREATE TABLE IF NOT EXISTS account (
    account_id INT AUTO_INCREMENT PRIMARY KEY,
    account_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    status_id INT,
    last_login TIMESTAMP NULL,
    role_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (status_id) REFERENCES status(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    FOREIGN KEY (role_id) REFERENCES role(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE    
);


	INSERT INTO `account` (`account_name`, `email`, `password_hash`, `status_id`, `last_login`, `role_id`, `created_at`, `updated_at`) 
VALUES
    ('taikhoan1', 'taikhoan1@gmail.com', '$2y$10$e/PBg6HlcflH462D0cSgtuLQCEeYTgAl9wjvQhGydRo.wNvyok0ge', 1, NULL, 1, '2025-04-20 08:11:58', '2025-04-20 08:11:58'), -- Quản trị viên
    ('taikhoan2', 'taikhoan2@gmail.com', '$2y$10$3d0o5XxM2WGpCQyTroTFgOwyiq74D8r.1Yb454to4yrF7S2jaAD4K', 1, NULL, 2, '2025-04-20 08:14:10', '2025-04-20 08:14:10'), -- Khách hàng
    ('taikhoan3', 'taikhoan3@gmail.com', '$2y$10$KP3Yc9x0AMGFk1nFv7eUtuQlSZyxI.U9sNTIlU78G7Y6Bx2ZrFPiy', 1, NULL, 3, '2025-04-20 08:14:36', '2025-04-20 08:14:36'), -- Nhà cung cấp
    ('taikhoan4', 'taikhoan4@gmail.com', '$2y$10$7JhyqctOcveZGxfD4Zka6eIys8.iXCL5po3zrAPDaHHbOF3nN6m/O', 1, NULL, 4, '2025-04-20 08:15:45', '2025-04-20 08:15:45'), -- Quản lý
    ('taikhoan5', 'taikhoan5@gmail.com', '$2y$10$bseQTK2cJIlOqME/ydBjRu4c8fELqHmvTrTVj1qJSsPTMR7qJC2Zm', 1, NULL, 2, '2025-04-20 08:16:10', '2025-04-20 08:16:10'), -- Khách hàng
    ('taikhoan6', 'taikhoan6@gmail.com', '$2y$10$chRF/iKO3iLAhZKmCx1/pe5kv9uwWvDV3GArN5x7gxF500J//Xrhu', 1, NULL, 2, '2025-04-20 08:16:27', '2025-04-20 08:16:27'), -- Khách hàng
    ('taikhoan7', 'taikhoan7@gmail.com', '$2y$10$pGFOxsZda1/FdNOVAjWK9Oe3jNrgTbDm37yIPCtqrehtAd/5P.mXO', 1, NULL, 3, '2025-04-20 08:16:53', '2025-04-20 08:16:53'), -- Nhà cung cấp
    ('taikhoan8', 'taikhoan8@gmail.com', '$2y$10$v0fpQsnJJuWzr8p.Rwxv2eEsKSp4jVwXr/gyZxVh.m8sYGGTiDX1K', 1, NULL, 2, '2025-04-20 08:17:11', '2025-04-20 08:17:11'), -- Khách hàng
    ('taikhoan9', 'taikhoan9@gmail.com', '$2y$10$79OGBUfdrwoxs38gbWlCvePYnzGU8fo8APm.pGMdy9yKXmU3dNbW6', 1, NULL, 2, '2025-04-20 08:17:30', '2025-04-20 08:17:30'), -- Khách hàng
    ('taikhoan10', 'taikhoan10@gmail.com', '$2y$10$CEr71AE0mZBkrIXzkgFV0.h8Zjuor3BIIDEFNbWvQJ0RgydLIMJQ.', 1, NULL, 4, '2025-04-20 08:17:41', '2025-04-20 08:17:41'), -- Quản lý
    ('taikhoan11', 'taikhoan11@gmail.com', '$2y$10$Oe7f.lahBiqaoVkoyj1Sbe9I7pkpymhvcNQIowz3CKGiWHlH.Rtae', 1, NULL, 2, '2025-04-20 08:17:51', '2025-04-20 08:17:51'), -- Khách hàng
    ('taikhoan12', 'taikhoan12@gmail.com', '$2y$10$oqXTtS3NRNxd.EfMSTM86.X.hELS9KXaBSwWP1Ho7FCwZMzj1RsXe', 1, NULL, 2, '2025-04-20 08:17:59', '2025-04-20 08:17:59'), -- Khách hàng
    ('taikhoan13', 'taikhoan13@gmail.com', '$2y$10$85MOwmB35ZdXQUPJ3Aht/.qP56.jax0nQnje9f2K4AJB0ejIZv0cK', 1, NULL, 3, '2025-04-20 08:18:07', '2025-04-20 08:18:07'), -- Nhà cung cấp
    ('taikhoan14', 'taikhoan14@gmail.com', '$2y$10$SkrBaIBPnK1TrmutvJr1KubVRqAz5yAv43nhVcK6hhV4.OQehf6TC', 1, NULL, 2, '2025-04-20 08:18:18', '2025-04-20 08:18:18'), -- Khách hàng
    ('taikhoan15', 'taikhoan15@gmail.com', '$10$K4uCTePQOEVCvlQmN2vgH.uo89p7wQsBjJxVKSfvEvAruv3dhRlKe', 1, NULL, 2, '2025-04-20 08:18:34', '2025-04-20 08:18:34'); -- Khách hàng
    
    

CREATE TABLE IF NOT EXISTS category_type (
    category_type_id INT AUTO_INCREMENT PRIMARY KEY,
    type_name VARCHAR(100) NOT NULL UNIQUE,
    type_description TEXT,
    status_id INT,
    FOREIGN KEY (status_id) REFERENCES status(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

-- Chèn dữ liệu mẫu cho bảng category_type
INSERT INTO `category_type` (`type_name`, `type_description`,`status_id`) 
VALUES
    ('Văn học Việt Nam', 'Các tác phẩm văn học do tác giả Việt Nam sáng tác',1),
    ('Văn học nước ngoài', 'Các tác phẩm văn học của tác giả quốc tế',1),
    ('Sách kỹ năng', 'Sách phát triển kỹ năng cá nhân',1),
    ('Sách giáo dục', 'Sách phục vụ học tập và giảng dạy',1),
    ('Sách giải trí', 'Sách mang tính giải trí',1);

-- Tạo bảng category (thể loại)
CREATE TABLE IF NOT EXISTS category (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    category_description TEXT,
    status_id INT,
    category_type_id INT,
    FOREIGN KEY (status_id) REFERENCES status(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    FOREIGN KEY (category_type_id) REFERENCES category_type(category_type_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

-- Chèn dữ liệu mẫu cho bảng category với mỗi category_type có nhiều category
INSERT INTO `category` (`category_name`, `category_description`, `status_id`, `category_type_id`) 
VALUES
    -- Chủng loại: Văn học Việt Nam (category_type_id = 1)
    ('Văn học Việt Nam', 'Các tác phẩm văn học của tác giả Việt Nam', 1, 1),
    ('Truyện ngắn Việt Nam', 'Tập hợp các truyện ngắn của tác giả Việt Nam', 1, 1),
    ('Thơ Việt Nam', 'Các tập thơ của các nhà thơ Việt Nam', 1, 1),
    ('Sách thiếu nhi Việt Nam', 'Sách dành cho trẻ em của tác giả Việt Nam', 1, 1),
    ('Tiểu thuyết lịch sử Việt Nam', 'Tiểu thuyết dựa trên lịch sử Việt Nam', 1, 1),
    
    -- Chủng loại: Văn học nước ngoài (category_type_id = 2)
    ('Khoa học viễn tưởng', 'Sách khám phá các khái niệm tương lai', 1, 2),
    ('Tiểu thuyết nước ngoài', 'Tiểu thuyết của các tác giả nước ngoài', 1, 2),
    
    -- Chủng loại: Sách kỹ năng (category_type_id = 3)
    ('Kỹ năng sống', 'Sách về kỹ năng sống và phát triển bản thân', 1, 3),
    ('Kỹ năng giao tiếp', 'Sách hướng dẫn giao tiếp hiệu quả', 1, 3),
    
    -- Chủng loại: Sách giáo dục (category_type_id = 4)
    ('Sách giáo khoa', 'Sách phục vụ học tập và giảng dạy', 1, 4),
    ('Sách tham khảo', 'Sách bổ trợ kiến thức học tập', 1, 4),
    
    -- Chủng loại: Sách giải trí (category_type_id = 5)
    ('Truyện tranh', 'Sách truyện tranh cho mọi lứa tuổi', 1, 5),
    ('Truyện cười', 'Sách tổng hợp truyện cười giải trí', 1, 5);

CREATE TABLE IF NOT EXISTS product (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    product_description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    status_id INT,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (status_id) REFERENCES status(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

INSERT INTO `product` (`product_name`, `product_description`, `price`, `stock_quantity`, `status_id`, `image_url`, `created_at`, `updated_at`) 
VALUES
    ('Cho Tôi Xin Một Vé Đi Tuổi Thơ', 'Tác phẩm nổi tiếng của Nguyễn Nhật Ánh, kể về những ký ức tuổi thơ đầy cảm xúc.', 120000, 200, 1, 'cho-toi-xin-mot-ve-di-tuoi-tho.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
    ('Tôi Thấy Hoa Vàng Trên Cỏ Xanh', 'Câu chuyện cảm động về tuổi thơ của Nguyễn Nhật Ánh, được chuyển thể thành phim.', 150000, 180, 1, 'toi-thay-hoa-vang-tren-co-xanh.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
    ('Dế Mèn Phiêu Lưu Ký', 'Tác phẩm kinh điển của Tô Hoài, kể về hành trình phiêu lưu của chú Dế Mèn.', 80000, 250, 1, 'de-men-phieu-luu-ky.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
    ('Nhật Ký Trong Tù', 'Tập thơ của Chủ tịch Hồ Chí Minh, viết trong thời gian bị giam cầm.', 90000, 100, 1, 'nhat-ky-trong-tu.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
    ('Số Đỏ', 'Tiểu thuyết trào phúng của Vũ Trọng Phụng, phản ánh xã hội Việt Nam thời kỳ thực dân.', 110000, 150, 1, 'so-do.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
    ('Truyện Kiều', 'Tác phẩm thơ kinh điển của Nguyễn Du, kể về cuộc đời nàng Kiều.', 95000, 200, 1, 'truyen-kieu.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
    ('Bố Con Cá Gai', 'Tập truyện ngắn của Nguyễn Khải, phản ánh cuộc sống và con người Việt Nam.', 85000, 120, 1, 'bo-con-ca-gai.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
    ('Cánh Đồng Bất Tận', 'Tiểu thuyết của Nguyễn Ngọc Tư, kể về cuộc sống miền Tây Nam Bộ.', 130000, 140, 1, 'canh-dong-bat-tan.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
    ('Nỗi Buồn Chiến Tranh', 'Tiểu thuyết của Bảo Ninh, tái hiện ký ức chiến tranh đầy ám ảnh.', 140000, 130, 1, 'noi-buon-chien-tranh.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
    ('Harry Potter và Hòn Đá Phù Thủy', 'Tập 1 của series Harry Potter, bản dịch tiếng Việt.', 180000, 300, 1, 'harry-potter-1.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
    ('Nhà Giả Kim', 'Tác phẩm nổi tiếng của Paulo Coelho, bản dịch tiếng Việt.', 110000, 220, 1, 'nha-gia-kim.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
    ('Đắc Nhân Tâm', 'Sách kỹ năng sống nổi tiếng của Dale Carnegie, bản dịch tiếng Việt.', 95000, 400, 1, 'dac-nhan-tam.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
    ('Doraemon Tập 1', 'Truyện tranh nổi tiếng của Fujiko F. Fujio, bản dịch tiếng Việt.', 35000, 500, 1, 'doraemon-1.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
    ('Toán Lớp 10', 'Sách giáo khoa Toán lớp 10, chương trình mới.', 45000, 300, 1, 'toan-lop-10.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
    ('Mắt Biếc', 'Tiểu thuyết tình cảm của Nguyễn Nhật Ánh, kể về mối tình đơn phương đầy day dứt.', 125000, 160, 1, 'mat-biec.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
    ('Thép Đã Tôi Thế Đấy', 'Tiểu thuyết kinh điển về nghị lực và lý tưởng sống của Nikolai Ostrovsky.', 100000, 180, 1, 'thep-da-toi-the-day.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
('Không Gia Đình', 'Tác phẩm cảm động của Hector Malot về cuộc đời cậu bé mồ côi Rémi.', 110000, 160, 1, 'khong-gia-dinh.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
('Hai Vạn Dặm Dưới Đáy Biển', 'Tiểu thuyết khoa học viễn tưởng kinh điển của Jules Verne.', 90000, 200, 1, 'hai-van-dam-duoi-day-bien.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
('Cuộc Đời Của Pi', 'Câu chuyện sinh tồn đầy kỳ lạ giữa cậu bé và con hổ trên chiếc thuyền nhỏ.', 115000, 170, 1, 'cuoc-doi-cua-pi.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
('Chiến Tranh Và Hòa Bình', 'Đại tác phẩm của Lev Tolstoy về chiến tranh và cuộc sống nước Nga thế kỷ 19.', 135000, 120, 1, 'chien-tranh-va-hoa-binh.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
('Chí Phèo', 'Truyện ngắn của Nam Cao phản ánh xã hội nông thôn Việt Nam trước Cách mạng.', 75000, 210, 1, 'chi-pheo.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
('Vợ Nhặt', 'Truyện ngắn xuất sắc của Kim Lân về nạn đói năm 1945.', 80000, 190, 1, 'vo-nhat.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
('Người Giàu Có Nhất Thành Babylon', 'Sách tài chính cá nhân nổi tiếng của George S. Clason.', 95000, 300, 1, 'nguoi-giau-co-nhat-thanh-babylon.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
('Bí Mật Của May Mắn', 'Truyện ngụ ngôn hiện đại truyền cảm hứng sống tốt đẹp.', 85000, 230, 1, 'bi-mat-cua-may-man.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
('Sherlock Holmes: Dấu Vết Con Chó Má', 'Vụ án nổi tiếng của thám tử Holmes.', 105000, 180, 1, 'sherlock-holmes-dau-vet-con-cho-ma.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
('Con Chim Xanh Biếc Bay Về', 'Tiểu thuyết đậm chất hiện thực của Nguyễn Nhật Ánh.', 120000, 200, 1, 'con-chim-xanh-biec-bay-ve.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
('Người Truy Tìm Vết Tích', 'Tiểu thuyết hình sự của Arthur Conan Doyle.', 99000, 250, 1, 'nguoi-truy-tim-vet-tich.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
('7 Thói Quen Hiệu Quả', 'Tác phẩm self-help kinh điển của Stephen R. Covey.', 125000, 270, 1, '7-thoi-quen-hieu-qua.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
('Cội Rễ', 'Cuốn tiểu thuyết sử thi nổi tiếng của Alex Haley về nô lệ da màu.', 130000, 150, 1, 'coi-re.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
('Những Kẻ Khốn Cùng', 'Tiểu thuyết kinh điển của Victor Hugo phản ánh xã hội Pháp thế kỷ 19.', 135000, 160, 1, 'nhung-ke-khon-cung.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
('Cô Gái Đến Từ Hôm Qua', 'Một cuốn khác của Nguyễn Nhật Ánh về mối tình tuổi học trò.', 110000, 210, 1, 'co-gai-den-tu-hom-qua.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
('Totto-Chan Bên Cửa Sổ', 'Hồi ký cảm động của Kuroyanagi Tetsuko.', 98000, 200, 1, 'totto-chan-ben-cua-so.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
('Những Người Khốn Khó', 'Tác phẩm nhân văn sâu sắc của Fyodor Dostoevsky.', 128000, 140, 1, 'nhung-nguoi-khon-kho.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
('Nếu Chỉ Còn Một Ngày Để Sống', 'Tản văn truyền cảm hứng sống ý nghĩa của Nguyễn Văn Phước.', 89000, 230, 1, 'neu-chi-con-mot-ngay-de-song.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'),
('Tâm Hồn Cao Thượng', 'Tuyển tập truyện ngắn về tình yêu thương và lòng nhân hậu của Edmondo De Amicis.', 95000, 260, 1, 'tam-hon-cao-thuong.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20');

CREATE TABLE IF NOT EXISTS product_category (
    product_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (product_id, category_id),
    FOREIGN KEY (product_id) REFERENCES product(product_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (category_id) REFERENCES category(category_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);    

-- Thêm dữ liệu cho bảng product_category
INSERT INTO `product_category` (`product_id`, `category_id`) 
VALUES
    (1, 1),  -- 'Cho Tôi Xin Một Vé Đi Tuổi Thơ' - 'Văn học Việt Nam'
    (1, 4),  -- 'Cho Tôi Xin Một Vé Đi Tuổi Thơ' - 'Sách thiếu nhi Việt Nam'
    (2, 1),  -- 'Tôi Thấy Hoa Vàng Trên Cỏ Xanh' - 'Văn học Việt Nam'
    (2, 4),  -- 'Tôi Thấy Hoa Vàng Trên Cỏ Xanh' - 'Sách thiếu nhi Việt Nam'
    (3, 4),  -- 'Dế Mèn Phiêu Lưu Ký' - 'Sách thiếu nhi Việt Nam'
    (4, 3),  -- 'Nhật Ký Trong Tù' - 'Thơ Việt Nam'
    (5, 1),  -- 'Số Đỏ' - 'Văn học Việt Nam'
    (6, 3),  -- 'Truyện Kiều' - 'Thơ Việt Nam'
    (7, 2),  -- 'Bố Con Cá Gai' - 'Truyện ngắn Việt Nam'
    (8, 1),  -- 'Cánh Đồng Bất Tận' - 'Văn học Việt Nam'
    (9, 5),  -- 'Nỗi Buồn Chiến Tranh' - 'Tiểu thuyết lịch sử Việt Nam'
    (9, 1),  -- 'Nỗi Buồn Chiến Tranh' - 'Văn học Việt Nam'
    (10, 7), -- 'Harry Potter và Hòn Đá Phù Thủy' - 'Khoa học viễn tưởng'
    (10, 8), -- 'Harry Potter và Hòn Đá Phù Thủy' - 'Tiểu thuyết nước ngoài'
    (11, 8), -- 'Nhà Giả Kim' - 'Tiểu thuyết nước ngoài'
    (12, 6), -- 'Đắc Nhân Tâm' - 'Kỹ năng sống'
    (13, 10),-- 'Doraemon Tập 1' - 'Truyện tranh'
    (14, 9), -- 'Toán Lớp 10' - 'Sách giáo khoa'
    (15, 1), -- 'Mắt Biếc' - 'Văn học Việt Nam'
    (16, 2), (16, 3),
	(17, 2),
	(18, 2),
	(19, 2),
	(20, 2),
	(21, 1), (21, 4),
	(22, 1), (22, 4),
	(23, 3), (23, 4),
	(24, 3),
	(25, 2), (25, 5),
	(26, 1),
	(27, 2), (27, 5),
	(28, 3),
	(29, 2),
	(30, 2),
	(31, 1), (31, 5),
	(32, 2), (32, 4),
	(33, 2),
	(34, 3), (34, 5),
	(35, 2), (35, 3);


CREATE TABLE IF NOT EXISTS user (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    address varchar(100) not null,
    account_id INT unique,
    profile_picture VARCHAR(255),
    date_of_birth DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES account(account_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

INSERT INTO `user` (`full_name`, `account_id`, `address`, `profile_picture`, `date_of_birth`) 
VALUES
    ('Nguyễn Văn A', 1, 'ADV', 'avatar1.png', '1990-01-01'),
    ('Trần Thị B', 2, 'ADV', 'avatar2.png', '1992-02-02'),
    ('Lê Văn C', 3, 'ADV', 'avatar3.png', '1985-03-03'),
    ('Phạm Thị D', 4, 'ADV', 'avatar4.png', '1978-04-04'),
    ('Hoàng Văn E', 5, 'ADV', 'avatar5.png', '1995-05-05'),
    ('Ngô Thị F', 6, 'ADV', 'avatar6.png', '1993-06-06'),
    ('Vũ Văn G', 7, 'ADV', 'avatar7.png', '1988-07-07'),
    ('Đỗ Thị H', 8, 'ADV', 'avatar8.png', '1991-08-08'),
    ('Bùi Văn I', 9, 'ADV', 'avatar9.png', '1994-09-09'),
    ('Đặng Thị K', 10, 'ADV', 'avatar10.png', '1980-10-10'),
    ('Trần Văn L', 11, 'ADV', 'avatar11.png', '1996-11-11'),
    ('Lê Thị M', 12, 'ADV', 'avatar12.png', '1997-12-12'),
    ('Phạm Văn N', 13, 'ADV', 'avatar13.png', '1982-01-13'),
    ('Nguyễn Thị P', 14, 'ADV', 'avatar14.png', '1998-02-14'),
    ('Hoàng Thị Q', 15, 'ADV', 'avatar15.png', '1999-03-15');
    
CREATE TABLE IF NOT EXISTS cart_items (
    user_id INT, 
    product_id INT,  
    quantity INT NOT NULL DEFAULT 1, 
    FOREIGN KEY (user_id) REFERENCES user(user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
INSERT INTO `cart_items` (`user_id`, `product_id`, `quantity`) 
VALUES
    (1, 1, 2),  -- Nguyễn Văn A - 'Cho Tôi Xin Một Vé Đi Tuổi Thơ'
    (1, 2, 1),  -- Nguyễn Văn A - 'Tôi Thấy Hoa Vàng Trên Cỏ Xanh'
    (2, 3, 3),  -- Trần Thị B - 'Dế Mèn Phiêu Lưu Ký'
    (2, 4, 1),  -- Trần Thị B - 'Nhật Ký Trong Tù'
    (3, 5, 2),  -- Lê Văn C - 'Số Đỏ'
    (3, 6, 1),  -- Lê Văn C - 'Truyện Kiều'
    (4, 7, 1),  -- Phạm Thị D - 'Bố Con Cá Gai'
    (4, 8, 2),  -- Phạm Thị D - 'Cánh Đồng Bất Tận'
    (5, 9, 1),  -- Hoàng Văn E - 'Nỗi Buồn Chiến Tranh'
    (5, 10, 2), -- Hoàng Văn E - 'Harry Potter và Hòn Đá Phù Thủy'
    (6, 11, 1), -- Ngô Thị F - 'Nhà Giả Kim'
    (6, 12, 3), -- Ngô Thị F - 'Đắc Nhân Tâm'
    (7, 13, 2), -- Vũ Văn G - 'Doraemon Tập 1'
    (7, 14, 1), -- Vũ Văn G - 'Toán Lớp 10'
    (8, 15, 1), -- Đỗ Thị H - 'Mắt Biếc'
    (8, 1, 2),  -- Đỗ Thị H - 'Cho Tôi Xin Một Vé Đi Tuổi Thơ'
    (9, 2, 1),  -- Bùi Văn I - 'Tôi Thấy Hoa Vàng Trên Cỏ Xanh'
    (9, 3, 2),  -- Bùi Văn I - 'Dế Mèn Phiêu Lưu Ký'
    (10, 4, 1), -- Đặng Thị K - 'Nhật Ký Trong Tù'
    (10, 5, 2); -- Đặng Thị K - 'Số Đỏ'

CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    user_admin_id INT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10, 2) NOT NULL,
    status_id INT,
    payment_method VARCHAR(50) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    address VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES user(user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (status_id) REFERENCES status(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);


CREATE TABLE IF NOT EXISTS order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL DEFAULT 1, 
    review INT NOT NULL DEFAULT 0,
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (product_id) REFERENCES product(product_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- Thêm 15 đơn hàng
INSERT INTO `orders` (`user_id`, `total_amount`, `status_id`, `payment_method`, `phone`, `address`) 
VALUES
    (1, 390000, 5, 'Tiền mặt', '0123456789', '123 Nguyễn Trãi, Quận 5, TP.HCM'),
    (1, 230000, 5, 'Chuyển khoản', '0987654321', '456 Lê Lợi, Quận 1, TP.HCM'),
    (3, 180000, 5, 'Tiền mặt', '0912345678', '789 Phạm Văn Đồng, Thủ Đức, TP.HCM'),
    (4, 270000, 5, 'Chuyển khoản', '0934567890', '321 Trần Phú, Quận 7, TP.HCM'),
    (5, 360000, 4, 'Tiền mặt', '0945678901', '654 Nguyễn Huệ, Quận 1, TP.HCM'),
    (6, 195000, 3, 'Chuyển khoản', '0956789012', '987 Lê Đại Hành, Quận 11, TP.HCM'),
    (7, 220000, 4, 'Tiền mặt', '0967890123', '147 Lý Thường Kiệt, Quận 10, TP.HCM'),
    (8, 250000, 3, 'Chuyển khoản', '0978901234', '258 Võ Văn Tần, Quận 3, TP.HCM'),
    (9, 310000, 4, 'Tiền mặt', '0989012345', '369 Nguyễn Thị Minh Khai, Quận 3, TP.HCM'),
    (10, 140000, 3, 'Chuyển khoản', '0990123456', '741 Cách Mạng Tháng 8, Quận Tân Bình, TP.HCM'),
    (11, 280000, 4, 'Tiền mặt', '0901234567', '852 Nguyễn Văn Cừ, Quận 5, TP.HCM'),
    (12, 165000, 3, 'Chuyển khoản', '0912345679', '963 Lê Văn Sỹ, Quận 3, TP.HCM'),
    (13, 200000, 4, 'Tiền mặt', '0923456780', '159 Trường Chinh, Quận Tân Bình, TP.HCM'),
    (1, 340000, 5, 'Chuyển khoản', '0934567891', '357 Điện Biên Phủ, Quận Bình Thạnh, TP.HCM'),
    (1, 175000, 5, 'Tiền mặt', '0945678902', '468 Nguyễn Đình Chiểu, Quận 3, TP.HCM');

-- Thêm nhiều mục trong order_items
INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`, `price`, `review`) 
VALUES
    (1, 1, 2, 120000, 0),  -- Nguyễn Văn A - 'Cho Tôi Xin Một Vé Đi Tuổi Thơ'
    (1, 2, 1, 150000, 0),  -- Nguyễn Văn A - 'Tôi Thấy Hoa Vàng Trên Cỏ Xanh'
    (2, 3, 2, 80000, 0),   -- Trần Thị B - 'Dế Mèn Phiêu Lưu Ký'
    (2, 4, 1, 90000, 0),   -- Trần Thị B - 'Nhật Ký Trong Tù'
    (3, 5, 1, 110000, 0),  -- Lê Văn C - 'Số Đỏ'
    (3, 6, 1, 95000, 0),   -- Lê Văn C - 'Truyện Kiều'
    (4, 7, 2, 85000, 0),   -- Phạm Thị D - 'Bố Con Cá Gai'
    (4, 8, 1, 130000, 0),  -- Phạm Thị D - 'Cánh Đồng Bất Tận'
    (5, 9, 1, 140000, 0),  -- Hoàng Văn E - 'Nỗi Buồn Chiến Tranh'
    (5, 10, 1, 180000, 0), -- Hoàng Văn E - 'Harry Potter và Hòn Đá Phù Thủy'
    (6, 11, 1, 110000, 0), -- Ngô Thị F - 'Nhà Giả Kim'
    (6, 12, 1, 95000, 0),  -- Ngô Thị F - 'Đắc Nhân Tâm'
    (7, 13, 2, 35000, 0),  -- Vũ Văn G - 'Doraemon Tập 1'
    (7, 14, 1, 45000, 0),  -- Vũ Văn G - 'Toán Lớp 10'
    (8, 15, 2, 125000, 0), -- Đỗ Thị H - 'Mắt Biếc'
    (9, 1, 1, 120000, 0),  -- Bùi Văn I - 'Cho Tôi Xin Một Vé Đi Tuổi Thơ'
    (9, 2, 1, 150000, 0),  -- Bùi Văn I - 'Tôi Thấy Hoa Vàng Trên Cỏ Xanh'
    (10, 3, 1, 80000, 0),  -- Đặng Thị K - 'Dế Mèn Phiêu Lưu Ký'
    (10, 4, 1, 90000, 0),  -- Đặng Thị K - 'Nhật Ký Trong Tù'
    (11, 5, 2, 110000, 0), -- Trần Văn L - 'Số Đỏ'
    (11, 6, 1, 95000, 0),  -- Trần Văn L - 'Truyện Kiều'
    (12, 7, 1, 85000, 0),  -- Lê Thị M - 'Bố Con Cá Gai'
    (12, 8, 1, 130000, 0), -- Lê Thị M - 'Cánh Đồng Bất Tận'
    (13, 9, 1, 140000, 0), -- Phạm Văn N - 'Nỗi Buồn Chiến Tranh'
    (13, 10, 1, 180000, 0),-- Phạm Văn N - 'Harry Potter và Hòn Đá Phù Thủy'
    (14, 11, 2, 110000, 0),-- Nguyễn Thị P - 'Nhà Giả Kim'
    (14, 12, 1, 95000, 0), -- Nguyễn Thị P - 'Đắc Nhân Tâm'
    (15, 13, 2, 35000, 0), -- Hoàng Thị Q - 'Doraemon Tập 1'
    (15, 14, 1, 45000, 0); -- Hoàng Thị Q - 'Toán Lớp 10'

CREATE TABLE IF NOT EXISTS supplier (
    supplier_id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_name VARCHAR(100) NOT NULL UNIQUE,                        
    contact_phone VARCHAR(15),                   
    address VARCHAR(255),                
    publisher VARCHAR(255),
    status_id INT,                             
    FOREIGN KEY (status_id) REFERENCES status(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);
INSERT INTO `supplier` (`supplier_name`, `contact_phone`, `address`, `publisher`, `status_id`) 
VALUES
    ('Nhà Xuất Bản Trẻ', '028-3822-4567', '161B Lý Chính Thắng, Quận 3, TP.HCM', 'NXB Trẻ', 1),
    ('Nhà Xuất Bản Kim Đồng', '024-3822-1234', '55 Quang Trung, Hai Bà Trưng, Hà Nội', 'NXB Kim Đồng', 1),
    ('Nhà Xuất Bản Văn Học', '024-3822-5678', '18 Nguyễn Du, Hai Bà Trưng, Hà Nội', 'NXB Văn Học', 1),
    ('Nhà Xuất Bản Tổng Hợp TP.HCM', '028-3822-7890', '62 Nguyễn Thị Minh Khai, Quận 1, TP.HCM', 'NXB Tổng Hợp', 1),
    ('Nhà Xuất Bản Giáo Dục', '024-3822-9012', '81 Trần Hưng Đạo, Hoàn Kiếm, Hà Nội', 'NXB Giáo Dục', 1),
    ('Nhà Xuất Bản Hội Nhà Văn', '024-3822-3456', '65 Nguyễn Du, Hai Bà Trưng, Hà Nội', 'NXB Hội Nhà Văn', 1),
    ('Nhà Xuất Bản Phụ Nữ', '028-3822-6789', '47 Hàng Chuối, Hai Bà Trưng, Hà Nội', 'NXB Phụ Nữ', 1),
    ('Nhà Xuất Bản Lao Động', '024-3822-2345', '175 Giảng Võ, Đống Đa, Hà Nội', 'NXB Lao Động', 1),
    ('Nhà Xuất Bản Thanh Niên', '028-3822-5670', '64 Bà Triệu, Hoàn Kiếm, Hà Nội', 'NXB Thanh Niên', 1),
    ('Nhà Xuất Bản Văn Hóa - Văn Nghệ', '028-3822-8901', '88-90 Ký Con, Quận 1, TP.HCM', 'NXB Văn Hóa - Văn Nghệ', 1);


CREATE TABLE IF NOT EXISTS purchase_order (
    purchase_order_id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT,
    user_id INT,
    order_date DATE NOT NULL DEFAULT (CURRENT_DATE),
    approve_date DATE DEFAULT NULL,
    total_amount INT NOT NULL,
    total_price DECIMAL(10, 2),  
    status_id INT,
    import_status TINYINT(1) DEFAULT 0,
    FOREIGN KEY (supplier_id) REFERENCES supplier(supplier_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user(user_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    FOREIGN KEY (status_id) REFERENCES status(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS purchase_order_items (
    purchase_order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_order_id INT,
    product_id INT,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10, 2) NOT NULL,
    profit DECIMAL(10, 2) NOT NULL,
    import_status TINYINT(1) DEFAULT 0,
    approve_date DATE DEFAULT NULL,
    FOREIGN KEY (purchase_order_id) REFERENCES purchase_order(purchase_order_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (product_id) REFERENCES product(product_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

INSERT INTO `purchase_order` (`supplier_id`, `user_id`, `order_date`, `approve_date`, `total_amount`, `total_price`, `status_id`, `import_status`) 
VALUES
    (1, 1, '2025-02-05 08:00:00', '2025-02-06 10:00:00', 50, 4500000, 5, 1), -- Nhà Xuất Bản Trẻ, đã nhập
    (2, 4, '2025-02-06 09:00:00', NULL, 30, 2400000, 3, 0), -- Nhà Xuất Bản Kim Đồng, đang chờ
    (3, 7, '2025-02-07 10:00:00', '2025-02-08 12:00:00', 40, 3600000, 5, 1), -- Nhà Xuất Bản Văn Học, đã nhập
    (4, 10, '2025-02-08 11:00:00', NULL, 20, 1800000, 3, 0), -- Nhà Xuất Bản Tổng Hợp TP.HCM, đang chờ
    (5, 1, '2025-02-09 12:00:00', '2025-02-10 14:00:00', 60, 5400000, 5, 1), -- Nhà Xuất Bản Giáo Dục, đã nhập
    (6, 4, '2025-02-10 13:00:00', NULL, 25, 2250000, 3, 0), -- Nhà Xuất Bản Hội Nhà Văn, đang chờ
    (7, 7, '2025-02-11 14:00:00', '2025-02-12 16:00:00', 35, 3150000, 5, 1), -- Nhà Xuất Bản Phụ Nữ, đã nhập
    (8, 10, '2025-02-12 15:00:00', NULL, 45, 4050000, 3, 0), -- Nhà Xuất Bản Lao Động, đang chờ
    (9, 1, '2025-02-13 16:00:00', '2025-02-14 18:00:00', 15, 1350000, 5, 1), -- Nhà Xuất Bản Thanh Niên, đã nhập
    (10, 4, '2025-02-14 17:00:00', NULL, 55, 4950000, 3, 0); -- Nhà Xuất Bản Văn Hóa - Văn Nghệ, đang chờ
    INSERT INTO `purchase_order_items` (`purchase_order_id`, `product_id`, `quantity`, `price`, `profit`, `import_status`, `approve_date`) 
VALUES
    -- Đơn nhập hàng 1 (Nhà Xuất Bản Trẻ)
    (1, 1, 20, 80000, 50.00, 1, '2025-02-06 10:00:00'),  -- 'Cho Tôi Xin Một Vé Đi Tuổi Thơ'
    (1, 2, 15, 100000, 50.00, 1, '2025-02-06 10:00:00'),  -- 'Tôi Thấy Hoa Vàng Trên Cỏ Xanh'
    (1, 15, 15, 80000, 56.25, 1, '2025-02-06 10:00:00'),  -- 'Mắt Biếc'

    -- Đơn nhập hàng 2 (Nhà Xuất Bản Kim Đồng)
    (2, 3, 20, 50000, 60.00, 0, NULL),  -- 'Dế Mèn Phiêu Lưu Ký'
    (2, 13, 10, 20000, 75.00, 0, NULL),  -- 'Doraemon Tập 1'

    -- Đơn nhập hàng 3 (Nhà Xuất Bản Văn Học)
    (3, 4, 15, 60000, 50.00, 1, '2025-02-08 12:00:00'),  -- 'Nhật Ký Trong Tù'
    (3, 5, 10, 70000, 57.14, 1, '2025-02-08 12:00:00'),  -- 'Số Đỏ'
    (3, 6, 15, 60000, 58.33, 1, '2025-02-08 12:00:00'),  -- 'Truyện Kiều'

    -- Đơn nhập hàng 4 (Nhà Xuất Bản Tổng Hợp TP.HCM)
    (4, 7, 10, 55000, 54.55, 0, NULL),  -- 'Bố Con Cá Gai'
    (4, 8, 10, 85000, 52.94, 0, NULL),  -- 'Cánh Đồng Bất Tận'

    -- Đơn nhập hàng 5 (Nhà Xuất Bản Giáo Dục)
    (5, 9, 20, 90000, 55.56, 1, '2025-02-10 14:00:00'),  -- 'Nỗi Buồn Chiến Tranh'
    (5, 14, 30, 30000, 50.00, 1, '2025-02-10 14:00:00'),  -- 'Toán Lớp 10'
    (5, 10, 10, 120000, 50.00, 1, '2025-02-10 14:00:00'), -- 'Harry Potter và Hòn Đá Phù Thủy'

    -- Đơn nhập hàng 6 (Nhà Xuất Bản Hội Nhà Văn)
    (6, 11, 15, 70000, 57.14, 0, NULL),  -- 'Nhà Giả Kim'
    (6, 12, 10, 60000, 58.33, 0, NULL),  -- 'Đắc Nhân Tâm'

    -- Đơn nhập hàng 7 (Nhà Xuất Bản Phụ Nữ)
    (7, 1, 10, 80000, 50.00, 1, '2025-02-12 16:00:00'),  -- 'Cho Tôi Xin Một Vé Đi Tuổi Thơ'
    (7, 2, 10, 100000, 50.00, 1, '2025-02-12 16:00:00'),  -- 'Tôi Thấy Hoa Vàng Trên Cỏ Xanh'
    (7, 15, 15, 80000, 56.25, 1, '2025-02-12 16:00:00'),  -- 'Mắt Biếc'

    -- Đơn nhập hàng 8 (Nhà Xuất Bản Lao Động)
    (8, 3, 20, 50000, 60.00, 0, NULL),  -- 'Dế Mèn Phiêu Lưu Ký'
    (8, 13, 15, 20000, 75.00, 0, NULL),  -- 'Doraemon Tập 1'
    (8, 14, 10, 30000, 50.00, 0, NULL),  -- 'Toán Lớp 10'

    -- Đơn nhập hàng 9 (Nhà Xuất Bản Thanh Niên)
    (9, 4, 5, 60000, 50.00, 1, '2025-02-14 18:00:00'),  -- 'Nhật Ký Trong Tù'
    (9, 5, 5, 70000, 57.14, 1, '2025-02-14 18:00:00'),  -- 'Số Đỏ'
    (9, 6, 5, 60000, 58.33, 1, '2025-02-14 18:00:00'),  -- 'Truyện Kiều'

    -- Đơn nhập hàng 10 (Nhà Xuất Bản Văn Hóa - Văn Nghệ)
    (10, 7, 15, 55000, 54.55, 0, NULL),  -- 'Bố Con Cá Gai'
    (10, 8, 20, 85000, 52.94, 0, NULL),  -- 'Cánh Đồng Bất Tận'
    (10, 9, 20, 90000, 55.56, 0, NULL);  -- 'Nỗi Buồn Chiến Tranh'
    

    
CREATE TABLE IF NOT EXISTS review (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    user_admin_id INT,
    product_id INT,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT,
    feedback TEXT,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status_id INT,
    FOREIGN KEY (user_id) REFERENCES user(user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (product_id) REFERENCES product(product_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (status_id) REFERENCES status(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

INSERT INTO `review` (`user_id`, `product_id`, `rating`, `review_text`, `feedback`, `review_date`, `status_id`) 
VALUES
    (1, 1, 5, 'Sách rất hay, gợi nhớ tuổi thơ!', 'Nên cải thiện chất lượng bìa sách.', '2025-02-05 03:10:00', 1),
    (2, 2, 4, 'Câu chuyện cảm động, rất đáng đọc.', 'Giao hàng hơi chậm.', '2025-02-05 03:15:00', 1),
    (3, 3, 5, 'Con tôi rất thích Dế Mèn Phiêu Lưu Ký!', NULL, '2025-02-05 03:20:00', 1),
    (4, 4, 3, 'Thơ hay nhưng bản in có vài lỗi nhỏ.', 'Cần kiểm tra kỹ hơn trước khi xuất bản.', '2025-02-05 03:25:00', 1),
    (5, 5, 4, 'Số Đỏ rất hài hước, phản ánh xã hội sâu sắc.', NULL, '2025-02-05 03:30:00', 1),
    (6, 6, 5, 'Truyện Kiều là tác phẩm kinh điển, rất ý nghĩa.', NULL, '2025-02-05 03:35:00', 1),
    (7, 7, 3, 'Truyện ngắn ổn, nhưng không quá ấn tượng.', 'Cần thêm chiều sâu cho câu chuyện.', '2025-02-05 03:40:00', 1),
    (8, 8, 4, 'Cánh Đồng Bất Tận rất cảm động, phản ánh đúng cuộc sống miền Tây.', NULL, '2025-02-05 03:45:00', 1),
    (9, 9, 5, 'Nỗi Buồn Chiến Tranh rất ám ảnh, đáng để đọc.', NULL, '2025-02-05 03:50:00', 1),
    (10, 10, 4, 'Harry Potter rất hấp dẫn, bản dịch tốt.', 'Giá hơi cao so với thị trường.', '2025-02-05 03:55:00', 1),
    (11, 11, 5, 'Nhà Giả Kim truyền cảm hứng mạnh mẽ!', NULL, '2025-02-05 04:00:00', 1),
    (12, 12, 4, 'Đắc Nhân Tâm rất hữu ích cho cuộc sống.', 'Cần cập nhật thêm nội dung mới.', '2025-02-05 04:05:00', 1),
    (13, 13, 5, 'Doraemon rất vui nhộn, con tôi mê lắm!', NULL, '2025-02-05 04:10:00', 1),
    (14, 14, 3, 'Sách giáo khoa Toán lớp 10 ổn, nhưng bài tập hơi khó.', 'Cần thêm hướng dẫn chi tiết hơn.', '2025-02-05 04:15:00', 1),
    (15, 15, 4, 'Mắt Biếc rất cảm động, đúng chất Nguyễn Nhật Ánh.', NULL, '2025-02-05 04:20:00', 1);

CREATE TABLE if not exists permission (
    permission_id INT AUTO_INCREMENT PRIMARY KEY,
    permission_name VARCHAR(100) NOT NULL UNIQUE,
    permission_description TEXT,
    status_id INT,
        FOREIGN KEY (status_id) REFERENCES status(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

INSERT INTO `permission` (`permission_name`, `permission_description`, `status_id`) 
VALUES
    ('Quản lý sản phẩm', 'Cho phép thêm, sửa, xóa sản phẩm và danh mục sản phẩm', 1),
    ('Quản lý hóa đơn', 'Cho phép xem, xử lý và hủy hóa đơn của khách hàng', 1),
    ('Quản lý tài khoản', 'Cho phép quản lý tài khoản người dùng và phân quyền', 1),
    ('Quản lý đánh giá', 'Cho phép xem và kiểm duyệt các đánh giá sản phẩm', 1),
    ('Thống kê', 'Cho phép xem các báo cáo và thống kê doanh thu', 1),
    ('Quản lý nhà cung cấp', 'Cho phép thêm, sửa, xóa thông tin nhà cung cấp', 1),
    ('Đặt hàng', 'Cho phép khách hàng đặt hàng và xem lịch sử đơn hàng', 1),
    ('Quản lý thể loại', 'Cho phép quản lý danh sách các thể loại', 1),
    ('Quản lý phiếu nhập', 'Cho phép quản lý danh sách các phiếu nhập', 1),
    ('Quản lý chức vụ', 'Cho phép quản lý chức vụ', 1),
    ('Quản lý phân quyền', 'Cho phép quản lý phân quyền', 1),
    ('Quản lý chủng loại', 'Cho phép quản lý chủng loại', 1);


CREATE TABLE if not exists role_permission (
    role_id INT,
    permission_id INT,
    action varchar(100),
    PRIMARY KEY (role_id, permission_id, action),
    FOREIGN KEY (role_id) REFERENCES role(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permission(permission_id) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO role_permission (role_id, permission_id, action) VALUES
(1, 1, 'Sửa'),
(1, 1, 'Thêm'),
(1, 1, 'Xem'),
(1, 1, 'Xóa'),
(1, 2, 'Duyệt đơn/Hoàn tất'),
(1, 2, 'Hủy'),
(1, 2, 'Xem'),
(1, 3, 'Sửa'),
(1, 3, 'Thêm'),
(1, 3, 'Xem'),
(1, 3, 'Xóa'),
(1, 4, 'Sửa'),
(1, 4, 'Xem'),
(1, 4, 'Xóa'),
(1, 5, 'Xem'),
(1, 6, 'Sửa'),
(1, 6, 'Thêm'),
(1, 6, 'Xem'),
(1, 6, 'Xóa'),
(1, 7, 'Đặt hàng'),
(1, 8, 'Sửa'),
(1, 8, 'Thêm'),
(1, 8, 'Xem'),
(1, 8, 'Xóa'),
(1, 9, 'Xem'),
(1, 10, 'Cập nhật phân quyền'),
(1, 10, 'Sửa'),
(1, 10, 'Thêm'),
(1, 10, 'Xem'),
(1, 10, 'Xóa'),
(1, 11, 'Sửa'),
(1, 11, 'Thêm'),
(1, 11, 'Xem'),
(1, 11, 'Xóa'),
(1, 12, 'Sửa'),
(1, 12, 'Thêm'),
(1, 12, 'Xem'),
(1, 12, 'Xóa');



CREATE TABLE IF NOT EXISTS price_history (
    history_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    old_price DECIMAL(10, 2) NOT NULL,
    new_price DECIMAL(10, 2) NOT NULL,
    change_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    changed_by INT, -- user_id của người thay đổi
    reason TEXT,
    FOREIGN KEY (product_id) REFERENCES product(product_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES user(user_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

INSERT INTO `price_history` (`product_id`, `old_price`, `new_price`, `change_date`, `changed_by`, `reason`) 
VALUES
    (1, 120000, 110000, '2025-02-06 10:00:00', 1, 'Giảm giá khuyến mãi đầu năm'),
    (2, 150000, 140000, '2025-02-07 10:00:00', 4, 'Điều chỉnh giá do nhập hàng mới'),
    (3, 80000, 85000, '2025-02-08 10:00:00', 7, 'Tăng giá do chi phí in ấn tăng'),
    (4, 90000, 95000, '2025-02-09 10:00:00', 10, 'Tăng giá do nhu cầu thị trường'),
    (5, 110000, 100000, '2025-02-10 10:00:00', 1, 'Giảm giá để thanh lý hàng tồn'),
    (6, 95000, 90000, '2025-02-11 10:00:00', 4, 'Giảm giá nhân dịp lễ'),
    (7, 85000, 80000, '2025-02-12 10:00:00', 7, 'Giảm giá để cạnh tranh thị trường'),
    (8, 130000, 125000, '2025-02-13 10:00:00', 10, 'Điều chỉnh giá do nhập hàng mới'),
    (9, 140000, 135000, '2025-02-14 10:00:00', 1, 'Giảm giá khuyến mãi'),
    (10, 180000, 170000, '2025-02-15 10:00:00', 4, 'Giảm giá nhân dịp lễ hội sách'),
    (11, 110000, 105000, '2025-02-16 10:00:00', 7, 'Giảm giá để thanh lý hàng tồn'),
    (12, 95000, 90000, '2025-02-17 10:00:00', 10, 'Giảm giá khuyến mãi'),
    (13, 35000, 30000, '2025-02-18 10:00:00', 1, 'Giảm giá để tăng doanh số'),
    (14, 45000, 40000, '2025-02-19 10:00:00', 4, 'Giảm giá cho học sinh'),
    (15, 125000, 120000, '2025-02-20 10:00:00', 7, 'Giảm giá nhân dịp lễ hội sách');
-- After updating table price will insert data into table price_history for later statistics
DELIMITER //
CREATE TRIGGER after_price_update
AFTER UPDATE ON product
FOR EACH ROW
BEGIN
    DECLARE v_latest_user_id INT;
    
    IF NEW.price != OLD.price THEN
        -- Lấy user_id từ đơn nhập hàng gần nhất chứa sản phẩm này
        SELECT po.user_id INTO v_latest_user_id
        FROM purchase_order po
        JOIN purchase_order_items poi ON po.purchase_order_id = poi.purchase_order_id
        WHERE poi.product_id = NEW.product_id
        ORDER BY po.order_date DESC
        LIMIT 1;
        
        INSERT INTO price_history (
            product_id,
            old_price,
            new_price,
            changed_by,
            reason
        ) VALUES (
            NEW.product_id,
            OLD.price,
            NEW.price,
            IFNULL(v_latest_user_id, NULL), 
            CONCAT('Tự động cập nhật. Giá thay đổi từ ', 
                  OLD.price, ' → ', NEW.price,
                  IF(v_latest_user_id IS NOT NULL, 
                     CONCAT('. Người nhập hàng gần nhất: ', v_latest_user_id),
                     ''))
        );
    END IF;
END //
DELIMITER ;

-- Procedure for update product price with max profit
-- UpdateProductPriceWithMaxProfit(5, 13000, 0.35)
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateProductPriceWithMaxProfit`(
    IN p_product_id INT,
    IN p_new_cost_price DECIMAL(10,2),
    IN p_profit_margin DECIMAL(10,2) -- Ví dụ: 0.3 cho 30%
)
BEGIN
    DECLARE v_old_cost_price DECIMAL(10,2);
    DECLARE v_old_selling_price DECIMAL(10,2);
    DECLARE v_old_profit DECIMAL(10,2);
    DECLARE v_new_profit DECIMAL(10,2);
    DECLARE v_new_selling_price DECIMAL(10,2);
    
    -- Lấy giá nhập cũ (từ bảng purchase_order_items)
    SELECT price INTO v_old_cost_price 
    FROM purchase_order_items 
    WHERE product_id = p_product_id  and approve_date is not null and import_status = 1
    ORDER BY approve_date DESC LIMIT 1;
    
    -- Lấy giá bán hiện tại
    SELECT price INTO v_old_selling_price 
    FROM product 
    WHERE product_id = p_product_id;
    
    -- Tính lợi nhuận cũ
    SET v_old_profit = v_old_selling_price - v_old_cost_price;
    
    -- Tính lợi nhuận mới theo tỷ lệ
    SET v_new_profit = p_new_cost_price * p_profit_margin;
    
    -- Xác định giá bán mới
    IF v_old_profit  > v_new_profit THEN
        -- Giữ giá bán cũ nếu lợi nhuận cao hơn
        SET v_new_selling_price = v_old_selling_price;
    ELSE
        -- Áp dụng giá bán mới theo tỷ lệ lợi nhuận
        SET v_new_selling_price = p_new_cost_price * (1 + p_profit_margin);
    END IF;
    
    
    -- Cập nhật giá bán trong bảng product
    UPDATE product 
    SET price = v_new_selling_price, 
        updated_at = CURRENT_TIMESTAMP
    WHERE product_id = p_product_id;
    
    -- Trả kết quả
    SELECT 
        p_product_id AS product_id,
        v_old_cost_price AS old_cost_price,
        p_new_cost_price AS new_cost_price,
        v_old_selling_price AS old_selling_price,
        v_new_selling_price AS new_selling_price,
        (v_new_selling_price - p_new_cost_price) AS actual_profit;
END //
DELIMITER ;

use web2_sql;
CREATE OR REPLACE VIEW sales_report AS
SELECT 
    o.order_id,
    o.order_date,
    DATE(o.order_date) AS sale_date,
    u.user_id,
    u.full_name AS customer_name,
    a.email AS customer_email,
    o.total_amount,
    o.payment_method,
    o.phone,
    o.address,
    s.status_name AS order_status,
    COUNT(oi.order_item_id) AS total_items,
    SUM(oi.quantity) AS total_quantity,
    -- Thông tin chi tiết sản phẩm (dạng JSON)
    (
        SELECT JSON_ARRAYAGG(
            JSON_OBJECT(
                'product_id', p.product_id,
                'product_name', p.product_name,
                'quantity', oi2.quantity,
                'unit_price', oi2.price,
                'subtotal', oi2.price * oi2.quantity,
                'categories', (
                    SELECT JSON_ARRAYAGG(c.category_name)
                    FROM product_category pc
                    JOIN category c ON pc.category_id = c.category_id
                    WHERE pc.product_id = p.product_id
                )
            )
        )
        FROM order_items oi2
        JOIN product p ON oi2.product_id = p.product_id
        WHERE oi2.order_id = o.order_id
    ) AS product_details,
    -- Thông tin nhân viên xử lý (nếu có)
    u_admin.full_name AS admin_handler,
    -- Phân loại theo thời gian
    YEAR(o.order_date) AS sale_year,
    MONTH(o.order_date) AS sale_month,
    DAY(o.order_date) AS sale_day,
    QUARTER(o.order_date) AS sale_quarter,
    -- Phân tích trạng thái
    CASE 
        WHEN o.status_id = 5 THEN 'Completed'
        WHEN o.status_id = 4 THEN 'Shipping'
        WHEN o.status_id = 3 THEN 'Pending'
        WHEN o.status_id = 7 THEN 'Cancelled'
        ELSE 'Other'
    END AS status_category
FROM 
    orders o
JOIN 
    user u ON o.user_id = u.user_id
JOIN 
    account a ON u.account_id = a.account_id
JOIN 
    status s ON o.status_id = s.id
LEFT JOIN 
    user u_admin ON o.user_admin_id = u_admin.user_id
LEFT JOIN 
    order_items oi ON o.order_id = oi.order_id
GROUP BY 
    o.order_id, o.order_date, u.user_id, u.full_name, a.email, 
    o.total_amount, o.payment_method, o.phone, o.address, 
    s.status_name, u_admin.full_name;
    
