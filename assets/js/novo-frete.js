// vektor/assets/js/novo-frete.js
var linhas = document.getElementById('linhas');
var modelo = document.getElementById('modelo-linha');
var aviso = document.getElementById('aviso');
var botaoDesfazer = document.getElementById('desfazer');
var removida = null;
var temporizador = null;

function formatar(numero, casas) {
  return numero.toLocaleString('pt-BR', { minimumFractionDigits: casas, maximumFractionDigits: casas });
}

function opcaoEscolhida(select) {
  return select.options[select.selectedIndex];
}

function recalcular() {
  var itens = 0;
  var peso = 0;
  var valor = 0;
  linhas.querySelectorAll('.linha-produto').forEach(function (linha) {
    var opcao = opcaoEscolhida(linha.querySelector('select'));
    var quantidade = parseInt(linha.querySelector('input').value, 10) || 0;
    itens += quantidade;
    peso += parseFloat(opcao.dataset.peso) * quantidade;
    valor += parseFloat(opcao.dataset.valor) * quantidade;
  });
  document.getElementById('r-itens').textContent = itens;
  document.getElementById('r-peso').textContent = formatar(peso, 1) + ' kg';
  document.getElementById('r-valor').textContent = 'R$ ' + formatar(valor, 2);
  var capacidade = parseInt(opcaoEscolhida(document.getElementById('transportador')).dataset.capacidade, 10);
  var alerta = document.getElementById('r-alerta');
  if (peso > capacidade) {
    alerta.textContent = 'O peso passa da capacidade do veículo (' + formatar(capacidade, 0) + ' kg).';
    alerta.hidden = false;
  } else {
    alerta.hidden = true;
  }
}

function resumirRota() {
  var transportador = opcaoEscolhida(document.getElementById('transportador'));
  var origem = opcaoEscolhida(document.getElementById('origem'));
  var destino = opcaoEscolhida(document.getElementById('destino'));
  document.getElementById('r-transportador').textContent = transportador.textContent;
  document.getElementById('r-rota').textContent = origem.dataset.cidade + ' → ' + destino.dataset.cidade;
}

function mostrarAviso(texto, comDesfazer) {
  document.getElementById('aviso-texto').textContent = texto;
  botaoDesfazer.hidden = !comDesfazer;
  aviso.hidden = false;
  clearTimeout(temporizador);
  temporizador = setTimeout(function () {
    aviso.hidden = true;
    removida = null;
  }, 6000);
}

document.getElementById('adicionar').addEventListener('click', function () {
  linhas.appendChild(modelo.content.firstElementChild.cloneNode(true));
  recalcular();
});

linhas.addEventListener('click', function (evento) {
  if (!evento.target.matches('.botao--icone')) {
    return;
  }
  var linha = evento.target.closest('.linha-produto');
  if (linhas.querySelectorAll('.linha-produto').length === 1) {
    mostrarAviso('O frete precisa de pelo menos um produto.', false);
    return;
  }
  removida = { no: linha, proximo: linha.nextSibling };
  linhas.removeChild(linha);
  recalcular();
  mostrarAviso('Produto removido', true);
});

botaoDesfazer.addEventListener('click', function () {
  if (removida) {
    linhas.insertBefore(removida.no, removida.proximo);
    removida = null;
    recalcular();
  }
  aviso.hidden = true;
});

linhas.addEventListener('input', recalcular);
linhas.addEventListener('change', recalcular);

['transportador', 'origem', 'destino'].forEach(function (id) {
  document.getElementById(id).addEventListener('change', function () {
    resumirRota();
    recalcular();
  });
});

resumirRota();
recalcular();
