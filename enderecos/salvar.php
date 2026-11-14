<?php // vektor/enderecos/salvar.php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'enderecos/');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$apelido = trim($_POST['apelido'] ?? '');
$tipo = $_POST['tipo'] ?? '';
$cep = trim($_POST['cep'] ?? '');
$logradouro = trim($_POST['logradouro'] ?? '');
$numero = trim($_POST['numero'] ?? '');
$complemento = trim($_POST['complemento'] ?? '');
$cidade = trim($_POST['cidade'] ?? '');
$uf = $_POST['uf'] ?? '';

$erros = [];
if ($apelido === '') {
    $erros['apelido'] = 'Informe um apelido para o endereço.';
} elseif (mb_strlen($apelido) > 80) {
    $erros['apelido'] = 'O apelido pode ter no máximo 80 caracteres.';
}
if (!in_array($tipo, ['origem', 'destino'], true)) {
    $erros['tipo'] = 'Escolha origem ou destino.';
}
if ($cep === '' || mb_strlen($cep) > 9) {
    $erros['cep'] = 'Informe o CEP.';
}
if ($logradouro === '' || mb_strlen($logradouro) > 150) {
    $erros['logradouro'] = 'Informe o logradouro.';
}
if ($numero === '' || mb_strlen($numero) > 10) {
    $erros['numero'] = 'Informe o número.';
}
if (mb_strlen($complemento) > 80) {
    $erros['complemento'] = 'O complemento pode ter no máximo 80 caracteres.';
}
if ($cidade === '' || mb_strlen($cidade) > 80) {
    $erros['cidade'] = 'Informe a cidade.';
}
if (!in_array($uf, lista_ufs(), true)) {
    $erros['uf'] = 'Escolha a UF.';
}

if ($erros) {
    $_SESSION['erros'] = $erros;
    $_SESSION['antigo'] = $_POST;
    $destino = BASE_URL . 'enderecos/' . ($id > 0 ? '?editar=' . $id : '') . '#formulario';
    header('Location: ' . $destino);
    exit;
}

$pdo = conectar();
$dados = [$apelido, $tipo, $cep, $logradouro, $numero, $complemento !== '' ? $complemento : null, $cidade, $uf];
if ($id > 0) {
    $comando = $pdo->prepare('UPDATE enderecos SET apelido = ?, tipo = ?, cep = ?, logradouro = ?, numero = ?, complemento = ?, cidade = ?, uf = ? WHERE id = ?');
    $comando->execute(array_merge($dados, [$id]));
    definir_aviso('Endereço atualizado com sucesso.');
} else {
    $comando = $pdo->prepare('INSERT INTO enderecos (apelido, tipo, cep, logradouro, numero, complemento, cidade, uf) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $comando->execute($dados);
    definir_aviso('Endereço cadastrado com sucesso.');
}

header('Location: ' . BASE_URL . 'enderecos/');
exit;
