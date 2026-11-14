<?php // vektor/produtos/salvar.php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'produtos/');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$nome = trim($_POST['nome'] ?? '');
$peso = numero_para_float($_POST['peso'] ?? '');
$valor = numero_para_float($_POST['valor'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');

$erros = [];
if ($nome === '') {
    $erros['nome'] = 'Informe o nome do produto.';
} elseif (mb_strlen($nome) > 120) {
    $erros['nome'] = 'O nome pode ter no máximo 120 caracteres.';
}
if ($peso === null || $peso <= 0) {
    $erros['peso'] = 'Informe um peso maior que zero.';
}
if ($valor === null || $valor < 0) {
    $erros['valor'] = 'Informe um valor válido.';
}
if (mb_strlen($descricao) > 255) {
    $erros['descricao'] = 'A descrição pode ter no máximo 255 caracteres.';
}

if ($erros) {
    $_SESSION['erros'] = $erros;
    $_SESSION['antigo'] = $_POST;
    $destino = BASE_URL . 'produtos/' . ($id > 0 ? '?editar=' . $id : '') . '#formulario';
    header('Location: ' . $destino);
    exit;
}

$pdo = conectar();
if ($id > 0) {
    $comando = $pdo->prepare('UPDATE produtos SET nome = ?, peso = ?, valor = ?, descricao = ? WHERE id = ?');
    $comando->execute([$nome, $peso, $valor, $descricao !== '' ? $descricao : null, $id]);
    definir_aviso('Produto atualizado com sucesso.');
} else {
    $comando = $pdo->prepare('INSERT INTO produtos (nome, peso, valor, descricao) VALUES (?, ?, ?, ?)');
    $comando->execute([$nome, $peso, $valor, $descricao !== '' ? $descricao : null]);
    definir_aviso('Produto cadastrado com sucesso.');
}

header('Location: ' . BASE_URL . 'produtos/');
exit;
