-- =============================================
-- TravelMate Database Setup
-- Run this script in phpMyAdmin or MySQL CLI
-- =============================================

-- Create database
CREATE DATABASE IF NOT EXISTS wisata_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE wisata_db;

-- =============================================
-- Users Table
-- =============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    foto VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================
-- Packages Table (Paket Wisata)
-- =============================================
CREATE TABLE IF NOT EXISTS paket (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(12,2) NOT NULL,
    durasi VARCHAR(50),
    lokasi VARCHAR(255),
    foto VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================
-- Bookings Table (Master)
-- =============================================
CREATE TABLE IF NOT EXISTS booking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    paket_id INT NOT NULL,
    tanggal_berangkat DATE NOT NULL,
    total_harga DECIMAL(12,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (paket_id) REFERENCES paket(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =============================================
-- Booking Details Table (Detail - Passengers)
-- =============================================
CREATE TABLE IF NOT EXISTS booking_detail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    nama_penumpang VARCHAR(100) NOT NULL,
    no_identitas VARCHAR(50),
    telepon VARCHAR(20),
    FOREIGN KEY (booking_id) REFERENCES booking(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =============================================
-- Sample Data
-- =============================================

-- Admin user (password: admin123)
INSERT INTO users (nama, email, password, role) VALUES 
('Administrator', 'admin@travelmate.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Sample user (password: password)
INSERT INTO users (nama, email, password, role) VALUES 
('John Doe', 'user@travelmate.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Sample packages
INSERT INTO paket (nama, deskripsi, harga, durasi, lokasi) VALUES 
('Pesona Bali 4D3N', 'Jelajahi keindahan Pulau Dewata dengan paket lengkap termasuk akomodasi, transportasi, dan guide profesional. Kunjungi Tanah Lot, Ubud, Kuta Beach, dan destinasi ikonik lainnya.', 3500000, '4 Hari 3 Malam', 'Bali'),
('Raja Ampat Explorer', 'Surga bawah laut Indonesia menanti. Nikmati snorkeling, diving, dan pemandangan alam spektakuler di kepulauan Raja Ampat yang memukau.', 8500000, '5 Hari 4 Malam', 'Papua Barat'),
('Sunrise Bromo Trip', 'Saksikan keajaiban matahari terbit dari kawah Bromo yang legendaris. Termasuk jeep tour dan kunjungan ke Bukit Teletubbies.', 1200000, '2 Hari 1 Malam', 'Jawa Timur'),
('Lombok Adventure', 'Pink Beach, Gili Islands, dan keindahan alam Lombok dalam satu paket. Cocok untuk honeymoon atau liburan keluarga.', 4200000, '4 Hari 3 Malam', 'NTB'),
('Jogja Heritage Tour', 'Jelajahi warisan budaya Jogja: Borobudur, Prambanan, Keraton, Malioboro, dan kuliner legendaris khas Yogyakarta.', 2800000, '3 Hari 2 Malam', 'Yogyakarta'),
('Komodo Island Safari', 'Bertemu langsung dengan Komodo Dragon di habitat aslinya. Jelajahi Pulau Padar, Pink Beach, dan Manta Point.', 6500000, '4 Hari 3 Malam', 'NTT');

-- =============================================
-- Login Credentials for Testing:
-- Admin: admin@travelmate.id / password
-- User:  user@travelmate.id / password
-- =============================================
