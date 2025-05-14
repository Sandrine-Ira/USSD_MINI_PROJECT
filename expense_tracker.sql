
CREATE DATABASE IF NOT EXISTS expense_tracker;
USE expense_tracker;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100),
    pin VARCHAR(10),
    phone_number VARCHAR(20) UNIQUE
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone_number VARCHAR(20),
    category_name VARCHAR(50),
    budget INT
);

CREATE TABLE expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone_number VARCHAR(20),
    category_id INT,
    amount INT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
