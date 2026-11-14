// Vektor/prototipo/js/vektor.js
document.querySelectorAll('[data-alvo]').forEach(function (el) {
  var alvo = parseInt(el.dataset.alvo, 10);
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    el.textContent = alvo;
    return;
  }
  var inicio = null;
  function passo(t) {
    if (inicio === null) inicio = t;
    var p = Math.min((t - inicio) / 900, 1);
    el.textContent = Math.round(alvo * (1 - Math.pow(1 - p, 3)));
    if (p < 1) requestAnimationFrame(passo);
  }
  el.textContent = '0';
  requestAnimationFrame(passo);
});
