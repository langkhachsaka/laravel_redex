<?php

namespace Modules\VerifyLadingCode\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Base\Http\Controllers\Controller;
use Modules\Complaint\Models\Complaint;
use Modules\CustomerOrder\Models\CustomerOrder;
use Modules\CustomerOrder\Models\CustomerOrderItem;
use Modules\LadingCode\Models\LadingCode;
use Modules\Task\Models\ComplaintTask;
use Modules\Task\Models\VerifyLadingCodeTask;
use Modules\VerifyLadingCode\Models\SubLadingCode;
use Modules\VerifyLadingCode\Models\VerifyCustomerOrderItem;
use Modules\VerifyLadingCode\Models\VerifyLadingCode;
use Modules\WarehouseReceivingVN\Models\WarehouseReceivingVN;
use Modules\WarehouseReceivingVN\Models\WarehouseVnLadingItem;
use Psy\Output\PassthruPager;

class VerifyLadingCodeController extends Controller
{

    protected $helper;
    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $verifyLadingCodes = VerifyLadingCode::with('user','ladingCodeItem')
                            ->whereIn('status',array(0,1,2))
                            ->orderBy('created_at','DESC')
                            ->paginate(20);
        return $this->respondSuccessData($verifyLadingCodes);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('verifyladingcode::create');
    }

    /**
     * Store VerifyLadingCode for Bill_Of_Lading
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function storeVerifyBillOfLading(Request $request)
    {
        $verifyLadingCode = new VerifyLadingCode();
        $verifyLadingCode->fill($request->input());

        $verifyLadingCode->lading_code = $request->code;
        $verifyLadingCode->verifier_id = auth()->user()->id;
        $verifyLadingCode->type= VerifyLadingCode::TYPE_VERIFY_BILL_OF_LADING;
        if(isset($request->is_gash_stamp)){
            $verifyLadingCode->is_gash_stamp = VerifyLadingCode::HAVE_PROBLEM;
        } else {
            $verifyLadingCode->is_gash_stamp = VerifyLadingCode::NO_PROBLEM;
        }
        if(isset($request->is_broken_gash)){
            $verifyLadingCode->is_broken_gash = VerifyLadingCode::HAVE_PROBLEM;
        } else {
            $verifyLadingCode->is_broken_gash = VerifyLadingCode::NO_PROBLEM;
        }
        if(isset($request->images)){
            $images = $request->images;
            $length = count($images);
            switch ($length){
                case 1 :
                    $verifyLadingCode->image1 = $images[0];
                    break;
                case 2 :
                    $verifyLadingCode->image1 = $images[0];
                    $verifyLadingCode->image2 = $images[1];
                    break;
                case 3 :
                    $verifyLadingCode->image1 = $images[0];
                    $verifyLadingCode->image2 = $images[1];
                    $verifyLadingCode->image3 = $images[2];
                    break;
                case 4 :
                    $verifyLadingCode->image1 = $images[0];
                    $verifyLadingCode->image2 = $images[1];
                    $verifyLadingCode->image3 = $images[2];
                    $verifyLadingCode->image4 = $images[3];
                    break;
                case 5 :
                    $verifyLadingCode->image1 = $images[0];
                    $verifyLadingCode->image2 = $images[1];
                    $verifyLadingCode->image3 = $images[2];
                    $verifyLadingCode->image4 = $images[3];
                    $verifyLadingCode->image5 = $images[4];
                    break;
            }
        }
        $verifyLadingCode->save();
        $verifyLadingCode->load('user','ladingCodeItem');
        return $this->respondSuccessData($verifyLadingCode,'Lưu thành công');
    }


    public function storeVerifyCustomerOrder(Request $request){
        try {

            $requestData = $request->input();
            $validator = \Validator::make(
                $requestData,
                [

                    'item.*.quantity_verify' => [
                        'required',
                        'min:0',
                    ],
                ],
                [
                    'item.*.quantity_verify.required' => 'Chưa nhập mã giao dịch',
                    'item.*.quantity_verify.min' => 'Số lượng kiểm phải lớn hơn 0',
                ]
            );
            if ($validator->fails()) {
                return $this->respondInvalidData($validator->messages());
            }
            DB::beginTransaction();
            $verifyLadingCode = VerifyLadingCode::where('lading_code',$request->lading_code)->first();
            $verifyLadingCode->status = $this->checkStatusVerifyLadingCode($request);
            $verifyLadingCode->type = VerifyLadingCode::TYPE_1_1;
            $verifyLadingCode->save();

            //Update table WarehouseVnLadingItem
            $warehouseVNItem = WarehouseVnLadingItem::where('lading_code',$request->lading_code)->first();
            if($this->checkStatusVerifyLadingCode($request)== 1 ){
                $warehouseVNItem->status = WarehouseVnLadingItem::STATUS_ERROR;
            } else {
                $warehouseVNItem->status = WarehouseVnLadingItem::STATUS_CHECKED;
            }
            $warehouseVNItem->customer_order_id = $request->customer_order_id;
            $warehouseVNItem->save();

            $ladings = LadingCode::where('code','=',$request->lading_code)->get();

            $index = 0;
            $items = $request->item;
            foreach ($items as$item) {
                $verifyCustomerOrderItem  = new VerifyCustomerOrderItem();
                $verifyCustomerOrderItem->verify_lading_code_id = $verifyLadingCode->id;
                $verifyCustomerOrderItem->lading_code = $request->lading_code;
                $verifyCustomerOrderItem->customer_order_item_id = $item["customer_order_item_id"];
                $verifyCustomerOrderItem->quantity_verify = $item["quantity_verify"];
                $countError = 0;

                if(isset($item["is_broken_gash"]) && $item["is_broken_gash"] == true){
                    $verifyCustomerOrderItem->is_broken_gash = VerifyLadingCode::HAVE_PROBLEM;
                } else {
                    $verifyCustomerOrderItem->is_broken_gash = VerifyLadingCode::NO_PROBLEM;
                }
                if(isset($item["is_error_size"])&& $item["is_error_size"] == true){
                    $verifyCustomerOrderItem->is_error_size = VerifyLadingCode::HAVE_PROBLEM;
                    $countError = $countError + 1;
                } else {
                    $verifyCustomerOrderItem->is_error_size = VerifyLadingCode::NO_PROBLEM;
                }
                if(isset($item["is_error_color"]) && $item["is_error_color"] == true){
                    $countError = $countError + 1;
                    $verifyCustomerOrderItem->is_error_color = VerifyLadingCode::HAVE_PROBLEM;
                } else {
                    $verifyCustomerOrderItem->is_error_color = VerifyLadingCode::NO_PROBLEM;
                }
                if(isset($item["is_error_product"]) && $item["is_error_product"] == true ){
                    $countError = $countError + 1;
                    $verifyCustomerOrderItem->is_error_product = VerifyLadingCode::HAVE_PROBLEM;
                } else {
                    $verifyCustomerOrderItem->is_error_product = VerifyLadingCode::NO_PROBLEM;
                }
                if(isset($item["is_exuberancy"]) && $item["is_exuberancy"] == true ){
                    $verifyCustomerOrderItem->is_exuberancy = VerifyLadingCode::HAVE_PROBLEM;
                } else {
                    $verifyCustomerOrderItem->is_exuberancy = VerifyLadingCode::NO_PROBLEM;
                }
                if(isset($item["is_inadequate"]) && $item["is_inadequate"] == true){
                    $countError = $countError + 1;
                    $verifyCustomerOrderItem->is_inadequate = VerifyLadingCode::HAVE_PROBLEM;
                } else {
                    $verifyCustomerOrderItem->is_inadequate = VerifyLadingCode::NO_PROBLEM;
                }
                if(isset($item["images"])){
                    $images = $item["images"];
                    $length = count($images);
                    switch ($length){
                        case 1 :
                            $verifyCustomerOrderItem->image1 = $images[0];
                            break;
                        case 2 :
                            $verifyCustomerOrderItem->image1 = $images[0];
                            $verifyCustomerOrderItem->image2 = $images[1];
                            break;
                        case 3 :
                            $verifyCustomerOrderItem->image1 = $images[0];
                            $verifyCustomerOrderItem->image2 = $images[1];
                            $verifyCustomerOrderItem->image3 = $images[2];
                            break;
                        case 4 :
                            $verifyCustomerOrderItem->image1 = $images[0];
                            $verifyCustomerOrderItem->image2 = $images[1];
                            $verifyCustomerOrderItem->image3 = $images[2];
                            $verifyCustomerOrderItem->image4 = $images[3];
                            break;
                        case 5 :
                            $verifyCustomerOrderItem->image1 = $images[0];
                            $verifyCustomerOrderItem->image2 = $images[1];
                            $verifyCustomerOrderItem->image3 = $images[2];
                            $verifyCustomerOrderItem->image4 = $images[3];
                            $verifyCustomerOrderItem->image5 = $images[4];
                            break;
                    }
                }
                $verifyCustomerOrderItem->note = isset($item["note"]) ? $item["note"] : "";
                $verifyCustomerOrderItem->save();

                //If have any error : create complaint;
                if($countError > 0){
                    $complaint = new Complaint();
                    $complaint->lading_code =$verifyCustomerOrderItem ->lading_code;
                    $complaint->status = Complaint::STATUS_PENDING;
                    $complaint->customer_order_item_id = $verifyCustomerOrderItem->customer_order_item_id;
                    $complaint->customer_id = $this->helper->getCustomerIdFromCustomerOrderItemId($verifyCustomerOrderItem->customer_order_item_id);
                    if($verifyCustomerOrderItem->is_error_size == 1){
                        $complaint->error_size = 1;
                    }
                    if($verifyCustomerOrderItem->is_error_color  == 1){
                        $complaint->error_collor = 1;
                    }
                    if($verifyCustomerOrderItem->is_error_product  == 1){
                        $complaint->error_product = 1;
                    }
                    if($verifyCustomerOrderItem->is_inadequate  == 1){
                        $complaint->inadequate_product = 1;
                    }
                    $complaint->save();

                    //AUTO CHANGE STATUS OF TASK.
                    ComplaintTask::newComplaintByUser($complaint,$verifyCustomerOrderItem->note);
                    //END PROCESS
                }

                //AUTO CHANGE STATUS OF TASK VERIFY_LADING_CODE.
                VerifyLadingCodeTask::updateVerifyTask($request->lading_code);
                //END PROCESS
                $index = $index + 1;
            }
            DB::commit();
            return $this->respondSuccessData([],'Lưu thành công');
        } catch (\Exception $e){
            DB::rollBack();
            throw $e;
        }

        return $this->respondSuccessData([],'Lưu thành công');
    }

    /**
     * Store verify 1 lading code - many customer_order
     * @param Request $request
     */
    public function storeVerifyManyCustomerOrder(Request $request){
        try {
            DB::beginTransaction();

            //Update table WarehouseVnLadingItem
            $warehouseVNItem = WarehouseVnLadingItem::where('lading_code',$request->lading_code)->first();
            if($this->checkStatusVerifyLadingCode($request)== 1 ){
                $warehouseVNItem->status = WarehouseVnLadingItem::STATUS_ERROR;
            } else {
                $warehouseVNItem->status = WarehouseVnLadingItem::STATUS_CHECKED;
            }
            $warehouseVNItem->save();

            $subLadingCodes = $request->subLadingCodes;
            foreach ($subLadingCodes as $subLadingCode){
                $subLadingC = new SubLadingCode();
                if(isset($subLadingCode['weight'])){
                    $subLadingC->fill($subLadingCode);
                    $subLadingC->lading_code = $request->lading_code;
                    $subLadingC->save();
                }

            }
            $index = 0;
            $items = $request->item;
            foreach ($items as $item) {

                $newSubLadingCode = $item["itemSubLadingCode"];
                //Update Kho VN
                // DISABLE PARENT LADING_CODE
                $parentWarehouseVnLadingItem = WarehouseVnLadingItem::where('lading_code',$request->lading_code)->whereNull('sub_lading_code')->first();
                $parentWarehouseVnLadingItem->status =WarehouseVnLadingItem::DISABLED;
                $parentWarehouseVnLadingItem->save();

                //CREATE NEW WarehouseVnLadingItem WITH SUB_LADING_CODE
                if(WarehouseVnLadingItem::where('sub_lading_code',$newSubLadingCode)->exists()){
                    $warehouseVNItem = WarehouseVnLadingItem::where('sub_lading_code',$newSubLadingCode)->first();
                    if($warehouseVNItem->status == WarehouseVnLadingItem::STATUS_CHECKED){
                        $warehouseVNItem->status = $this->checkStatusVerifyLadingCodeItem($item) + 4;
                        $warehouseVNItem->save();
                    }
                } else {
                    $warehouseVNItem = new WarehouseVnLadingItem();
                    $subLadingCode = null;
                    foreach ($subLadingCodes as $subLading){
                        if(isset($subLading['weight']) && $subLading['sub_lading_code'] == $newSubLadingCode){
                            $subLadingCode = $subLading;
                        }
                    }
                    $warehouseVNItem->customer_order_id = $subLadingCode['order_id'];
                    unset($subLadingCode['order_id']);
                    $warehouseVNItem->fill($subLadingCode);
                    $warehouseVNItem->lading_code = $request->lading_code;
                    $warehouseVNItem->warehouse_receiving_v_ns_id = $parentWarehouseVnLadingItem->warehouse_receiving_v_ns_id;
                    $warehouseVNItem->status = $this->checkStatusVerifyLadingCodeItem($item) + 4;
                    $warehouseVNItem->other_fee = 0;
                    $warehouseVNItem->pack = $parentWarehouseVnLadingItem->pack;
                    $warehouseVNItem->save();
                }

                // Update VerifyLadingCode by new sub_lading_code

                // Disable parent lading_code
                VerifyLadingCode::where('lading_code',$request->lading_code)->update(['status' =>VerifyLadingCode::DISABLED]);
                if(VerifyLadingCode::where('lading_code',$newSubLadingCode)->exists()){
                    $verifyLadingCode = VerifyLadingCode::where('lading_code',$newSubLadingCode)->first();
                    if($verifyLadingCode->status == VerifyLadingCode::NO_PROBLEM){
                        $verifyLadingCode->status = $this->checkStatusVerifyLadingCodeItem($item);
                        $verifyLadingCode->save();
                    }
                } else {
                    $verifyLadingCode = new VerifyLadingCode();
                    $verifyLadingCode->lading_code = $newSubLadingCode;
                    $verifyLadingCode->verifier_id = auth()->user()->id;
                    $verifyLadingCode->status = $this->checkStatusVerifyLadingCodeItem($item);
                    $verifyLadingCode->type = VerifyLadingCode::TYPE_1_MANY;
                    $verifyLadingCode->save();
                }

                $verifyCustomerOrderItem  = new VerifyCustomerOrderItem();
                $verifyCustomerOrderItem->verify_lading_code_id = $verifyLadingCode->id;
                $verifyCustomerOrderItem->customer_order_item_id = $item["customer_order_item_id"];

                $verifyCustomerOrderItem->lading_code = $newSubLadingCode;
                $verifyCustomerOrderItem->quantity_verify = $item["quantity_verify"];

                $countError = 0;
                if(isset($item["is_broken_gash"]) && $item["is_broken_gash"] == true){
                    $verifyCustomerOrderItem->is_broken_gash = VerifyLadingCode::HAVE_PROBLEM;
                } else {
                    $verifyCustomerOrderItem->is_broken_gash = VerifyLadingCode::NO_PROBLEM;
                }
                if(isset($item["is_error_size"])&& $item["is_error_size"] == true){
                    $countError = $countError + 1;
                    $verifyCustomerOrderItem->is_error_size = VerifyLadingCode::HAVE_PROBLEM;
                } else {
                    $verifyCustomerOrderItem->is_error_size = VerifyLadingCode::NO_PROBLEM;
                }
                if(isset($item["is_error_color"]) && $item["is_error_color"] == true){
                    $countError = $countError + 1;
                    $verifyCustomerOrderItem->is_error_color = VerifyLadingCode::HAVE_PROBLEM;
                } else {
                    $verifyCustomerOrderItem->is_error_color = VerifyLadingCode::NO_PROBLEM;
                }
                if(isset($item["is_error_product"]) && $item["is_error_product"] == true ){
                    $countError = $countError + 1;
                    $verifyCustomerOrderItem->is_error_product = VerifyLadingCode::HAVE_PROBLEM;
                } else {
                    $verifyCustomerOrderItem->is_error_product = VerifyLadingCode::NO_PROBLEM;
                }
                if(isset($item["is_exuberancy"]) && $item["is_exuberancy"] == true ){
                    $countError = $countError + 1;
                    $verifyCustomerOrderItem->is_exuberancy = VerifyLadingCode::HAVE_PROBLEM;
                } else {
                    $verifyCustomerOrderItem->is_exuberancy = VerifyLadingCode::NO_PROBLEM;
                }
                if(isset($item["is_inadequate"]) && $item["is_inadequate"] == true){
                    $countError = $countError + 1;
                    $verifyCustomerOrderItem->is_inadequate = VerifyLadingCode::HAVE_PROBLEM;
                } else {
                    $verifyCustomerOrderItem->is_inadequate = VerifyLadingCode::NO_PROBLEM;
                }

                if(isset($item["images"])){
                    $images = $item["images"];
                    $length = count($images);
                    switch ($length){
                        case 1 :
                            $verifyCustomerOrderItem->image1 = $images[0];
                            break;
                        case 2 :
                            $verifyCustomerOrderItem->image1 = $images[0];
                            $verifyCustomerOrderItem->image2 = $images[1];
                            break;
                        case 3 :
                            $verifyCustomerOrderItem->image1 = $images[0];
                            $verifyCustomerOrderItem->image2 = $images[1];
                            $verifyCustomerOrderItem->image3 = $images[2];
                            break;
                        case 4 :
                            $verifyCustomerOrderItem->image1 = $images[0];
                            $verifyCustomerOrderItem->image2 = $images[1];
                            $verifyCustomerOrderItem->image3 = $images[2];
                            $verifyCustomerOrderItem->image4 = $images[3];
                            break;
                        case 5 :
                            $verifyCustomerOrderItem->image1 = $images[0];
                            $verifyCustomerOrderItem->image2 = $images[1];
                            $verifyCustomerOrderItem->image3 = $images[2];
                            $verifyCustomerOrderItem->image4 = $images[3];
                            $verifyCustomerOrderItem->image5 = $images[4];
                            break;
                    }
                }
                $verifyCustomerOrderItem->note = isset($item["note"]) ? $item["note"] : "";
                $verifyCustomerOrderItem->save();

                //If have any error : create complaint;
                if($countError > 0){
                    $complaint = new Complaint();
                    $complaint->lading_code =$verifyCustomerOrderItem ->lading_code;
                    $complaint->status = Complaint::STATUS_PENDING;
                    $complaint->customer_order_item_id = $verifyCustomerOrderItem->customer_order_item_id;
                    $complaint->customer_id = $this->helper->getCustomerIdFromCustomerOrderItemId($verifyCustomerOrderItem->customer_order_item_id);
                    if($verifyCustomerOrderItem->is_error_size == 1){
                        $complaint->error_size = 1;
                    }
                    if($verifyCustomerOrderItem->is_error_color  == 1){
                        $complaint->error_collor = 1;
                    }
                    if($verifyCustomerOrderItem->is_error_product  == 1){
                        $complaint->error_product = 1;
                    }
                    if($verifyCustomerOrderItem->is_inadequate  == 1){
                        $complaint->inadequate_product = 1;
                    }
                    $complaint->save();

                    //AUTO CHANGE STATUS OF TASK.
                    ComplaintTask::newComplaintByUser($complaint,$verifyCustomerOrderItem->note);
                    //END PROCESS
                }
            //AUTO CHANGE STATUS OF TASK VERIFY_LADING_CODE.
            VerifyLadingCodeTask::updateVerifyTask($request->lading_code);
            //END PROCESS

            }
            DB::commit();
            return $this->respondSuccessData([],'Lưu thành công');
        } catch (\Exception $e){
            DB::rollBack();
            throw $e;
        }

        return $this->respondSuccessData([],'Lưu thành công');
    }
    private static function getSubLadingCode($ladingCode, $customerOrderItemId,$itemIndexSubLadingCode){
        $customerOrderItem = CustomerOrderItem::find($customerOrderItemId);
        $customerOrder = CustomerOrder::find($customerOrderItem->customer_order_id);
        $customerId = $customerOrder->customer_id;
        return $ladingCode . '-'.$customerId.'-'.$itemIndexSubLadingCode;
    }

    private function checkStatusVerifyLadingCode(Request $request){

        foreach ($request->item as $item) {

            if(isset($item["is_broken_gash"]) && $item["is_broken_gash"] == true){
                return 1;
            }
            if(isset($item["is_error_size"])&& $item["is_error_size"] == true){
                return 1;
            }
            if(isset($item["is_gash_stamp"]) && $item["is_gash_stamp"] == true){
                return 1;
            }
            if(isset($item["is_error_product"]) && $item["is_error_product"] == true ){
                return 1;
            }
            if(isset($item["is_exuberancy"]) && $item["is_exuberancy"] == true ){
                return 1;
            }
            if(isset($item["is_inadequate"]) && $item["is_inadequate"] == true){
                return 1;
            }

        }
        return 0;
    }
    private function checkStatusVerifyLadingCodeItem($item){
        if(isset($item["is_broken_gash"]) && $item["is_broken_gash"] == true){
            return 1;
        }
        if(isset($item["is_error_size"])&& $item["is_error_size"] == true){
            return 1;
        }
        if(isset($item["is_gash_stamp"]) && $item["is_gash_stamp"] == true){
            return 1;
        }
        if(isset($item["is_error_product"]) && $item["is_error_product"] == true ){
            return 1;
        }
        if(isset($item["is_exuberancy"]) && $item["is_exuberancy"] == true ){
            return 1;
        }
        if(isset($item["is_inadequate"]) && $item["is_inadequate"] == true){
            return 1;
        }
        return 0;
    }
    /**
     * @param $ladingCode
     * Get ladingCode info
     */
    public function checkLadingCode($ladingCode){
        $lading = LadingCode::with('billCode','warehouseVnLadingItem','verifyLadingCode')->where('code','=',$ladingCode)->get();
        return $this->respondSuccessData($lading);
    }

    public function getCustomerOrder($id){
        /** @var LadingCode[] $ladings */
        $ladings = LadingCode::query()->where('code','=',$id)->get();
        foreach ($ladings as $lading) {
            $lading->load([
                'warehouseVnLadingItem','billCode'
            ]);
            $lading->billCode->load('customerOrderItems','customerOrderItems.images','customerOrderItems.customerOrder');
        }
        return $this->respondSuccessData($ladings);
        /*$ladings = LadingCode::with('warehouseVnLadingItem','billCode','billCode.customerOrder.customerOrderItems','billCode.customerOrder.customerOrderItems.images')->where('code','=',$id)->get();
        return $this->respondSuccessData($ladings);*/
    }

    public function getCustomerOrderDetail($id){
        $subLadingCode = SubLadingCode::where('sub_lading_code',$id)->first();
        if(is_null($subLadingCode)){
            $ladings = LadingCode::query()->where('code','=',$id)->get();
            foreach ($ladings as $lading) {
                $lading->load([
                    'warehouseVnLadingItem','billCode'
                ]);
                $lading->billCode->load('customerOrderItems','customerOrderItems.images','customerOrderItems.customerOrder');
            }
            $verifyLadingCode = VerifyLadingCode::where('lading_code','=',$id)->first();
            $verifyCustomerOrderItems = VerifyCustomerOrderItem::where('verify_lading_code_id','=',$verifyLadingCode->id)->get();
            return $this->respondSuccessData(['ladingCode' =>$ladings,'verifyCustomerOrderItems'=>$verifyCustomerOrderItems]);

        } else {
            $lading_code = $subLadingCode->lading_code;
            $ladings = LadingCode::query()->where('code','=',$lading_code)->get();
            foreach ($ladings as $lading) {
                $lading->load([
                    'warehouseVnLadingItem','billCode'
                ]);
                $lading->billCode->load('customerOrderItems','customerOrderItems.images','customerOrderItems.customerOrder');
            }
            $verifyLadingCode = VerifyLadingCode::where('lading_code','=',$id)->first();
            $verifyCustomerOrderItems = VerifyCustomerOrderItem::where('verify_lading_code_id','=',$verifyLadingCode->id)->get();
            return $this->respondSuccessData(['ladingCode' =>$ladings,'verifyCustomerOrderItems'=>$verifyCustomerOrderItems,'subLadingCode'=>$subLadingCode]);
        }



    }


    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try{
            DB::beginTransaction();
            $verifyLadingCode = VerifyLadingCode::findOrFail($id);
            $ladingCode = $verifyLadingCode->lading_code;
            $subLadingCode = SubLadingCode::where('sub_lading_code',$ladingCode)->first();
            $type = 1;
            if(is_null($subLadingCode)){
                $verifyLadingCode->status = VerifyLadingCode::NOT_YET_VERIFY;
                $verifyLadingCode->save();

                VerifyCustomerOrderItem::where('verify_lading_code_id',$id)->delete();

                //UPDATE WarehouseVnLadingItem to WAIT TO TEST
                WarehouseVnLadingItem::where('lading_code',$ladingCode)->update(['status' => WarehouseVnLadingItem::STATUS_WAIT_TEST]);

                DB::commit();
                return $this->respondSuccessData(['type' =>$type,], 'Xóa thành công');
            } else {
                //Update parent LadingCode
                $parentLadingCode = $subLadingCode->lading_code;
                $parentVerifyLadingCode = VerifyLadingCode::where('lading_code',$parentLadingCode)->first();
                $parentVerifyLadingCode->status = VerifyLadingCode::NOT_YET_VERIFY;
                $parentVerifyLadingCode->save();

                $listSubLadingCode = SubLadingCode::where('lading_code',$subLadingCode->lading_code)->select('sub_lading_code')->get();

                // Delete SUB_LADING_CODE in SubLadingCode and VerifyLadingCode and VerifyCustomerOrderItem
                SubLadingCode::whereIn('sub_lading_code',$listSubLadingCode->toArray())->delete();
                $listVerifyLadingCodeId = VerifyLadingCode::whereIn('lading_code',$listSubLadingCode->toArray())->select('id')->get();
                VerifyCustomerOrderItem::whereIn('verify_lading_code_id',$listVerifyLadingCodeId->toArray())->delete();
                VerifyLadingCode::whereIn('lading_code',$listSubLadingCode->toArray())->delete();

                //RESTORE OLD DATA IN WAREHOUSE VN
                WarehouseVnLadingItem::whereIn('sub_lading_code',$listSubLadingCode->toArray())->delete();
                WarehouseVnLadingItem::where('lading_code',$parentLadingCode)->update(['status' => WarehouseVnLadingItem::STATUS_WAIT_TEST]);

                //Update type = 2;
                $type = 2;

                DB::commit();
                return $this->respondSuccessData(['type' =>$type,'addItem'=>$parentVerifyLadingCode->load('user','ladingCodeItem'),'deleteItem' =>$listVerifyLadingCodeId->toArray()], 'Xóa thành công');
            }
        }catch (\Exception $e){
            DB::rollBack();
            throw $e;
        }


    }
}
