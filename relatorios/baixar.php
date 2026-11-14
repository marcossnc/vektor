<?php // vektor/relatorios/baixar.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../includes/relatorio.php';

$dia = dia_valido($_GET['dia'] ?? null);
$fretes = fretes_do_dia(conectar(), $dia);
$totais = totais_do_dia($fretes);

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="vektor-fretes-' . $dia . '.csv"');

$saida = fopen('php://output', 'w');
fwrite($saida, "\xEF\xBB\xBF");
fputcsv($saida, ['Frete', 'Data', 'Transportador', 'Origem', 'Destino', 'Itens', 'Peso (kg)', 'Valor (R$)', 'Status'], ';');
foreach ($fretes as $frete) {
    fputcsv($saida, [
        codigo_frete((int) $frete['id']),
        data_br($dia),
        $frete['transportador'],
        $frete['origem_cidade'] . '/' . $frete['origem_uf'],
        $frete['destino_cidade'] . '/' . $frete['destino_uf'],
        (int) $frete['itens'],
        number_format((float) $frete['peso'], 2, ',', ''),
        number_format((float) $frete['valor'], 2, ',', ''),
        status_frete($frete['status'])[0],
    ], ';');
}
fputcsv($saida, ['Total', '', '', '', '', $totais['itens'], number_format($totais['peso'], 2, ',', ''), number_format($totais['valor'], 2, ',', ''), $totais['fretes'] . ' fretes'], ';');
fclose($saida);
