<?php // vektor/index.php
$titulo = 'Início';
$pagina = 'inicio';
require __DIR__ . '/includes/cabecalho.php';

$pdo = conectar();
$hoje = date('Y-m-d');

$fretes_hoje = (int) $pdo->query('SELECT COUNT(*) FROM fretes WHERE data_frete = CURDATE()')->fetchColumn();
$total_produtos = (int) $pdo->query('SELECT COUNT(*) FROM produtos')->fetchColumn();
$total_transportadores = (int) $pdo->query('SELECT COUNT(*) FROM transportadores')->fetchColumn();
$ativos_hoje = (int) $pdo->query('SELECT COUNT(DISTINCT transportador_id) FROM fretes WHERE data_frete = CURDATE()')->fetchColumn();
$percentual_meta = min(100, (int) round($fretes_hoje / META_DIARIA * 100));

$ultimos = $pdo->query(
    'SELECT f.id, f.status, t.nome AS transportador,
            o.cidade AS origem_cidade, o.uf AS origem_uf,
            d.cidade AS destino_cidade, d.uf AS destino_uf,
            (SELECT COALESCE(SUM(i.quantidade), 0) FROM frete_itens i WHERE i.frete_id = f.id) AS itens
     FROM fretes f
     JOIN transportadores t ON t.id = f.transportador_id
     JOIN enderecos o ON o.id = f.origem_id
     JOIN enderecos d ON d.id = f.destino_id
     ORDER BY f.data_frete DESC, f.id DESC
     LIMIT 4'
)->fetchAll();

$consulta = $pdo->query(
    'SELECT data_frete, COUNT(*) AS total FROM fretes
     WHERE data_frete >= CURDATE() - INTERVAL 6 DAY GROUP BY data_frete'
);
$por_dia = $consulta->fetchAll(PDO::FETCH_KEY_PAIR);
$dias = [];
for ($i = 6; $i >= 0; $i--) {
    $data = date('Y-m-d', strtotime("-$i day"));
    $dias[] = ['data' => $data, 'total' => (int) ($por_dia[$data] ?? 0)];
}
$maximo = max(1, max(array_column($dias, 'total')));
$soma_semana = array_sum(array_column($dias, 'total'));
$descricao_grafico = implode(', ', array_map(fn($d) => dia_semana($d['data']) . ' ' . $d['total'], $dias));
?>
      <header class="d-flex justify-content-between align-items-center mb-4 animar">
        <div>
          <h1 class="titulo">Início</h1>
          <p class="subtitulo">Resumo dos fretes de hoje, <?= data_br($hoje) ?></p>
        </div>
        <div class="cabecalho__acoes">
          <span class="status">Sistema ativo</span>
          <a class="botao botao--principal" href="<?= BASE_URL ?>fretes/novo.php">Novo frete <span class="botao__seta" aria-hidden="true">→</span></a>
        </div>
      </header>
      <section class="row g-4 mb-4" aria-label="Resumo">
        <div class="col-12 col-md-4 animar" style="--i:1">
          <div class="painel h-100">
            <div class="resumo">
              <div>
                <div class="cartao-resumo__rotulo">Fretes hoje</div>
                <div class="cartao-resumo__numero" data-alvo="<?= $fretes_hoje ?>"><?= $fretes_hoje ?></div>
              </div>
              <div class="resumo__icone"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h11v10H3z"/><path d="M14 9h4l3 3v4h-7"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/></svg></div>
            </div>
            <div class="barra" role="img" aria-label="<?= $fretes_hoje ?> de <?= META_DIARIA ?> fretes da meta"><span style="--p:<?= $percentual_meta ?>%"></span></div>
            <p class="nota">Meta do dia: <?= META_DIARIA ?> fretes</p>
          </div>
        </div>
        <div class="col-12 col-md-4 animar" style="--i:2">
          <div class="painel h-100">
            <div class="resumo">
              <div>
                <div class="cartao-resumo__rotulo">Produtos cadastrados</div>
                <div class="cartao-resumo__numero" data-alvo="<?= $total_produtos ?>"><?= $total_produtos ?></div>
              </div>
              <div class="resumo__icone"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 8l-9-5-9 5v8l9 5 9-5z"/><path d="M3 8l9 5 9-5M12 13v8"/></svg></div>
            </div>
            <p class="nota"><a href="<?= BASE_URL ?>produtos/">Ver produtos</a></p>
          </div>
        </div>
        <div class="col-12 col-md-4 animar" style="--i:3">
          <div class="painel h-100">
            <div class="resumo">
              <div>
                <div class="cartao-resumo__rotulo">Transportadores</div>
                <div class="cartao-resumo__numero" data-alvo="<?= $total_transportadores ?>"><?= $total_transportadores ?></div>
              </div>
              <div class="resumo__icone"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7"/></svg></div>
            </div>
            <p class="nota"><?= $ativos_hoje ?> ativos hoje</p>
          </div>
        </div>
      </section>
      <div class="row g-4">
        <section class="col-12 col-lg-8 animar" style="--i:4">
          <div class="painel h-100">
            <h2 class="painel__titulo">Últimos fretes</h2>
            <div class="table-responsive">
            <table class="tabela">
              <thead>
                <tr><th>Frete</th><th>Transportador</th><th>Rota</th><th>Itens</th><th>Status</th></tr>
              </thead>
              <tbody>
<?php foreach ($ultimos as $frete): [$rotulo_status, $classe_status] = status_frete($frete['status']); ?>
                <tr>
                  <td>#<?= str_pad((string) $frete['id'], 4, '0', STR_PAD_LEFT) ?></td>
                  <td><?= e($frete['transportador']) ?></td>
                  <td><?= e($frete['origem_cidade']) ?>/<?= e($frete['origem_uf']) ?> → <?= e($frete['destino_cidade']) ?>/<?= e($frete['destino_uf']) ?></td>
                  <td><?= (int) $frete['itens'] ?></td>
                  <td><span class="<?= $classe_status ?>"><?= e($rotulo_status) ?></span></td>
                </tr>
<?php endforeach; ?>
              </tbody>
            </table>
            </div>
          </div>
        </section>
        <section class="col-12 col-lg-4 animar" style="--i:5">
          <div class="painel h-100">
            <h2 class="painel__titulo">Fretes por dia</h2>
            <svg class="grafico" viewBox="0 0 300 190" role="img" aria-label="Fretes dos últimos 7 dias: <?= e($descricao_grafico) ?>">
<?php foreach ($dias as $indice => $dia):
    $altura = $dia['total'] > 0 ? max(4, (int) round($dia['total'] / $maximo * 140)) : 3;
    $x = 8 + $indice * 42; ?>
              <rect class="col<?= $indice === 6 ? ' col--hoje' : '' ?>" style="--i:<?= $indice ?>" x="<?= $x ?>" y="<?= 150 - $altura ?>" width="28" height="<?= $altura ?>" rx="4"/>
              <text x="<?= $x + 14 ?>" y="172" text-anchor="middle"><?= dia_semana($dia['data']) ?></text>
<?php endforeach; ?>
            </svg>
            <p class="nota">Média dos 7 dias: <?= number_format($soma_semana / 7, 1, ',', '.') ?> fretes por dia</p>
          </div>
        </section>
      </div>
<?php require __DIR__ . '/includes/rodape.php'; ?>
