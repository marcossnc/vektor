<?php // vektor/includes/rodape.php ?>
    </main>
  </div>
  <script src="<?= BASE_URL ?>assets/js/vektor.js"></script>
<?php foreach (($scripts ?? []) as $script): ?>
  <script src="<?= BASE_URL . e($script) ?>"></script>
<?php endforeach; ?>
</body>
</html>
