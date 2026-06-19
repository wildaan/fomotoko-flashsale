<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Str;
use Tests\TestCase;

class FlashSaleRaceConditionTest extends TestCase
{
    private string $testProductUuid;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testProductUuid = Str::uuid()->toString();

        Product::create([
            'products_id' => rand(10000, 99999), 
            'products_uuid' => $this->testProductUuid,
            'products_name' => 'Flash Sale Flagship Device',
            'products_normal_price' => 15000000.00,
            'products_flashsale_price' => 1000000.00,
            'products_status' => 'active',
        ]);

        Inventory::create([
            'inventories_id' => rand(10000, 99999),
            'inventories_products_uuid' => $this->testProductUuid,
            'inventories_quantity' => 10,
            'inventories_status' => 'active',
        ]);
    }

    protected function tearDown(): void
    {
        $orderUuids = OrderItem::where('order_items_products_uuid', $this->testProductUuid)
            ->pluck('order_items_orders_uuid');
            
        Order::whereIn('orders_uuid', $orderUuids)->delete();
        Product::where('products_uuid', $this->testProductUuid)->delete();

        parent::tearDown();
    }

    public function test_it_handles_race_conditions_and_prevents_negative_inventory()
    {
        $initialOrderCount = Order::count();
        $concurrentRequests = 20;
        
        $productUuid = $this->testProductUuid; 

        $tasks = [];

        for ($i = 0; $i < $concurrentRequests; $i++) {
            $tasks[] = function () use ($productUuid) {
                $request = Request::create('/api/purchase', 'POST', [
                    'product_uuid' => $productUuid,
                    'quantity' => 1,
                ]);
                
                $kernel = app()->make(\Illuminate\Contracts\Http\Kernel::class);
                $response = $kernel->handle($request);
                
                return $response->getStatusCode();
            };
        }

        $results = Concurrency::run($tasks);

        $successCount = collect($results)->filter(fn ($status) => $status === 200)->count();
        $failCount = collect($results)->filter(fn ($status) => $status === 400)->count();

        $this->assertEquals(10, $successCount, 'Exactly 10 users should successfully purchase the item.');
        $this->assertEquals(10, $failCount, 'Exactly 10 users should fail due to out-of-stock validation.');

        $finalInventory = Inventory::where('inventories_products_uuid', $productUuid)->first();
        $this->assertEquals(0, $finalInventory->inventories_quantity, 'Inventory quantity must never fall below zero.');

        $totalNewOrders = Order::count() - $initialOrderCount;
        $this->assertEquals(10, $totalNewOrders, 'Exactly 10 orders should be created in the database.');
    }
}