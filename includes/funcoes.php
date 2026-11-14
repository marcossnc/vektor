<?php // vektor/includes/funcoes.php
function e(?string $texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

function moeda(float $valor): string
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function peso_kg(float $valor): string
{
    return number_format($valor, 1, ',', '.') . ' kg';
}

function data_br(string $data): string
{
    return date('d/m/Y', strtotime($data));
}

function dia_semana(string $data): string
{
    $nomes = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
    return $nomes[(int) date('w', strtotime($data))];
}

function status_frete(string $status): array
{
    $mapa = [
        'aguardando' => ['Aguardando', 'chip chip--aguardando'],
        'em_transito' => ['Em trânsito', 'chip chip--transito'],
        'entregue' => ['Entregue', 'chip'],
    ];
    return $mapa[$status] ?? [$status, 'chip'];
}
