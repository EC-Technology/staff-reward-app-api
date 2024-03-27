<?php

namespace App\Enum;

class ErrorCode {
    const HTTP_BAD_REQUEST = '400';
    const CAMPAIGN_QUOTA_FULL = '4001';
    const REQUEST_PARAMETER_MISSING_OR_INCORRECT = '4002';
    const CRM_MERCHANT_API_ERROR = '5001';
    const HTTP_INTERNAL_SERVER_ERROR = '500';
    const HTTP_UNAUTHORIZED = 401;

    const OBJECT_NOT_EXIST = '4000';
    const VOUCHER_EXPIRED = '4001';
    const VOUCHER_USED = '4002';


    const INVALID_ACTIVATION_CODE = '4003';
}
