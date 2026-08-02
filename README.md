# FinBase

Projeto pessoal para estudar desenvolvimento de uma aplicação de controle financeiro. A proposta é centralizar o registro de receitas e despesas para tornar os gastos mais fáceis de acompanhar e compreender.

> O projeto está em fase inicial: a estrutura, as ferramentas de qualidade e os primeiros contratos do Core estão sendo preparados. As funcionalidades de negócio ainda serão implementadas.

## Objetivos de estudo

- Aplicar DDD, SOLID e Clean Architecture de forma prática.
- Explorar padrões de projeto quando eles fizerem sentido para o problema.
- Construir uma base de código organizada, testável e de fácil evolução.

## Tecnologias e ferramentas

- PHP
- [Composer](https://getcomposer.org/), para gerenciamento de dependências e autoload PSR-4
- [PHPStan](https://phpstan.org/), para análise estática
- [PHP CS Fixer](https://cs.symfony.com/), para padronização de código em PSR-12

## Requisitos

- PHP 7.4 ou superior (o ambiente de desenvolvimento atual usa PHP 8.4)
- Composer 2

## Como executar localmente

1. Clone o repositório e entre na pasta do projeto:

   ```bash
   git clone <url-do-repositorio>
   cd FinBase
   ```

2. Instale as dependências:

   ```bash
   composer install
   ```

3. À medida que a aplicação for implementada, mantenha o código em `src/` e os testes em `tests/`. Essas são as pastas já consideradas pelas ferramentas de qualidade.

## Qualidade de código

Depois de adicionar arquivos PHP em `src/`, execute a análise estática:

```bash
composer analyse
```

Para aplicar a formatação configurada:

```bash
composer fix
```

## Arquitetura

A organização planejada para os componentes técnicos está documentada em [Core](src/Core/doc.md). O primeiro contrato definido é o [`ContainerInterface`](src/Core/Contracts/ContainerInterface.php), que estabelece o comportamento esperado do contêiner de injeção de dependências.

## Roadmap

- [x] Inicializar o projeto com Composer e autoload PSR-4
- [x] Configurar análise estática com PHPStan
- [x] Configurar padronização de código com PHP CS Fixer
- [x] Documentar a estrutura inicial do Core
- [x] Definir o contrato inicial do contêiner de dependências
- [ ] Definir o domínio e os casos de uso financeiros
- [ ] Implementar os componentes do Core
- [ ] Adicionar testes automatizados
- [ ] Criar uma interface para uso da aplicação

## Licença

Distribuído sob a [licença MIT](LICENSE).
