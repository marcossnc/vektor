<?php // vektor/transportadores/salvar.php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'transportadores/');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$nome = trim($_POST['nome'] ?? '');
$cnpj = trim($_POST['cnpj'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$veiculo = $_POST['veiculo'] ?? '';
$capacidade = trim($_POST['capacidade'] ?? '');
$placa = trim($_POST['placa'] ?? '');

$erros = [];
if ($nome === '') {
    $erros['nome'] = 'Informe o nome da empresa.';
} elseif (mb_strlen($nome) > 120) {
    $erros['nome'] = 'O nome pode ter no máximo 120 caracteres.';
}
if ($cnpj === '' || mb_strlen($cnpj) > 18) {
    $erros['cnpj'] = 'Informe o CNPJ.';
}
if (mb_strlen($telefone) > 20) {
    $erros['telefone'] = 'O telefone pode ter no máximo 20 caracteres.';
}
if (!in_array($veiculo, lista_veiculos(), true)) {
    $erros['veiculo'] = 'Escolha o tipo de veículo.';
}
if (!ctype_digit($capacidade) || (int) $capacidade <= 0) {
    $erros['capacidade'] = 'Informe a capacidade em kg, só números.';
}
if (mb_strlen($placa) > 8) {
    $erros['placa'] = 'A placa pode ter no máximo 8 caracteres.';
}

if ($erros) {
    $_SESSION['erros'] = $erros;
    $_SESSION['antigo'] = $_POST;
    $destino = BASE_URL . 'transportadores/' . ($id > 0 ? '?editar=' . $id : '') . '#formulario';
    header('Location: ' . $destino);
    exit;
}

$pdo = conectar();
$dados = [$nome, $cnpj, $telefone !== '' ? $telefone : null, $veiculo, (int) $capacidade, $placa !== '' ? $placa : null];
if ($id > 0) {
    $comando = $pdo->prepare('UPDATE transportadores SET nome = ?, cnpj = ?, telefone = ?, veiculo = ?, capacidade_kg = ?, placa = ? WHERE id = ?');
    $comando->execute(array_merge($dados, [$id]));
    definir_aviso('Transportador atualizado com sucesso.');
} else {
    $comando = $pdo->prepare('INSERT INTO transportadores (nome, cnpj, telefone, veiculo, capacidade_kg, placa) VALUES (?, ?, ?, ?, ?, ?)');
    $comando->execute($dados);
    definir_aviso('Transportador cadastrado com sucesso.');
}

header('Location: ' . BASE_URL . 'transportadores/');
exit;
