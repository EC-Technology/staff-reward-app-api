<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_ASSIGNED = 2;
    const STATUS_USED = 3;
    const STATUS_EXPIRED = 4;

    protected $table = 'voucher';

    protected $fillable = [
        'merchant_id',
        'product_id',
        'owner_user_id',
        'code',
        'code_external',
        'code_reference',
        'use_begin_time',
        'use_expiry_time',
        'assign_time',
        'used_time',
        'status',
    ];

    const CREATED_AT = 'created_time';
    const UPDATED_AT = 'updated_time';
}
