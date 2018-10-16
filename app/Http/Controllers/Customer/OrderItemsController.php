<?php

namespace App\Http\Controllers\Customer;

use Modules\Image\Models\Image;
use Modules\CustomerOrder\Models\CustomerOrderItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Rules\CheckNumberRule;
use App\Rules\CheckImageRule;
use App\Rules\CheckBalance;

class OrderItemsController extends Controller
{

    public function store(Request $request){

        $data = $request->all();

        $message = array(
            'required' => 'Thông tin bắt buộc',
            '_quantity.numeric' => 'Số lượng phải là số',
            '_price_cny.numeric' => 'Giá sản phẩm phải là số',
        );
        $this->validate($request, [
            '_description' => 'required',
            '_link'     => 'required',
            '_size'     => 'required',
            '_colour'   => 'required',
            '_unit'     => 'required',
            '_quantity' => ['required','numeric', new CheckNumberRule()],
            '_price_cny'=> ['required','numeric', new CheckNumberRule()],
            'images'   => 'required',
            'images.*' => new CheckImageRule()
        ],$message);

        $attr = $this->convertData($data);

        $orderItem = CustomerOrderItem::create($attr);
        foreach($data['images'] as $image){
            Image::create([
                'path' => $image,
                'imagetable_id' => $orderItem->id,
                'imagetable_type' => CustomerOrderItem::class
            ]);
        }

        \Session::flash('flash_message','Thêm sản phẩm thành công.');

        return redirect(route('order.edit',$data['customer_order_id']));
    }

    public function update(Request $request, $id){
        $data = $request->all();
        $message = array(
            'required' => 'Thông tin bắt buộc',
            'quantity.numeric' => 'Số lượng phải là số',
            'price_cny.numeric' => 'Giá sản phẩm phải là số'
        );
        $this->validate($request, [
            'description.*' => 'required',
            'link.*'     => 'required',
            'size.*'     => 'required',
            'colour.*'   => 'required',
            'unit.*'     => 'required',
            'quantity.*' => ['numeric', new CheckNumberRule()],
            'price_cny.*'=> ['numeric', new CheckNumberRule()],
//            'images'   => 'required',
//            'images.*'   => new CheckImageRule()
        ],$message);

        $orderItem = CustomerOrderItem::find($id);
        $orderItem->update($data);

        $imagesItem = Image::where('imagetable_id', $id)->pluck('path');

        $intersect = $imagesItem->intersect($data['images']);
        $imgRemove =  $imagesItem->diff($intersect->all());
        foreach ($imgRemove as $img){
            $image = Image::where('path', $img)->first();
            Image::destroy($image->id);
        }

        $collection = collect($data['images']);
        $intersect = $collection->intersect($imagesItem->all());
        $imgInsert = $collection->diff($intersect->all());
        foreach($imgInsert as $img){
            Image::create([
                'path' => $img,
                'imagetable_id' => $orderItem->id,
                'imagetable_type' => CustomerOrderItem::class
            ]);
        }

        \Session::flash('flash_message','Cập nhật sản phẩm thành công.');

        return redirect(route('order.edit',$data['customer_order_id']));
    }

    public function validateItem(Request $request)
    {
        $data = $request->all();

        /** @var \Illuminate\Validation\Validator $validator */
        $validator = \Validator::make(
            $data,
            [
                'description.*' => 'required',
                'link.*'     => 'required',
                'size.*'     => 'required',
                'colour.*'   => 'required',
                'unit.*'     => 'required',
                'quantity.*' => ['required','numeric', new CheckNumberRule()],
                'price_cny.*'=> ['required','numeric', new CheckNumberRule()],
                'images.*' => 'required',
            ],
            [
                'required' => 'Thông tin bắt buộc',
                'quantity.numeric' => 'Số lượng phải là số',
                'price_cny.numeric' => 'Giá sản phẩm phải là số',
            ]
        );

        $errors = [];

        if ($validator->fails()) {
            foreach ($validator->messages()->messages() as $att => $messages) {
                $errors[$att] = $messages[0];
            }
        }

        return response()->json([
            'is_valid' => empty($errors),
            'errors' => $errors,
        ]);
    }

    public function delete($id){
        $item = CustomerOrderItem::with('customerOrder')->where('id',$id)->first();
        $orderId = $item->customerOrder->id;
        $items = CustomerOrderItem::where('customer_order_id',$orderId)->get();
        if(count($items) == 1){
            \Session::flash('flash_message_errors','Đơn hàng phải có ít nhất 1 sản phẩm');
        }else{
            CustomerOrderItem::destroy($id);
            \Session::flash('flash_message','Xóa sản phẩm thành công');
        }
        return redirect(route('order.edit',$orderId));
    }

    public function convertData($data){
        $attr = array();
        $attr['description'] = $data['_description'];
        $attr['link'] = $data['_link'];
        $attr['size'] = $data['_size'];
        $attr['colour'] = $data['_colour'];
        $attr['unit'] = $data['_unit'];
        $attr['quantity'] = $data['_quantity'];
        $attr['price_cny'] = $data['_price_cny'];
        $attr['customer_order_id'] = $data['customer_order_id'];

        return $attr;
    }

    public function confirmNotEnoughProduct($id){
        $customerOrderItem = CustomerOrderItem::findOrFail($id);
        $customerOrderItem->alerted = 2;
        $customerOrderItem->quantity = $customerOrderItem->shop_quantity;
        $customerOrderItem->save();
        \Session::flash('flash_message','Đã xác nhận mua hàng theo số lượng shop có');
        return redirect(route('order.edit',$customerOrderItem->customer_order_id));
    }
    public function removeItemNotEnoughProduct($id){

        $customerOrderItem = CustomerOrderItem::findOrFail($id);
        $customerOrderId= $customerOrderItem->customer_order_id;
        $customerOrderItem->delete();
        \Session::flash('flash_message','Đã xóa sản phẩm này khỏi đơn hàng');
        return redirect(route('order.view',$customerOrderId));
    }
}
