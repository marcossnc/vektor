<?php // vektor/includes/relatorio.php
function dia_valido(?string $dia): string
{
    $data = $dia !== null ? DateTime::createFromFormat('Y-m-d', $dia) : false;
    if ($data && $data->format('Y-m-d') === $dia) {
        return $dia;
    }
    return date('Y-m-d');
}

function fretes_do_dia(PDO $pdo, string $dia): array
{
    $consulta = $pdo->prepare(
        'SELECT f.id, f.status, t.nome AS transportador,
                o.cidade AS origem_cidade, o.uf AS origem_uf,
                d.cidade AS destino_cidade, d.uf AS destino_uf,
                COALESCE(SUM(i.quantidade), 0) AS itens,
                COALESCE(SUM(i.quantidade * p.peso), 0) AS peso,
                COALESCE(SUM(i.quantidade * p.valor), 0) AS valor
         FROM fretes f
         JOIN transportadores t ON t.id = f.transportador_id
         JOIN enderecos o ON o.id = f.origem_id
         JOIN enderecos d ON d.id = f.destino_id
         LEFT JOIN frete_itens i ON i.frete_id = f.id
         LEFT JOIN produtos p ON p.id = i.produto_id
         WHERE f.data_frete = ?
         GROUP BY f.id, f.status, t.nome, o.cidade, o.uf, d.cidade, d.uf
         ORDER BY f.id'
    );
    $consulta->execute([$dia]);
    return $consulta->fetchAll();
}

function totais_do_dia(array $fretes): array
{
    return [
        'fretes' => count($fretes),
        'itens' => (int) array_sum(array_column($fretes, 'itens')),
        'peso' => (float) array_sum(array_column($fretes, 'peso')),
        'valor' => (float) array_sum(array_column($fretes, 'valor')),
    ];
}

function codigo_frete(int $id): string
{
    return '#' . str_pad((string) $id, 4, '0', STR_PAD_LEFT);
}
