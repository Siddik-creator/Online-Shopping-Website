-- RESET DATABASE
DROP DATABASE IF EXISTS ecommerce;
CREATE DATABASE ecommerce;
USE ecommerce;

SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- admin_info
-- ----------------------------
CREATE TABLE admin_info (
  admin_id INT AUTO_INCREMENT PRIMARY KEY,
  admin_name VARCHAR(100) NOT NULL,
  admin_email VARCHAR(300) NOT NULL,
  admin_password VARCHAR(300) NOT NULL
);

INSERT INTO admin_info VALUES
(1, 'admin', 'admin@gmail.com', '25f9e794323b453885f5181f1b624d0b');

-- ----------------------------
-- brands
-- ----------------------------
CREATE TABLE brands (
  brand_id INT AUTO_INCREMENT PRIMARY KEY,
  brand_title TEXT NOT NULL
);

INSERT INTO brands VALUES
(1,'HP'),(2,'Samsung'),(3,'Apple'),(4,'Motorola'),(5,'LG'),(6,'Cloth Brand');

-- ----------------------------
-- categories
-- ----------------------------
CREATE TABLE categories (
  cat_id INT AUTO_INCREMENT PRIMARY KEY,
  cat_title TEXT NOT NULL
);

INSERT INTO categories VALUES
(1,'Electronics'),(2,'Ladies Wears'),(3,'Mens Wear'),
(4,'Kids Wear'),(5,'Furnitures'),(6,'Home Appliances'),
(7,'Electronics Gadgets');

-- ----------------------------
-- products
-- ----------------------------
CREATE TABLE products (
  product_id INT AUTO_INCREMENT PRIMARY KEY,
  product_cat INT NOT NULL,
  product_brand INT NOT NULL,
  product_title VARCHAR(255) NOT NULL,
  product_price INT NOT NULL,
  product_desc TEXT NOT NULL,
  product_image TEXT NOT NULL,
  product_keywords TEXT NOT NULL
);

-- (Insert only few sample rows to avoid overload)
INSERT INTO products VALUES
(1,1,2,'Samsung Galaxy S7',5000,'Samsung phone','product07.png','samsung'),
(2,1,3,'iPhone 5s',25000,'Apple phone','iphone.png','iphone'),
(3,1,1,'HP Laptop',35000,'Laptop','laptop.png','hp laptop');

-- ----------------------------
-- user_info
-- ----------------------------
CREATE TABLE user_info (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100),
  last_name VARCHAR(100),
  email VARCHAR(300),
  password VARCHAR(300),
  mobile VARCHAR(15),
  address1 VARCHAR(300),
  address2 VARCHAR(100)
);

INSERT INTO user_info VALUES
(1,'John','Doe','john@gmail.com','1234','9999999999','City','Area');

-- ----------------------------
-- user_info_backup
-- ----------------------------
CREATE TABLE user_info_backup LIKE user_info;

-- ----------------------------
-- cart
-- ----------------------------
CREATE TABLE cart (
  id INT AUTO_INCREMENT PRIMARY KEY,
  p_id INT,
  ip_add VARCHAR(255),
  user_id INT,
  qty INT
);

-- ----------------------------
-- orders
-- ----------------------------
CREATE TABLE orders (
  order_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  product_id INT,
  qty INT,
  trx_id VARCHAR(255),
  p_status VARCHAR(50)
);

-- ----------------------------
-- orders_info
-- ----------------------------
CREATE TABLE orders_info (
  order_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  f_name VARCHAR(255),
  email VARCHAR(255),
  address VARCHAR(255),
  city VARCHAR(255),
  state VARCHAR(255),
  zip INT,
  cardname VARCHAR(255),
  cardnumber VARCHAR(20),
  expdate VARCHAR(20),
  prod_count INT,
  total_amt INT,
  cvv INT
);

-- ----------------------------
-- order_products
-- ----------------------------
CREATE TABLE order_products (
  order_pro_id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  product_id INT,
  qty INT,
  amt INT
);

-- ----------------------------
-- FOREIGN KEYS
-- ----------------------------
ALTER TABLE orders_info
ADD FOREIGN KEY (user_id) REFERENCES user_info(user_id);

ALTER TABLE order_products
ADD FOREIGN KEY (order_id) REFERENCES orders_info(order_id),
ADD FOREIGN KEY (product_id) REFERENCES products(product_id);

SET FOREIGN_KEY_CHECKS = 1;