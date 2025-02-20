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
	(1, 'Active', 'The record is currently active and operational'),
	(2, 'Inactive', 'The record is currently inactive and not in use'),
	(3, 'Pending', 'The record is awaiting further processing or approval'),
	(4, 'Completed', 'The record has been successfully completed'),
	(5, 'Failed', 'The record has encountered an error or was unsuccessful'),
    (6, 'Deleted', 'The record has been deleted');


CREATE TABLE IF NOT EXISTS role (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    role_description VARCHAR(255),
    status_id INT
);

INSERT INTO `role` (`id`, `role_name`, `role_description`, `status_id`) 
VALUES
	(1, 'Admin', 'Administrator with full access', 1),
	(2, 'Customer', 'Regular customer', 1),
	(3, 'Supplier', 'Supplier of products', 1),
	(4, 'Manager', 'Manager with limited admin access', 1);

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

INSERT INTO `account` (`account_id`, `account_name`, `email`, `password_hash`, `status_id`, `last_login`, `role_id`, `created_at`, `updated_at`) 
VALUES
	(1, 'test1', 'john.doe@example.com', '123', 2, NULL, 2, '2025-02-05 03:00:08', '2025-02-08 09:32:04'),
	(2, 'test2', 'jane.smith@example.com', '123', 2, NULL, 2, '2025-02-05 03:00:08', '2025-02-08 09:32:07'),
	(3, 'test3', 'alice.johnson@example.com', '123', 1, NULL, 3, '2025-02-05 03:00:08', '2025-02-08 09:32:09'),
	(4, 'test4', 'bob.brown@example.com', '123', 1, NULL, 4, '2025-02-05 03:00:08', '2025-02-08 09:32:12');


CREATE TABLE IF NOT EXISTS category (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    category_description TEXT,
    status_id INT,
    FOREIGN KEY (status_id) REFERENCES status(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

INSERT INTO `category` (`category_id`, `category_name`, `category_description`, `status_id`) 
VALUES
	(1, 'Fiction', 'Books that are fictional stories', 1),
	(2, 'Non-Fiction', 'Books based on real events and facts', 1),
	(3, 'Science Fiction', 'Books that explore futuristic concepts', 1),
	(4, 'Mystery', 'Books that involve solving a mystery', 1),
	(6, 'testing123', '123455', NULL);


CREATE TABLE IF NOT EXISTS product (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    product_description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    category_id INT,
    status_id INT,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES category(category_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    FOREIGN KEY (status_id) REFERENCES status(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

INSERT INTO `product` (`product_id`, `product_name`, `product_description`, `price`, `stock_quantity`, `category_id`, `status_id`, `image_url`, `created_at`, `updated_at`) 
VALUES
	(1, 'The Great Gatsby', 'A classic novel by F. Scott Fitzgerald', 15.99, 100, 1, 1, 
		'The Great Gaspi.png', '2025-02-05 03:00:08', '2025-02-05 03:09:12'),
	(2, '1984', 'A dystopian novel by George Orwell', 12.99, 150, 1, 1, 
		'1984.png', '2025-02-05 03:00:08', '2025-02-05 03:09:16'),
	(3, 'Sapiens: A Brief History of Humankind', 
		'A book by Yuval Noah Harari', 18.99, 200, 2, 1, 'Sapiens.png', '2025-02-05 03:00:08', '2025-02-05 03:09:18'),
	(4, 'Dune', 'A science fiction novel by Frank Herbert', 14.99, 120, 3, 1, 
		'Dune.png', '2025-02-05 03:00:08', '2025-02-05 03:09:20'
	);

CREATE TABLE IF NOT EXISTS user (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    account_id INT unique,
    profile_picture VARCHAR(255),
    date_of_birth DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES account(account_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

INSERT INTO `user` (`full_name`, `account_id`, `profile_picture`, `date_of_birth`) 
VALUES
    ('John Doe', 1, 'p1.png', '1990-01-01'),
    ('Jane Smith', 2, 'p2.png', '1992-02-02'),
    ('Alice Johnson', 3, 'p3.png', '1985-03-03'),
    ('Bob Brown', 4, 'p4.png', '1978-04-04');
    
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
    (1, 1, 2),
    (1, 3, 1),
    (2, 2, 3),
    (3, 4, 1),
    (4, 1, 5);

CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
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
INSERT INTO `orders` (`user_id`, `total_amount`, `status_id`, `payment_method`, `phone`, `address`) 
VALUES
    (1, 45.97, 4, 'Credit Card', '0123456789', 'LacLongQuan'),
    (2, 38.97, 4, 'PayPal', '0987654321', 'DHSG'),
    (3, 14.99, 4, 'Cash', '0912345678', 'CuChi'),
    (4, 31.98, 3, 'Credit Card', '0934567890', 'anhdaden'),
    (4, 31.98, 3, 'Credit Card', '0945678901', 'ooo'),
    (3, 31.98, 3, 'Credit Card', '0956789012', 'kkk'),
    (1, 31.98, 3, 'Credit Card', '0967890123', 'onichan'),
    (1, 31.98, 3, 'Credit Card', '0967890123', 'onichan'),
    (1, 31.98, 3, 'Credit Card', '0967890123', 'onichan'),
    (1, 31.98, 3, 'Credit Card', '0967890123', 'onichan'),
    (1, 31.98, 3, 'Credit Card', '0967890123', 'onichan'),
    (1, 31.98, 3, 'Credit Card', '0967890123', 'onichan'),
    (1, 31.98, 3, 'Credit Card', '0967890123', 'onichan'),
    (1, 31.98, 3, 'Credit Card', '0967890123', 'onichan');

CREATE TABLE IF NOT EXISTS order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL DEFAULT 1,
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (product_id) REFERENCES product(product_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`) 
VALUES
    (1, 1, 2),
    (1, 3, 1),
    (2, 2, 3),
    (3, 4, 1),
    (4, 1, 2);

CREATE TABLE IF NOT EXISTS supplier (
    supplier_id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_name VARCHAR(100) NOT NULL UNIQUE,                        
    contact_phone VARCHAR(15),                   
    address VARCHAR(255),                     
    status_id INT,                             
    FOREIGN KEY (status_id) REFERENCES status(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

INSERT INTO `supplier` (`supplier_id`, `supplier_name`, `contact_phone`, `address`, `status_id`) 
VALUES
	(1, 'Book Supplier Inc.', '123-456-7890', '123 Supplier St, Supplier City', 1),
	(2, 'Global Books', '987-654-3210', '456 Global Ave, Global City', 1);


CREATE TABLE IF NOT EXISTS purchase_order (
    purchase_order_id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT,
    user_id INT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount int NOT NULL,
    total_price decimal(10, 2), 
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
INSERT INTO `purchase_order` (`supplier_id`, `user_id`, `total_amount`, `total_price`, `status_id`) 
VALUES
    (1, 1, 15.00, 254.7, 2),
    (2, 2, 8.00, 97.92, 2); 
    
CREATE TABLE IF NOT EXISTS purchase_order_items (
    purchase_order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_order_id INT,
    product_id INT,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10, 2) NOT NULL,
    profit DECIMAL(10, 2) NOT NULL,
    import_status TINYINT(1) DEFAULT 0,
    FOREIGN KEY (purchase_order_id) REFERENCES purchase_order(purchase_order_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (product_id) REFERENCES product(product_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
INSERT INTO `purchase_order_items` (`purchase_order_id`, `product_id`, `quantity`, `price`, `profit`) 
VALUES
    (1, 1, 10, 15.00, 5.00),
    (1, 3, 5, 18.00, 8.00),
    (2, 2, 8, 12.00, 2.00);
    
CREATE TABLE if not exists review (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    rating INT CHECK (rating BETWEEN 1 AND 5), -- Đánh giá từ 1 đến 5 sao
    review_text TEXT,
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
INSERT INTO `review` (`user_id`, `product_id`, `rating`, `review_text`, `status_id`) 
VALUES
    (1, 1, 5, 'Amazing book! A must-read for everyone.', 1),
    (2, 2, 4, 'Great read, but a bit lengthy.', 1),
    (3, 3, 5, 'Fascinating insights into human history.', 1),
    (4, 4, 3, 'Interesting story, but the plot was predictable.', 1);

CREATE TABLE if not exists permission (
    permission_id INT AUTO_INCREMENT PRIMARY KEY,
    permission_name VARCHAR(100) NOT NULL UNIQUE,
    permission_description TEXT,
    status_id INT
);

INSERT INTO `permission` (`permission_id`, `permission_name`, `permission_description`, `status_id`) 
VALUES
	(1, 'CREATE_PRODUCT', 'Permission to create a new product', 1),
	(2, 'EDIT_PRODUCT', 'Permission to edit an existing product', 1),
	(3, 'DELETE_PRODUCT', 'Permission to delete a product', 1),
	(4, 'VIEW_PRODUCT', 'Permission to view product details', 1);

CREATE TABLE if not exists role_permission (
    role_id INT,
    permission_id INT,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES role(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permission(permission_id) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO `role_permission` (`role_id`, `permission_id`) 
VALUES
	(1, 1),
	(1, 2),
	(1, 3),
	(1, 4),
	(2, 4),
	(3, 4),
	(4, 1),
	(4, 2),
	(4, 4);

