<?php // vektor/transportadores/index.php
$titulo = 'Transportadores';
$pagina = 'transportadores';
require __DIR__ . '/../includes/cabecalho.php';

$pdo = conectar();
$transportadores = $pdo->query('SELECT id, nome, cnpj, veiculo, capacidade_kg FROM transportadores ORDER BY id')->fetchAll();

$edicao = null;
if (isset($_GET['editar'])) {
    $consulta = $pdo->prepare('SELECT * FROM transportadores WHERE id = ?');
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
    'cnpj' => $antigo['cnpj'] ?? ($edicao['cnpj'] ?? ''),
    'telefone' => $antigo['telefone'] ?? ($edicao['telefone'] ?? ''),
    'veiculo' => $antigo['veiculo'] ?? ($edicao['veiculo'] ?? ''),
    'capacidade' => $antigo['capacidade'] ?? ($edicao['capacidade_kg'] ?? ''),
    'placa' => $antigo['placa'] ?? ($edicao['placa'] ?? ''),
];
$editando = $campo['id'] !== '' && (int) $campo['id'] > 0;
?>
      <header class="d-flex justify-content-between align-items-center mb-4 animar">
        <div>
          <h1 class="titulo">Transportadores</h1>
          <p class="subtitulo">Empresas e veículos responsáveis pelos fretes</p>
        </div>
        <span class="status">Sistema ativo</span>
      </header>
<?php if ($aviso): ?>
      <p class="mensagem" role="status"><?= e($aviso) ?></p>
<?php endif; ?>
      <div class="row g-4">
        <section class="col-12 col-lg-7 animar" style="--i:1">
          <div class="painel h-100">
            <h2 class="painel__titulo">Transportadores cadastrados</h2>
            <div class="table-responsive">
            <table class="tabela tabela--cnpj">
              <thead>
                <tr><th>ID</th><th>Nome</th><th>CNPJ</th><th>Veículo</th><th></th></tr>
              </thead>
              <tbody>
<?php foreach ($transportadores as $transportador): ?>
                <tr>
                  <td><?= (int) $transportador['id'] ?></td>
                  <td><?= e($transportador['nome']) ?></td>
                  <td><?= e($transportador['cnpj']) ?></td>
                  <td><?= e($transportador['veiculo']) ?><br><?= capacidade_formatada((int) $transportador['capacidade_kg']) ?></td>
                  <td><a class="botao botao--sm" href="?editar=<?= (int) $transportador['id'] ?>#formulario">Editar</a></td>
                </tr>
<?php endforeach; ?>
<?php if (!$transportadores): ?>
                <tr><td colspan="5">Nenhum transportador cadastrado ainda.</td></tr>
<?php endif; ?>
              </tbody>
            </table>
            </div>
          </div>
        </section>
        <section class="col-12 col-lg-5 animar" style="--i:2" id="formulario">
          <form class="painel h-100" action="<?= BASE_URL ?>transportadores/salvar.php" method="post">
            <h2 class="painel__titulo"><?= $editando ? 'Editar transportador #' . (int) $campo['id'] : 'Novo transportador' ?></h2>
            <input type="hidden" name="id" value="<?= e((string) $campo['id']) ?>">
            <div class="row g-3">
              <div class="col-12 campo<?= isset($erros['nome']) ? ' invalido' : '' ?>">
                <label for="nome">Nome da empresa *</label>
                <input class="form-control" id="nome" name="nome" type="text" maxlength="120" placeholder="Ex.: TransLog Express" value="<?= e($campo['nome']) ?>" required>
<?php if (isset($erros['nome'])): ?><p class="campo__erro"><?= e($erros['nome']) ?></p><?php endif; ?>
              </div>
              <div class="col-7 campo<?= isset($erros['cnpj']) ? ' invalido' : '' ?>">
                <label for="cnpj">CNPJ *</label>
                <input class="form-control" id="cnpj" name="cnpj" type="text" inputmode="numeric" maxlength="18" placeholder="00.000.000/0000-00" value="<?= e($campo['cnpj']) ?>" required>
<?php if (isset($erros['cnpj'])): ?><p class="campo__erro"><?= e($erros['cnpj']) ?></p><?php endif; ?>
              </div>
              <div class="col-5 campo<?= isset($erros['telefone']) ? ' invalido' : '' ?>">
                <label for="telefone">Telefone</label>
                <input class="form-control" id="telefone" name="telefone" type="text" inputmode="tel" maxlength="20" placeholder="(00) 00000-0000" value="<?= e($campo['telefone']) ?>">
<?php if (isset($erros['telefone'])): ?><p class="campo__erro"><?= e($erros['telefone']) ?></p><?php endif; ?>
              </div>
              <div class="col-12 campo<?= isset($erros['veiculo']) ? ' invalido' : '' ?>">
                <label for="veiculo">Tipo de veículo *</label>
                <select class="form-select" id="veiculo" name="veiculo">
<?php foreach (lista_veiculos() as $tipo): ?>
                  <option<?= $campo['veiculo'] === $tipo ? ' selected' : '' ?>><?= e($tipo) ?></option>
<?php endforeach; ?>
                </select>
<?php if (isset($erros['veiculo'])): ?><p class="campo__erro"><?= e($erros['veiculo']) ?></p><?php endif; ?>
              </div>
              <div class="col-6 campo<?= isset($erros['capacidade']) ? ' invalido' : '' ?>">
                <label for="capacidade">Capacidade (kg) *</label>
                <input class="form-control" id="capacidade" name="capacidade" type="text" inputmode="numeric" placeholder="0" value="<?= e((string) $campo['capacidade']) ?>" required>
<?php if (isset($erros['capacidade'])): ?><p class="campo__erro"><?= e($erros['capacidade']) ?></p><?php endif; ?>
              </div>
              <div class="col-6 campo<?= isset($erros['placa']) ? ' invalido' : '' ?>">
                <label for="placa">Placa</label>
                <input class="form-control" id="placa" name="placa" type="text" maxlength="8" placeholder="ABC-1D23" value="<?= e($campo['placa']) ?>">
<?php if (isset($erros['placa'])): ?><p class="campo__erro"><?= e($erros['placa']) ?></p><?php endif; ?>
              </div>
            </div>
            <div class="acoes">
              <button class="botao botao--principal" type="submit">Salvar transportador <span class="botao__seta" aria-hidden="true">→</span></button>
              <a class="botao" href="<?= BASE_URL ?>transportadores/">Cancelar</a>
            </div>
          </form>
        </section>
      </div>
<?php require __DIR__ . '/../includes/rodape.php'; ?>
