# FoodDesk

FoodDesk é um sistema para lanchonetes gerenciarem pedidos do salão e integrações com plataformas como iFood e Anota Aí.

---

## Tecnologias

- PHP 8.5 + Laravel 12
- MySQL 8
- Redis
- Node.js + NPM
- Docker + Docker Compose

---

## Como executar

### 1. Clone o repositório

```bash
git clone <URL_DO_REPOSITORIO> fooddesk
cd fooddesk
```

### 2. Crie o arquivo `.env` a partir do exemplo

```bash
cp .env.example .env
```

### 3. Suba os containers do Docker

```bash
docker-compose up --build -d
```

### 4. Entre no container do app

```bash
docker-compose exec app bash
```

### 5. Instale as dependências do PHP e Node

```bash
composer install
npm install
npm run dev
```

### 6. Gere a chave da aplicação

```bash
php artisan key:generate
```

### 7. Rode as migrations

```bash
php artisan migrate
```

### 8. Acesse a aplicação

Abra no navegador:

```
http://localhost:8000
```
