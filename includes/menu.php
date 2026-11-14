<?php // vektor/includes/menu.php
$itens = [
    'inicio' => ['index.php', 'Início', '<path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/>'],
    'produtos' => ['produtos/', 'Produtos', '<path d="M21 8l-9-5-9 5v8l9 5 9-5z"/><path d="M3 8l9 5 9-5M12 13v8"/>'],
    'enderecos' => ['enderecos/', 'Endereços', '<path d="M12 21s7-6.2 7-11a7 7 0 10-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/>'],
    'transportadores' => ['transportadores/', 'Transportadores', '<path d="M3 6h11v10H3z"/><path d="M14 9h4l3 3v4h-7"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>'],
    'novo-frete' => ['fretes/novo.php', 'Novo frete', '<circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/>'],
    'relatorio' => ['relatorios/', 'Relatório', '<path d="M6 3h9l4 4v14H6z"/><path d="M9 13h7M9 17h7M9 9h3"/>'],
];
?>
    <nav class="menu" aria-label="Menu principal">
      <img class="menu__logo" src="<?= BASE_URL ?>assets/img/logo.png" alt="Vektor - Gerenciamento logístico">
<?php foreach ($itens as $chave => [$link, $rotulo, $icone]): ?>
      <a class="menu__item<?= $chave === $pagina ? ' ativo' : '' ?>" href="<?= BASE_URL . $link ?>"<?= $chave === $pagina ? ' aria-current="page"' : '' ?>><svg viewBox="0 0 24 24" aria-hidden="true"><?= $icone ?></svg><?= e($rotulo) ?></a>
<?php endforeach; ?>
      <p class="menu__rodape">Fundamentos de Programação para Internet</p>
    </nav>
