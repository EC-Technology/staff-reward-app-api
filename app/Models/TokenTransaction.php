<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TokenTransaction extends Model
{

    protected $table = 'token_transaction';

    protected $fillable = [
        'type',
        'user_id',
        'description',
        'transaction_time',
        'voucher_id',
        'product_id',
        'value'
    ];

    const CREATED_AT = 'created_time';
    const UPDATED_AT = 'updated_time';
}
