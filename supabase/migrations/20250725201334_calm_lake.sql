-- Database: sistema_produtos_limpeza
-- Criação das tabelas do sistema

CREATE DATABASE IF NOT EXISTS sistema_produtos_limpeza;
USE sistema_produtos_limpeza;

-- Tabela produtos
CREATE TABLE produtos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    imagem VARCHAR(255),
    categoria VARCHAR(100),
    estoque INT DEFAULT 0,
    ativo TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela usuarios (para admin)
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inserir usuário admin padrão (senha: admin123)
INSERT INTO usuarios (usuario, senha) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Inserir produtos de exemplo
INSERT INTO produtos (nome, preco, imagem, categoria, estoque, ativo) VALUES
('Detergente Neutro 500ml', 3.50, 'detergente-neutro.jpg', 'Detergentes', 100, 1),
('Desinfetante Pinho 1L', 5.90, 'desinfetante-pinho.jpg', 'Desinfetantes', 80, 1),
('Água Sanitária 1L', 2.30, 'agua-sanitaria.jpg', 'Sanitários', 120, 1),
('Sabão em Pó 1kg', 8.90, 'sabao-po.jpg', 'Sabões', 60, 1),
('Limpa Vidros 500ml', 4.20, 'limpa-vidros.jpg', 'Limpadores', 90, 1),
('Alvejante 1L', 3.80, 'alvejante.jpg', 'Sanitários', 70, 1),
('Desengordurante 500ml', 6.50, 'desengordurante.jpg', 'Limpadores', 50, 1),
('Amaciante 2L', 7.90, 'amaciante.jpg', 'Amaciantes', 40, 1);