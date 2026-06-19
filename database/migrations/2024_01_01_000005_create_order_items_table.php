<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->bigInteger('order_items_id')->primary();
            $table->string('order_items_uuid', 36)->unique();
            $table->string('order_items_orders_uuid', 36);
            $table->string('order_items_products_uuid', 36);
            $table->integer('order_items_quantity');
            $table->decimal('order_items_price', 12, 2);
            $table->string('order_items_status', 20)->default('active');
            $table->timestamp('order_items_created_at')->useCurrent();
            $table->timestamp('order_items_updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('order_items_orders_uuid')
                  ->references('orders_uuid')
                  ->on('orders')
                  ->onDelete('cascade');
                  
            $table->foreign('order_items_products_uuid')
                  ->references('products_uuid')
                  ->on('products')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};