-- ============================================================
--  OFV Inventory Management System – Database Setup Script
--  Ordnance Factory Varangaon, Ministry of Defence
<<<<<<< HEAD
--  v3.0 – Multi-role login system
=======
>>>>>>> 8e508c9b963c8e29112b5e5a4ab939b3626529ab
-- ============================================================

CREATE DATABASE IF NOT EXISTS ordnance_ims CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ordnance_ims;

<<<<<<< HEAD
-- ── Users table (admin + section operators) ──────────────────
CREATE TABLE IF NOT EXISTS users (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    username     VARCHAR(50)  NOT NULL UNIQUE,
    password     VARCHAR(255) NOT NULL,
    full_name    VARCHAR(100),
    role         VARCHAR(20)  DEFAULT 'operator',   -- 'admin' or 'operator'
    section      VARCHAR(60)  DEFAULT NULL,          -- NULL for admin; product_name for operators
    product_code VARCHAR(50)  DEFAULT NULL,          -- locked product code for operators
    created_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

-- ── Products table ────────────────────────────────────────────
=======
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    full_name  VARCHAR(100),
    role       VARCHAR(20)  DEFAULT 'staff',
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

>>>>>>> 8e508c9b963c8e29112b5e5a4ab939b3626529ab
CREATE TABLE IF NOT EXISTS products (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(150) NOT NULL,
    product_code VARCHAR(50)  NOT NULL UNIQUE,
    quantity     INT          NOT NULL DEFAULT 0,
    unit         VARCHAR(30)  DEFAULT 'Units',
    category     VARCHAR(80),
    date_added   DATE         NOT NULL,
    last_updated TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status       VARCHAR(20)  DEFAULT 'active'
);

<<<<<<< HEAD
-- ── Audit / history table ─────────────────────────────────────
=======
>>>>>>> 8e508c9b963c8e29112b5e5a4ab939b3626529ab
CREATE TABLE IF NOT EXISTS inventory_history (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    product_id   INT,
    product_name VARCHAR(150),
    product_code VARCHAR(50),
    action_type  VARCHAR(30),
    old_quantity INT     DEFAULT 0,
    new_quantity INT     DEFAULT 0,
    changed_by   VARCHAR(50),
    remarks      TEXT,
    action_date  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

<<<<<<< HEAD
-- ── Admin user ────────────────────────────────────────────────
INSERT INTO users (username, password, full_name, role, section, product_code) VALUES
('admin', 'admin123', 'Administrator', 'admin', NULL, NULL)
ON DUPLICATE KEY UPDATE password = 'admin123', role = 'admin';

-- ── Section operator accounts ─────────────────────────────────
-- Each operator can ONLY update their own section's inventory
INSERT INTO users (username, password, full_name, role, section, product_code) VALUES
('op_556',  'ofv556',  '5.56 Section Operator',       'operator', '5.56 Bullet',       'OFV-556'),
('op_762',  'ofv762',  '7.62 Section Operator',       'operator', '7.62 Bullet',       'OFV-762'),
('op_prm',  'ofvprm',  'Primer Section Operator',     'operator', 'Primer',            'OFV-PRM'),
('op_cal',  'ofvcal',  'Calibur Section Operator',    'operator', 'Calibur',           'OFV-CAL'),
('op_pkg',  'ofvpkg',  'Packing Section Operator',    'operator', 'Packing Container', 'OFV-PKG')
ON DUPLICATE KEY UPDATE password = VALUES(password);

-- ── OFV Products ──────────────────────────────────────────────
=======
-- Demo login: admin / admin123
INSERT INTO users (username, password, full_name, role) VALUES
('admin', 'admin123', 'Administrator', 'admin')
ON DUPLICATE KEY UPDATE password = 'admin123';

-- OFV Product Inventory (Ordnance-specific items only)
>>>>>>> 8e508c9b963c8e29112b5e5a4ab939b3626529ab
INSERT IGNORE INTO products (product_name, product_code, quantity, unit, category, date_added) VALUES
('5.56 Bullet',       'OFV-556',  12500, 'Rounds',    'Ammunition',            '2025-01-10'),
('7.62 Bullet',       'OFV-762',   8750, 'Rounds',    'Ammunition',            '2025-01-10'),
('Primer',            'OFV-PRM',  50000, 'Pieces',    'Ammunition Components', '2025-02-15'),
('Calibur',           'OFV-CAL',  35000, 'Pieces',    'Ammunition Components', '2025-02-20'),
('Copper Brass',      'OFV-CPB',   4200, 'Kilograms', 'Raw Materials',         '2025-03-05'),
('Packing Container', 'OFV-PKG',    380, 'Units',     'Packaging',             '2025-03-12');
