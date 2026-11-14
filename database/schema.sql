-- vektor/database/schema.sql
CREATE DATABASE IF NOT EXISTS vektor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vektor;

CREATE TABLE IF NOT EXISTS produtos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome VARCHAR(120) NOT NULL,
  peso DECIMAL(10,2) NOT NULL,
  valor DECIMAL(12,2) NOT NULL,
  descricao VARCHAR(255) NULL,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS enderecos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  apelido VARCHAR(80) NOT NULL,
  tipo ENUM('origem','destino') NOT NULL,
  cep VARCHAR(9) NOT NULL,
  logradouro VARCHAR(150) NOT NULL,
  numero VARCHAR(10) NOT NULL,
  complemento VARCHAR(80) NULL,
  cidade VARCHAR(80) NOT NULL,
  uf CHAR(2) NOT NULL,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS transportadores (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome VARCHAR(120) NOT NULL,
  cnpj VARCHAR(18) NOT NULL,
  telefone VARCHAR(20) NULL,
  veiculo VARCHAR(40) NOT NULL,
  capacidade_kg INT UNSIGNED NOT NULL,
  placa VARCHAR(8) NULL,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS fretes (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  transportador_id INT UNSIGNED NOT NULL,
  origem_id INT UNSIGNED NOT NULL,
  destino_id INT UNSIGNED NOT NULL,
  data_frete DATE NOT NULL,
  status ENUM('aguardando','em_transito','entregue') NOT NULL DEFAULT 'aguardando',
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_fretes_data (data_frete),
  CONSTRAINT fk_fretes_transportador FOREIGN KEY (transportador_id) REFERENCES transportadores (id),
  CONSTRAINT fk_fretes_origem FOREIGN KEY (origem_id) REFERENCES enderecos (id),
  CONSTRAINT fk_fretes_destino FOREIGN KEY (destino_id) REFERENCES enderecos (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS frete_itens (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  frete_id INT UNSIGNED NOT NULL,
  produto_id INT UNSIGNED NOT NULL,
  quantidade INT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY uq_frete_produto (frete_id, produto_id),
  CONSTRAINT fk_itens_frete FOREIGN KEY (frete_id) REFERENCES fretes (id) ON DELETE CASCADE,
  CONSTRAINT fk_itens_produto FOREIGN KEY (produto_id) REFERENCES produtos (id)
) ENGINE=InnoDB;
