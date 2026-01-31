-- ==========================================
-- ALUMÍNIOS PREMIUM - DATABASE SCHEMA
-- ==========================================

-- Create database
CREATE DATABASE IF NOT EXISTS aluminios_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE aluminios_db;

-- Table for contact form submissions
CREATE TABLE IF NOT EXISTS contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    telefone VARCHAR(50) NOT NULL,
    servico VARCHAR(50) NOT NULL,
    localidade VARCHAR(255) NOT NULL,
    mensagem TEXT NOT NULL,
    marketing TINYINT(1) DEFAULT 0,
    data_submissao DATETIME NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    status ENUM('novo', 'em_analise', 'contactado', 'orcamento_enviado', 'concluido', 'cancelado') DEFAULT 'novo',
    notas TEXT,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_data (data_submissao),
    INDEX idx_status (status),
    INDEX idx_servico (servico)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for user management (admin panel)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    ativo TINYINT(1) DEFAULT 1,
    ultimo_acesso DATETIME,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: admin123 - CHANGE THIS!)
INSERT INTO users (username, password_hash, nome, email, role)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'admin@aluminios.pt', 'admin');

-- Table for activity log
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    contacto_id INT,
    acao VARCHAR(100) NOT NULL,
    descricao TEXT,
    data_acao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (contacto_id) REFERENCES contactos(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_contacto (contacto_id),
    INDEX idx_data (data_acao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for services catalog
CREATE TABLE IF NOT EXISTS servicos_catalogo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    imagem VARCHAR(255),
    ativo TINYINT(1) DEFAULT 1,
    ordem INT DEFAULT 0,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default services
INSERT INTO servicos_catalogo (codigo, nome, descricao, ordem) VALUES
('janelas', 'Janelas & Portadas', 'Janelas de alumínio de alta qualidade', 1),
('portas', 'Portas de Entrada', 'Portas de segurança em alumínio', 2),
('fachadas', 'Fachadas & Envidraçados', 'Sistemas de fachadas para edifícios', 3),
('estores', 'Estores & Proteções', 'Sistemas de proteção solar e segurança', 4),
('varandas', 'Envidraçamento de Varandas', 'Fechos de varandas e marquises', 5),
('medida', 'Projetos à Medida', 'Soluções personalizadas', 6);

-- Table for quotes/budgets
CREATE TABLE IF NOT EXISTS orcamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contacto_id INT NOT NULL,
    numero_orcamento VARCHAR(50) UNIQUE NOT NULL,
    valor_total DECIMAL(10, 2),
    descricao TEXT,
    ficheiro_pdf VARCHAR(255),
    status ENUM('rascunho', 'enviado', 'aprovado', 'rejeitado', 'expirado') DEFAULT 'rascunho',
    validade_ate DATE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (contacto_id) REFERENCES contactos(id) ON DELETE CASCADE,
    INDEX idx_numero (numero_orcamento),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create database user (run this separately with root privileges)
-- CREATE USER 'aluminios_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON aluminios_db.* TO 'aluminios_user'@'localhost';
-- FLUSH PRIVILEGES;