<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\CustomerOrder\Models\CustomerOrder;
use Modules\BillOfLading\Models\BillOfLading;
use Modules\Complaint\Models\Complaint;
use Auth;
use Modules\Task\Models\ComplaintTask;
use Carbon\Carbon;
use Modules\Notification\Models\ComplaintNotification;

class ComplaintController extends Controller
{
    public function index(Request $request){
        $data= $request->all();
        $customerId = Auth::guard('customer')->user()->id;
        if($data && !isset($data['page'])){
            $list = Complaint::where('customer_id', $customerId)
                ->filterWhere(\DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y")'),'=', $data['created_at'])
                ->filterWhere('status','=',$data['status'])
                ->paginate(10);
        }else{
            $list = Complaint::where('customer_id',$customerId)->paginate(10);
        }
        foreach($list as $complaint){
            $complaint['status_name'] = $complaint->status_name;
        }
        $status = [
            Complaint::STATUS_PENDING => 'Chờ xử lý',
            Complaint::STATUS_PROCESSING => 'Đang xử lý',
            Complaint::STATUS_FINISHED => 'Đã xử lý'
        ];

        return view('customer.complaint.index')->with([
            'complaints' => $list,
            'status' => $status
        ]);
    }

    public function show($id){
        $complaint = Complaint::find($id);

        return view('customer.complaint.view')->with([
            'complaint' => $complaint
        ]);
    }

    public function store(Request $request){
        $current_date = date('d-m-Y');
        $this->validate($request,[
            'title' => 'required',
            'content' => 'required',
            'date_end_expected' => 'after_or_equal:'.$current_date
        ],
        [
            'required' => 'Thông tin bắt buộc',
            'after_or_equal'=> 'Ngày phải lớn hơn hoặc bằng '.$current_date
        ]);
        $data = $request->all();
        $attr = array();
        $attr['ordertable_id'] = $data['ordertable_id'];
        if($data['ordertable_type'] == 'order'){
            $attr['ordertable_type'] = CustomerOrder::class;
            $order = CustomerOrder::find($attr['ordertable_id']);
            $attr['user_id'] = $order['seller_id'];
            $order->update(['status' => CustomerOrder::STATUS_COMPLAINT]);
        }else{
            $attr['ordertable_type'] = BillOfLading::class;
            $order = BillOfLading::find($attr['ordertable_id']);
            $attr['user_id'] = $order['seller_id'];
            $order->update(['status' => BillOfLading::STATUS_COMPLAINT]);
        }
        $attr['title'] = $data['title'];
        $attr['content'] = $data['content'];
        $attr['status'] = 0;
        $attr['customer_id'] = Auth::guard('customer')->user()->id;
        $attr['date_end_expected'] = Carbon::createFromFormat('d-m-Y', $data['date_end_expected'])->toDateTimeString();

        $complaint = Complaint::create($attr);

        //AUTO CHANGE STATUS OF TASK.
        ComplaintTask::newComplaintByCustomer($complaint, Auth::guard('customer')->user()->name);
        //END PROCESS CHANGE STATUS OF TASK.

        // BEGIN NOTIFICATION
        ComplaintNotification::newComplaintByCustomer(
            $complaint->id,
            $complaint->ordertable_id,
            $complaint->ordertable_type,
            $complaint->user_id,
            Auth::guard('customer')->user()->id
        );
        ComplaintNotification::assignComplaint($complaint->id, $complaint->ordertable_id, $complaint->ordertable_type, $complaint->user_id);
        // END

        \Session::flash('flash_message','Gửi khiếu nại thành công');

        if($data['ordertable_type'] == 'order'){
            return redirect(route('order.view',$data['ordertable_id']));
        }else{
            return redirect(route('order-transport.view',$data['ordertable_id']));
        }
    }

    public function delete($id){
        /** @var Complaint $complaint -- for notification -- trinq */
        $complaint = Complaint::findOrFail($id);

        Complaint::destroy($id);

        //AUTO CHANGE STATUS OF TASK.
        ComplaintTask::deleteComplaintByCustomer($complaint->ordertable_id,Auth::guard('customer')->user()->name);
        //END PROCESS CHANGE STATUS OF TASK.

        // BEGIN NOTIFICATION
        ComplaintNotification::deleteComplaintByCustomer($complaint->id,
            $complaint->ordertable_id,
            $complaint->ordertable_type,
            $complaint->user_id,
            Auth::guard('customer')->user()->id
        );
        // END

        \Session::flash('flash_message','Xóa khiếu nại thành công');

        return redirect(route('complaint.index'));
    }
}
