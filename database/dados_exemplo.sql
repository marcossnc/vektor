-- vektor/database/dados_exemplo.sql
SET NAMES utf8mb4;
USE vektor;

INSERT INTO produtos (nome, peso, valor, descricao) VALUES
('Notebook 15"', 2.40, 3899.00, NULL),
('Caixa de ferramentas', 6.80, 420.00, NULL),
('Monitor 27"', 4.10, 1350.00, NULL),
('Cadeira ergonômica', 14.50, 1790.00, NULL),
('Impressora laser', 9.20, 980.00, NULL);

INSERT INTO enderecos (apelido, tipo, cep, logradouro, numero, complemento, cidade, uf) VALUES
('Depósito Campinas', 'origem', '13010-000', 'Av. Brasil', '1200', NULL, 'Campinas', 'SP'),
('Matriz São Paulo', 'origem', '01305-000', 'Rua Augusta', '450', NULL, 'São Paulo', 'SP'),
('Loja Curitiba', 'destino', '80020-000', 'Rua XV de Novembro', '88', NULL, 'Curitiba', 'PR'),
('Filial Londrina', 'destino', '86010-000', 'Av. Higienópolis', '300', NULL, 'Londrina', 'PR'),
('Cliente Maringá', 'destino', '87013-000', 'Av. Colombo', '2100', NULL, 'Maringá', 'PR');

INSERT INTO transportadores (nome, cnpj, telefone, veiculo, capacidade_kg, placa) VALUES
('TransLog Express', '12.345.678/0001-90', '(41) 99999-0001', 'Caminhão baú', 12000, 'ABC-1D23'),
('Rápido Sul', '23.456.789/0001-01', '(44) 99999-0002', 'Van', 1500, 'BCD-2E34'),
('Cargas Brasil', '34.567.890/0001-12', '(41) 99999-0003', 'Carreta', 28000, 'CDE-3F45'),
('Via Norte Logística', '45.678.901/0001-23', '(43) 99999-0004', 'Caminhão toco', 6000, 'DEF-4G56'),
('Entrega Fácil', '56.789.012/0001-34', '(44) 99999-0005', 'Utilitário', 800, 'EFG-5H67');

INSERT INTO fretes (transportador_id, origem_id, destino_id, data_frete, status) VALUES
(1, 1, 3, CURDATE(), 'em_transito'),
(2, 2, 5, CURDATE(), 'em_transito'),
(1, 2, 4, CURDATE(), 'aguardando'),
(3, 1, 3, CURDATE(), 'entregue'),
(4, 1, 4, CURDATE() - INTERVAL 1 DAY, 'entregue'),
(5, 2, 3, CURDATE() - INTERVAL 1 DAY, 'entregue'),
(2, 1, 5, CURDATE() - INTERVAL 1 DAY, 'entregue'),
(1, 1, 3, CURDATE() - INTERVAL 2 DAY, 'entregue'),
(3, 2, 4, CURDATE() - INTERVAL 2 DAY, 'entregue'),
(4, 1, 5, CURDATE() - INTERVAL 2 DAY, 'entregue'),
(5, 2, 3, CURDATE() - INTERVAL 2 DAY, 'entregue'),
(2, 1, 4, CURDATE() - INTERVAL 2 DAY, 'entregue'),
(1, 2, 5, CURDATE() - INTERVAL 3 DAY, 'entregue'),
(3, 1, 3, CURDATE() - INTERVAL 3 DAY, 'entregue'),
(5, 1, 4, CURDATE() - INTERVAL 3 DAY, 'entregue'),
(4, 2, 5, CURDATE() - INTERVAL 3 DAY, 'entregue'),
(2, 1, 3, CURDATE() - INTERVAL 4 DAY, 'entregue'),
(1, 2, 4, CURDATE() - INTERVAL 4 DAY, 'entregue'),
(3, 1, 5, CURDATE() - INTERVAL 4 DAY, 'entregue'),
(4, 2, 3, CURDATE() - INTERVAL 5 DAY, 'entregue'),
(5, 1, 4, CURDATE() - INTERVAL 6 DAY, 'entregue'),
(2, 2, 5, CURDATE() - INTERVAL 6 DAY, 'entregue');

INSERT INTO frete_itens (frete_id, produto_id, quantidade)
SELECT id, (id % 5) + 1, 1 + (id % 3) FROM fretes;

INSERT INTO frete_itens (frete_id, produto_id, quantidade)
SELECT id, ((id + 2) % 5) + 1, 1 FROM fretes WHERE id % 2 = 0;
