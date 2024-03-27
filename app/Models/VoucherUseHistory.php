<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherUseHistory extends Model
{
    use HasFactory;

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    protected $table = 'voucher_use_history';

    protected $fillable = [
        'merchant_id',
        'merchant_user_id',
        'product_id',
        'voucher_id',
        'internal_remarks',
        'reference_number',
        'used_time',
        'status',
    ];

    const CREATED_AT = 'created_time';
    const UPDATED_AT = 'updated_time';
}
