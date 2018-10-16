<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Customer\Models\Customer;
use Modules\Customer\Models\CustomerAddress;
use Modules\AreaCode\Models\AreaCode;
use Auth;
use Validator;
use App\Rules\CheckPhoneNumber;

class InfoController extends Controller
{
    //
    public function info(){
        $customerId = Auth::guard('customer')->user()->id;
        $customer = Customer::with('customerAddresses')->where('id', $customerId)->first();
        $provincials = \DB::table('devvn_tinhthanhpho')->get();
        return view('customer.info')->with([
            'customer' => $customer,
            'provincials' => $provincials
        ]);
    }

    public function update(Request $request){
        $data = $request->all();
        $this->validate($request,[
            'name'     => 'required|max:255|regex:/^[\pL\s\-]+$/u',
            'email'    => 'required|unique:customers,email,'.$data['id'],
            'phone'    => ['required','min:10', new CheckPhoneNumber()],
            'address'  => 'required',
            'provincial_id' => 'required',
            'district_id' => 'required',
            'ward_id' => 'required',
        ],
        [
            'required' => 'Thông tin bắt buộc',
            'name.max' => 'Họ tên có tối đa :max kí tự',
            'phone.min' => 'Số điện thoại phải có ít nhất 10 kí tự',
            'email.unique' => 'Email đã tồn tại',
            'regex' => 'Không được nhập kí tự đặc biệt'
        ]);
        $attr = array();
        $customer = Customer::find($data['id']);
        $attr['name'] = $data['name'];
        $attr['email'] = $data['email'];
        $customer->update($attr);

        $attrAddress = array();
        $attrAddress['name'] = $data['name'];
        $attrAddress['phone'] = $data['phone'];
        $attrAddress['address'] = $data['address'];
        $attrAddress['provincial_id'] = $data['provincial_id'];
        $attrAddress['district_id'] = $data['district_id'];
        $attrAddress['ward_id'] = $data['ward_id'];

        $customerAddress = CustomerAddress::where('customer_id',$data['id'])->where('is_default', 1)->first();
        if($customerAddress){
            $customerAddress->update($attrAddress);
        }else{
            $attrAddress['customer_id'] = $data['id'];
            $attrAddress['is_default'] = 1;
            CustomerAddress::create($attrAddress);
        }

        \Session::flash('info_update_message','Cập nhật thông tin thành công');
        return redirect(route('customer.info'));
    }
}
