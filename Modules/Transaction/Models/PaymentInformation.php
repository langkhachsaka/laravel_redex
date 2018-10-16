<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 30/05/2018
 * Time: 10:17 SA
 */

namespace Modules\Transaction\Models;

use Modules\Base\Models\BaseModel;
use Modules\User\Models\User;

class PaymentInformation extends BaseModel
{
    const TYPE_ADDRESS = 0;
    const TYPE_SHIPPING_FEE = 1;
    const TYPE_AMOUNT_ORDER = 2;

    protected $fillable = [
        'transaction_id',
        'type',
        'data',
        'order_id',
        'total_amount'
    ];

    public function transaction(){
        return $this->belongsTo(Transaction::class);
    }
}
