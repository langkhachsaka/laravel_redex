<?php

namespace Modules\BillOfLading\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Base\Http\Controllers\Controller;
use Modules\BillOfLading\Models\BillOfLading;
use Modules\Notification\Models\BillOfLadingNotification;

class BillOfLadingController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('index', BillOfLading::class);

        $perPage = $this->getPerPage($request);

        /** Customer Service Officer can view only their bill of ladings */
        $seller_id = auth()->user()->isCustomerServiceOfficer()
            ? auth()->id()
            : $request->input('seller_id');

        /** Accountant can view only approved bill of ladings */
        $bill_status = auth()->user()->isAccountant()
            ?  BillOfLading::STATUS_APPROVED
            :  $request->input('status');

        $bills = BillOfLading::with(
            'courierCompany',
            'customer',
            'customer.customerAddresses',
            'seller',
            'ladingCodes'
        )
            ->filterWhere('bill_of_ladings.seller_id', '=', $seller_id)
            ->filterWhere('bill_of_ladings.customer_id', '=', $request->input('customer_id'))
            ->filterWhere('bill_of_ladings.courier_company_id', '=', $request->input('courier_company_id'))
            ->filterWhere('bill_of_ladings.bill_of_lading_code', '=', $request->input('bill_of_lading_code'))
            ->filterWhere('bill_of_ladings.created_at', '>=', $request->input('created_at_from'))
            ->filterWhere('bill_of_ladings.created_at', '<=', $request->input('created_at_to'))
            ->filterWhere('bill_of_ladings.status', '=', $bill_status)
            ->orderBy('bill_of_ladings.id', 'desc')
            ->paginate($perPage);

        return $this->respondSuccessData($bills);
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
        $this->authorize('create', BillOfLading::class);

        $bill = new BillOfLading();
        $requestData = $request->all();

        $filePath = '';

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $path = $file->storeAs(
                'upload/bill-of-lading/' . date('Y/m/d'),
                rand(10000000, 999999999) . '-' . $file->getClientOriginalName()
            );

            $arrPath = explode('/', $path);
            $filePath = 'upload/bill-of-lading/' . date('Y/m/d') . '/' . last($arrPath);
        }

        $bill->fill($requestData);
        $bill->file_path = $filePath;
        $bill->status = BillOfLading::STATUS_PENDING;

        auth()->user()->isCustomerServiceOfficer()
            ? $bill->seller_id = auth()->id()
            : $request->input('seller_id');

        $bill->save();

        // BEGIN NOTIFICATION
        BillOfLadingNotification::newBillOffLadingByUser($bill->id, auth()->id());
        BillOfLadingNotification::assignBillOfLading($bill->id, $bill->seller_id, auth()->id());
        //END

        $bill->load('courierCompany', 'customer', 'customer.customerAddresses', 'seller', 'ladingCodes');

        return $this->respondSuccessData($bill, 'Thêm vận đơn thành công');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($id)
    {
        $bill = BillOfLading::with(
            'courierCompany',
            'customer',
            'customer.customerAddresses',
            'seller',
            'ladingCodes'
        )
            ->orderBy('id', 'desc')
            ->findOrFail($id);

        $this->authorize('view', $bill);

        return $this->respondSuccessData($bill);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function approve($id)
    {
        $this->authorize('approve', BillOfLading::class);

        DB::beginTransaction();
        try {

            /** @var BillOfLading $bill */
            $bill = BillOfLading::findOrFail($id);
            $bill->status = BillOfLading::STATUS_APPROVED;

            $bill->save();

            // BEGIN NOTIFICATION
            BillOfLadingNotification::approveBillOfLading($bill->id, $bill->seller_id, auth()->id());
            // END

            DB::commit();
            $bill->load('courierCompany', 'customer', 'customer.customerAddresses', 'seller', 'ladingCodes');

            return $this->respondSuccessData($bill, 'Cập nhật trạng thái đơn vận chuyển thành công');
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, $id)
    {
        /** @var BillOfLading $bill */
        $bill = BillOfLading::findOrFail($id);

        $this->authorize('update', $bill);

        $oldSellerID = $bill->seller_id;

        $requestData = $request->all();

        $filePath = $bill->file_path;

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $path = $file->storeAs(
                'upload/bill-of-lading/' . date('Y/m/d'),
                rand(10000000, 999999999) . '-' . $file->getClientOriginalName()
            );

            $arrPath = explode('/', $path);
            $filePath = 'upload/bill-of-lading/' . date('Y/m/d') . '/' . last($arrPath);

            Storage::delete($bill->file_path);
        }

        $bill->fill($requestData);
        $bill->file_path = $filePath;
        $bill->save();

        if ($oldSellerID != $bill->seller_id) {
            BillOfLadingNotification::assignBillOfLading($bill->id, $bill->seller_id, auth()->id());
            BillOfLadingNotification::unAssignBillOfLading($bill->id, $oldSellerID, auth()->id());
        }

        $bill->load('courierCompany', 'customer', 'customer.customerAddresses', 'seller', 'ladingCodes');

        return $this->respondSuccessData($bill, 'Thay đổi thông tin vận đơn thành công');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $this->authorize('delete', BillOfLading::class);
        $bill = BillOfLading::findOrFail($id);

        if (!Storage::delete($bill->file_path)) {
            abort(500, 'Xảy ra lỗi khi xoá file excel');
        }

        $bill->delete();

        //BEGIN NOTIFICATION
        BillOfLadingNotification::deleteBillOfLadingByManagement($bill->id, $bill->seller_id, auth()->id());
        //END

        return $this->respondSuccessData([], 'Xóa vận đơn thành công');
    }

    /**
     * @param $requestData
     * @return \Illuminate\Validation\Validator
     */
    /*private function validateRequestData($requestData)
    {
        return \Validator::make(
            $requestData,
            [
                'code_vn' => 'bail|required|string|max:255',
            ],
            [
                'code_vn.required' => 'Chưa nhập mã vận đơn',
                'code_vn.max' => 'Mã vận đơn dài tối đa 255 ký tự',
            ]
        );
    }*/
}
