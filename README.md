# Car Parts E-Commerce Store

A Laravel web application for browsing and ordering car parts and accessories. Features product catalog, user registration, session-based cart, quantity-based discounts, and order history.

## Features

- **Public pages**: Home page with featured products, full product catalog with category names and prices
- **Authentication**: Register (with phone number and address), login, logout via Laravel Breeze
- **Shopping cart**: Session-based cart — add products, update quantities, remove items
- **Order creation**: Confirm cart to create Order + OrderItem records with discount calculations
- **Quantity discounts**: Automatic discounts when ordered quantity meets a product's minimum threshold
- **Member pages**: Account info page, order history with item details

## Setup

```bash
# Clone and install dependencies
git clone <repo-url> && cd project
composer install
npm install && npm run build

# Environment
cp .env.example .env
php artisan key:generate

# Database (SQLite by default)
touch database/database.sqlite
php artisan migrate --seed

# Run
php artisan serve
```

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
