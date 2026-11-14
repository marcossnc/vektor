<?php // vektor/fretes/salvar.php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'fretes/novo.php');
    exit;
}

$pdo = conectar();
$transportador_id = (int) ($_POST['transportador'] ?? 0);
$origem_id = (int) ($_POST['origem'] ?? 0);
$destino_id = (int) ($_POST['destino'] ?? 0);
$data = $_POST['data'] ?? '';
$produtos = $_POST['produto'] ?? [];
$quantidades = $_POST['quantidade'] ?? [];
$erros = [];

$consulta = $pdo->prepare('SELECT id, capacidade_kg FROM transportadores WHERE id = ?');
$consulta->execute([$transportador_id]);
$transportador = $consulta->fetch();
if (!$transportador) {
    $erros['transportador'] = 'Escolha um transportador.';
}

$consulta = $pdo->prepare('SELECT id FROM enderecos WHERE id = ? AND tipo = ?');
$consulta->execute([$origem_id, 'origem']);
if (!$consulta->fetch()) {
    $erros['origem'] = 'Escolha um endereço de origem.';
}
$consulta->execute([$destino_id, 'destino']);
if (!$consulta->fetch()) {
    $erros['destino'] = 'Escolha um endereço de destino.';
}

$data_valida = DateTime::createFromFormat('Y-m-d', $data);
if (!$data_valida || $data_valida->format('Y-m-d') !== $data) {
    $erros['data'] = 'Informe uma data válida.';
}

$itens = [];
if (is_array($produtos) && is_array($quantidades)) {
    foreach ($produtos as $posicao => $produto_id) {
        $produto_id = (int) $produto_id;
        $quantidade = (int) ($quantidades[$posicao] ?? 0);
        if ($produto_id > 0 && $quantidade > 0 && $quantidade <= 10000) {
            $itens[$produto_id] = ($itens[$produto_id] ?? 0) + $quantidade;
        }
    }
}

if (!$itens) {
    $erros['itens'] = 'Adicione pelo menos um produto com quantidade maior que zero.';
} else {
    $marcadores = implode(',', array_fill(0, count($itens), '?'));
    $consulta = $pdo->prepare("SELECT id, peso FROM produtos WHERE id IN ($marcadores)");
    $consulta->execute(array_keys($itens));
    $pesos = $consulta->fetchAll(PDO::FETCH_KEY_PAIR);
    if (count($pesos) !== count($itens)) {
        $erros['itens'] = 'Um dos produtos escolhidos não existe mais.';
    } elseif ($transportador) {
        $peso_total = 0.0;
        foreach ($itens as $produto_id => $quantidade) {
            $peso_total += (float) $pesos[$produto_id] * $quantidade;
        }
        if ($peso_total > (int) $transportador['capacidade_kg']) {
            $erros['itens'] = 'O peso total (' . peso_kg($peso_total) . ') passa da capacidade do veículo (' . capacidade_formatada((int) $transportador['capacidade_kg']) . ').';
        }
    }
}

if ($erros) {
    $_SESSION['erros'] = $erros;
    $_SESSION['antigo'] = $_POST;
    header('Location: ' . BASE_URL . 'fretes/novo.php');
    exit;
}

$pdo->beginTransaction();
try {
    $comando = $pdo->prepare('INSERT INTO fretes (transportador_id, origem_id, destino_id, data_frete, status) VALUES (?, ?, ?, ?, ?)');
    $comando->execute([$transportador_id, $origem_id, $destino_id, $data, 'aguardando']);
    $frete_id = (int) $pdo->lastInsertId();
    $comando = $pdo->prepare('INSERT INTO frete_itens (frete_id, produto_id, quantidade) VALUES (?, ?, ?)');
    foreach ($itens as $produto_id => $quantidade) {
        $comando->execute([$frete_id, $produto_id, $quantidade]);
    }
    $pdo->commit();
} catch (Exception $erro) {
    $pdo->rollBack();
    $_SESSION['erros'] = ['itens' => 'Não foi possível gravar o frete. Tente novamente.'];
    $_SESSION['antigo'] = $_POST;
    header('Location: ' . BASE_URL . 'fretes/novo.php');
    exit;
}

header('Location: ' . BASE_URL . 'fretes/novo.php?gerado=' . $frete_id);
exit;
