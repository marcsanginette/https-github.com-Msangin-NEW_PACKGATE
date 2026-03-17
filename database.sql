CREATE DATABASE IF NOT EXISTS packgate_db;
USE packgate_db;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100) NOT NULL,
    name VARCHAR(255) NOT NULL,
    rating DECIMAL(3,1) DEFAULT 5.0,
    price DECIMAL(10,2) NOT NULL,
    old_price DECIMAL(10,2) NULL,
    image VARCHAR(255) NOT NULL,
    badge VARCHAR(50) NULL,
    badge_color VARCHAR(50) NULL,
    section VARCHAR(50) NOT NULL
);

INSERT INTO products (category, name, rating, price, old_price, image, badge, badge_color, section) VALUES
('Papel', 'Caixa de Papelão Ondulado 50x50x50', 4.5, 4.50, 6.00, 'https://images.unsplash.com/photo-1589939705384-5185137a7f0f?w=500&q=80', '-25%', 'bg-red-500', 'deals'),
('Vidro', 'Pote de Vidro Hermético 1L', 5.0, 12.99, NULL, 'https://images.unsplash.com/photo-1584346133934-a3afd2a33c4c?w=500&q=80', NULL, NULL, 'deals'),
('Plástico', 'Bobina de Plástico Bolha 100m', 4.0, 45.00, NULL, 'https://images.unsplash.com/photo-1626863905121-3b0c0ed7b94c?w=500&q=80', 'Novo', 'bg-brand-green', 'deals'),
('Metal', 'Lata de Alumínio para Mantimentos', 4.5, 18.50, NULL, 'https://images.unsplash.com/photo-1614735241165-6756e1df61ab?w=500&q=80', NULL, NULL, 'deals'),
('Madeira', 'Caixa de Madeira Pinus Decorativa', 5.0, 35.00, 40.00, 'https://images.unsplash.com/photo-1611077544811-042813ce8282?w=500&q=80', '-12%', 'bg-red-500', 'deals'),
('Especiais', 'Embalagem para Presente Premium', 4.5, 8.99, NULL, 'https://images.unsplash.com/photo-1549465220-1a8b9238cd48?w=500&q=80', NULL, NULL, 'deals'),

('Sustentável', 'Sacola Kraft Ecológica (100 un)', 4.8, 89.90, NULL, 'https://images.unsplash.com/photo-1592840062668-9812689c17e6?w=500&q=80', 'Novo', 'bg-brand-green', 'arrivals'),
('Plástico', 'Pote Plástico Descartável 250ml (50 un)', 4.2, 15.50, NULL, 'https://images.unsplash.com/photo-1606502973842-f64bc2785fe5?w=500&q=80', NULL, NULL, 'arrivals'),
('Vidro', 'Garrafa de Vidro Âmbar 500ml', 4.9, 6.50, NULL, 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?w=500&q=80', NULL, NULL, 'arrivals'),
('Metal', 'Lata de Flandres Redonda', 4.5, 12.00, NULL, 'https://images.unsplash.com/photo-1565586419448-95b774010ee4?w=500&q=80', NULL, NULL, 'arrivals'),
('Papel', 'Tubo de Papelão para Envio', 4.7, 3.20, 4.00, 'https://images.unsplash.com/photo-1587582423116-ec07293f0395?w=500&q=80', '-20%', 'bg-red-500', 'arrivals'),
('Madeira', 'Palete de Madeira Padrão PBR', 4.6, 45.00, NULL, 'https://images.unsplash.com/photo-1501430654243-c934cec2e1c0?w=500&q=80', NULL, NULL, 'arrivals'),

('Plástico', 'Saco Plástico Transparente (1000 un)', 4.9, 25.00, NULL, 'https://images.unsplash.com/photo-1530587191325-3db32d826c18?w=500&q=80', NULL, NULL, 'popular'),
('Papel', 'Caixa para Pizza Oitavada 35cm', 4.8, 2.50, NULL, 'https://images.unsplash.com/photo-1566843972142-a7fcb70de55a?w=500&q=80', NULL, NULL, 'popular'),
('Especiais', 'Fita Adesiva Personalizada 50m', 5.0, 18.90, NULL, 'https://images.unsplash.com/photo-1586864387789-628af9feed72?w=500&q=80', 'Top', 'bg-yellow-500', 'popular'),
('Vidro', 'Frasco de Vidro para Perfume 50ml', 4.7, 8.50, NULL, 'https://images.unsplash.com/photo-1594035910387-fea47794261f?w=500&q=80', NULL, NULL, 'popular'),
('Metal', 'Tambor Metálico 200L', 4.5, 150.00, 180.00, 'https://images.unsplash.com/photo-1605000797499-95a51c5269ae?w=500&q=80', '-16%', 'bg-red-500', 'popular'),
('Sustentável', 'Embalagem Biodegradável para Hambúrguer', 4.9, 1.20, NULL, 'https://images.unsplash.com/photo-1624372554743-162804798363?w=500&q=80', NULL, NULL, 'popular');

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role ENUM('comprador', 'fabricante', 'admin') NOT NULL DEFAULT 'comprador',
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    company_name VARCHAR(255) NULL,
    phone VARCHAR(50) NULL,
    cnpj VARCHAR(20) NULL,
    description TEXT NULL,
    employees_count VARCHAR(50) NULL,
    annual_revenue VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
