<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_DRAFT = 2;

    protected $table = 'product';

    protected $fillable = [
        'merchant_id',
        'category_id',
        'name',
        'sku',
        'description',
        'image_url',
        'original_price',
        'discounted_price',
        'status',
        'start_time',
        'end_time',
        'display_start_time',
        'display_end_time'
    ];

    const CREATED_AT = 'created_time';
    const UPDATED_AT = 'updated_time';
}
