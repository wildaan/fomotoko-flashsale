<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Inventory extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'inventories_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'inventories_id',
        'inventories_uuid',
        'inventories_products_uuid',
        'inventories_quantity',
        'inventories_status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->inventories_id)) {
                $model->inventories_id = random_int(1000000000, 999999999999999);
            }
            if (empty($model->inventories_uuid)) {
                $model->inventories_uuid = Str::uuid()->toString();
            }
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'inventories_products_uuid', 'products_uuid');
    }
}
