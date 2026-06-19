# Fomotoko Flash Sale (Technical Test)

Backend implementation untuk technical test menggunakan Laravel 11 dan PostgreSQL. 
Project ini mencakup dua task:
1. **Task 1:** Flash Sale API (handle race condition via pessimistic locking).
2. **Task 2:** Hidden Item Game (CLI pathfinding algorithm).

## Requirements
- PHP >= 8.2
- Composer
- PostgreSQL

## Setup & Instalasi

```bash
git clone https://github.com/wildaan/fomotoko-flashsale.git
cd fomotoko-flashsale
composer install
cp .env.example .env
php artisan key:generate
```

Konfigurasi kredensial PostgreSQL di file `.env`, kemudian jalankan migrate dan seeder:

```bash
php artisan migrate --seed
```

## Task 1: Online Store API (Flash Sale)

Jalankan lokal server:
```bash
php artisan serve
```

**Endpoint:** `POST /api/purchase`  
**Headers:** `Content-Type: application/json`

**Request Payload:** *(Ambil nilai `product_uuid` dari database hasil seeder)*
```json
{
  "product_uuid": "masukkan-uuid-produk-dari-database",
  "quantity": 1
}
```

**Success Response (200 OK):**
```json
{
  "message": "Purchase successful",
  "order": {
    "orders_id": 1,
    "orders_uuid": "...",
    "orders_total_amount": 1000000.00,
    "orders_status": "active",
    "orders_updated_at": "...",
    "orders_created_at": "..."
  },
  "order_item": {
    "order_items_id": 1,
    "order_items_orders_uuid": "...",
    "order_items_products_uuid": "...",
    "order_items_quantity": 1,
    "order_items_price": 1000000.00
  }
}
```

**Testing Race Condition:**
```bash
php artisan test --filter FlashSaleRaceConditionTest
```

## Task 2: Hidden Item Game

```bash
php artisan game:hidden-item
```

**Output:**
```text
Probable Item Coordinate(s) found:

- Row: 4, Col: 3
- Row: 2, Col: 5
- Row: 3, Col: 5
- Row: 4, Col: 5
- Row: 2, Col: 6

Visual Grid (Probable locations marked with '$'):

########
#......#
#.###$$#
#...#$##
#X#$.$.#
########
```