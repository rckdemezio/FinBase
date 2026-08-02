# FinBase

Projeto pessoal para estudar desenvolvimento de uma aplicação de controle financeiro. A proposta é centralizar o registro de receitas e despesas para tornar os gastos mais fáceis de acompanhar e compreender.

> O projeto está em fase inicial: a estrutura e as ferramentas de qualidade já estão sendo preparadas, mas as funcionalidades de negócio ainda serão implementadas.

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

## Roadmap

- [x] Inicializar o projeto com Composer e autoload PSR-4
- [x] Configurar análise estática com PHPStan
- [x] Configurar padronização de código com PHP CS Fixer
- [ ] Definir o domínio e os casos de uso financeiros
- [ ] Implementar o núcleo da aplicação
- [ ] Adicionar testes automatizados
- [ ] Criar uma interface para uso da aplicação

## Licença

Projeto de estudo para uso pessoal. A licença será definida caso o repositório seja disponibilizado publicamente.
