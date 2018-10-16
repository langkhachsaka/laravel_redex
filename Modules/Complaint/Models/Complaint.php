<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 11/05/2018
 * Time: 9:22 SA
 */

namespace Modules\Complaint\Models;

use Modules\Base\Models\BaseModel;
use Modules\Customer\Models\Customer;
use Modules\CustomerOrder\Models\CustomerOrder;
use Modules\CustomerOrder\Models\CustomerOrderItem;
use Modules\LadingCode\Models\LadingCode;
use Modules\User\Models\User;
use Modules\VerifyLadingCode\Models\VerifyCustomerOrderItem;

class Complaint extends BaseModel
{
    const STATUS_PENDING = 0;
    const STATUS_ADMIN_PROCESSED = 1;
    const STATUS_CUSTOMER_SERVICE_PROCESSED = 2;
    const STATUS_ORDERING_OFFICER_PROCESSED = 3;

    const CASE_ERROR_SIZE = 1;
    const CASE_ERROR_COLLOR = 2;
    const CASE_ERROR_PRODUCT = 3;
    const CASE_ERROR_INADEQUATE_PRODUCT =4;


    protected $fillable = [
        'lading_code',
        'status',
        'error_size',
        'error_collor',
        'error_product',
        'inadequate_product',
        'comment_error_size',
        'comment_error_collor',
        'comment_error_product',
        'comment_inadequate_product',
        'customer_order_item_id',
        'performer_id',
        'customer_id',
    ];

    protected $appends = [
        'status_name',
    ];

    public function ordertable()
    {
        return $this->morphTo();
    }

    public function complaintHistories()
    {
        return $this->hasMany(ComplaintHistory::class);
    }

    public function verifyCustomerOrderItem()
    {
        return $this->belongsTo(VerifyCustomerOrderItem::class,'customer_order_item_id','customer_order_item_id');
    }

    public function customerOrderItem()
    {
        return $this->belongsTo(CustomerOrderItem::class,'customer_order_item_id','id');
    }

    public function caseComplaint()
    {
        return $this->hasMany(CaseComplaint::class,'complaint_id','id');
    }

    public function ladingCode()
    {
        return $this->belongsTo(LadingCode::class,'lading_code','code');
    }

    public function userPerformer()
    {
        return $this->belongsTo(User::class,'performer_id','id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getStatusNameAttribute()
    {
        $status = [
            self::STATUS_PENDING => 'Chờ xử lý',
            self::STATUS_ADMIN_PROCESSED => 'Admin đã duyệt',
            self::STATUS_CUSTOMER_SERVICE_PROCESSED => 'NVCSKH đã xử lý',
            self::STATUS_ORDERING_OFFICER_PROCESSED => 'NV Order đã xử lý'
        ];

        return array_get($status, $this->status);
    }

    public function getFileReportNameAttribute()
    {
        return $this->file_report_path ? basename($this->file_report_path) : null;
    }

    public function getFileReportLinkDownloadAttribute()
    {
        return $this->file_report_path ? asset('/storage/' . $this->file_report_path) : null;
    }

    public function getSolutionNameAttribute()
    {
        $solutions = [
            1 => 'Shop gửi bổ sung hàng',
            2 => 'Shop hết hàng và hoàn tiền',
            3 => 'Hàng được gửi trả lại shop và chờ hoàn tiền',
            11 => 'Trả hàng lại cho shop và hoàn tiền',
            12 => 'Khách hàng chấp nhận lấy hàng và shop bồi thường 1 khoản tiền',
            21 => 'Được miễn toàn bộ cước và hoàn lại tiền hàng của sản phẩm (có mua bảo hiểm)',
            22 => 'Được miễn giảm 1 phần cước vận chuyển (không mua bảo hiểm)',
            23 => 'Không được miễn giảm 1 phần cước vận chuyển (không mua bảo hiểm)'
        ];

        return isset($solutions[$this->solution]) ? $solutions[$this->solution] : null;
    }

    public function getOrderTypeAttribute()
    {
        return $this->ordertable_type == CustomerOrder::class ? 'Đơn hàng' : 'Đơn hàng vận chuyển';
    }

}