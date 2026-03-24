# Sistema de Reservas de Viagens (API)

API REST para gerenciamento de usuários e pedidos de viagem, construída com Laravel e preparada para execução com Docker (Laravel Sail).

## Tecnologias e Versões

- **Laravel**: `^12.0`
- **PHP**: `^8.2` (ambiente Docker com runtime `8.4` do Sail)
- **MySQL**: `mysql/mysql-server:8.0`
- **PHPUnit**: `^11.5.3`
- **Docker Compose**: via `compose.yaml`

## Funcionalidades

- Cadastro e consulta de usuários.
- Inativação de usuário (soft delete por status).
- Criação de pedido de viagem.
- Listagem de viagens com filtros.
- Consulta de viagem por ID.
- Atualização de status da viagem (`aprovado` ou `cancelado`) com regras de autorização.

## Regras de Negócio Principais

- Apenas usuário administrador pode alterar status de viagem.
- O solicitante da viagem não pode alterar o próprio pedido.
- Não é permitido cancelar viagem já aprovada.
- Ao alterar status, o usuário solicitante é notificado.

## Como baixar e executar com Docker

### 1) Clonar o projeto

```bash
git clone <URL_DO_SEU_REPOSITORIO>
cd sistema_reservas
```

### 2) Configurar ambiente

Crie o arquivo `.env` a partir do exemplo:

```bash
cp .env.example .env
```

No Windows PowerShell, caso o comando acima não funcione:

```powershell
Copy-Item .env.example .env
```

### 3) Subir containers

```bash
docker compose up -d
```

> O projeto utiliza os serviços:
> - `laravel.test` (aplicação Laravel)
> - `mysql` (banco MySQL 8.0)
> - `phpmyadmin` (interface web para banco)

### 4) Instalar dependências e preparar aplicação

```bash
docker compose exec laravel.test composer install
docker compose exec laravel.test php artisan key:generate
docker compose exec laravel.test php artisan migrate
```

### 5) Acessar aplicação

- **API**: `http://localhost` (ou porta definida em `APP_PORT`)
- **phpMyAdmin**: `http://localhost:8080` (ou porta definida em `PHPMYADMIN_PORT`)

## Executando os testes

### Rodar todos os testes

```bash
docker compose exec laravel.test php artisan test
```

### Rodar apenas testes de API de viagem

```bash
docker compose exec laravel.test php artisan test tests/Feature/ApiTripTest.php
```

## Endpoints da API

Base URL: `http://localhost/api`

### Usuários

- `POST /user` — cria usuário
- `GET /users` — lista usuários
- `GET /user/{id}` — busca usuário por ID
- `DELETE /user/delete/{id}` — inativa usuário (`status = cancelado`)

### Viagens

- `POST /trip` — cria pedido de viagem
- `GET /trips` — lista viagens (com filtros opcionais)
- `GET /trip/{id}` — busca viagem por ID
- `PATCH /trip/{id}/status` — altera status da viagem

## Exemplos de Requisição

### Criar usuário

```bash
curl -X POST http://localhost/api/user \
    -H "Content-Type: application/json" \
    -d '{
        "nome": "Ana",
        "idade": 29,
        "email": "ana@teste.com",
        "senha": "123456"
    }'
```

### Criar viagem

```bash
curl -X POST http://localhost/api/trip \
    -H "Content-Type: application/json" \
    -d '{
        "usuario_id": 1,
        "destino": "São Paulo",
        "partida_de": "Recife",
        "retorno_de": "São Paulo",
        "data_viagem_ida": "2026-04-10",
        "data_viagem_volta": "2026-04-15"
    }'
```

### Listar viagens com filtros

```bash
curl "http://localhost/api/trips?status=solicitada&destino=Sao"
```

### Atualizar status da viagem

```bash
curl -X PATCH http://localhost/api/trip/1/status \
    -H "Content-Type: application/json" \
    -d '{
        "status": "aprovado",
        "usuario_id": 2
    }'
```

## Filtros disponíveis em `GET /api/trips`

Parâmetros opcionais:

- `status`: `solicitada`, `aprovado`, `cancelado`
- `destino`: texto parcial
- `partida_de`: texto parcial
- `retorno_de`: texto parcial
- `data_viagem_ida`: data (`YYYY-MM-DD`) para início do período
- `data_viagem_volta`: data (`YYYY-MM-DD`) para fim do período

## Estrutura do banco (resumo)

- Tabela `usuarios`
- Tabela `viagens`

As migrations estão em `database/migrations`.

## Observações

- Devido a algumas questões que compremeteram meu tempo que vou explicar não foi possivel fazer algumas coisas que queria, como:

1- Autenticação JWT
2- Melhorar um pouco essas validações colocando elas em um arquivo a parte
3- Melhorar esses testes e criar mais alguns
4- Melhorar meus endpoints e estruturar eles de uma forma melhor aproveitando alguns principios de clean code
5- Dentre algumas pequenas melhorias que enxerguei mais infelizmente não vou ter tempo de implementar.

Novamente: Vou explicar mesmo porque tive 4 dias só conseguir mexer na segunda feira a noite.

Até..
