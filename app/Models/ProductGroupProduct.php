<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductGroupProduct extends Model
{

    protected $table = 'product_group_product';

    protected $fillable = [
        'product_group_id',
        'product_id',
        'sort_order',
    ];

    const CREATED_AT = 'created_time';
    const UPDATED_AT = 'updated_time';
}
