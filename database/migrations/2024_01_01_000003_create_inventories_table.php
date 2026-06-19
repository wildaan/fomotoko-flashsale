<?php 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->bigInteger('inventories_id')->primary();
            $table->string('inventories_uuid', 36)->unique();
            $table->string('inventories_products_uuid', 36);
            $table->integer('inventories_quantity');
            $table->string('inventories_status', 20)->default('active');
            $table->timestamp('inventories_updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('inventories_products_uuid')
                  ->references('products_uuid')
                  ->on('products')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};