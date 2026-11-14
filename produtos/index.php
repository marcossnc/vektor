<?php // vektor/produtos/index.php
$titulo = 'Produtos';
$pagina = 'produtos';
require __DIR__ . '/../includes/cabecalho.php';

$pdo = conectar();
$produtos = $pdo->query('SELECT id, nome, peso, valor FROM produtos ORDER BY id')->fetchAll();

$edicao = null;
if (isset($_GET['editar'])) {
    $consulta = $pdo->prepare('SELECT * FROM produtos WHERE id = ?');
    $consulta->execute([(int) $_GET['editar']]);
    $edicao = $consulta->fetch() ?: null;
}

$antigo = $_SESSION['antigo'] ?? [];
$erros = $_SESSION['erros'] ?? [];
unset($_SESSION['antigo'], $_SESSION['erros']);
$aviso = obter_aviso();

$campo = [
    'id' => $antigo['id'] ?? ($edicao['id'] ?? ''),
    'nome' => $antigo['nome'] ?? ($edicao['nome'] ?? ''),
    'peso' => $antigo['peso'] ?? ($edicao ? number_format((float) $edicao['peso'], 2, ',', '') : ''),
    'valor' => $antigo['valor'] ?? ($edicao ? number_format((float) $edicao['valor'], 2, ',', '') : ''),
    'descricao' => $antigo['descricao'] ?? ($edicao['descricao'] ?? ''),
];
$editando = $campo['id'] !== '' && (int) $campo['id'] > 0;
?>
      <header class="d-flex justify-content-between align-items-center mb-4 animar">
        <div>
          <h1 class="titulo">Produtos</h1>
          <p class="subtitulo">Cadastre e edite os produtos transportados</p>
        </div>
        <span class="status">Sistema ativo</span>
      </header>
<?php if ($aviso): ?>
      <p class="mensagem" role="status"><?= e($aviso) ?></p>
<?php endif; ?>
      <div class="row g-4">
        <section class="col-12 col-lg-8 animar" style="--i:1">
          <div class="painel h-100">
            <h2 class="painel__titulo">Produtos cadastrados</h2>
            <div class="table-responsive">
            <table class="tabela">
              <thead>
                <tr><th>ID</th><th>Nome</th><th>Peso</th><th>Valor</th><th></th></tr>
              </thead>
              <tbody>
<?php foreach ($produtos as $produto): ?>
                <tr>
                  <td><?= (int) $produto['id'] ?></td>
                  <td><?= e($produto['nome']) ?></td>
                  <td><?= peso_kg((float) $produto['peso']) ?></td>
                  <td><?= moeda((float) $produto['valor']) ?></td>
                  <td><a class="botao botao--sm" href="?editar=<?= (int) $produto['id'] ?>#formulario">Editar</a></td>
                </tr>
<?php endforeach; ?>
<?php if (!$produtos): ?>
                <tr><td colspan="5">Nenhum produto cadastrado ainda.</td></tr>
<?php endif; ?>
              </tbody>
            </table>
            </div>
          </div>
        </section>
        <section class="col-12 col-lg-4 animar" style="--i:2" id="formulario">
          <form class="painel h-100" action="<?= BASE_URL ?>produtos/salvar.php" method="post">
            <h2 class="painel__titulo"><?= $editando ? 'Editar produto #' . (int) $campo['id'] : 'Novo produto' ?></h2>
            <input type="hidden" name="id" value="<?= e((string) $campo['id']) ?>">
            <div class="campo<?= isset($erros['nome']) ? ' invalido' : '' ?>">
              <label for="nome">Nome do produto *</label>
              <input class="form-control" id="nome" name="nome" type="text" maxlength="120" placeholder="Ex.: Notebook 15&quot;" value="<?= e($campo['nome']) ?>" required>
<?php if (isset($erros['nome'])): ?><p class="campo__erro"><?= e($erros['nome']) ?></p><?php endif; ?>
            </div>
            <div class="campo<?= isset($erros['peso']) ? ' invalido' : '' ?>">
              <label for="peso">Peso (kg) *</label>
              <input class="form-control" id="peso" name="peso" type="text" inputmode="decimal" placeholder="0,00" value="<?= e($campo['peso']) ?>" required>
<?php if (isset($erros['peso'])): ?><p class="campo__erro"><?= e($erros['peso']) ?></p><?php endif; ?>
            </div>
            <div class="campo<?= isset($erros['valor']) ? ' invalido' : '' ?>">
              <label for="valor">Valor (R$) *</label>
              <input class="form-control" id="valor" name="valor" type="text" inputmode="decimal" placeholder="0,00" value="<?= e($campo['valor']) ?>" required>
<?php if (isset($erros['valor'])): ?><p class="campo__erro"><?= e($erros['valor']) ?></p><?php endif; ?>
            </div>
            <div class="campo<?= isset($erros['descricao']) ? ' invalido' : '' ?>">
              <label for="descricao">Descrição</label>
              <input class="form-control" id="descricao" name="descricao" type="text" maxlength="255" placeholder="Opcional" value="<?= e($campo['descricao']) ?>">
<?php if (isset($erros['descricao'])): ?><p class="campo__erro"><?= e($erros['descricao']) ?></p><?php endif; ?>
            </div>
            <div class="acoes">
              <button class="botao botao--principal" type="submit">Salvar produto <span class="botao__seta" aria-hidden="true">→</span></button>
              <a class="botao" href="<?= BASE_URL ?>produtos/">Cancelar</a>
            </div>
          </form>
        </section>
      </div>
<?php require __DIR__ . '/../includes/rodape.php'; ?>
