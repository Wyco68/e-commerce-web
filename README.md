# Car Parts E-Commerce Store

A Laravel web application for browsing and ordering car parts and accessories. Features product catalog, user registration, session-based cart, quantity-based discounts, and order history.

## Features

- **Public pages**: Home page with featured products, full product catalog with category names and prices
- **Authentication**: Register (with phone number and address), login, logout via Laravel Breeze
- **Shopping cart**: Session-based cart — add products, update quantities, remove items
- **Order creation**: Confirm cart to create Order + OrderItem records with discount calculations
- **Quantity discounts**: Automatic discounts when ordered quantity meets a product's minimum threshold
- **Member pages**: Account info page, order history with item details

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed and running

## Setup (Docker with Laravel Sail)

### 1. Clone and install PHP dependencies

```bash
git clone <repo-url> && cd project
docker run --rm -v $(pwd):/var/www/html -w /var/www/html laravelsail/php85-composer:latest composer install
```

> This uses a temporary Docker container to run `composer install` without needing PHP or Composer on your host machine.

### 2. Configure environment

```bash
cp .env.example .env
```

Open `.env` and update the database settings to use MySQL via Sail:

```dotenv
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=sail
DB_USERNAME=laravel
DB_PASSWORD=password
```

> **Note:** The `DB_HOST` must be `mysql` (the Docker service name), not `localhost` or `127.0.0.1`.

### 3. Start the containers

```bash
./vendor/bin/sail up -d
```

This starts two containers:
- **laravel.test** — the PHP application server (accessible at `http://localhost`)
- **mysql** — a MySQL 8.4 database server

Wait a few seconds for MySQL to finish initializing on first start.

### 4. Generate app key, run migrations, and seed the database

```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
```

### 5. Install frontend dependencies and build assets

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

For development with hot reloading:

```bash
./vendor/bin/sail npm run dev
```

### 6. Visit the application

Open `http://localhost` in your browser.

## Common Sail Commands

| Command | Description |
|---|---|
| `./vendor/bin/sail up -d` | Start containers in the background |
| `./vendor/bin/sail down` | Stop containers |
| `./vendor/bin/sail artisan migrate:fresh --seed` | Reset and re-seed the database |
| `./vendor/bin/sail artisan tinker` | Open Laravel REPL |
| `./vendor/bin/sail mysql` | Open a MySQL shell |
| `./vendor/bin/sail npm run dev` | Start Vite dev server |
| `./vendor/bin/sail test` | Run tests |

## Troubleshooting

### Access denied for MySQL user

If you see `SQLSTATE[HY000] [1045] Access denied`, the MySQL volume was initialized with different credentials than your current `.env`. Fix by destroying the volume and restarting:

```bash
./vendor/bin/sail down -v
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
```

> **Warning:** `sail down -v` deletes all database data. The `-v` flag removes Docker volumes.

### Port conflicts

If port 80 or 3306 is already in use, set alternative ports in `.env`:

```dotenv
APP_PORT=8080
FORWARD_DB_PORT=3307
```

Then restart with `./vendor/bin/sail up -d`.

### Connecting to MySQL from a GUI tool

Use these settings in your database client (e.g., TablePlus, DBeaver):

| Setting  | Value                          |
|----------|--------------------------------|
| Host     | `127.0.0.1`                    |
| Port     | `3306` (or your `FORWARD_DB_PORT`) |
| Database | `sail`                         |
| Username | `laravel`                      |
| Password | `password`                     |

## Demo Credentials

All seeded users use password: `password`

| Name   | Email               |
|--------|---------------------|
| Wyco   | wyco@example.com    |
| Joe    | joe@example.com     |
| Anakin | anakin@example.com  |
| Saw    | saw@example.com     |
| Shiro  | shiro@example.com   |

## Running Tests

```bash
php artisan test
```

## Key Routes

| Method | URI              | Name            | Description                  |
|--------|------------------|-----------------|------------------------------|
| GET    | /                | home            | Home page with featured products |
| GET    | /products        | products.index  | Browse all products          |
| GET    | /products/{id}   | products.show   | Product detail page          |
| GET    | /register        | register        | Registration form            |
| GET    | /login           | login           | Login form                   |
| GET    | /cart            | cart.index      | View cart (auth)             |
| POST   | /cart/{product}  | cart.add        | Add product to cart (auth)   |
| GET    | /orders/create   | orders.create   | Review order before confirm  |
| POST   | /orders          | orders.store    | Place order (auth)           |
| GET    | /orders          | orders.index    | Order history (auth)         |
| GET    | /account         | account.show    | Member info (auth)           |

## Discount System

Discounts are per-product and quantity-based. If a product has a discount with `min_quantity = 5` and `percentage = 20`, ordering 5+ units applies 20% off that line item's subtotal. Discount data is stored in the `discounts` table.
