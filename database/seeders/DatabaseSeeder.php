<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $productUuid = Str::uuid()->toString();
        DB::table('products')->insert([
            'products_id' => 1,
            'products_uuid' => $productUuid,
            'products_name' => 'Super Flash Sale Gadget',
            'products_description' => 'Gadget flagship terbaru dengan harga miring khusus flash sale.',
            'products_normal_price' => 10000000.00,
            'products_flashsale_price' => 1000000.00,
            'products_status' => 'active',
            'products_created_at' => $now,
            'products_updated_at' => $now,
        ]);

        DB::table('inventories')->insert([
            'inventories_id' => 1,
            'inventories_uuid' => Str::uuid()->toString(),
            'inventories_products_uuid' => $productUuid,
            'inventories_quantity' => 100,
            'inventories_status' => 'active',
            'inventories_updated_at' => $now,
        ]);

        $orderUuid = Str::uuid()->toString();
        DB::table('orders')->insert([
            'orders_id' => 1,
            'orders_uuid' => $orderUuid,
            'orders_transaction_status' => 'success',
            'orders_status' => 'active',
            'orders_total_amount' => 1000000.00,
            'orders_created_at' => $now,
            'orders_updated_at' => $now,
        ]);

        DB::table('order_items')->insert([
            'order_items_id' => 1,
            'order_items_uuid' => Str::uuid()->toString(),
            'order_items_orders_uuid' => $orderUuid,
            'order_items_products_uuid' => $productUuid,
            'order_items_quantity' => 1,
            'order_items_price' => 1000000.00,
            'order_items_status' => 'active',
            'order_items_created_at' => $now,
            'order_items_updated_at' => $now,
        ]);
    }
}
