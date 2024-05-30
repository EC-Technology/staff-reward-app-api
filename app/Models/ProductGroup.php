<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductGroup extends Model
{

    protected $table = 'product_group';

    protected $fillable = [
        'name_tc',
        'name_en',
        'name_sc',
        'tag_line_color_code',
        'display_location',
        'sort_order'
    ];

    const CREATED_AT = 'created_time';
    const UPDATED_AT = 'updated_time';
}
