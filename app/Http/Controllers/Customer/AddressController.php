<?php

namespace App\Http\Controllers\Customer;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Customer\Models\CustomerAddress;
use Modules\AreaCode\Models\AreaCode;
use App\Rules\CheckPhoneNumber;

class AddressController extends Controller
{
    public function index(){
        $customer = Auth::guard('customer')->user();
        $listAddress = CustomerAddress::where('customer_id', $customer->id)->get();


        return view('customer.address.index')->with([
            'addresses' => $listAddress,
            'customer'  => $customer
        ]);
    }

    public function create(){
        $customerName = Auth::guard('customer')->user()->name;
        return view('customer.address.create')->with(['name' => $customerName]);
    }

    public function show($id){
        $address = CustomerAddress::find($id);

        return view('customer.address.show')->with(['address' => $address]);
    }

    public function edit($id){
        $address = CustomerAddress::find($id);

        return view('customer.address.edit')->with(['address' => $address]);
    }

    public function store(Request $request){
        $data = $request->all();
        $this->validate($request,[
            'name' => 'required',
            'phone'=> ['required', new CheckPhoneNumber()],
            'address' => 'required'
        ],[
            'required' => 'Thông tin bắt buộc'
        ]);
        $data['customer_id'] = Auth::guard('customer')->user()->id;
        CustomerAddress::create($data);

        \Session::flash('flash_message','Thêm địa chỉ thành công');

        return redirect(route('address.index'));
    }

    public function update(Request $request, $id){
        $data = $request->all();
        $address = CustomerAddress::find($id);
        $address->update($data);

        \Session::flash('flash_message','Cập nhật địa chỉ thành công');

        return redirect(route('address.index'));
    }

    public function delete($id){
        CustomerAddress::destroy($id);
        \Session::flash('flash_message','Xóa địa chỉ thành công');
        return redirect(route('address.index'));
    }

    public function getPhone(Request $request){
        $id = $request->id;
        $address = CustomerAddress::find($id);
        return $address->phone;
    }

    public function addNewAddress(Request $request){
        $data = $request->all();

        /** @var \Illuminate\Validation\Validator $validator */
        $validator = \Validator::make(
            $data,
            [
                'customer_shipping_name' => 'required',
                'customer_shipping_phone'=> ['required', new CheckPhoneNumber()],
                'customer_shipping_address' => 'required',
                'provincial_id' => 'required',
                'district_id' => 'required',
                'ward_id' => 'required',
            ],
            [
                'required' => 'Thông tin bắt buộc',
            ]
        );

        $errors = [];

        if ($validator->fails()) {
            foreach ($validator->messages()->messages() as $att => $messages) {
                $errors[$att] = $messages[0];
            }
            return response()->json([
                'status' => 'invalid',
                'errors' => $errors,
            ]);
        }
        $attr = array();
        $attr['name'] = $data['customer_shipping_name'];
        $attr['phone'] = $data['customer_shipping_phone'];
        $attr['address'] = $data['customer_shipping_address'];
        $attr['customer_id'] = Auth::guard('customer')->user()->id;
        $attr['provincial_id'] = $data['provincial_id'];
        $attr['district_id'] = $data['district_id'];
        $attr['ward_id'] = $data['ward_id'];

        $address = CustomerAddress::create($attr);

        $provincialName = \DB::table('devvn_tinhthanhpho')->select('name')->where('matp',$data['provincial_id'])->first();
        $districtName = \DB::table('devvn_quanhuyen')->select('name')->where('maqh',$data['district_id'])->first();
        $addressFull = $data['customer_shipping_name'].' - ĐT: '.$data['customer_shipping_phone'].' - '.$data['customer_shipping_address'].', '.$districtName->name.', '.$provincialName->name;
        return response()->json([
            'status' => 'success',
            'address' => $addressFull,
            'id' => $address->id
        ]);
    }

    public function updateAjax(Request $request){
        $data = $request->all();

        /** @var \Illuminate\Validation\Validator $validator */
        $validator = \Validator::make(
            $data,
            [
                'customer_shipping_name' => 'required',
                'customer_shipping_phone'=> ['required', new CheckPhoneNumber()],
                'customer_shipping_address' => 'required',
                'provincial_id' => 'required',
                'district_id' => 'required',
                'ward_id' => 'required',
            ],
            [
                'required' => 'Thông tin bắt buộc',
            ]
        );

        $errors = [];

        if ($validator->fails()) {
            foreach ($validator->messages()->messages() as $att => $messages) {
                $errors[$att] = $messages[0];
            }
            return response()->json([
                'status' => 'invalid',
                'errors' => $errors,
            ]);
        }

        $attr = array();
        $attr['name'] = $data['customer_shipping_name'];
        $attr['phone'] = $data['customer_shipping_phone'];
        $attr['address'] = $data['customer_shipping_address'];
        $attr['customer_id'] = Auth::guard('customer')->user()->id;
        $attr['provincial_id'] = $data['provincial_id'];
        $attr['district_id'] = $data['district_id'];
        $attr['ward_id'] = $data['ward_id'];

        CustomerAddress::where('id',$data['addressId'])->update($attr);

        $provincialName = \DB::table('devvn_tinhthanhpho')->select('name')->where('matp',$data['provincial_id'])->first();
        $districtName = \DB::table('devvn_quanhuyen')->select('name')->where('maqh',$data['district_id'])->first();
        $addressFull = $data['customer_shipping_name'].' - ĐT: '.$data['customer_shipping_phone'].' - '.$data['customer_shipping_address'].', '.$districtName->name.', '.$provincialName->name;

        return response()->json([
            'status' => 'success',
            'id' => $data['addressId'],
            'address' => $addressFull,
        ]);
    }
}
