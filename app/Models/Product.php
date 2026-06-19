<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'products_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'products_id',
        'products_uuid',
        'products_name',
        'products_description',
        'products_normal_price',
        'products_flashsale_price',
        'products_status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->products_id)) {
                $model->products_id = random_int(1000000000, 999999999999999);
            }
            if (empty($model->products_uuid)) {
                $model->products_uuid = Str::uuid()->toString();
            }
        });
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'inventories_products_uuid', 'products_uuid');
    }
}
