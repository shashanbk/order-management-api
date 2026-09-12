# Mini Order Management API

A robust, production-ready RESTful API built with **Laravel 12**, **PHP 8.3**, and **MySQL**.

---

## 🚀 Features

- **Authentication:** Secure API authentication using Laravel Sanctum.
- **Product Management:** Full CRUD with Soft Deletes, Pagination, and Search filters.
- **Order System:** Business logic with Database Transactions and Stock Validation.
- **Security:** API Rate Limiting (60 requests/min).
- **Notifications:** Automated Email confirmations for every order.

---

## 🛠️ Installation & Setup

Follow these steps to set up the project locally:

### 1. Clone the Repository

```bash
git clone https://github.com/shashanbk/order-management-api.git
cd order-management-api
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Configuration

Copy the example environment file:

```bash
cp .env.example .env
```

- Open `.env` and set your MySQL database credentials.
- Set `MAIL_MAILER=log`.

### 4. Initialize Database

```bash
php artisan key:generate
php artisan migrate --seed
```

### 5. Run Tests

```bash
php artisan test
```

---

## 📝 API Endpoints

### 🔐 Authentication

| Method | Endpoint       | Description                    |
|--------|----------------|---------------------------------|
| POST   | `/api/register`| Register a new user             |
| POST   | `/api/login`   | Login and get Bearer Token      |
| POST   | `/api/logout`  | Revoke token (Protected)        |

### 📦 Product Management

| Method | Endpoint             | Description                          |
|--------|----------------------|---------------------------------------|
| GET    | `/api/products`      | List all products (Search & Paginate) |
| GET    | `/api/products/{id}` | View single product                   |
| POST   | `/api/products`      | Create product (Protected)            |
| PUT    | `/api/products/{id}` | Update product (Protected)            |
| DELETE | `/api/products/{id}` | Soft Delete product (Protected)       |

### 🛒 Order System

| Method | Endpoint          | Description                       |
|--------|-------------------|-------------------------------------|
| POST   | `/api/orders`     | Place order (Validates stock)       |
| GET    | `/api/orders`     | View user order history             |
| GET    | `/api/orders/{id}`| View specific order details         |

---

## 🔬 R&D Implementation Details

### 1. Data Integrity & Transactions

Used `DB::transaction` and `lockForUpdate()` in the Order System. This ensures that stock is only reduced if the entire order is successful, preventing data corruption during high traffic.

### 2. API Rate Limiting

Configured a limit of 60 requests per minute. A custom exception handler returns a clean JSON response with a `retry_after_seconds` timer for a better developer experience.

### 3. Soft Deletes

Products use `SoftDeletes` to preserve order history. This means a product can be removed from the store without breaking old order records.

---

## 🧪 Automated Testing

Includes a Feature Test (`OrderTest.php`) that programmatically verifies that users cannot purchase out-of-stock items.

---

## 📄 License

This project is open-sourced software.
