<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    protected $table = 'category';

    protected $fillable = [
        'parent_category_id',
        'name_tc',
        'name_en',
        'name_sc',
        'bg_img_url',
        'status',
        'sort_order'
    ];

    const CREATED_AT = 'created_time';
    const UPDATED_AT = 'updated_time';
}
