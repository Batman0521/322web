-- 322web Portfolio Database
CREATE DATABASE IF NOT EXISTS portfolio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE portfolio_db;

-- Admin users
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Navigation menus
CREATE TABLE IF NOT EXISTS menus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- News / Projects
CREATE TABLE IF NOT EXISTS news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    category ENUM('Мэдээ','Төсөл','Зар','Блог') DEFAULT 'Мэдээ',
    image_url VARCHAR(255),
    is_published TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Contact messages
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin: admin / admin123
INSERT INTO admins (username, password) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Default menus
INSERT INTO menus (name, url, sort_order) VALUES
('Миний ур чадвар', '#skills', 1),
('Бүтээсэн төсөл', '#projects', 2),
('Холбоо барих', '#contact', 3);

-- Sample projects
INSERT INTO news (title, content, category, is_published) VALUES
('Портфолио вэб хуудас', 'HTML, CSS, JavaScript ашиглан хийсэн хувийн портфолио хуудас.', 'Төсөл', 1),
('Онлайн дэлгүүр', 'PHP болон MySQL ашиглан хийсэн e-commerce систем.', 'Төсөл', 1),
('Вэб хөгжүүлэлтийн мэдээ', 'React 19 шинэ хувилбар гарлаа. Олон шинэ боломжуудтай.', 'Мэдээ', 1);
