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

## Evolução do contêiner de dependências

O `Container` é a entrada pública para registrar e resolver dependências. Ele resolve uma classe registrada ou concreta e cria automaticamente as dependências de seu construtor quando elas possuem tipos de classe nomeados.

À medida que os construtores exigirem outras dependências, a resolução e a instanciação deverão continuar sendo responsabilidades separadas.

### Responsabilidades

| Componente | Responsabilidade |
| --- | --- |
| `Container` | Mantém os bindings, lê os metadados do construtor por reflection e resolve dependências recursivamente. |
| `Instantiator` | Recebe um `ReflectionClass` e argumentos já resolvidos; apenas cria o objeto com `newInstanceArgs()`. |

O `Instantiator` não deve conhecer o `Container`. Dessa forma, ele não tenta resolver dependências por conta própria; apenas descreve o que é necessário para criar um objeto e, depois, recebe os argumentos prontos.

Essa separação expressa o princípio de **orquestração versus execução**:

- O `Container` orquestra: decide o que resolver, quando criar, quando reutilizar e qual exceção expor.
- O `Instantiator` executa: cria a instância da classe que recebeu, sem decidir como as dependências são obtidas.

```text
Container::make(UserService)
        │
        ├─ encontra a classe concreta
        ├─ lê o construtor e seus parâmetros por Reflection
        │       │
        │       └─ Reflection informa Logger, Config e Database
        │
        ├─ make(Logger)
        ├─ make(Config)
        ├─ make(Database)
        │
        └─ entrega os argumentos resolvidos ao Instantiator
                │
                └─ newInstanceArgs(...) → UserService
```

Essa direção evita o fluxo `Container → Instantiator → Container`, que criaria acoplamento entre os dois objetos. O `Container` permanece como orquestrador; o `Instantiator` é um componente técnico e reutilizável.

Nesta etapa, apenas `ReflectionNamedType` que não represente um tipo interno é aceito para autowiring. Tipos como `string`, `int` e `array`, tipos ausentes e union types resultam em `ContainerException`. Valores padrão, a escolha de `null` como fallback e union types serão tratados somente quando houver uma regra de resolução definida.

### Próximos passos

Quando a resolução de construtores for implementada, o `Container` deverá tratar, progressivamente:

- parâmetros tipados com classes concretas;
- interfaces resolvidas por bindings;
- valores padrão e tipos anuláveis;
- union types, quando houver uma regra de escolha bem definida;
- ciclos de dependência.

Falhas de resolução devem continuar saindo do Core como `ContainerException`. Quando os cenários forem diferenciados, ela poderá ser especializada — por exemplo, em `NotFoundBindingException` e `CircularDependencyException` — sem alterar quem consome o contêiner.

Um `ObjectFactory` só deve ser introduzido se tiver uma responsabilidade diferente da do `Instantiator`; enquanto ambos apenas criarem objetos, manter um único componente evita uma camada sem propósito.
