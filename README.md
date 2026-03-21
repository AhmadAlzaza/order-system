# Order Management System

## About

A RESTful API for managing orders, built with Laravel 12.
Users can register, browse products, create orders, and track order status.
Total price is calculated automatically based on order items.
Product management is restricted to admin users only.

## Tech Stack

- Laravel 12
- PHP 8.2
- MySQL
- Laravel Sanctum (Authentication)
- Laravel API Resources
- Laravel Form Requests
- PHPUnit (Feature Tests)
- Laragon

## Installation

1. Clone the repository
   git clone https://github.com/AhmadAlzaza/order-system.git

2. Install dependencies
   composer install

3. Copy environment file
   cp .env.example .env

4. Configure .env
   DB_DATABASE=order_system
   DB_USERNAME=root
   DB_PASSWORD=

5. Run migrations
   php artisan migrate

6. Start the server
   php artisan serve

## Postman Collection

Import `order-system-api.postman_collection.json` to Postman to test all API endpoints.

## API Endpoints

### Authentication

| Method | Endpoint      | Description             |
| ------ | ------------- | ----------------------- |
| POST   | /api/register | Register a new user     |
| POST   | /api/login    | Login and get token     |
| POST   | /api/logout   | Logout (requires token) |

### Products

| Method | Endpoint           | Description                 |
| ------ | ------------------ | --------------------------- |
| GET    | /api/products      | Get all products (public)   |
| GET    | /api/products/{id} | Get single product (public) |
| POST   | /api/products      | Create product (admin only) |
| PUT    | /api/products/{id} | Update product (admin only) |
| DELETE | /api/products/{id} | Delete product (admin only) |

### Orders

| Method | Endpoint                | Description                                  |
| ------ | ----------------------- | -------------------------------------------- |
| GET    | /api/orders             | Get user orders (requires token)             |
| POST   | /api/orders             | Create new order with items (requires token) |
| GET    | /api/orders/{id}        | Get single order (requires token)            |
| PUT    | /api/orders/{id}        | Update order status (requires token)         |
| DELETE | /api/orders/{id}        | Delete order (requires token)                |
| POST   | /api/orders/{id}/cancel | Cancel order (requires token)                |

### Order Items

| Method | Endpoint              | Description                            |
| ------ | --------------------- | -------------------------------------- |
| GET    | /api/order-items      | Get user order items (requires token)  |
| POST   | /api/order-items      | Add item to order (requires token)     |
| GET    | /api/order-items/{id} | Get single order item (requires token) |
| PUT    | /api/order-items/{id} | Update order item (requires token)     |
| DELETE | /api/order-items/{id} | Delete order item (requires token)     |

## Authentication

All protected endpoints require a Bearer token in the headers:

```
Authorization: Bearer {your_token}
```

## Roles

| Role  | Permissions                            |
| ----- | -------------------------------------- |
| user  | Browse products, manage own orders     |
| admin | All user permissions + manage products |

## Notes

- Product price is fetched automatically from the database
- Order total price updates automatically when items are added or removed
- Orders can only be updated or deleted when status is **pending**
- Stock is validated before order creation — overselling is prevented
- Product management requires **admin** role
- Orders can only be cancelled when status is **pending**
- Create order example:

```json
{
    "items": [
        { "product_id": 1, "quantity": 2 },
        { "product_id": 2, "quantity": 1 }
    ]
}
```

## Running Tests

```bash
php artisan test
```

15 tests, 59 assertions — all passing
