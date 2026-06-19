<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigInteger('products_id')->primary();
            $table->string('products_uuid', 36)->unique();
            $table->string('products_name');
            $table->text('products_description')->nullable();
            $table->decimal('products_normal_price', 12, 2);
            $table->decimal('products_flashsale_price', 12, 2);
            $table->string('products_status', 20)->default('active');
            $table->timestamp('products_created_at')->useCurrent();
            $table->timestamp('products_updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};