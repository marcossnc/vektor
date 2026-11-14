# Vektor - Gerenciamento logístico

Aplicação web de gerenciamento logístico desenvolvida como Atividade Prática Orientada (APO) da disciplina **Fundamentos de Programação para Internet**, da UNIPAR EAD.

**Acadêmico:** Marcos Antonio Sanchez Junior - RA 09062144

## Descrição

O Vektor permite cadastrar produtos, endereços (origem e destino) e transportadores e, a partir deles, gerar fretes. Um mesmo frete liga um transportador a vários produtos (relação de um para muitos). Também é possível baixar um relatório dos fretes de um dia.

## Telas

- Início: resumo dos fretes e dos cadastros
- Produtos: cadastro e edição
- Endereços: cadastro e edição de origem e destino
- Transportadores: cadastro e edição
- Novo frete: ligação entre transportador, endereços e produtos, com campos dinâmicos em JavaScript
- Relatório: fretes de um dia, para baixar

O PDF com o desenho das telas (Etapa 1) está em `docs/vektor-etapa1-telas.pdf`.

## Tecnologias

- PHP 8 no backend
- MySQL com PDO
- HTML, CSS e JavaScript no frontend
- Bootstrap 5
- XAMPP (Apache e MySQL) para desenvolvimento
- Git e GitHub para versionamento

## Estrutura de pastas

```
vektor/
  assets/      css, js e imagens
  config/      configuração do banco de dados
  includes/    cabeçalho, menu e rodapé reutilizados
  produtos/    telas e ações de produtos
  enderecos/   telas e ações de endereços
  transportadores/
  fretes/      geração dos fretes
  relatorios/  relatório de fretes por dia
  database/    script SQL do banco
  docs/        documentos do projeto
```

## Como executar

1. Instalar o XAMPP e iniciar o Apache e o MySQL.
2. Copiar a pasta do projeto para `C:\xampp\htdocs\vektor`.
3. Importar `database/schema.sql` no MySQL (phpMyAdmin).
4. Acessar `http://localhost/vektor/`.

Este README será completado conforme o projeto avançar.
