# Core

O `Core` reúne componentes técnicos reutilizáveis pela aplicação. Ele não deve conter regras de negócio do domínio financeiro; sua responsabilidade é fornecer a infraestrutura mínima para a execução da aplicação.

> Esta é a estrutura planejada. As classes e pastas abaixo serão criadas conforme forem necessárias.

## Estrutura prevista

```text
src/Core/
├── Contracts/
├── Container/
├── Exceptions/
├── Http/
├── Routing/
└── Support/
```

## Responsabilidades

### `Contracts`

Contém interfaces compartilhadas pelo Core. São contratos — não implementações — que permitem trocar detalhes técnicos sem acoplar as demais camadas.

Exemplos previstos:

- `ContainerInterface`
- `RequestInterface`
- `ResponseInterface`

### `Container`

Concentrará a implementação do contêiner de injeção de dependências, responsável por registrar e resolver dependências da aplicação.

Exemplos previstos:

- `Container`
- `Binding`

### `Exceptions`

Reúne exceções técnicas compartilhadas pelo Core. Cada exceção deve representar uma situação específica e oferecer uma mensagem útil para diagnóstico.

Exemplos previstos:

- `ContainerException`
- `RouteNotFoundException`
- `HttpException`

### `Http`

Contém abstrações relacionadas ao protocolo HTTP, como a representação de requisições e respostas.

Exemplos previstos:

- `Request`
- `Response`
- `JsonResponse`
- `RedirectResponse`

### `Routing`

Responsável por registrar rotas e associar uma requisição ao seu destino na aplicação.

Exemplos previstos:

- `Router`
- `Route`
- `RouteCollection`

### `Support`

Agrupa classes utilitárias pequenas e coesas que não pertencem a uma regra de negócio nem dependem de infraestrutura externa. Essas classes não devem ser helpers globais.

Exemplos previstos:

- `Str`
- `Arr`
- `Uuid`

## Limites do Core

- Não conter entidades, casos de uso ou regras específicas de finanças.
- Evitar dependência direta da camada de apresentação ou de bancos de dados.
- Manter as abstrações simples; uma pasta ou classe só deve ser criada quando houver uma necessidade concreta.
