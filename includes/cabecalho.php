<?php // vektor/includes/cabecalho.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/funcoes.php';
$titulo = $titulo ?? 'Vektor';
$pagina = $pagina ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vektor - <?= e($titulo) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Michroma&display=swap" rel="stylesheet">
  <link href="<?= BASE_URL ?>assets/css/vektor.css" rel="stylesheet">
</head>
<body>
  <div class="app">
    <?php include __DIR__ . '/menu.php'; ?>
    <main class="conteudo">
