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

function numero_para_float(string $texto): ?float
{
    $texto = trim($texto);
    if (strpos($texto, ',') === false && preg_match('/^\d+\.\d{1,2}$/', $texto)) {
        return (float) $texto;
    }
    $limpo = str_replace(',', '.', str_replace(['.', ' '], '', $texto));
    return is_numeric($limpo) ? (float) $limpo : null;
}

function definir_aviso(string $texto): void
{
    $_SESSION['aviso'] = $texto;
}

function obter_aviso(): ?string
{
    $aviso = $_SESSION['aviso'] ?? null;
    unset($_SESSION['aviso']);
    return $aviso;
}

function lista_ufs(): array
{
    return ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
}

function lista_veiculos(): array
{
    return ['Caminhão baú', 'Carreta', 'Caminhão toco', 'Van', 'Utilitário'];
}

function capacidade_formatada(int $kg): string
{
    if ($kg >= 1000) {
        return rtrim(rtrim(number_format($kg / 1000, 1, ',', '.'), '0'), ',') . ' t';
    }
    return $kg . ' kg';
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
