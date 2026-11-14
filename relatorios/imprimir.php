<?php // vektor/relatorios/imprimir.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../includes/relatorio.php';

$dia = dia_valido($_GET['dia'] ?? null);
$fretes = fretes_do_dia(conectar(), $dia);
$totais = totais_do_dia($fretes);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vektor - Fretes de <?= data_br($dia) ?></title>
  <style>
    body { margin: 32px; font-family: Arial, sans-serif; font-size: 14px; color: #080808; }
    h1 { margin: 0 0 4px; font-size: 22px; }
    p { margin: 0 0 16px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 8px 10px; border-bottom: 1px solid #cdcbca; text-align: left; }
    th { background: #080808; color: #ffffff; }
    tfoot td { font-weight: bold; border-top: 2px solid #080808; }
    @page { margin: 16mm; }
  </style>
</head>
<body>
  <h1>Vektor - Fretes de <?= data_br($dia) ?></h1>
  <p><?= $totais['fretes'] ?> fretes, <?= $totais['itens'] ?> itens, <?= peso_kg($totais['peso']) ?> e <?= moeda($totais['valor']) ?> em mercadorias.</p>
  <table>
    <thead>
      <tr><th>Frete</th><th>Transportador</th><th>Rota</th><th>Itens</th><th>Peso</th><th>Valor</th><th>Status</th></tr>
    </thead>
    <tbody>
<?php foreach ($fretes as $frete): ?>
      <tr>
        <td><?= codigo_frete((int) $frete['id']) ?></td>
        <td><?= e($frete['transportador']) ?></td>
        <td><?= e($frete['origem_cidade']) ?>/<?= e($frete['origem_uf']) ?> → <?= e($frete['destino_cidade']) ?>/<?= e($frete['destino_uf']) ?></td>
        <td><?= (int) $frete['itens'] ?></td>
        <td><?= peso_kg((float) $frete['peso']) ?></td>
        <td><?= moeda((float) $frete['valor']) ?></td>
        <td><?= e(status_frete($frete['status'])[0]) ?></td>
      </tr>
<?php endforeach; ?>
<?php if (!$fretes): ?>
      <tr><td colspan="7">Nenhum frete neste dia.</td></tr>
<?php endif; ?>
    </tbody>
    <tfoot>
      <tr><td colspan="3">Total</td><td><?= $totais['itens'] ?></td><td><?= peso_kg($totais['peso']) ?></td><td><?= moeda($totais['valor']) ?></td><td></td></tr>
    </tfoot>
  </table>
  <script>window.print();</script>
</body>
</html>
