# FinBase

Projeto pessoal de controle financeiro para estudo prático de DDD, Clean Architecture e persistência desacoplada. A aplicação permite criar contas, registrar receitas e despesas, consultar saldo, histórico e resumo mensal.

## Objetivos de estudo

- Aplicar DDD, SOLID e Clean Architecture de forma prática.
- Explorar padrões de projeto quando eles fizerem sentido para o problema.
- Construir uma base de código organizada, testável e de fácil evolução.

## Tecnologias e ferramentas

- PHP
- MySQL 8 via Docker, para desenvolvimento local
- PDO, sem ORM
- [Composer](https://getcomposer.org/), para gerenciamento de dependências e autoload PSR-4
- [PHPStan](https://phpstan.org/), para análise estática
- [PHP CS Fixer](https://cs.symfony.com/), para padronização de código em PSR-12

## Requisitos

- PHP 7.4 ou superior (o ambiente de desenvolvimento atual usa PHP 8.4)
- Composer 2
- Docker e Docker Compose, quando for usar o adapter MySQL

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

3. Execute a aplicação com o servidor embutido do PHP:

   ```bash
   php -S localhost:8000 -t public
   ```

## Banco de dados local

O MySQL de desenvolvimento é executado por Docker. Copie o exemplo de ambiente, inicie o contêiner e aplique as migrations:

```bash
cp .env.example .env
docker compose up -d
php database/migrate.php
```

O compose publica o MySQL em `127.0.0.1:3307`, pois a porta `3306` costuma estar ocupada por instalações locais. As credenciais reais ficam somente em `.env`, que não é versionado.

As migrations são arquivos PHP ordenados pelo nome em `database/migrations/`. O runner registra cada migration aplicada na tabela `migrations` e só executa as pendentes. Nesta primeira versão não há rollback, batches ou seeds.

## Persistência

Os contratos de repositório permitem coexistência de três adapters:

| Adapter | Uso |
| --- | --- |
| InMemory | Testes unitários e de casos de uso. |
| JSON | Persistência local simples e fallback padrão. |
| MySQL/PDO | Persistência relacional para desenvolvimento local. |

O bootstrap escolhe o adapter de contas e transações pela variável `PERSISTENCE_DRIVER`:

```dotenv
PERSISTENCE_DRIVER=json  # usa os arquivos JSON
# ou
PERSISTENCE_DRIVER=mysql # usa MySQL/PDO
```

Em modo MySQL, `PdoAccountRepository` e `PdoTransactionRepository` recebem o mesmo objeto `PDO`. Isso mantém a infraestrutura preparada para uma transação SQL compartilhada no próximo ciclo.

O schema atual contém `accounts` e `transactions`; transações referenciam contas por chave estrangeira e possuem índice em `account_id`. `occurred_at` é persistido como `Y-m-d H:i:s`, mantendo a hora local da aplicação sem conversão ou offset de timezone nesta versão.

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

A aplicação separa domínio, casos de uso, infraestrutura e apresentação. A organização dos componentes técnicos compartilhados está documentada em [Core](src/Core/doc.md).

```text
Presentation → Application → Domain
                   ↑
             Infrastructure
```

- `Domain`: entidades, value objects e contratos de repositório.
- `Application`: orquestra os casos de uso e aplica regras que dependem de repositórios, como unicidade de categorias.
- `Infrastructure`: adapters InMemory, JSON e PDO, conexão e migrations.
- `Presentation`: controllers HTTP/web, rotas e templates.

## Limitações atuais

As alterações de saldo e o histórico de transações ainda são salvos sequencialmente pelos casos de uso. Mesmo no adapter PDO, ainda não há `BEGIN/COMMIT/ROLLBACK` envolvendo as duas gravações. O compartilhamento de conexão já existe, mas a atomicidade será introduzida em um slice próprio.

## Roadmap

- [x] Inicializar o projeto com Composer e autoload PSR-4
- [x] Configurar análise estática com PHPStan
- [x] Configurar padronização de código com PHP CS Fixer
- [x] Documentar a estrutura inicial do Core
- [x] Definir o contrato inicial do contêiner de dependências
- [x] Modelar contas, transações e categorias
- [x] Adicionar casos de uso financeiros e interface web
- [x] Implementar repositórios InMemory, JSON e PDO
- [x] Adicionar testes automatizados e contratos de repositório
- [x] Criar migrations locais para accounts e transactions
- [ ] Tornar o registro de receitas e despesas atômico no MySQL
- [ ] Associar categorias opcionalmente às transações

## Licença

Distribuído sob a [licença MIT](LICENSE).
