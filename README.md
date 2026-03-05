# Order Management System

## About
A RESTful API for managing orders, built with Laravel 11. 
Users can register, browse products, create orders, and track order status. 
Total price is calculated automatically based on order items.

## Tech Stack
- Laravel 11
- MySQL
- Laravel Sanctum (Authentication)
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

## API Endpoints

### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/register | Register a new user |
| POST | /api/login | Login and get token |
| POST | /api/logout | Logout (requires token) |

### Products
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/products | Get all products |
| GET | /api/products/{id} | Get single product |
| POST | /api/products | Create product (requires token) |
| PUT | /api/products/{id} | Update product (requires token) |
| DELETE | /api/products/{id} | Delete product (requires token) |

### Orders
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/orders | Get user orders (requires token) |
| POST | /api/orders | Create new order (requires token) |
| GET | /api/orders/{id} | Get single order (requires token) |
| PUT | /api/orders/{id} | Update order (requires token) |
| DELETE | /api/orders/{id} | Delete order (requires token) |

### Order Items
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/order-items | Add item to order (requires token) |

## Authentication
All protected endpoints require a Bearer token in the headers:
```
Authorization: Bearer {your_token}
```

## Notes
- Product price is fetched automatically from the database
- Order total price updates automatically when items are added