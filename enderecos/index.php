<?php // vektor/enderecos/index.php
$titulo = 'Endereços';
$pagina = 'enderecos';
require __DIR__ . '/../includes/cabecalho.php';

$pdo = conectar();
$enderecos = $pdo->query('SELECT id, apelido, tipo, logradouro, numero, cidade, uf FROM enderecos ORDER BY id')->fetchAll();

$edicao = null;
if (isset($_GET['editar'])) {
    $consulta = $pdo->prepare('SELECT * FROM enderecos WHERE id = ?');
    $consulta->execute([(int) $_GET['editar']]);
    $edicao = $consulta->fetch() ?: null;
}

$antigo = $_SESSION['antigo'] ?? [];
$erros = $_SESSION['erros'] ?? [];
unset($_SESSION['antigo'], $_SESSION['erros']);
$aviso = obter_aviso();

$campo = [];
foreach (['id', 'apelido', 'tipo', 'cep', 'logradouro', 'numero', 'complemento', 'cidade', 'uf'] as $nome) {
    $campo[$nome] = $antigo[$nome] ?? ($edicao[$nome] ?? '');
}
if ($campo['uf'] === '') {
    $campo['uf'] = 'PR';
}
$editando = $campo['id'] !== '' && (int) $campo['id'] > 0;
?>
      <header class="d-flex justify-content-between align-items-center mb-4 animar">
        <div>
          <h1 class="titulo">Endereços</h1>
          <p class="subtitulo">Locais de origem e destino dos fretes</p>
        </div>
        <span class="status">Sistema ativo</span>
      </header>
<?php if ($aviso): ?>
      <p class="mensagem" role="status"><?= e($aviso) ?></p>
<?php endif; ?>
      <div class="row g-4">
        <section class="col-12 col-lg-7 animar" style="--i:1">
          <div class="painel h-100">
            <h2 class="painel__titulo">Endereços cadastrados</h2>
            <table class="tabela">
              <thead>
                <tr><th>ID</th><th>Apelido</th><th>Tipo</th><th>Endereço</th><th></th></tr>
              </thead>
              <tbody>
<?php foreach ($enderecos as $endereco): ?>
                <tr>
                  <td><?= (int) $endereco['id'] ?></td>
                  <td><?= e($endereco['apelido']) ?></td>
                  <td><span class="chip<?= $endereco['tipo'] === 'origem' ? ' chip--origem' : '' ?>"><?= $endereco['tipo'] === 'origem' ? 'Origem' : 'Destino' ?></span></td>
                  <td><?= e($endereco['logradouro']) ?>, <?= e($endereco['numero']) ?><br><?= e($endereco['cidade']) ?>/<?= e($endereco['uf']) ?></td>
                  <td><a class="botao botao--sm" href="?editar=<?= (int) $endereco['id'] ?>#formulario">Editar</a></td>
                </tr>
<?php endforeach; ?>
<?php if (!$enderecos): ?>
                <tr><td colspan="5">Nenhum endereço cadastrado ainda.</td></tr>
<?php endif; ?>
              </tbody>
            </table>
          </div>
        </section>
        <section class="col-12 col-lg-5 animar" style="--i:2" id="formulario">
          <form class="painel h-100" action="<?= BASE_URL ?>enderecos/salvar.php" method="post">
            <h2 class="painel__titulo"><?= $editando ? 'Editar endereço #' . (int) $campo['id'] : 'Novo endereço' ?></h2>
            <input type="hidden" name="id" value="<?= e((string) $campo['id']) ?>">
            <div class="row g-3">
              <div class="col-7 campo<?= isset($erros['apelido']) ? ' invalido' : '' ?>">
                <label for="apelido">Apelido *</label>
                <input class="form-control" id="apelido" name="apelido" type="text" maxlength="80" placeholder="Ex.: Depósito Cascavel" value="<?= e($campo['apelido']) ?>" required>
<?php if (isset($erros['apelido'])): ?><p class="campo__erro"><?= e($erros['apelido']) ?></p><?php endif; ?>
              </div>
              <div class="col-5 campo<?= isset($erros['tipo']) ? ' invalido' : '' ?>">
                <label for="tipo">Tipo *</label>
                <select class="form-select" id="tipo" name="tipo">
                  <option value="origem"<?= $campo['tipo'] === 'origem' ? ' selected' : '' ?>>Origem</option>
                  <option value="destino"<?= $campo['tipo'] === 'destino' ? ' selected' : '' ?>>Destino</option>
                </select>
<?php if (isset($erros['tipo'])): ?><p class="campo__erro"><?= e($erros['tipo']) ?></p><?php endif; ?>
              </div>
              <div class="col-5 campo<?= isset($erros['cep']) ? ' invalido' : '' ?>">
                <label for="cep">CEP *</label>
                <input class="form-control" id="cep" name="cep" type="text" inputmode="numeric" maxlength="9" placeholder="00000-000" value="<?= e($campo['cep']) ?>" required>
<?php if (isset($erros['cep'])): ?><p class="campo__erro"><?= e($erros['cep']) ?></p><?php endif; ?>
              </div>
              <div class="col-7 campo<?= isset($erros['logradouro']) ? ' invalido' : '' ?>">
                <label for="logradouro">Logradouro *</label>
                <input class="form-control" id="logradouro" name="logradouro" type="text" maxlength="150" placeholder="Rua, avenida..." value="<?= e($campo['logradouro']) ?>" required>
<?php if (isset($erros['logradouro'])): ?><p class="campo__erro"><?= e($erros['logradouro']) ?></p><?php endif; ?>
              </div>
              <div class="col-4 campo<?= isset($erros['numero']) ? ' invalido' : '' ?>">
                <label for="numero">Número *</label>
                <input class="form-control" id="numero" name="numero" type="text" maxlength="10" placeholder="0" value="<?= e($campo['numero']) ?>" required>
<?php if (isset($erros['numero'])): ?><p class="campo__erro"><?= e($erros['numero']) ?></p><?php endif; ?>
              </div>
              <div class="col-8 campo<?= isset($erros['complemento']) ? ' invalido' : '' ?>">
                <label for="complemento">Complemento</label>
                <input class="form-control" id="complemento" name="complemento" type="text" maxlength="80" placeholder="Opcional" value="<?= e($campo['complemento']) ?>">
<?php if (isset($erros['complemento'])): ?><p class="campo__erro"><?= e($erros['complemento']) ?></p><?php endif; ?>
              </div>
              <div class="col-8 campo<?= isset($erros['cidade']) ? ' invalido' : '' ?>">
                <label for="cidade">Cidade *</label>
                <input class="form-control" id="cidade" name="cidade" type="text" maxlength="80" placeholder="Ex.: Cascavel" value="<?= e($campo['cidade']) ?>" required>
<?php if (isset($erros['cidade'])): ?><p class="campo__erro"><?= e($erros['cidade']) ?></p><?php endif; ?>
              </div>
              <div class="col-4 campo<?= isset($erros['uf']) ? ' invalido' : '' ?>">
                <label for="uf">UF *</label>
                <select class="form-select" id="uf" name="uf">
<?php foreach (lista_ufs() as $sigla): ?>
                  <option value="<?= $sigla ?>"<?= $campo['uf'] === $sigla ? ' selected' : '' ?>><?= $sigla ?></option>
<?php endforeach; ?>
                </select>
<?php if (isset($erros['uf'])): ?><p class="campo__erro"><?= e($erros['uf']) ?></p><?php endif; ?>
              </div>
            </div>
            <div class="acoes">
              <button class="botao botao--principal" type="submit">Salvar endereço <span class="botao__seta" aria-hidden="true">→</span></button>
              <a class="botao" href="<?= BASE_URL ?>enderecos/">Cancelar</a>
            </div>
          </form>
        </section>
      </div>
<?php require __DIR__ . '/../includes/rodape.php'; ?>
