<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Rate\Models\Rate;

class ExtensionController extends Controller
{
    public function getRate(){
        $lastRate = Rate::orderBy('date', 'desc')->value('order_rate');
        return response()->json([
            'success' => true,
            'rate' => $lastRate,
        ]);
    }

    public function itemReceiver(Request $request){
        $data = json_decode($request->get('data'));
        $count = 0;
        $shop = array();
        foreach($data as $item){
            // lưu shop vào session
            $shop[$item->shop_uid] = $item->shop_name;
            if(\Session::has('shops')){
                $shops = \Session::get('shops');
                $shops[$item->shop_uid] = $item->shop_name;
                \Session::put('shops',$shops);
            }else{
                \Session::put('shops',$shop);
            }

            //lưu product vào session
            if(\Session::has('products')){
                $products = \Session::get('products');
                foreach($products as $key => &$product){
                    if($product['url'] == $item->url && $product['color'] == $item->color && $product['size'] == $item->size){
                        $product['quantity'] += $item->quantity;
                        $count++;
                    }
                }
            }
            if($count > 0){
                \Session::put('products', $products);
            }else{
                \Session::push('products',(array)$item);
            }

        }

        if($data){
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }
        return  response()->json([
            'success' => false,
        ]);
    }

    public function updateSession(Request $request){
        $id = $request->get('id');
        $shops = \Session::get('shops');
        $products = \Session::get('products');
        foreach($products as $key=> &$item){
            if($item['id'] == $id){
                unset($products[$key]);
                break;
            }
        }
        if(count($products) == 0){
            \Session::forget('products');
        }else{
            \Session::put('products', $products);
        }

        return response()->json(['success'=>true]);
    }
}
