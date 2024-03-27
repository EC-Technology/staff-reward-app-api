<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivationCode extends Model
{
    use HasFactory;

    const STATUS_USED = 2;
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    protected $table = 'activation_code';

    protected $fillable = [
        'company_user_id',
        'used_time',
        'status',
    ];



    const CREATED_AT = 'created_time';
    const UPDATED_AT = 'updated_time';
}
