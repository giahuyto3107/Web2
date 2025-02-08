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
	(5, 'Failed', 'The record has encountered an error or was unsuccessful');


CREATE TABLE IF NOT EXISTS role (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    role_description VARCHAR(255),
    status_id INT
);

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

CREATE TABLE IF NOT EXISTS category (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    category_description TEXT,
    status_id INT,
    FOREIGN KEY (status_id) REFERENCES status(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

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

CREATE TABLE IF NOT EXISTS cart_items (
    user_id INT, 
    product_id INT,  
    quantity INT NOT NULL DEFAULT 1, 
    FOREIGN KEY (user_id) REFERENCES user(user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10, 2) NOT NULL,
    status_id INT,
    payment_method VARCHAR(50) NOT NULL,
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
    quantity INT NOT NULL DEFAULT 1,
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (product_id) REFERENCES product(product_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

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

-- INSERT INTO supplier (supplier_name, contact_person, contact_email, contact_phone, address, status_id) 
-- VALUES
-- 	('', 'John Doe', 'john.doe@techsupply.com', '1234567890', '123 Tech Street, New York, USA', 1),
-- 	('GadgetWorld', 'Alice Smith', 'alice.smith@gadgetworld.com', '9876543210', '456 Gadget Ave, San Francisco, USA', 1),
-- 	('Digital Solutions', 'Bob Johnson', 'bob.johnson@digitalsolutions.com', '1122334455', '789 Digital Rd, Los Angeles, USA', 1),
-- 	('Hardware Hub', 'Emma Brown', 'emma.brown@hardwarehub.com', '6677889900', '321 Hardware Lane, Chicago, USA', 1),
-- 	('ElectroMart', 'Michael Green', 'michael.green@electromart.com', '9988776655', '654 Electro Blvd, Houston, USA', 1);-- 


CREATE TABLE IF NOT EXISTS purchase_order (
    purchase_order_id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT,
    user_id INT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount int NOT NULL,
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

-- INSERT INTO purchase_order (supplier_id, user_id, order_date, total_amount, status_id, import_status) VALUES
-- (1, 2, '2025-02-08 10:15:00', 15, 2, 0),  
-- (2, 3, '2025-02-07 15:30:00', 7, 2, 0),  
-- (3, 4, '2025-02-06 09:45:00', 13, 2, 0),   
-- (4, 5, '2025-02-05 18:20:00', 6, 2, 0),  
-- (5, 6, '2025-02-04 12:10:00', 12, 2, 0);  

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

CREATE TABLE if not exists permission (
    permission_id INT AUTO_INCREMENT PRIMARY KEY,
    permission_name VARCHAR(100) NOT NULL UNIQUE,
    permission_description TEXT,
    status_id INT
);

CREATE TABLE if not exists role_permission (
    role_id INT,
    permission_id INT,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES role(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permission(permission_id) ON DELETE CASCADE ON UPDATE CASCADE
);

