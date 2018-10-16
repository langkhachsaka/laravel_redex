<?php

namespace Modules\LadingCode\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Base\Http\Controllers\Controller;
use Modules\BillOfLading\Models\BillOfLading;
use Modules\CustomerOrder\Models\CustomerOrder;
use Modules\CustomerOrder\Models\CustomerOrderItem;
use Modules\LadingCode\Models\LadingCode;
use Modules\Rate\Models\Rate;
use Modules\Setting\Models\Setting;
use Modules\Shipment\Models\ShipmentItem;
use Modules\WarehouseReceivingVN\Models\WarehouseReceivingVN;

class LadingCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('index', LadingCode::class);

        $perPage = $this->getPerPage($request);
        $ladingCodes = LadingCode::whereFullLike('bill_code', $request->input('bill_code'))
            ->whereFullLike('code', $request->input('code'))
            ->orderby('id', 'desc')
            ->paginate($perPage);

        return $this->respondSuccessData($ladingCodes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        $this->authorize('create', LadingCode::class);

        $requestData = $request->input();
        $validator = \Validator::make(
            $requestData,
            [
                'code' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'bill_codes.*' => [
                    'required',
                    'string',
                    'distinct',
                    'max:255',
                    'exists:bill_codes,bill_code',
                    Rule::unique('lading_codes', 'bill_code')
                        ->where('code', data_get($requestData, 'code')),
                ],
            ],
            [
                'bill_codes.*.required' => 'Chưa nhập mã giao dịch',
                'bill_codes.*.string' => 'Mã giao dịch không hợp lệ',
                'bill_codes.*.max' => 'Mã giao dịch chứa tối đa 225 ký tự',
                'bill_codes.*.exists' => 'Mã giao dịch chưa có trên hệ thống',
                'bill_codes.*.distinct' => 'Mã giao dịch không được trùng lặp',
                'bill_codes.*.unique' => 'Mã giao dịch đã tồn tại với vận đơn trên hệ thống',

                'code.required' => 'Chưa nhập mã vận đơn',
                'code.max' => 'Mã vận đơn chứa tối đa 225 ký tự',
            ]
        );

        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }

        foreach ($requestData['bill_codes'] as $billCode) {
            $ladingCode = new LadingCode();

            $ladingCode->fill([
                'code' => $requestData['code'],
                'bill_code' => $billCode,
            ]);
            $ladingCode->save();
        }

        return $this->respondSuccessData($ladingCode, 'Thêm mã vận đơn thành công');
    }

    public function storeLadingCodeForBillOfLading(Request $request,$id){
        try{
            DB::beginTransaction();
            foreach ($request->lading_codes as $lading_code){
                $ladingCode = new LadingCode();
                $ladingCode->ladingcodetable_id = $id;
                $ladingCode->ladingcodetable_type = BillOfLading::class;
                $ladingCode->code = $lading_code;
                $ladingCode->save();

            }
            DB::commit();
            $billOfLading = BillOfLading::with(
                'courierCompany',
                'customer',
                'customer.customerAddresses',
                'seller',
                'ladingCodes'
            )->where('id',$id)->first();
            return $this->respondSuccessData($billOfLading, 'Thêm mã vận đơn thành công');
        } catch (\Exception $e){
            DB::rollBack();
            throw $e;
        }

    }
    public function updateLadingCodeForBillOfLading(Request $request,$id){
        try{

            DB::beginTransaction();
            LadingCode::where('ladingcodetable_id',$id)->delete();
            foreach ($request->lading_codes as $lading_code){
                $ladingCode = new LadingCode();
                $ladingCode->ladingcodetable_id = $id;
                $ladingCode->ladingcodetable_type = BillOfLading::class;
                $ladingCode->code = $lading_code;
                $ladingCode->save();

            }
            DB::commit();
            $billOfLading = BillOfLading::with(
                'courierCompany',
                'customer',
                'customer.customerAddresses',
                'seller',
                'ladingCodes'
            )->where('id',$id)->first();
            return $this->respondSuccessData($billOfLading, 'Thêm mã vận đơn thành công');
        } catch (\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string $bill_code
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, $bill_code)
    {
        $this->authorize('update', LadingCode::class);

        $requestData = $request->input();
        $requestData['bill_code'] = $bill_code;

        $validator = \Validator::make(
            $requestData,
            [
                'bill_code' => [
                    'required',
                    'string',
                    'max:255',
                    'exists:bill_codes,bill_code',
                ],
                'lading_codes.*' => [
                    'required',
                    'string',
                    'max:255',
                ],
            ],
            [
                'bill_code.required' => 'Chưa nhập mã giao dịch',
                'bill_code.string' => 'Mã giao dịch không hợp lệ',
                'bill_code.max' => 'Mã giao dịch chứa tối đa 225 ký tự',
                'bill_code.exists' => 'Mã giao dịch chưa có trên hệ thống',

                'lading_codes.*.required' => 'Chưa nhập mã vận đơn',
                'lading_codes.*.string' => 'Mã vận đơn không hợp lệ',
                'lading_codes.*.max' => 'Mã vận đơn chứa tối đa 225 ký tự',
            ]
        );

        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }

        $responseData = [];

        // Quick solution: delete all then insert new
        LadingCode::query()->where('bill_code', $bill_code)->delete();

        foreach ($requestData['lading_codes'] as $lading_code) {
            $ladingCode = new LadingCode([
                'code' => $lading_code,
                'bill_code' => $requestData['bill_code'],
            ]);
            $ladingCode->save();

            $responseData[] = $ladingCode;
        }

        return $this->respondSuccessData($responseData, 'Sửa mã vận đơn thành công');
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function import(Request $request)
    {
        DB::beginTransaction();
        try {
            $file = $request->file('file');

            $results = collect([]);

            Excel::load($file, function ($reader) use (&$results) {
                $results = $reader->get();
            });

            $responseData = [];

            foreach ($results as $row) {
                $attr = [
                    'code' => $row['ma_van_don'],
                    'bill_code' => $row['ma_giao_dich'],
                ];

                $validator = \Validator::make(
                    $attr,
                    [
                        'code' => [
                            'required',
                            'string',
                            'max:255',
                        ],
                        'bill_code' => [
                            'required',
                            'string',
                            'max:255',
                            'distinct',
                            'exists:bill_codes,bill_code',
                            Rule::unique('lading_codes', 'bill_code')
                                ->where('code', $attr['code']),
                        ],
                    ],
                    [
                        'bill_code.required' => 'Chưa nhập mã giao dịch',
                        'bill_code.string' => 'Mã giao dịch không hợp lệ',
                        'bill_code.max' => 'Mã giao dịch chứa tối đa 225 ký tự',
                        'bill_code.exists' => 'Mã giao dịch chưa có trên hệ thống',
                        'bill_code.unique' => 'Mã giao dịch đã tồn tại với vận đơn trên hệ thống',
                        'bill_codes.*.distinct' => 'Mã giao dịch không được trùng lặp',
                        'code.required' => 'Chưa nhập mã vận đơn',
                        'code.string' => 'Mã vận đơn không hợp lệ',
                        'code.max' => 'Mã vận đơn chứa tối đa 225 ký tự',
                    ]
                );

                $ladingCode = new LadingCode($attr);
                if ($validator->passes()) {
                    $ladingCode->save();

                    $responseData[] = [
                        'model' => $ladingCode,
                        'message' => 'Thêm thành công',
                        'status' => 'ok',
                    ];
                } else {
                    $responseData[] = [
                        'model' => $ladingCode,
                        'message' => $validator->getMessageBag()->first(),
                        'status' => 'error',
                    ];
                }
            }

            DB::commit();

            return $this->respondSuccessData($responseData);

        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }


    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $this->authorize('delete', LadingCode::class);

        /** @var LadingCode $ladingCode */
        $ladingCode = LadingCode::findOrFail($id);

        $ladingCode->delete();

        return $this->respondSuccessData($ladingCode, 'Xóa mã vận đơn thành công');
    }






    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     *
     * Link ví dụ http://dev.redex.vn/api/v1/lading-code/get-order/VD00200
     */
    public function getOrder($code)
    {
        $ladingcode = LadingCode::where('code', $code)->first();

        /** So sánh, trường 'ladingcodetable_type' :
         *      CustomerOrderItem::class thì ladingcodetable_id' là 'id' của CustomerOrderItem
         *      BillOfLading:class thì 'ladingcodetable_id' là 'id' của BillOfLading
         *
         *  Lấy relation thì cứ dùng load -> 'ladingcodetable' là nó sẽ tự hiểu.
         */
        if ($ladingcode && $ladingcode->ladingcodetable_type === CustomerOrderItem::class) {
            $ladingcode->load('ladingcodetable', 'ladingcodetable.customerOrder'); // Load customerOrderItem và CustomerOrder của nó.
        } elseif ($ladingcode) {
            $ladingcode->load('ladingcodetable'); // load billOfLading
        }

        return $this->respondSuccessData($ladingcode);
    }

    public function getRateCustomerOrder($id)
    {
        $order = CustomerOrder::findOrFail($id);
        $customer = $order->customer;

        if ($customer->rate) {
            return $this->respondSuccessData($customer->rate);
        }

        $setting = Setting::first();
        return $this->respondSuccessData($setting->rate);
    }

    public function getRateBillOfLading($id)
    {
        $bill = BillOfLading::findOrFail($id);

        $customer = $bill->customer;

        if ($customer->rate) {
            return $this->respondSuccessData($customer->rate);
        }

        $rate = Rate::lastOrderRate();

        return $this->respondSuccessData($rate);
    }

    public function getRate($ladingCodeId)
    {
        $ladingCode = LadingCode::findOrFail($ladingCodeId);

        if ($ladingCode->ladingcodetable_type === CustomerOrder::class) {
            $order = CustomerOrder::findOrFail($ladingCode->ladingcodetable_id);
            $customer = $order->customer;
        } else {
            $bill = BillOfLading::findOrFail($ladingCode->ladingcodetable_id);
            $customer = $bill->customer;
        }

        if ($customer->rate) {
            return $this->respondSuccessData($customer->rate);
        }

        $rate= Rate::lastOrderRate();

        return $this->respondSuccessData($rate);
    }
    public function getFactorConversion()
    {
        $factorConversion = Setting::select('factor_conversion')->first();

        return $this->respondSuccessData($factorConversion->factor_conversion);
    }

    public function getConversionVolume($id)
    {
        $ladingCode = LadingCode::findOrFail($id);
        $rate = 1;
        if ($ladingCode->ladingcodetable_type === CustomerOrder::class) {
            $order = CustomerOrder::findOrFail($ladingCode->ladingcodetable_id);
            $customer = $order->customer;
        } else {
            $bill = BillOfLading::findOrFail($ladingCode->ladingcodetable_id);
            $customer = $bill->customer;
        }

        if ($customer->rate) {
            $rate = $customer->rate;
        } else {
            $setting = Setting::first();
            $rate = $setting->rate;
        }
        $conversionVolume = ($ladingCode->height * $ladingCode->width * $ladingCode->length) / $rate;

        return $this->respondSuccessData($conversionVolume);
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function listForDelivery(Request $request)
    {
        //$this->authorize('list', LadingCode::class);

        $ladingCodeColumn = 'bill_of_lading_code';
        $shipmentItemTable = (new ShipmentItem())->getTable();
        $warehouseReceivingVNTable = (new WarehouseReceivingVN())->getTable();

        $query = ShipmentItem::query()
            ->selectRaw($ladingCodeColumn . ' AS code')
            ->join($warehouseReceivingVNTable, $warehouseReceivingVNTable . '.shipment_code', '=', $shipmentItemTable . '.shipment_code')
            ->where($warehouseReceivingVNTable . '.status', WarehouseReceivingVN::STATUS_CONFIRMED)
            ->limit(20);

        if ($request->has('q')) {
            $query->whereFullLike($ladingCodeColumn, $request->input('q'));
        }

        $codes = $query->pluck('code');

        $results = $codes->map(function ($code) {
            return [
                'id' => $code,
                'text' => $code,
            ];
        });

        return ['results' => $results];
    }

    public function getItemsByLadingCode($code)
    {
        $items = [];
        $a = LadingCode::where('code', $code)->with('ladingcodetable')->get();
        foreach ($a as $v) {
            $item = $v->ladingcodetable;
            if ($item instanceof CustomerOrderItem) {
                $item->load('customerOrder', 'images');
                $items['customer'] = $item->customerOrder->customer;
                $items['shipping'] = [
                    'name' => $item->customerOrder->customer_shipping_name,
                    'address' => $item->customerOrder->customer_shipping_address,
                    'phone' => $item->customerOrder->customer_shipping_phone,
                ];
                $items['order_items'][] = $item;
            } elseif ($item instanceof BillOfLading) {
                $item->load('customer', 'courierCompany');
                $items['customer'] = $item->customer;
                $items['shipping'] = [
                    'name' => $item->customer_shipping_name,
                    'address' => $item->customer_shipping_address,
                    'phone' => $item->customer_shipping_phone,
                ];
                $items['bill_of_ladings'][] = $item;
            }
        }

        return $this->respondSuccessData([
            'code' => $code,
            'items' => $items,
        ]);
    }





    /**
     * @param $requestData
     * @param bool $hasPassword
     * @param bool $isWarehouseStaff
     * @param int $modelId
     * @return \Illuminate\Validation\Validator
     */
    private function validateRequestData($requestData, $modelId = 0)
    {
        return \Validator::make(
            $requestData,
            [
                'code' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'bill_codes.*' => [
                    'required',
                    'string',
                    'max:255',
                    'exists:bill_codes,bill_code',
                    Rule::unique('lading_codes', 'bill_code')
                        ->where('code', data_get($requestData, 'code'))
                        ->ignore($modelId),
                ],
            ],
            [
                'bill_codes.*.required' => 'Chưa nhập mã giao dịch',
                'bill_codes.*.string' => 'Mã giao dịch không hợp lệ',
                'bill_codes.*.max' => 'Mã giao dịch chứa tối đa 225 ký tự',
                'bill_codes.*.exists' => 'Mã giao dịch chưa có trên hệ thống',
                'bill_codes.*.unique' => 'Mã giao dịch đã tồn tại với vận đơn trên hệ thống',

                'code.required' => 'Chưa nhập mã vận đơn',
                'code.max' => 'Mã vận đơn chứa tối đa 225 ký tự',
            ]
        );
    }

}
