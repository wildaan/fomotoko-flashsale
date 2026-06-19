# Technical Test - Flash Sale API & Hidden Item Game

Repositori ini berisi *source code* untuk menyelesaikan dua tugas *technical test* menggunakan framework **Laravel 11** dan database **PostgreSQL**.

## ⚙️ Setup & Instalasi

Pastikan sistem Anda sudah terinstall PHP >= 8.2, Composer, dan PostgreSQL.

1. **Clone repository:**
   ```bash
   git clone https://github.com/wildaan/fomotoko-flashsale.git
   cd fomotoko-flashsale
   ```

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Setup environment:**
   Salin file `.env.example` menjadi `.env` lalu generate application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi Database:**
   Buat database kosong di PostgreSQL, lalu sesuaikan kredensial di file `.env`:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=nama_database_anda
   DB_USERNAME=user_database_anda
   DB_PASSWORD=password_database_anda
   ```

5. **Migrate & Seed:**
   Jalankan perintah ini untuk membuat struktur tabel (menggunakan custom primary key & UUID) beserta data dummy produk untuk simulasi flash sale:
   ```bash
   php artisan migrate --seed
   ```

## 🛒 Task 1: Online Store API (Flash Sale)

API backend untuk menangani proses pembelian barang flash sale. Solusi ini menggunakan database transactions dan pessimistic locking untuk mencegah race condition dan memastikan stok (inventory) tidak pernah minus.

### Cara Menjalankan Server
Jalankan lokal server Laravel:
```bash
php artisan serve
```

### Endpoint Purchase
**URL:** `http://localhost:8000/api/purchase`

**Method:** `POST`

**Headers:** `Content-Type: application/json`, `Accept: application/json`

**Request Body:**  
*(Silakan ambil products_uuid dari database hasil seeder)*
```json
{
  "product_uuid": "masukkan-uuid-produk-dari-database",
  "quantity": 1
}
```

**Response Sukses (200 OK):**
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

### Cara Menjalankan Feature Test (Race Condition)
Aplikasi ini menyertakan pengujian otomatis (feature test) yang menggunakan concurrency untuk mensimulasikan kejadian race condition (20 request paralel berebut 10 stok barang).

Jalankan pengujian dengan perintah:
```bash
php artisan test --filter FlashSaleRaceConditionTest
```

## 🎮 Task 2: Hidden Item Game

Program CLI (Command Line Interface) yang dibuat menggunakan Laravel Artisan Command untuk mencari kemungkinan lokasi koordinat barang yang tersembunyi di dalam grid 2D.

### Cara Menjalankan
Gunakan perintah berikut di terminal Anda:
```bash
php artisan game:hidden-item
```

### Output Terminal
Program akan memproses pola pergerakan dan mencetak daftar koordinat yang valid beserta visualisasi grid-nya secara langsung di terminal:

```plaintext
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