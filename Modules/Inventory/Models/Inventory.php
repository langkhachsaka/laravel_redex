<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 03/05/2018
 * Time: 4:07 CH
 */

namespace Modules\Inventory\Models;

use Modules\Base\Models\BaseModel;
use Modules\Shop\Models\Shop;

class Inventory extends BaseModel
{
    protected $fillable = [
        'date_receiving',
        'invoice_code',
        'bill_of_lading_code',
        'reason',
        'description',
        'note',
        'shop_id',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
