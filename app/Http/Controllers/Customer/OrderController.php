<?php

namespace App\Http\Controllers\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Auth;
use Modules\Complaint\Models\Complaint;
use Modules\Customer\Models\Customer;
use Modules\Customer\Models\Provincial;
use Modules\Customer\Models\District;
use Modules\Customer\Models\Ward;
use Modules\Notification\Models\CustomerOrderNotification;
use Validator;
use Modules\CustomerOrder\Models\CustomerOrder;
use Modules\CustomerOrder\Models\CustomerOrderItem;
use Modules\Customer\Models\CustomerAddress;
use Modules\Image\Models\Image;
use Modules\Rate\Models\Rate;
use Modules\AreaCode\Models\AreaCode;
use Modules\Transaction\Models\Transaction;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Excel;
use Storage;
use Modules\Task\Models\CustomerOrderTask;
use Modules\Shop\Models\Shop;
use Modules\BillCode\Models\BillCode;
use App\Helpers\Helper;

class OrderController extends Controller
{
    protected $helper;
    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    public function index(Request $request){
        $customer = Auth::guard('customer')->user();
        $data= $request->all();

        // danh sách tất cả đơn hàng
        if($data && !isset($data['page'])){
            $list = CustomerOrder::with("customerOrderItems",'seller')
                ->where('customer_id', $customer->id)
                ->filterWhere(\DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y")'),'=', $data['created_at'])
                ->filterWhere(\DB::raw('DATE_FORMAT(end_date, "%d-%m-%Y")'),'=',$data['end_date'])
                ->filterWhere('status','=',$data['status'])
                ->orderBy('id','desc')
                ->paginate(10);
        }else{
            $list = CustomerOrder::with("customerOrderItems",'seller')->where('customer_id', $customer->id)->orderBy('id','desc')->paginate(10);
        }
        foreach ($list as &$order){
            foreach($order->customerOrderItems as $customerOrderItem){
                $order->total += $customerOrderItem->total_price * $customer->order_rate;
            }
            $order['status_name'] = $order->status_name;
            $order->total_lading_codes = $this->helper->getTotalLadingCodeByOrder($order->id);
            $order->package_tranfered = $this->helper->getTotalPackageTranfered($order->id);
            $order->package_wait_to_tranfer = $this->helper->getTotalPackageWaitingForTranfer($order->id);
            $order->package_need_pay = $order->package_wait_to_tranfer;
            $order->package_complaint = $this->helper->getTotalPackageComplaint($order->id);
        }

        // trạng thái đơn hàng
        $status = [
            CustomerOrder::STATUS_PENDING => 'Chờ duyệt',
            CustomerOrder::STATUS_APPROVED => 'Đã duyệt mua',
            CustomerOrder::STATUS_CANCELED => 'Đã huỷ',
            CustomerOrder::STATUS_DEPOSITED => 'Đã đặt cọc',
            CustomerOrder::STATUS_DELIVERY => 'Đang giao hàng',
            CustomerOrder::STATUS_COMPLAINT => 'Khiếu nại',
            CustomerOrder::STATUS_FINISHED => 'Hoàn thành',
        ];

        return view('customer.order.index')->with([
            'orders' => $list,
            'status' => $status,
            'customer'=> Auth::guard('customer')->user(),
            'orderDeposit' => $this->helper->countTotalOrderDeposit(),
            'ladingCodePayment' => $this->helper->countLadingCodePayment()
        ]);
    }

    public function show($id){
        $fee = $this->helper->getTransportFee($id);
        $inlandShippingFee = $this->helper->getInlandShippingFee($id);
        $surcharge = 0;
        $discount = 0;
        $total = 0;
        $totalDeposit = 0;
        $order = CustomerOrder::with('customer','seller')->where('id', $id)->first();
        $order->total_lading_codes = $this->helper->getTotalLadingCodeByOrder($order->id);
        $order->package_tranfered = $this->helper->getTotalPackageTranfered($order->id);
        $order->package_wait_to_tranfer = $this->helper->getTotalPackageWaitingForTranfer($order->id) - $order->package_tranfered;
        $orderItems = CustomerOrderItem::with('images','shop')->where('customer_order_id',$id)->get();
        $shops = [];
        $billCodes = [];
        foreach ($orderItems as $item){
            $order->total_quantity += $item->quantity;
            $order->total_price += $item->total_price;
            foreach($item->images as &$image){
                $image->path = $this->url($image->path);
            }
            if(!is_null($item->shop)){
                $shops[$item->shop->id] = $item->shop;
            }
            if(!is_null($item->surcharge))$surcharge += $item->surcharge;
            if(!is_null($item->discount_customer_percent)){
                $discount += $item->total_price * $item->quantity * $item->discount_customer_percent / 100;
            }elseif(!is_null($item->discount_customer_price)){
                $discount += $item->discount_customer_price * $item->quantity;
            }
        }
        $shops['underfined'] = '';
        foreach ($shops as $key => &$item)
        {
             $billCode = BillCode::where('shop_id',$key)->where('customer_order_id',$id)->first();
            if($billCode){
                $billCodes[$key]['delivery_type'] = $billCode->delivery_type;
                $billCodes[$key]['insurance_type'] = $billCode->insurance_type;
                $billCodes[$key]['reinforced_type'] = $billCode->reinforced_type;
            }
        }

        $customer = Auth::guard('customer')->user();
        $totalPackage = $this->helper->getTotalLadingCodeByOrder($id);
        $totalPackageTranfered = $this->helper->getTotalPackageTranfered($id);

        $transaction = Transaction::where('transactiontable_id',$id)->where('transactiontable_type',CustomerOrder::class)->where('type',0)->get();
        foreach($transaction as $trans){
            $totalDeposit += $trans->money;
        }

        // tổng giá toàn đơn hàng
        if($inlandShippingFee !=0 && $fee !=0){
            $total = ($order->total_price + $surcharge - $discount + $inlandShippingFee) * $customer->order_rate + $fee;
        }

        return view('customer.order.view')->with([
            'order'   => $order,
            'orderItems' => $orderItems,
            'customer' => $customer,
            'shops' => $shops,
            'billCodes' => $billCodes,
            'totalPackage' => $totalPackage,
            'totalPackageTranfered' => $totalPackageTranfered,
            'totalDeposit' => $totalDeposit,
            'fee' => $fee,
            'inlandShippingFee' => $inlandShippingFee,
            'total' => $total,
            'totalDiscount' => $discount,
            'totalSurcharge' => $surcharge
        ]);
    }

    public function create(){
        $customer = Auth::guard('customer')->user();
        $address = CustomerAddress::where('customer_id',$customer->id)->where('is_default',1)->first();
        $shipping_addresses = CustomerAddress::with('provincial','district','ward')->where('customer_id',$customer->id)->get();
        $provincials = \DB::table('devvn_tinhthanhpho')->get();

        return view('customer.order.create')->with([
            'customer'=> $customer,
            'address' => $address,
            'shipping_addresses' => $shipping_addresses,
            'provincials' => $provincials
        ]);
    }

    public function edit($id){
        $order = CustomerOrder::with('customer')->where('id', $id)->first();
        $orderItems = CustomerOrderItem::with('images','shop')->where('customer_order_id',$id)->get();
        $shops = [];
        $billCodes = [];
        foreach($orderItems as &$item){
            $order->total_quantity += $item->quantity;
            $order->total_price += $item->total_price;
            foreach($item->images as &$image){
                $image->path = $this->url($image->path);
            }
            if(!is_null($item->shop)) {
                $shops[$item->shop->id] = $item->shop;
            }
        }
        $shops['underfined'] = '';
        foreach ($shops as $key => &$item)
        {
            $billCode = BillCode::where('shop_id',$key)->where('customer_order_id',$id)->first();
            if($billCode){
                $billCodes[$key]['delivery_type'] = $billCode->delivery_type;
                $billCodes[$key]['insurance_type'] = $billCode->insurance_type;
                $billCodes[$key]['reinforced_type'] = $billCode->reinforced_type;
            }
        }
        $customer = Auth::guard('customer')->user();
        $shipping_addresses = CustomerAddress::with('provincial','district','ward')->where('customer_id',$customer->id)->get();


        return view('customer.order.edit')->with([
            'order' => $order,
            'orderItems' => $orderItems,
            'rate' => $customer->order_rate,
            'shipping_addresses' => $shipping_addresses,
            'shops' => $shops,
            'billCodes' => $billCodes,
        ]);
    }

    public function store(Request $request){
        $customer = Auth::guard('customer')->user();
        $data = $request->all();
        $errors = [];
        if(!isset($data['shipping_address'])){
            $validator = \Validator::make(
                $data,
                [
                    'customer_billing_name' => 'required',
                    'customer_billing_phone' => 'required',
                    'customer_billing_address' => 'required',
                    'billing_provincial_id' => 'required',
                    'billing_district_id' => 'required',
                    'billing_ward_id' => 'required',
                ],
                [
                    'required' => 'Thông tin bắt buộc'
                ]
            );
            $errors['shipping_address'] = "Thông tin bắt buộc";
            if ($validator->fails()) {
                foreach ($validator->messages()->messages() as $att => $messages) {
                    $errors[$att] = $messages[0];
                }
            }

            return response()->json([
                'status' => 'invalid',
                'errors' => $errors,
            ]);
        }else if($data['shipping_address'] != '0'){
            /** @var \Illuminate\Validation\Validator $validator */
            $validator = \Validator::make(
                $data,
                [
                    'customer_billing_name' => 'required',
                    'customer_billing_phone' => 'required',
                    'customer_billing_address' => 'required',
                    'customer_shipping_name' => 'required',
                    'provincial_id' => 'required',
                    'district_id' => 'required',
                    'ward_id' => 'required',
                    'billing_provincial_id' => 'required',
                    'billing_district_id' => 'required',
                    'billing_ward_id' => 'required',
                    'customer_shipping_address' => 'required',
                    'customer_shipping_phone' => 'required',
                ],
                [
                    'required' => 'Thông tin bắt buộc'
                ]
            );

            if ($validator->fails()) {
                foreach ($validator->messages()->messages() as $att => $messages) {
                    $errors[$att] = $messages[0];
                }
                return response()->json([
                    'status' => 'invalid',
                    'errors' => $errors,
                ]);
            }
        }else{
            $validator = \Validator::make(
                $data,
                [
                    'customer_billing_name' => 'required',
                    'customer_billing_phone' => 'required',
                    'customer_billing_address' => 'required',
                    'billing_provincial_id' => 'required',
                    'billing_district_id' => 'required',
                    'billing_ward_id' => 'required',
                ],
                [
                    'required' => 'Thông tin bắt buộc'
                ]
            );

            if ($validator->fails()) {
                foreach ($validator->messages()->messages() as $att => $messages) {
                    $errors[$att] = $messages[0];
                }
                return response()->json([
                    'status' => 'invalid',
                    'errors' => $errors,
                ]);
            }
        }

        $customerId = $customer->id;

        try {
            DB::beginTransaction();

            $addressAttr = array();
            $addressAttr['name'] = $data['customer_shipping_name'];
            $addressAttr['phone'] = $data['customer_shipping_phone'];
            $addressAttr['address'] = $data['customer_shipping_address'];
            $addressAttr['customer_id'] = Auth::guard('customer')->user()->id;
            if(isset($data['provincial_id']) && isset($data['district_id']) && isset($data['ward_id'])){
                $addressAttr['provincial_id'] = $data['provincial_id'];
                $addressAttr['district_id'] = $data['district_id'];
                $addressAttr['ward_id'] = $data['ward_id'];
            }

            $orderAttr = array();

            if($data['shipping_address'] == 'add-new'){
                $address = CustomerAddress::create($addressAttr);
                $addressOrder = CustomerAddress::with('provincial','district','ward')->find($address->id);
                $orderAttr['customer_shipping_address_id'] = $address->id;
            }else if($data['shipping_address'] != '0'){
                $address = CustomerAddress::where('id',$data['shipping_address'])->update($addressAttr);
                $addressOrder = CustomerAddress::with('provincial','district','ward')->find($data['shipping_address']);
                $orderAttr['customer_shipping_address_id'] = $data['shipping_address'];
            }
            $billing_provincial = Provincial::where('matp',$data['billing_provincial_id'])->first();
            $billing_district = District::where('maqh',$data['billing_district_id'])->first();
            $billing_ward = Ward::where('xaid',$data['billing_ward_id'])->first();

            // Create order
            $orderAttr['customer_id'] = $customerId;
            $orderAttr['customer_billing_name'] = $data['customer_billing_name'];
            $orderAttr['customer_billing_address'] = $data['customer_billing_address'].', '.$billing_ward->name.', '.$billing_district->name.', '.$billing_provincial->name;
            $orderAttr['customer_billing_phone'] = $data['customer_billing_phone'];
            if($data['shipping_address'] == 0){
                $orderAttr['customer_shipping_name'] = $data['customer_billing_name'];
                $orderAttr['customer_shipping_address'] = 'Kho hàng Redex';
                $orderAttr['customer_shipping_phone'] = $data['customer_billing_phone'];
            }else{
                $orderAttr['customer_shipping_name'] = $addressOrder['name'];
                $orderAttr['customer_shipping_address'] = $addressOrder['address'].', '.$addressOrder->ward->name.', '.$addressOrder->district->name.', '.$addressOrder->provincial->name;
                $orderAttr['customer_shipping_phone'] = $addressOrder['phone'];
            }

            $order = CustomerOrder::create($orderAttr);

            $shops = array();
            //Create order items
            foreach($data['link'] as $key => $item){
                $orderItemAttr = array();
                $orderItemAttr['link'] = $data['link'][$key];
                $orderItemAttr['size'] = $data['size'][$key];
                $orderItemAttr['colour'] = $data['colour'][$key];
                $orderItemAttr['description'] = $data['description'][$key];
                $orderItemAttr['quantity'] = $data['quantity'][$key];
                $orderItemAttr['unit'] = $data['unit'][$key];
                $orderItemAttr['price_cny'] = $data['price_cny'][$key];
                $orderItemAttr['customer_order_id'] = $order['id'];
                $orderItemAttr['note'] = $data['note'][$key];

                //shop
                if(isset($data['shop'][$key])){
                    $shop = Shop::where('name', $data['shop'][$key])->first();
                    if($shop){
                        $orderItemAttr['shop_id'] = $shop->id;
                    }else{
                        $shop = Shop::create([
                            'name' => $data['shop'][$key]
                        ]);
                        $orderItemAttr['shop_id'] = $shop->id;
                    }

                    $shops[$shop->id] = $data['shop'][$key];

                }

                $orderItem = CustomerOrderItem::create($orderItemAttr);

                // Create images
                if(isset($data['images'][$key])){
                    foreach($data['images'][$key] as $image){
                        Image::create([
                            'path' => $image,
                            'imagetable_id' => $orderItem->id,
                            'imagetable_type' => CustomerOrderItem::class
                        ]);
                    }
                }
            }
            $billCodeAttr = array();
            foreach ($shops as $id=>$shop){
                foreach($data['delivery_type'] as $key=>$item){
                    if($key == $shop){
                        $billCodeAttr['shop_id'] = $id;
                        $billCodeAttr['customer_order_id'] = $order->id;
                        $billCodeAttr['delivery_type'] = $data['delivery_type'][$key];
                        $billCodeAttr['insurance_type'] = $data['insurance_type'][$key];
                        $billCodeAttr['reinforced_type'] = $data['reinforced_type'][$key];
                        $billCodeAttr['order_date'] = $order->created_at;
                        BillCode::create($billCodeAttr);
                    }
                }
            }
            if(\Session::has('products')){
                \Session::forget('products');
            }
            // Auto create task
            CustomerOrderTask::newCustomerOrderByCustomer($order);
            // End process auto create task

            // BEGIN CREATE CUSTOMER ORDER'S NOTIFICATION
            CustomerOrderNotification::newCustomerOrderByCustomer($order->id, Auth::guard('customer')->user()->id);
            // END

            DB::commit();

            \Session::flash('flash_message','Đơn hàng đã được tạo thành công');
            return response()->json([
                'status' => 'success',
                'url' => route('order.index'),
            ]);
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public function update(Request $request, $id){
        $this->validate(
            $request,
            [
                'customer_billing_name' => 'required',
                'customer_billing_phone' => 'required',
                'customer_billing_address' => 'required',
            ],
            [
                'required' => 'Thông tin bắt buộc'
            ]
        );

        $data = $request->all();
        try {
            DB::beginTransaction();
            $order = CustomerOrder::find($id);

            $orderAttr = array();
            $orderAttr['customer_billing_name'] = $data['customer_billing_name'];
            $orderAttr['customer_billing_phone'] = $data['customer_billing_phone'];
            $orderAttr['customer_billing_address'] = $data['customer_billing_address'];
            if($data['shipping_address'] != 0){
                $address = CustomerAddress::find($data['shipping_address']);
                $orderAttr['customer_shipping_name'] = $address->name;
                $orderAttr['customer_shipping_phone'] = $address->phone;
                $orderAttr['customer_shipping_address'] = $address->full_address;
            }

            $order->update($orderAttr);

            //update order items
            $itemAttr = array();
            $itemList = CustomerOrderItem::where('customer_order_id',$id)->pluck('id');
            foreach($data['item'] as $key=>$item){
                $itemAttr['description'] = $data['description'][$key];
                $itemAttr['link'] = $data['link'][$key];
                $itemAttr['size'] = $data['size'][$key];
                $itemAttr['colour'] = $data['colour'][$key];
                $itemAttr['unit'] = $data['unit'][$key];
                $itemAttr['quantity'] = $data['quantity'][$key];
                $itemAttr['price_cny'] = $data['price_cny'][$key];
                $itemAttr['customer_order_id'] = $id;
                $itemAttr['note'] = $data['note'][$key];
                if(in_array($item,$itemList->toArray())){
                    $orderItem = CustomerOrderItem::find($item);
                    $orderItem->update($itemAttr);

                    //update image
                    $imagesItem = Image::where('imagetable_id', $item)->pluck('path');
                    $intersect = $imagesItem->intersect($data['images'][$key]);
                    $imgRemove =  $imagesItem->diff($intersect->all());
                    foreach ($imgRemove as $img){
                        $image = Image::where('path', $img)->where('imagetable_id', $item)->first();
                        Image::destroy($image->id);
                    }

                    $collection = collect($data['images'][$key]);
                    $intersect = $collection->intersect($imagesItem->all());
                    $imgInsert = $collection->diff($intersect->all());
                    foreach($imgInsert as $img){
                        Image::create([
                            'path' => $img,
                            'imagetable_id' => $item,
                            'imagetable_type' => CustomerOrderItem::class
                        ]);
                    }
                }else{
                    $orderItem = CustomerOrderItem::create($itemAttr);
                    foreach($data['images'][$key] as $image){
                        Image::create([
                            'path' => $image,
                            'imagetable_id' => $orderItem->id,
                            'imagetable_type' => CustomerOrderItem::class
                        ]);
                    }
                }
            }

            $newItemList = $data['item'];
            $intersect = $itemList->intersect($newItemList);
            $productRemove = $itemList->diff($intersect->all());
            foreach ($productRemove as $product){
                CustomerOrderItem::destroy($product);
            }

            $billCodeAttr = array();
            if(isset($data['delivery_type'])){
                foreach($data['delivery_type'] as $key => $item){
                    $billCodeAttr['shop_id'] = $key;
                    $billCodeAttr['customer_order_id'] = $id;
                    $billCodeAttr['delivery_type'] = $data['delivery_type'][$key];
                    isset($data['insurance_type']) ? $billCodeAttr['insurance_type'] = $data['insurance_type'][$key] : '';
                    isset($billCodeAttr['reinforced_type']) ? $billCodeAttr['reinforced_type'] = $data['reinforced_type'][$key] : '';
                    $billCodeAttr['order_date'] = $order->created_at;

                    $billCode = BillCode::where('shop_id',$key)->where('customer_order_id',$id)->first();
                    if($billCode){
                        $billCode->update($billCodeAttr);
                    }else{
                        BillCode::create($billCodeAttr);
                    }
                }
            }

            // BEGIN UPDATE TASKS
            CustomerOrderTask::updateCustomerOrderByCustommer($order,Auth::guard('customer')->user()->name);
            //END PROCESS
            DB::commit();

            \Session::flash('flash_message_success','Cập nhật đơn hàng thành công');

            return response()->json([
                'status' => 'success',
                'url' => route('order.edit',$id),
            ]);
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public function deleteItem(Request $request){
        if ($request->ajax()) {
            $id = $request->id;
            return CustomerOrderItem::destroy($id);
        }
    }

    public function delete($id){
        
        try {
            DB::beginTransaction();

            /** @var CustomerOrder $customerOrder -- for notification -- trinq */
            $customerOrder = CustomerOrder::findOrFail($id);

            CustomerOrder::destroy($id);
            
            //UPDATE STATUS OF TASK.
            CustomerOrderTask::deleteCustomerOrderByCustomer($id,Auth::guard('customer')->user()->name);
            //END PROCESS UPDATE STATUS OF TASKS
            
            // BEGIN NOTIFICATION
            CustomerOrderNotification::deleteCustomerOrderByCustomer($customerOrder->id, $customerOrder->seller_id, $customerOrder->customer_id);
            //END

            DB::commit();
            \Session::flash('flash_message','Order '.$id.' deleted successfully.');
            return redirect(route('order.index'));
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
        CustomerOrder::destroy($id);

        \Session::flash('flash_message','Xóa đơn hàng thành công');
        return redirect(route('order.index'));
    }

    public function upload(Request $request){
        $file = $request->file('file');

//        $validator = Validator::make([
//            'file' => $file
//        ], [
//            "file" => 'mimes:xlsx,xls,csv'
//        ]);
//
//        if($validator->fails()) {
//            return response()->json('Lỗi! Sai định dạng file.', 400);
//        }

        $filename = md5(date('dmyhms'));
        $ext = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);

        $upload = Storage::disk('public')->put('import/' . $filename . "." . $ext, file_get_contents($file->getRealPath()));

        if($upload){
            return response()->json('import/' . $filename . "." . $ext, 200);
        }

        return response()->json('Lỗi! Đã xảy ra sự cố. Hãy thử lại', 400);
    }

    public function import(Request $request){
        $files = $request->input('imports');
        $newFiles = [];
        foreach ($files as $file){
            $newFiles[] = storage_path('app/public/'.$file);
        }
        $results = collect([]);
        foreach ($newFiles as $file){
            Excel::load($file, function($reader) use (&$results) {
                $results = $reader->get();
            });
            foreach($results as $row){
                if(!is_array($row)){
                    $item = $row->toArray();
                } else {
                    $item = $row;
                }
                $attr = array();
                if($item['stt']){
                    $attr['description'] = $item['mo_ta'];
                    $attr['link'] = $item['link_sp'];
                    $attr['size'] = $item['kich_co_cm'];
                    $attr['color'] = $item['mau_sac'];
                    $attr['unit'] = $item['don_vi'];
                    $attr['quantity'] = $item['so_luong'];
                    $attr['price_cny'] = $item['gia_web'];
                    $attr['customer_order_id'] = $request->get('order_id');

                    CustomerOrderItem::create($attr);
                }else{
                    break;
                }
            }
        }

        \Session::flash('flash_message','Thêm sản phẩm thành công');
        return redirect(route('order.edit',$request->get('order_id')));
    }

    public function uploadImage(Request $request){
        $file = $request->file('file');
        $imagePaths = [];

        $path = $file->store('upload/'.date('Y/m/d'));
        $arrPath = explode('/', $path);
        $sortPath = 'upload/'.date('Y/m/d').'/'.last($arrPath);
        array_push($imagePaths, $sortPath);

        return $this->getResponseJson(Controller::STATUS_SUCCESS, 'Thêm ảnh thành công', $imagePaths);

    }

    public function getExcel(Request $request){
        $file = $request->input('imports');
        $newFile = storage_path('app/public/'.$file);
        $results = collect([]);
        Excel::load($newFile, function($reader) use (&$results) {
            $results = $reader->get();
        });
        $attr = array();
        foreach($results as $key=>$row){
            if(!is_array($row)){
                $item = $row->toArray();
            } else {
                $item = $row;
            }
            if($item['stt']){
                $attr[$key]['images'] = $item['hinh_anh'];
                $attr[$key]['description'] = $item['mo_ta'];
                $attr[$key]['link'] = $item['link_sp'];
                $attr[$key]['size'] = $item['kich_co_cm'];
                $attr[$key]['colour'] = $item['mau_sac'];
                $attr[$key]['unit'] = $item['don_vi'];
                $attr[$key]['quantity'] = $item['so_luong'];
                $attr[$key]['price_cny'] = $item['gia_web'];
                $attr[$key]['customer_order_id'] = $request->get('order_id');
            }else{
                break;
            }
        }

        return response()->json(array_values($attr));
    }

    public function getComplaint($id){
        $complaint = Complaint::where('ordertable_id',$id)->where('ordertable_type',CustomerOrder::class)->first();

        return redirect(route('complaint.view',$complaint->id));
    }

    public function getStatusName(){
        $status = [
            CustomerOrder::STATUS_PENDING => 'Chờ duyệt',
            CustomerOrder::STATUS_APPROVED => 'Đã duyệt mua',
            CustomerOrder::STATUS_CANCELED => 'Đã huỷ',
            CustomerOrder::STATUS_DEPOSITED => 'Đã đặt cọc',
            CustomerOrder::STATUS_DELIVERY => 'Đang giao hàng',
            CustomerOrder::STATUS_COMPLAINT => 'Khiếu nại',
            CustomerOrder::STATUS_FINISHED => 'Hoàn thành',
        ];


    }

    protected function url($url){
        $checkHttp = strpos($url,'http');
        $checkHttps = strpos($url,'https');
        if($checkHttp !== false ||$checkHttps !== false){
            return $url;
        }
        return url('storage/'.$url);
    }

    protected function getRate(){
        $now = new \DateTime();
        $rates = Rate::select('order_rate')->where(\DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y")'),'=', $now->format('d-m-Y'))->first();

        return $rates;
    }

    public function deposit(Request $request){
        $customer = Auth::guard('customer')->user();
        $balanceWallet = $customer->wallet;
        $orderId = [];
        $data = $request->all();
        $totalDeposit = (float)$data['deposit_by_wallet'] + (float)$data['deposit_by_pay'];
        $validator = \Validator::make(
            $data,
            [
                'deposit_by_pay' => 'numeric',
            ],
            [
                'numeric' => 'Số tiền cọc phải là số'
            ]
        );

        $errors = [];

        if ($validator->fails()) {
            foreach ($validator->messages()->messages() as $att => $messages) {
                $errors[$att] = $messages[0];
            }
            return response()->json([
                'status' => 'invalid-validator',
                'errors' => $errors,
            ]);
        }

        if($totalDeposit < $data['total_deposit']){
            return response()->json([
                'status' => 'invalid',
                'message' => 'Số tiền đặt cọc chưa đủ'
            ]);
        }

        try{
            if($data['deposit_by_wallet'] != 0 && $data['deposit_by_pay'] == 0){
                foreach ($data['order'] as $key=>$money){
                    $this->depositByWallet($key, $money);
                    CustomerOrder::where('id',$key)->update(['status' => CustomerOrder::STATUS_PROCESS_DEPOSIT]);
                    $orderId[] = $key;
                }
                $balanceWallet = (float)$balanceWallet - (float)$data['deposit_by_wallet'];
                $customer->update(['wallet' => $balanceWallet]);
            }
            if($data['deposit_by_pay'] != 0 && $data['deposit_by_wallet'] == 0){
                $depositAmount = 0;
                $count = 0;
                foreach ($data['order'] as $key=>$money){
                    $count ++;
                    if($count == count($data['order'])){
                        $this->depositByPay($key, $data['deposit_by_pay'] - $depositAmount);
                    }else{
                        $this->depositByPay($key, $money);
                    }
                    $depositAmount +=$money;
                    CustomerOrder::where('id',$key)->update(['status' => CustomerOrder::STATUS_PROCESS_DEPOSIT]);
                    $orderId[] = $key;
                }
            }

            if($data['deposit_by_pay'] != 0 && $data['deposit_by_wallet'] != 0){
                $depositAmount = 0;
                $balance = 0;
                $count = 0;
                $first_key = key($data['order']);
                foreach ($data['order'] as $key=>$money){
                    $count ++;
                    // kiểm tra order đầu tiên
                    if($key == $first_key && $data['deposit_by_wallet'] < $money){
                        // đặt cọc bằng ví
                        $this->depositByWallet($key, $data['deposit_by_wallet']);

                        // đặt cọc bằng chuyển khoản
                        $this->depositByPay($key, (float)$money - (float)$data['deposit_by_wallet']);
                        $depositAmount += (float)$money - (float)$data['deposit_by_wallet'];
                        continue;
                    }elseif($key == $first_key && $data['deposit_by_wallet'] >= $money){
                        $this->depositByWallet($key,$money);
                        $balance = (float)$data['deposit_by_wallet'] - (float)$money;
                        continue;
                    }

                    if($balance > 0 && $balance < $money){
                        $this->depositByWallet($key,$balance);
                        $this->depositByPay($key, (float)$money - (float)$balance);
                    }elseif($balance > 0 && $balance >= $money){
                        $this->depositByWallet($key,$money);
                        $balance = (float)$balance - (float)$money;
                    }elseif($balance == 0){
                        $this->depositByPay($key,$money);
                    }
                    CustomerOrder::where('id',$key)->update(['status' => CustomerOrder::STATUS_PROCESS_DEPOSIT]);
                    $orderId[] = $key;
                }
                $balanceWallet = (float)$balanceWallet - (float)$data['deposit_by_wallet'];
                $customer->update(['wallet' => $balanceWallet]);
            }

            return response()->json([
                'status' => 'success',
                'orderId' => $orderId,
                'totalDeposit' => $totalDeposit,
                'message' => 'Tạo giao dịch đặt cọc thành công'
            ]);
        }catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Xảy ra lỗi trong quá trình xử lý. Vui lòng thử lại sau'
            ]);
        }
    }

    public function deleteOrders(Request $request){
        $selectedOrder = collect(json_decode($request->input('orders')));
        if($selectedOrder->isEmpty()){
            \Session::flash('error','Vui lòng chọn ít nhất 1 order');
            return redirect()->back();
        }
        foreach ($selectedOrder as $orderId){
            CustomerOrder::destroy($orderId);
        }

        \Session::flash('flash_message','Xóa đơn hàng thành công');
        return redirect(route('order.index'));
    }

    public function depositOrders(Request $request){
        $customer = Auth::guard('customer')->user();
        $depositPercent = $customer->order_pre_deposit_percent;
        $totalDeposit = 0;
        $selectedOrder = collect(json_decode($request->input('orders')));
        if($selectedOrder->isEmpty()){
            return response()->json([
                'status' => 'invalid',
                'message' => 'Vui lòng chọn ít nhất 1 order'
            ]);
        }
        $data = array();
        foreach ($selectedOrder as $orderId){
            $orderTotalPrice = 0;
            $order = CustomerOrder::with('customerOrderItems')->find($orderId);
            foreach($order->customerOrderItems as &$item){
                $orderTotalPrice += $item->total_price;
            }
            $data[$orderId] = $orderTotalPrice * $customer->order_rate * $depositPercent/100;
            $totalDeposit += $data[$orderId];
        }
        return response()->json([
            'status' => 'success',
            'totalDeposit' => $totalDeposit,
            'data' => $data,
            'balanceWallet' => $customer->wallet
        ]);
    }

    public function depositByWallet($orderId, $money){
        $attr = array();
        $attr['transactiontable_id'] = $orderId;
        $attr['transactiontable_type'] = CustomerOrder::class;
        $attr['money'] = $money;
        $attr['note'] = 'Đặt cọc bằng ví tiền';
        $attr['type'] = Transaction::TYPE_DEPOSIT;
        $attr['customer_id'] = Auth::guard('customer')->user()->id;

        return Transaction::create($attr);
    }

    public function depositByPay($orderId, $money){
        $attr = array();
        $attr['transactiontable_id'] = $orderId;
        $attr['transactiontable_type'] = CustomerOrder::class;
        $attr['money'] = $money;
        $attr['note'] = 'Đặt cọc bằng chuyển khoản';
        $attr['type'] = Transaction::TYPE_DEPOSIT;
        $attr['customer_id'] = Auth::guard('customer')->user()->id;

        return Transaction::create($attr);
    }

    public function getTotalAmountNeedPayment(Request $request){
        $data = $request->all();

        $totalAmountPaymented = 0;
        $transactions = Transaction::where('transactiontable_id',$data['id'])->where('transactiontable_type',CustomerOrder::class)->get();
        foreach ($transactions as $transaction){
            $totalAmountPaymented += $transaction->money;
        }

        $totalAmountNeedPayment = (float)$data['totalAmount'] - (float)$totalAmountPaymented;

        return response()->json([
            'status' => 'success',
            'amountNeedPayment' => $totalAmountNeedPayment
        ]);
    }

    public function payment(Request $request){
        $customer = Auth::guard('customer')->user();
        $balanceWallet = $customer->wallet;
        $data = $request->all();
        $validator = \Validator::make(
            $data,
            [
                'pay_by_pay' => 'numeric',
            ],
            [
                'numeric' => 'Số tiền thanh toán phải là số'
            ]
        );

        $errors = [];

        if ($validator->fails()) {
            foreach ($validator->messages()->messages() as $att => $messages) {
                $errors[$att] = $messages[0];
            }
            return response()->json([
                'status' => 'invalid-validator',
                'errors' => $errors,
            ]);
        }

        if((float)$data['payment_by_wallet'] + (float)$data['payment_by_pay'] < $data['total_payment']){
            return response()->json([
                'status' => 'invalid',
                'message' => 'Số tiền thanh toán chưa đủ'
            ]);
        }
        try{
            if($data['payment_by_wallet'] != 0 && $data['payment_by_pay'] == 0){
                foreach ($data['order'] as $key=>$money){
                    $this->paymentByWallet($key, $money);
                }
                $balanceWallet = (float)$balanceWallet - (float)$data['payment_by_wallet'];
                $customer->update(['wallet' => $balanceWallet]);
            }
            if($data['payment_by_pay'] != 0 && $data['payment_by_wallet'] == 0){
                foreach ($data['order'] as $key=>$money){
                    $this->paymentByPay($key, $money);
                }
            }

            if($data['payment_by_pay'] != 0 && $data['payment_by_wallet'] != 0){
                $balance = 0;
                $first_key = key($data['order']);
                foreach ($data['order'] as $key=>$money){
                    // kiểm tra order đầu tiên
                    if($key == $first_key && $data['payment_by_wallet'] < $money){
                        // thanh toan bằng ví
                        $this->paymentByWallet($key, $data['payment_by_wallet']);

                        // thanh toan bằng chuyển khoản
                        $this->paymentByPay($key, (float)$money - (float)$data['payment_by_wallet']);
                        continue;
                    }elseif($key == $first_key && $data['payment_by_wallet'] >= $money){
                        $this->paymentByWallet($key,$money);
                        $balance = (float)$data['payment_by_wallet'] - (float)$money;
                        continue;
                    }

                    if($balance > 0 && $balance < $money){
                        $this->paymentByWallet($key,$balance);
                        $this->paymentByPay($key, (float)$money - (float)$balance);
                    }elseif($balance > 0 && $balance >= $money){
                        $this->paymentByWallet($key,$money);
                        $balance = (float)$balance - (float)$money;
                    }elseif($balance == 0){
                        $this->paymentByPay($key,$money);
                    }
                }
                $balanceWallet = (float)$balanceWallet - (float)$data['payment_by_wallet'];
                $customer->update(['wallet' => $balanceWallet]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Gửi yêu cầu thanh toán thành công'
            ]);
        }catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Xảy ra lỗi trong quá trình xử lý. Vui lòng thử lại sau'
            ]);
        }
    }

    public function paymentByWallet($orderId, $money){
        $attr = array();
        $attr['transactiontable_id'] = $orderId;
        $attr['transactiontable_type'] = CustomerOrder::class;
        $attr['money'] = $money;
        $attr['note'] = 'Thanh toán bằng ví tiền';
        $attr['type'] = Transaction::TYPE_PAYMENT;
        $attr['customer_id'] = Auth::guard('customer')->user()->id;

        return Transaction::create($attr);
    }

    public function paymentByPay($orderId, $money){
        $attr = array();
        $attr['transactiontable_id'] = $orderId;
        $attr['transactiontable_type'] = CustomerOrder::class;
        $attr['money'] = $money;
        $attr['note'] = 'Thanh toán bằng chuyển khoản';
        $attr['type'] = Transaction::TYPE_PAYMENT;
        $attr['customer_id'] = Auth::guard('customer')->user()->id;

        return Transaction::create($attr);
    }

    protected function getUrban($districtId){
        $district = \DB::table('devvn_quanhuyen')->where('matp','01')->pluck('maqh');
        if(in_array($districtId,$district->toArray())){
            return true;
        }
        return false;
    }
}
