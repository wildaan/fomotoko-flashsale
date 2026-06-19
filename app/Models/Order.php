<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'orders_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'orders_id',
        'orders_uuid',
        'orders_transaction_status',
        'orders_status',
        'orders_total_amount',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->orders_id)) {
                $model->orders_id = random_int(1000000000, 999999999999999);
            }
            if (empty($model->orders_uuid)) {
                $model->orders_uuid = Str::uuid()->toString();
            }
        });
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_items_orders_uuid', 'orders_uuid');
    }
}
