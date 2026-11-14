<?php // vektor/relatorios/index.php
$titulo = 'Relatório';
$pagina = 'relatorio';
require __DIR__ . '/../includes/cabecalho.php';
require_once __DIR__ . '/../includes/relatorio.php';

$dia = dia_valido($_GET['dia'] ?? null);
$fretes = fretes_do_dia(conectar(), $dia);
$totais = totais_do_dia($fretes);
$consulta_dia = '?dia=' . urlencode($dia);
?>
      <header class="d-flex justify-content-between align-items-center mb-4 animar">
        <div>
          <h1 class="titulo">Relatório</h1>
          <p class="subtitulo">Escolha um dia e baixe os fretes</p>
        </div>
        <span class="status">Sistema ativo</span>
      </header>
      <form class="painel mb-4 animar" style="--i:1" action="<?= BASE_URL ?>relatorios/" method="get">
        <div class="d-flex flex-wrap align-items-end gap-3">
          <div class="campo mb-0">
            <label for="dia">Dia do relatório</label>
            <input class="form-control" id="dia" name="dia" type="date" value="<?= e($dia) ?>" required>
          </div>
          <button class="botao" type="submit">Gerar relatório</button>
<?php if ($fretes): ?>
          <div class="ms-lg-auto d-flex flex-wrap gap-3">
            <a class="botao" href="<?= BASE_URL ?>relatorios/baixar.php<?= $consulta_dia ?>">Baixar CSV</a>
            <a class="botao botao--principal" href="<?= BASE_URL ?>relatorios/imprimir.php<?= $consulta_dia ?>" target="_blank" rel="noopener">Gerar PDF <span class="botao__seta" aria-hidden="true">↓</span></a>
          </div>
<?php endif; ?>
        </div>
      </form>
      <section class="row g-4 mb-4" aria-label="Totais do dia">
        <div class="col-12 col-md-4 animar" style="--i:2"><div class="painel h-100"><div class="cartao-resumo__rotulo">Fretes do dia</div><div class="cartao-resumo__numero" data-alvo="<?= $totais['fretes'] ?>"><?= $totais['fretes'] ?></div></div></div>
        <div class="col-12 col-md-4 animar" style="--i:3"><div class="painel h-100"><div class="cartao-resumo__rotulo">Itens transportados</div><div class="cartao-resumo__numero" data-alvo="<?= $totais['itens'] ?>"><?= $totais['itens'] ?></div></div></div>
        <div class="col-12 col-md-4 animar" style="--i:4"><div class="painel h-100"><div class="cartao-resumo__rotulo">Valor total</div><div class="cartao-resumo__numero resumo-frete__total"><?= moeda($totais['valor']) ?></div></div></div>
      </section>
      <section class="painel animar" style="--i:5">
        <h2 class="painel__titulo">Fretes de <?= data_br($dia) ?></h2>
        <div class="table-responsive">
            <table class="tabela">
          <thead>
            <tr><th>Frete</th><th>Transportador</th><th>Rota</th><th>Itens</th><th>Peso</th><th>Valor</th></tr>
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
            </tr>
<?php endforeach; ?>
<?php if (!$fretes): ?>
            <tr><td colspan="6">Nenhum frete neste dia.</td></tr>
<?php endif; ?>
          </tbody>
        </table>
            </div>
      </section>
<?php require __DIR__ . '/../includes/rodape.php'; ?>
