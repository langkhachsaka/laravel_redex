<?php

namespace Modules\Withdrawal\Models;

use Modules\Base\Models\BaseModel;

class Withdrawal extends BaseModel
{
    protected $fillable = [
        'name',
        'bank',
        'account_number',
        'money_withdrawal',
        'branch',
        'content'
    ];
}
