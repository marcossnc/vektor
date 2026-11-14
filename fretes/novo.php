<?php // vektor/fretes/novo.php
$titulo = 'Novo frete';
$pagina = 'novo-frete';
$scripts = ['assets/js/novo-frete.js'];
require __DIR__ . '/../includes/cabecalho.php';

$pdo = conectar();
$transportadores = $pdo->query('SELECT id, nome, capacidade_kg FROM transportadores ORDER BY nome')->fetchAll();
$origens = $pdo->query("SELECT id, apelido, cidade, uf FROM enderecos WHERE tipo = 'origem' ORDER BY apelido")->fetchAll();
$destinos = $pdo->query("SELECT id, apelido, cidade, uf FROM enderecos WHERE tipo = 'destino' ORDER BY apelido")->fetchAll();
$produtos = $pdo->query('SELECT id, nome, peso, valor FROM produtos ORDER BY nome')->fetchAll();

$antigo = $_SESSION['antigo'] ?? [];
$erros = $_SESSION['erros'] ?? [];
unset($_SESSION['antigo'], $_SESSION['erros']);

$gerado = (int) ($_GET['gerado'] ?? 0);
if ($gerado > 0) {
    $consulta = $pdo->prepare('SELECT COUNT(*) FROM fretes WHERE id = ?');
    $consulta->execute([$gerado]);
    $gerado = (int) $consulta->fetchColumn() > 0 ? $gerado : 0;
}

$valor = [
    'transportador' => (int) ($antigo['transportador'] ?? 0),
    'origem' => (int) ($antigo['origem'] ?? 0),
    'destino' => (int) ($antigo['destino'] ?? 0),
    'data' => $antigo['data'] ?? date('Y-m-d'),
];

$linhas = [];
if (!empty($antigo['produto']) && is_array($antigo['produto'])) {
    foreach ($antigo['produto'] as $posicao => $produto_id) {
        $linhas[] = [(int) $produto_id, max(1, (int) ($antigo['quantidade'][$posicao] ?? 1))];
    }
}
if (!$linhas && $produtos) {
    $linhas[] = [(int) $produtos[0]['id'], 1];
}

function linha_produto(array $produtos, int $selecionado, int $quantidade): string
{
    $html = '<div class="linha-produto"><select class="form-select" name="produto[]" aria-label="Produto">';
    foreach ($produtos as $produto) {
        $html .= '<option value="' . (int) $produto['id'] . '" data-peso="' . e((string) $produto['peso']) . '" data-valor="' . e((string) $produto['valor']) . '"'
            . ((int) $produto['id'] === $selecionado ? ' selected' : '') . '>' . e($produto['nome']) . '</option>';
    }
    $html .= '</select><input class="form-control" name="quantidade[]" type="number" min="1" max="10000" value="' . $quantidade . '" aria-label="Quantidade">'
        . '<button class="botao botao--icone" type="button" aria-label="Remover produto">×</button></div>';
    return $html;
}
?>
      <header class="d-flex justify-content-between align-items-center mb-4 animar">
        <div>
          <h1 class="titulo">Novo frete</h1>
          <p class="subtitulo">Ligue transportador, origem, destino e produtos</p>
        </div>
        <span class="status">Sistema ativo</span>
      </header>
      <form action="<?= BASE_URL ?>fretes/salvar.php" method="post" id="form-frete">
        <div class="row g-4">
          <div class="col-12 col-lg-8">
            <section class="painel mb-4 animar" style="--i:1">
              <h2 class="painel__titulo">Dados do frete</h2>
              <div class="row g-3">
                <div class="col-12 col-md-6 campo<?= isset($erros['transportador']) ? ' invalido' : '' ?>">
                  <label for="transportador">Transportador *</label>
                  <select class="form-select" id="transportador" name="transportador">
<?php foreach ($transportadores as $item): ?>
                    <option value="<?= (int) $item['id'] ?>" data-capacidade="<?= (int) $item['capacidade_kg'] ?>"<?= $valor['transportador'] === (int) $item['id'] ? ' selected' : '' ?>><?= e($item['nome']) ?></option>
<?php endforeach; ?>
                  </select>
<?php if (isset($erros['transportador'])): ?><p class="campo__erro"><?= e($erros['transportador']) ?></p><?php endif; ?>
                </div>
                <div class="col-12 col-md-6 campo<?= isset($erros['data']) ? ' invalido' : '' ?>">
                  <label for="data">Data *</label>
                  <input class="form-control" id="data" name="data" type="date" value="<?= e($valor['data']) ?>" required>
<?php if (isset($erros['data'])): ?><p class="campo__erro"><?= e($erros['data']) ?></p><?php endif; ?>
                </div>
                <div class="col-12 col-md-6 campo<?= isset($erros['origem']) ? ' invalido' : '' ?>">
                  <label for="origem">Origem *</label>
                  <select class="form-select" id="origem" name="origem">
<?php foreach ($origens as $item): ?>
                    <option value="<?= (int) $item['id'] ?>" data-cidade="<?= e($item['cidade'] . '/' . $item['uf']) ?>"<?= $valor['origem'] === (int) $item['id'] ? ' selected' : '' ?>><?= e($item['apelido']) ?> - <?= e($item['cidade']) ?>/<?= e($item['uf']) ?></option>
<?php endforeach; ?>
                  </select>
<?php if (isset($erros['origem'])): ?><p class="campo__erro"><?= e($erros['origem']) ?></p><?php endif; ?>
                </div>
                <div class="col-12 col-md-6 campo<?= isset($erros['destino']) ? ' invalido' : '' ?>">
                  <label for="destino">Destino *</label>
                  <select class="form-select" id="destino" name="destino">
<?php foreach ($destinos as $item): ?>
                    <option value="<?= (int) $item['id'] ?>" data-cidade="<?= e($item['cidade'] . '/' . $item['uf']) ?>"<?= $valor['destino'] === (int) $item['id'] ? ' selected' : '' ?>><?= e($item['apelido']) ?> - <?= e($item['cidade']) ?>/<?= e($item['uf']) ?></option>
<?php endforeach; ?>
                  </select>
<?php if (isset($erros['destino'])): ?><p class="campo__erro"><?= e($erros['destino']) ?></p><?php endif; ?>
                </div>
              </div>
            </section>
            <section class="painel animar" style="--i:2">
              <h2 class="painel__titulo">Produtos do frete</h2>
              <div id="linhas">
<?php foreach ($linhas as [$produto_id, $quantidade]): ?>
                <?= linha_produto($produtos, $produto_id, $quantidade) ?>

<?php endforeach; ?>
              </div>
<?php if (isset($erros['itens'])): ?>
              <p class="campo__erro mb-3"><?= e($erros['itens']) ?></p>
<?php endif; ?>
              <button class="botao" type="button" id="adicionar">+ Adicionar produto</button>
            </section>
          </div>
          <div class="col-12 col-lg-4 animar" style="--i:3">
            <aside class="painel resumo-frete" aria-live="polite">
              <h2 class="painel__titulo">Resumo</h2>
              <div class="carimbo<?= $gerado > 0 ? ' visivel' : '' ?>" id="carimbo">Frete #<?= str_pad((string) $gerado, 4, '0', STR_PAD_LEFT) ?> gerado</div>
              <dl>
                <dt>Transportador</dt><dd id="r-transportador"></dd>
                <dt>Rota</dt><dd id="r-rota"></dd>
                <dt>Itens</dt><dd id="r-itens">0</dd>
                <dt>Peso total</dt><dd id="r-peso">0,0 kg</dd>
                <dt>Valor total</dt><dd class="resumo-frete__total" id="r-valor">R$ 0,00</dd>
              </dl>
              <p class="campo__erro mb-3" id="r-alerta" hidden></p>
              <button class="botao botao--principal w-100" type="submit">Gerar frete <span class="botao__seta" aria-hidden="true">→</span></button>
            </aside>
          </div>
        </div>
        <template id="modelo-linha"><?= linha_produto($produtos, $produtos ? (int) $produtos[0]['id'] : 0, 1) ?></template>
      </form>
      <div class="aviso" id="aviso" hidden><span id="aviso-texto"></span><button class="botao botao--sm" type="button" id="desfazer">Desfazer</button></div>
<?php require __DIR__ . '/../includes/rodape.php'; ?>
