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
use Modules\User\Models\User;

class CaseComplaint extends BaseModel
{
    const STATUS_PENDING = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_FINISHED = 2;

    protected $fillable = [
        'complaint_id',
        'case',
        'solution',
        'customer_comment',
        'customer_service_comment',
        'redex_comment',
        'redex_solution',
        'order_office_solution',
        'money_shop_return',
        'date_return_money',
        'add_lading_code',
        'date_of_delivery',
        'sum_weight_back',
        'sum_weight_delivery',
        'total_customer_pay',
        'ship_inland_fee',
        'shop_pay',
        'fee_ship_vn_cn',
        'redex_support',
        'note',
    ];

    protected $appends = [
        'case_name',
        'solution_name',
    ];

    public function ordertable()
    {
        return $this->morphTo();
    }

    public function complaintHistories()
    {
        return $this->hasMany(ComplaintHistory::class);
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
            self::STATUS_PROCESSING => 'Đang xử lý',
            self::STATUS_FINISHED => 'Đã xử lý'
        ];

        return array_get($status, $this->status);
    }
    public function getCaseNameAttribute()
    {
        $cases = [
            1 => 'Sai cỡ',
            2 => 'Sai màu',
            3 => 'Sai hàng',
            4 => 'Thiếu hàng',
        ];

        return array_get($cases, $this->case);
    }
    public function getSolutionNameAttribute()
    {
        $solutions = [
            1 => 'Nhận hàng và bồi hoàn tiền từ Shop',
            2 => 'Trả hàng lại cho Shop',
            3 => 'Shop bổ sung hàng thiếu',
            4 => 'Shop hoàn tiền hàng thiếu',
        ];

        return array_get($solutions, $this->solution);
    }

    public function getFileReportNameAttribute()
    {
        return $this->file_report_path ? basename($this->file_report_path) : null;
    }

    public function getFileReportLinkDownloadAttribute()
    {
        return $this->file_report_path ? asset('/storage/' . $this->file_report_path) : null;
    }

   /* public function getSolutionNameAttribute()
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
    }*/

    public function getOrderTypeAttribute()
    {
        return $this->ordertable_type == CustomerOrder::class ? 'Đơn hàng' : 'Đơn hàng vận chuyển';
    }

}