# Sistema de Reservas - Onfly

Teste técnico: criação de um sistema de reservas com Laravel.

## Requisitos

- PHP >= 8.3
- Composer
- Node.js & npm

## Instalação

```bash
# 1. Clone o repositório
git clone https://github.com/kidbruno/sistema_reservas_onfly.git
cd sistema_reservas_onfly

# 2. Instale as dependências PHP
composer install

# 3. Copie o arquivo de ambiente e gere a chave da aplicação
cp .env.example .env
php artisan key:generate

# 4. Execute as migrações
php artisan migrate

# 5. (Opcional) Instale as dependências front-end
npm install && npm run build

# 6. Inicie o servidor de desenvolvimento
php artisan serve
```

A aplicação estará disponível em `http://localhost:8000`.

## Testes

```bash
php artisan test
```
