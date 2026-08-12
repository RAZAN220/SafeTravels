-- database.sql
CREATE DATABASE IF NOT EXISTS safe_travels;
USE safe_travels;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    role ENUM('traveler','admin') DEFAULT 'traveler',
    is_verified TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- User Profile (extends users)
CREATE TABLE profiles (
    user_id INT PRIMARY KEY,
    emergency_contact_name VARCHAR(100),
    emergency_contact_phone VARCHAR(20),
    blood_group VARCHAR(10),
    allergies TEXT,
    medical_conditions TEXT,
    preferred_language VARCHAR(20) DEFAULT 'English',
    profile_pic VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Travel History
CREATE TABLE travel_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    destination VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    purpose VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Safety Alerts (admin posted)
CREATE TABLE safety_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    location_lat DECIMAL(10,8) NOT NULL,
    location_lng DECIMAL(11,8) NOT NULL,
    location_name VARCHAR(255),
    severity ENUM('low','medium','high','critical') DEFAULT 'medium',
    category VARCHAR(50), -- weather, crime, accident, road, health, etc.
    expires_at DATETIME,
    is_active TINYINT(1) DEFAULT 1,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Incidents (user reported)
CREATE TABLE incidents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    location_lat DECIMAL(10,8) NOT NULL,
    location_lng DECIMAL(11,8) NOT NULL,
    location_name VARCHAR(255),
    type VARCHAR(50), -- accident, theft, harassment, medical, other
    severity VARCHAR(20) DEFAULT 'medium',
    image VARCHAR(255),
    status ENUM('pending','verified','resolved','dismissed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Nearby Places (admin managed)
CREATE TABLE places (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category ENUM('hospital','police','hotel','restaurant','fuel','atm','pharmacy','tourist_attraction','other') NOT NULL,
    address TEXT,
    location_lat DECIMAL(10,8) NOT NULL,
    location_lng DECIMAL(11,8) NOT NULL,
    phone VARCHAR(20),
    website VARCHAR(255),
    rating DECIMAL(2,1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Notifications
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) DEFAULT 'info',
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(255),
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Emergency Contacts (system wide)
CREATE TABLE emergency_contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    category VARCHAR(50), -- police, ambulance, fire, helpline
    description TEXT,
    is_active TINYINT(1) DEFAULT 1
);

-- Feedback
CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    trip_id INT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (trip_id) REFERENCES travel_history(id) ON DELETE SET NULL
);

-- ========== INSERT SAMPLE DATA ==========

-- Admin (password: admin123)
INSERT INTO users (fullname, email, password, phone, role) VALUES
('Super Admin', 'admin@safe.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0771234567', 'admin');

-- Sample Traveler (password: traveler123)
INSERT INTO users (fullname, email, password, phone, role) VALUES
('John Traveler', 'john@traveler.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0712345678', 'traveler');

INSERT INTO profiles (user_id, emergency_contact_name, emergency_contact_phone, blood_group) VALUES
(2, 'Mary Traveler', '0718765432', 'O+');

-- Sample Travel History
INSERT INTO travel_history (user_id, destination, start_date, end_date, purpose) VALUES
(2, 'Kandy, Sri Lanka', '2026-08-01', '2026-08-05', 'Vacation'),
(2, 'Galle, Sri Lanka', '2026-08-10', '2026-08-12', 'Business');

-- Sample Places
INSERT INTO places (name, category, location_lat, location_lng, address, phone) VALUES
('Kandy General Hospital', 'hospital', 7.2906, 80.6337, 'Kandy', '081-2223333'),
('Kandy Police Station', 'police', 7.2945, 80.6350, 'Kandy', '081-2224444'),
('Earls Regency Hotel', 'hotel', 7.2778, 80.6411, 'Kandy', '081-2222222'),
('Kandy City Center', 'restaurant', 7.2892, 80.6317, 'Kandy', '081-1234567'),
('Cargills Fuel Station', 'fuel', 7.2920, 80.6280, 'Kandy', '081-1111111'),
('Commercial Bank ATM', 'atm', 7.2875, 80.6345, 'Kandy', '081-2225555'),
('Kandy Pharmacy', 'pharmacy', 7.2888, 80.6330, 'Kandy', '081-2226666');

-- Sample Safety Alerts
INSERT INTO safety_alerts (title, description, location_lat, location_lng, location_name, severity, category, expires_at) VALUES
('Heavy Rain Warning', 'Expect heavy rain in Kandy area from 2PM to 6PM. Use caution on roads.', 7.2900, 80.6300, 'Kandy', 'medium', 'weather', DATE_ADD(NOW(), INTERVAL 24 HOUR)),
('Road Closure Alert', 'Colombo - Kandy main road blocked near Peradeniya due to landslide. Use alternative route via Kurunegala.', 7.2700, 80.6000, 'Peradeniya', 'critical', 'road', DATE_ADD(NOW(), INTERVAL 48 HOUR));

-- Sample Emergency Contacts
INSERT INTO emergency_contacts (name, phone, category, description) VALUES
('Police Emergency', '119', 'police', 'National Police Emergency'),
('Ambulance Service', '110', 'ambulance', 'National Ambulance Service'),
('Fire Department', '112', 'fire', 'National Fire Rescue Service'),
('Tourist Police', '1912', 'helpline', 'Tourist Police Hotline'),
('Suicide Prevention', '1333', 'helpline', 'Mental Health Support');

-- Sample Notification
INSERT INTO notifications (user_id, type, title, message, link) VALUES
(2, 'alert', 'New Safety Alert', 'Heavy rain warning in Kandy area.', 'traveler/safety-alerts.php');