-- 1. Criação do Banco de Dados
CREATE DATABASE IF NOT EXISTS plataforma_denuncias;
USE plataforma_denuncias;

-- 2. Tabela de Usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('cidadao', 'admin') DEFAULT 'cidadao',
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Tabela de Categorias
CREATE TABLE IF NOT EXISTS categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nome_categoria VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

-- 4. Tabela de Denúncias
CREATE TABLE IF NOT EXISTS denuncias (
    id_denuncia INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT NOT NULL,
    localizacao VARCHAR(255) NOT NULL,
    foto_path VARCHAR(255),
    status ENUM('Aberto', 'Em Andamento', 'Resolvido') DEFAULT 'Aberto',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fk_usuario INT NOT NULL,
    fk_categoria INT NOT NULL,
    FOREIGN KEY (fk_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (fk_categoria) REFERENCES categorias(id_categoria) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- 5. Tabela de Comentários
CREATE TABLE IF NOT EXISTS comentarios (
    id_comentario INT AUTO_INCREMENT PRIMARY KEY,
    texto TEXT NOT NULL,
    data_comentario TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fk_usuario INT NOT NULL,
    fk_denuncia INT NOT NULL,
    FOREIGN KEY (fk_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (fk_denuncia) REFERENCES denuncias(id_denuncia) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. Tabela de Curtidas em Denúncias
CREATE TABLE IF NOT EXISTS curtida_denuncias (
    fk_usuario INT NOT NULL,
    fk_denuncia INT NOT NULL,
    data_curtida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (fk_usuario, fk_denuncia),
    FOREIGN KEY (fk_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (fk_denuncia) REFERENCES denuncias(id_denuncia) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 7.Tabela: Curtidas em Comentários
CREATE TABLE IF NOT EXISTS curtida_comentarios (
    fk_usuario INT NOT NULL,
    fk_comentario INT NOT NULL,
    data_curtida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (fk_usuario, fk_comentario),
    FOREIGN KEY (fk_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (fk_comentario) REFERENCES comentarios(id_comentario) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 8. Inserção de Categorias Iniciais
INSERT INTO categorias (nome_categoria) VALUES 
('Iluminação Pública'),
('Asfalto e Buracos'),
('Lixo e Limpeza'),
('Saneamento/Esgoto'),
('Segurança');