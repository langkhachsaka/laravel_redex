<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 11/05/2018
 * Time: 9:34 SA
 */

namespace Modules\Complaint\Models;

use Modules\Base\Models\BaseModel;
use Modules\User\Models\User;

class ComplaintHistory extends BaseModel
{
    protected $fillable = [
        'complaint_id',
        'user_id',
        'content'
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}