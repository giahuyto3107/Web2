# Web2
NHỚ TỰ THÊM DỮ LIỆU VÀO "status" và "category" VÀO DATABASE RỒI HÃY CHẠY CODE
Đồ án web2, đề tài web bán đồ công nghệ
Đặt tên file là "Web2" và cơ sở dữ liệu là "web2_sql" nhé

dữ liệu mô phỏng "status"
INSERT INTO status (status_name, status_description) VALUES
('Active', 'The entity is currently active and in use.'),
('Inactive', 'The entity is currently inactive and not in use.'),
('Pending', 'The entity is awaiting approval or further action.'),
('Completed', 'The entity has been successfully completed.'),
('Cancelled', 'The entity has been cancelled and will not be processed.'),
('Shipped', 'The order has been shipped to the customer.'),
('Delivered', 'The order has been delivered to the customer.'),
('Returned', 'The order has been returned by the customer.'),
('On Hold', 'The entity is temporarily on hold and not being processed.'),
('Archived', 'The entity has been archived and is no longer active.');

dữ liệu mô phỏng "categories"
INSERT INTO category (category_name, category_description, status_id) VALUES
('Smartphones', 'Latest smartphones from top brands.', 1),
('Laptops', 'High-performance laptops for work and gaming.', 1),
('Tablets', 'Portable tablets for entertainment and productivity.', 1),
('Accessories', 'Chargers, cables, cases, and other accessories.', 1),
('Smart Home Devices', 'Smart home gadgets like smart bulbs, cameras, and speakers.', 1),
('Gaming Consoles', 'Popular gaming consoles and accessories.', 1),
('Wearable Technology', 'Smartwatches, fitness trackers, and other wearables.', 1),
('Audio Devices', 'Headphones, earphones, and speakers.', 1),
('Cameras', 'DSLRs, mirrorless cameras, and action cameras.', 1),
('Computer Components', 'PC parts like CPUs, GPUs, and motherboards.', 1);


Dữ liệu mô phỏng "product"
INSERT INTO product (product_name, product_description, price, stock_quantity, category_id, status_id, image_url) VALUES
('iPhone 15 Pro', 'Latest iPhone with A17 chip and 48MP camera.', 999.99, 50, 1, 1, 'https://example.com/iphone15pro.jpg'),
('Samsung Galaxy S23 Ultra', 'Flagship Android phone with 200MP camera.', 1199.99, 30, 1, 1, 'https://example.com/s23ultra.jpg'),
('MacBook Pro 16-inch', 'Powerful laptop with M2 Max chip for professionals.', 2499.99, 20, 2, 1, 'https://example.com/macbookpro16.jpg'),
('iPad Pro 12.9', 'High-performance tablet with M2 chip and Liquid Retina display.', 1099.99, 25, 3, 1, 'https://example.com/ipadpro12.jpg'),
('Sony WH-1000XM5', 'Noise-cancelling over-ear headphones with premium sound quality.', 349.99, 40, 8, 1, 'https://example.com/sonyxm5.jpg'),
('PlayStation 5', 'Next-gen gaming console with 4K gaming and ray tracing.', 499.99, 15, 6, 1, 'https://example.com/ps5.jpg'),
('Nikon Z9', 'Flagship mirrorless camera with 8K video recording.', 5499.99, 10, 9, 1, 'https://example.com/nikonz9.jpg'),
('Apple Watch Series 8', 'Smartwatch with health tracking and ECG features.', 399.99, 35, 7, 1, 'https://example.com/applewatch8.jpg'),
('Google Nest Hub', 'Smart display with Google Assistant for home control.', 99.99, 50, 5, 1, 'https://example.com/nesthub.jpg'),
('Logitech MX Master 3S', 'Wireless mouse for productivity with ergonomic design.', 99.99, 60, 4, 1, 'https://example.com/mxmaster3s.jpg');