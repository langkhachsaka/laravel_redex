<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\BillOfLading\Models\BillOfLading;
use Modules\CourierCompany\Models\CourierCompany;
use Modules\Complaint\Models\Complaint;
use Modules\Customer\Models\CustomerAddress;
use Modules\Notification\Models\BillOfLadingNotification;
use Modules\LadingCode\Models\LadingCode;
use Auth;
use Validator;
use App\Rules\CheckPhoneNumber;

class OrderTransportController extends Controller
{
    public function index(Request $request){
        $customerId = Auth::guard('customer')->user()->id;
        $data= $request->all();
        if($data && !isset($data['page'])){
            $lists = BillOfLading::with('courierCompany')
                ->where('customer_id',$customerId)
                ->filterWhere(\DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y")'),'=', $data['created_at'])
                ->filterWhere(\DB::raw('DATE_FORMAT(end_date, "%d-%m-%Y")'),'=',$data['end_date'])
                ->filterWhere('courier_company_id','=',$data['courier_company_id'])
                ->filterWhere('status','=',$data['status'])
                ->paginate(10);
        }else{
            $lists = BillOfLading::with('courierCompany')->where('customer_id',$customerId)->paginate(10);
        }
        $address = CustomerAddress::where('customer_id', $customerId)->where('is_default',1)->first();
        $courier_companies = CourierCompany::get();
        $status = [
            BillOfLading::STATUS_PENDING => 'Chờ duyệt',
            BillOfLading::STATUS_APPROVED => 'Đã duyệt',
            BillOfLading::STATUS_DELIVERY => 'Đang giao hàng',
            BillOfLading::STATUS_COMPLAINT => 'Khiếu nại',
            BillOfLading::STATUS_FINISHED => 'Hoàn thành',
        ];
        return view('customer.order-transport.index')->with([
            'orders' => $lists,
            'address' => $address,
            'courier_companies' => $courier_companies,
            'status' => $status
        ]);
    }

    public function show($id){
        $order = BillOfLading::with('ladingCodes')->find($id);
        if($order->ladingCodes()->first()){
            $ladingCode = $order->ladingCodes()->first()->code;
        }
        $courier_companies = CourierCompany::get();
        return view('customer.order-transport.view')->with([
            'order' => $order,
            'courier_companies' => $courier_companies,
            'ladingCode' => $ladingCode ?? ''
        ]);
    }

    public function edit($id){
        $order = BillOfLading::with('ladingCodes')->find($id);
        if($order->ladingCodes()->first()){
            $ladingCode = $order->ladingCodes()->first()->code;
        }
        $courier_companies = CourierCompany::get();
        return view('customer.order-transport.update')->with([
            'order' => $order,
            'courier_companies' => $courier_companies,
            'ladingCode' => $ladingCode ?? ''
        ]);
    }

    public function store(Request $request){
        $data = $request->all();
        $message = array(
            'required' => 'Thông tin bắt buộc',
            'min' => 'Số điện thoại phải có ít nhất :min kí tự'
        );
        $this->validate($request, [
            'customer_billing_name' => 'required',
            'customer_billing_address' => 'required',
            'customer_billing_phone' => ['required','min:10', new CheckPhoneNumber()],
            'customer_shipping_name' => 'required',
            'customer_shipping_address' => 'required',
            'customer_shipping_phone' => ['required','min:10', new CheckPhoneNumber()],
            'courier_company_id'  => 'required',
            'file' => 'required'
        ],$message);

        // upload file
        $file = $request->file('file');
        $path = $file->storeAs(
            'upload/bill-of-lading/' . date('Y/m/d'),
            rand(10000000, 999999999) . '-' . $file->getClientOriginalName()
        );
        $arrPath = explode('/', $path);

        $attr = array();
        $attr['file_path'] = 'upload/bill-of-lading/' . date('Y/m/d') . '/' . last($arrPath);
        $attr['customer_id'] = Auth::guard('customer')->user()->id;
        $attr['customer_billing_name'] = $data['customer_billing_name'];
        $attr['customer_billing_address'] = $data['customer_billing_address'];
        $attr['customer_billing_phone'] = $data['customer_billing_phone'];
        $attr['customer_shipping_name'] = $data['customer_shipping_phone'];
        $attr['customer_shipping_address'] = $data['customer_shipping_address'];
        $attr['customer_shipping_phone'] = $data['customer_shipping_phone'];
        $attr['courier_company_id'] = $data['courier_company_id'];
        $attr['status'] = 0;

        $billOfLading = BillOfLading::create($attr);

        if(!is_null($data['bill_of_lading_code'])){
            $attrCode = array();
            $attrCode['ladingcodetable_id'] = $billOfLading->id;
            $attrCode['ladingcodetable_type'] = BillOfLading::class;
            $attrCode['code'] = $data['bill_of_lading_code'];
            LadingCode::create($attrCode);
        }

        // BEGIN BILL OF LADING'S NOTIFICATION
        BillOfLadingNotification::newBillOffLadingByCustomer($billOfLading->id, Auth::guard('customer')->user()->id);
        // END

        \Session::flash('flash_message','Thêm vận đơn thành công');

        return redirect(route('order-transport.index'));
    }

    public function update(Request $request, $id){
        $data = $request->all();
        $message = array(
            'required' => 'Thông tin bắt buộc',
            'min' => 'Số điện thoại phải có ít nhất :min kí tự'
        );
        $this->validate($request, [
            'customer_billing_name' => 'required',
            'customer_billing_address' => 'required',
            'customer_billing_phone' => ['required','min:10', new CheckPhoneNumber()],
            'customer_shipping_name' => 'required',
            'customer_shipping_address' => 'required',
            'customer_shipping_phone' => ['required','min:10', new CheckPhoneNumber()],
            'courier_company_id'  => 'required',
        ],$message);
        $attr = array();
        $attr['customer_billing_name'] = $data['customer_billing_name'];
        $attr['customer_billing_address'] = $data['customer_billing_address'];
        $attr['customer_billing_phone'] = $data['customer_billing_phone'];
        $attr['customer_shipping_name'] = $data['customer_shipping_name'];
        $attr['customer_shipping_address'] = $data['customer_shipping_address'];
        $attr['customer_shipping_phone'] = $data['customer_shipping_phone'];
        $attr['courier_company_id'] = $data['courier_company_id'];
        if($request->hasFile('file')){
            $file = $request->file('file');
            $path = $file->storeAs(
                'upload/bill-of-lading/' . date('Y/m/d'),
                rand(10000000, 999999999) . '-' . $file->getClientOriginalName()
            );
            $arrPath = explode('/', $path);
            $attr['file_path'] = 'upload/bill-of-lading/' . date('Y/m/d') . '/' . last($arrPath);
        }
        $bill_of_lading = BillOfLading::with('ladingCodes')->find($id);
        $bill_of_lading->update($attr);

        if(!is_null($bill_of_lading->ladingCodes->first()) && !is_null($data['bill_of_lading_code'])){
            LadingCode::where('id',$bill_of_lading->ladingCodes->first()->id)->update(['code'=>$data['bill_of_lading_code']]);
        }elseif(!is_null($bill_of_lading->ladingCodes->first()) && is_null($data['bill_of_lading_code'])){
            LadingCode::destroy($bill_of_lading->ladingCodes->first()->id);
        }elseif(is_null($bill_of_lading->ladingCodes->first()) && !is_null($data['bill_of_lading_code'])){
            LadingCode::create([
                'ladingcodetable_id' => $id,
                'ladingcodetable_type' => BillOfLading::class,
                'code' => $data['bill_of_lading_code']
            ]);

        }

        \Session::flash('flash_message','Cập nhật vận đơn thành công');

        return redirect(route('order-transport.edit',$id));
    }

    public function delete($id){

        /** @var BillOfLading $billOfLading -- for notification -- trinq */
        $billOfLading = BillOfLading::findOrFail($id);

        BillOfLading::destroy($id);

        // BEGIN NOTIFICATION
        BillOfLadingNotification::deleteBillOfLadingByCustomer($billOfLading->id, $billOfLading->seller_id, Auth::guard('customer')->user()->id);
        // END

        \Session::flash('flash_message','Xóa vận đơn thành công');

        return redirect(route('order-transport.index'));
    }

    public function getComplaint($id){
        $complaint = Complaint::where('ordertable_id',$id)->where('ordertable_type',BillOfLading::class)->first();

        return redirect(route('complaint.view',$complaint->id));
    }
}
