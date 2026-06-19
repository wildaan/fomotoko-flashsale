<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigInteger('orders_id')->primary();
            $table->string('orders_uuid', 36)->unique();
            $table->string('orders_transaction_status', 50)->default('pending');
            $table->string('orders_status', 20)->default('active');
            $table->decimal('orders_total_amount', 12, 2);
            $table->timestamp('orders_created_at')->useCurrent();
            $table->timestamp('orders_updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};