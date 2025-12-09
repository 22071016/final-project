
CREATE DATABASE IF NOT EXISTS blood_donation 
    DEFAULT CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE blood_donation;


CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(191) NOT NULL,
    role ENUM('admin','staff','donor','guest') DEFAULT 'donor',
    avatar VARCHAR(191) NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB;


CREATE TABLE donors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,                   
    full_name VARCHAR(191) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    email VARCHAR(150) NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('male','female','other') NOT NULL,
    address TEXT NULL,
    blood_type ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
    weight DECIMAL(5,2) NULL,                            -- kg
    is_active TINYINT(1) DEFAULT 1,
    last_donation_date DATE NULL,
    total_donations INT DEFAULT 0,
    user_id INT NULL,                                    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_donor_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_code (code),
    INDEX idx_phone (phone),
    INDEX idx_blood_type (blood_type)
) ENGINE=InnoDB;


CREATE TABLE health_checks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT NOT NULL,
    check_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    weight DECIMAL(5,2) NULL,
    blood_pressure VARCHAR(20) NULL,       
    heart_rate INT NULL,
    temperature DECIMAL(4,2) NULL,
    hemoglobin DECIMAL(4,2) NULL,           
    is_normal TINYINT(1) DEFAULT 1,
    notes TEXT NULL,
    staff_id INT NULL,                      
    
    CONSTRAINT fk_hc_donor FOREIGN KEY (donor_id) REFERENCES donors(id) ON DELETE CASCADE,
    CONSTRAINT fk_hc_staff FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_donor_date (donor_id, check_date)
) ENGINE=InnoDB;

CREATE TABLE  donations (


id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT NOT NULL,
    health_check_id INT NOT NULL,
    donation_code VARCHAR(30) UNIQUE NOT NULL,   -- Mã túi máu: BM-2025-000123
    donation_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    volume_ml INT NOT NULL DEFAULT 350,          -- 250/350/450
    blood_type_collected ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
    notes TEXT NULL,
    staff_id INT NULL,
    status ENUM('completed','deferred','rejected') DEFAULT 'completed',
    
    CONSTRAINT fk_donation_donor FOREIGN KEY (donor_id) REFERENCES donors(id) ON DELETE RESTRICT,
    CONSTRAINT fk_donation_hc FOREIGN KEY (health_check_id) REFERENCES health_checks(id) ON DELETE RESTRICT,
    CONSTRAINT fk_donation_staff FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_donation_date (donation_date)
) ENGINE=InnoDB;


CREATE TABLE blood_inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donation_id INT NOT NULL,
    blood_bag_code VARCHAR(50) UNIQUE NOT NULL,   
    blood_type ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
    volume_ml INT NOT NULL,
    collection_date DATE NOT NULL,
    expiry_date DATE NOT NULL,                    
    storage_location VARCHAR(100) NULL,
    status ENUM('available','reserved','used','expired','discarded') DEFAULT 'available',
    used_at DATETIME NULL,
    used_for VARCHAR(191) NULL,                   
    
    CONSTRAINT fk_inventory_donation FOREIGN KEY (donation_id) REFERENCES donations(id) ON DELETE RESTRICT,
    INDEX idx_blood_type (blood_type),
    INDEX idx_status (status),
    INDEX idx_expiry (expiry_date)
) ENGINE=InnoDB;


CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT NOT NULL,
    appointment_date DATETIME NOT NULL,
    location VARCHAR(191) NOT NULL,
    status ENUM('pending','confirmed','completed','cancelled','noshow') DEFAULT 'pending',
    notes TEXT NULL,
    created_by INT NULL,                          
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_apt_donor FOREIGN KEY (donor_id) REFERENCES donors(id) ON DELETE CASCADE,
    CONSTRAINT fk_apt_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_date (appointment_date)
) ENGINE=InnoDB;


CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(191) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info','warning','success','appointment','blood_request') DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_notif_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_read (is_read),id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT NOT NULL,
    health_check_id INT NOT NULL,
    donation_code VARCHAR(30) UNIQUE NOT NULL,   
    donation_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    volume_ml INT NOT NULL DEFAULT 350,         
    blood_type_collected ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
    notes TEXT NULL,
    staff_id INT NULL,
    status ENUM('completed','deferred','rejected') DEFAULT 'completed',
    
    CONSTRAINT fk_donation_donor FOREIGN KEY (donor_id) REFERENCES donors(id) ON DELETE RESTRICT,
    CONSTRAINT fk_donation_hc FOREIGN KEY (health_check_id) REFERENCES health_checks(id) ON DELETE RESTRICT,
    CONSTRAINT fk_donation_staff FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_donation_date (donation_date)
) ENGINE=InnoDB;


