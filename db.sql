CREATE DATABASE IF NOT EXISTS car_workshop;
USE car_workshop;

-- Admin users table for login authentication
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Default admin: username=admin, password=admin123
INSERT INTO admin_users (username, password) VALUES
('admin', 'admin123')
ON DUPLICATE KEY UPDATE username=username;

-- Mechanics table with workload configuration
CREATE TABLE IF NOT EXISTS mechanics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    max_cars INT DEFAULT 4,
    day_type ENUM('full', 'half') DEFAULT 'full',
    half_day_max INT DEFAULT 2
);

INSERT INTO mechanics (name) VALUES
('John Doe'),
('Jane Smith'),
('Mike Johnson'),
('Emily Davis'),
('David Wilson')
ON DUPLICATE KEY UPDATE name=name;

-- Appointments table
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(50) NOT NULL,
    car_license VARCHAR(50) NOT NULL,
    car_engine VARCHAR(50) NOT NULL,
    appointment_date DATE NOT NULL,
    mechanic_id INT NOT NULL,
    FOREIGN KEY (mechanic_id) REFERENCES mechanics(id)
);

-- System configuration (e.g. user daily booking limit)
CREATE TABLE IF NOT EXISTS system_config (
    config_key VARCHAR(100) PRIMARY KEY,
    config_value VARCHAR(255) NOT NULL
);

INSERT INTO system_config (config_key, config_value) VALUES
('user_daily_limit', '3')
ON DUPLICATE KEY UPDATE config_key=config_key;
