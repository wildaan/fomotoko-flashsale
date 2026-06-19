<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrderItem extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'order_items_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'order_items_id',
        'order_items_uuid',
        'order_items_orders_uuid',
        'order_items_products_uuid',
        'order_items_quantity',
        'order_items_price',
        'order_items_status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->order_items_id)) {
                $model->order_items_id = random_int(1000000000, 999999999999999);
            }
            if (empty($model->order_items_uuid)) {
                $model->order_items_uuid = Str::uuid()->toString();
            }
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_items_orders_uuid', 'orders_uuid');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'order_items_products_uuid', 'products_uuid');
    }
}
