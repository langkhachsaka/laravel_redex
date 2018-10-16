<?php

namespace Modules\Complaint\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Config;
use Modules\Base\Http\Controllers\Controller;
use Modules\Complaint\Models\CaseComplaint;
use Modules\Complaint\Models\Complaint;
use Modules\Notification\Models\ComplaintNotification;
use Modules\Rate\Models\Rate;
use Modules\Setting\Models\Setting;
use Modules\Task\Models\ComplaintTask;
use Modules\Task\Models\Task;
use Modules\Task\Models\TasksUpdate;
use Illuminate\Support\Facades\DB;
use Modules\User\Models\User;
use Modules\VerifyLadingCode\Models\SubLadingCode;
use Psy\Exception\Exception;

class ComplaintController extends Controller
{

    protected $helper;
    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('index', Complaint::class);

        $perPage = $this->getPerPage($request);
        $complaints = Complaint::with('ordertable','customerOrderItem', 'userPerformer', 'customer')
            ->filterWhere('complaints.customer_id', '=', $request->input('customer_id'))
//            ->filterWhere('complaints.performer_id', '=', self::getCompaintAssignForUser())
            ->filterWhere('complaints.status', '=', $request->input('status'))
            ->filterWhere('complaints.created_at', '>=', $request->input('created_at_from'))
            ->filterWhere('complaints.created_at', '<=', $request->input('created_at_to'))
            ->orderBy('complaints.id', 'desc')
            ->paginate($perPage);

        return $this->respondSuccessData($complaints);
    }

    /*public static function getCompaintAssignForUser(){
        $role = auth()->user()->role;
        if($role == User::ROLE_ADMIN){
            return null;
        }
        if($role == User::ROLE_ORDERING_SERVICE_OFFICER || $role == User::ROLE_CUSTOMER_SERVICE_OFFICER){
            return auth()->user()->id;
        }
    }*/

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $this->authorize('create', Complaint::class);

        $requestData = $request->input();

        $validator = $this->validateRequestData($requestData);
        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }

        $filePath = '';

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $path = $file->storeAs(
                'upload/complaint/' . date('Y/m/d'),
                rand(10000000, 999999999) . '-' . $file->getClientOriginalName()
            );

            $arrPath = explode('/', $path);
            $filePath = 'upload/complaint/' . date('Y/m/d') . '/' . last($arrPath);
        }
        try {
            DB::beginTransaction();

            /** @var Complaint $complaint */
            $complaint = new Complaint(['status' => Complaint::STATUS_PENDING]);

            $complaint->fill($requestData);
            $complaint->file_report_path = $filePath;

            $complaint->save();

            //AUTO CHANGE STATUS OF TASK.
            ComplaintTask::newComplaintByUser($complaint);
            //END PROCESS

            // BEGIN NOTIFICATION
            ComplaintNotification::newComplaintByUser($complaint->id, $complaint->ordertable_id, $complaint->ordertable_type, auth()->id());
            ComplaintNotification::assignComplaint($complaint->id, $complaint->ordertable_id, $complaint->ordertable_type, $complaint->user_id);
            // END

            DB::commit();

            $complaint->load('ordertable', 'customer', 'complaintHistories');
            return $this->respondSuccessData($complaint, 'Thêm mới khiếu nại thành công');
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * Show the specified resource.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($id)
    {
        $this->authorize('view', Complaint::class);

        /** @var Complaint $complaint */
        $complaint = Complaint::findOrfail($id);
        $subLadingCode = SubLadingCode::where('sub_lading_code',$complaint->lading_code)->first();

        if(is_null($subLadingCode)){
            $complaint->load('caseComplaint','verifyCustomerOrderItem','customerOrderItem','customerOrderItem.images','customerOrderItem.shop','ladingCode','ladingCode.shop', 'user', 'customer', 'complaintHistories', 'complaintHistories.user');
            $cusRate = $this->helper->getCustomerRate($complaint->customerOrderItem->customer_order_id);
            $rate = Rate::lastOrderRate();
            return $this->respondSuccessData(['model' =>$complaint,'rate' => $rate,'customer_rate' => $cusRate]);
        } else {
            $complaint->load('caseComplaint','verifyCustomerOrderItem','customerOrderItem','customerOrderItem.images','customerOrderItem.shop','ladingCode','ladingCode.shop', 'user', 'customer', 'complaintHistories', 'complaintHistories.user');
            $cusRate = $this->helper->getCustomerRate($complaint->customerOrderItem->customer_order_id);
            $rate = Rate::lastOrderRate();
            return $this->respondSuccessData(['model' =>$complaint,'rate' => $rate,'customer_rate' => $cusRate]);
        }

    }

    /**
     * Admin confirm complaint
     * @param Request $request
     */
    public function adminConfirm(Request $request,$id){

        try{
            DB::beginTransaction();
            $requestData = $request->input();
            $complaint = Complaint::find($id);
            $complaint->fill($requestData);
            $complaint->status = Complaint::STATUS_ADMIN_PROCESSED;
            $complaint->save();

            $typeErrors = [];
            if(!is_null($complaint->error_size) && $complaint->error_size == 1){
                array_push($typeErrors,Complaint::CASE_ERROR_SIZE);
            }
            if(!is_null($complaint->error_collor)  && $complaint->error_collor == 1){
                array_push($typeErrors,Complaint::CASE_ERROR_COLLOR);
            }
            if(!is_null($complaint->error_product)  && $complaint->error_product == 1){
                array_push($typeErrors,Complaint::CASE_ERROR_PRODUCT);
            }
            if(!is_null($complaint->inadequate_product)  && $complaint->inadequate_product == 1 ){
                array_push($typeErrors,Complaint::CASE_ERROR_INADEQUATE_PRODUCT);
            }
            foreach($typeErrors as $typeError){
                $caseComplaint = new CaseComplaint();
                $caseComplaint->complaint_id = $complaint->id;
                $caseComplaint->case = $typeError;
                $caseComplaint->save();
            }
            //AUTO UPDATE TASK STATUS
            ComplaintTask::updateComplaintByUser($complaint);
            //END PROCESS

            DB::commit();
            return $this->respondSuccessData([],'Cập nhật thành công');
        } catch (\Exception $e){
            DB::rollBack();
        }
    }

    /**
     * @param Request $request
     * @param $id
     * Customer service confirm with customer
     */
    public function customerServiceConfirm(Request $request, $id){
        try{
            DB::beginTransaction();

            $complaint = Complaint::find($id);
            $complaint->status = Complaint::STATUS_CUSTOMER_SERVICE_PROCESSED;
            $complaint->save();
            $solutions = $request->solution;
            $customerComments = $request->customer_comment;
            foreach ($solutions as $key=>$solution){
                if(!!$solution){
                    $caseComplaint = CaseComplaint::where('complaint_id',$id)->where('case',$key)->first();
                    $caseComplaint->solution = $solution;
                    $caseComplaint->customer_comment = $customerComments[$key];
                    $caseComplaint->save();
                }
            }


            //AUTO UPDATE TASK STATUS
            ComplaintTask::updateComplaintByUser($complaint);
            //END PROCESS

            DB::commit();
            return $this->respondSuccessData([],'Cập nhật thành công');
        }
        catch (\Exception $e){
            dd($e);
            DB::rollBack();
        }
    }

    /**
     * @param Request $request
     * @param $id
     * Order Officer confirm with Shop
     */
    public function orderOfficerConfirm(Request $request, $id) {

        try{
            DB::beginTransaction();

            $complaint = Complaint::find($id);
            $complaint->status = Complaint::STATUS_ORDERING_OFFICER_PROCESSED;
            $complaint->save();
            $requestData = $request->input();
            if(isset($requestData['case_1'])){
                $caseComplaint = CaseComplaint::where('complaint_id',$id)->where('case', Complaint::CASE_ERROR_SIZE)->first();
                $case = $requestData['case_1'];
                $caseComplaint = $this->fillDataForCaseComplaint($caseComplaint,$case);
                $caseComplaint->save();
            }
            if(isset($requestData['case_2'])){
                $caseComplaint = CaseComplaint::where('complaint_id',$id)->where('case', Complaint::CASE_ERROR_COLLOR)->first();
                $case = $requestData['case_2'];
                $caseComplaint = $this->fillDataForCaseComplaint($caseComplaint,$case);
                $caseComplaint->save();
            }
            if(isset($requestData['case_3'])){
                $caseComplaint = CaseComplaint::where('complaint_id',$id)->where('case', Complaint::CASE_ERROR_PRODUCT)->first();
                $case = $requestData['case_3'];
                $caseComplaint = $this->fillDataForCaseComplaint($caseComplaint,$case);
                $caseComplaint->save();
            }
            if(isset($requestData['case_4'])){
                $caseComplaint = CaseComplaint::where('complaint_id',$id)->where('case', Complaint::CASE_ERROR_INADEQUATE_PRODUCT)->first();
                $case = $requestData['case_4'];
                $caseComplaint = $this->fillDataForCaseComplaint($caseComplaint,$case);
                $caseComplaint->save();
            }


            //AUTO UPDATE TASK STATUS
            ComplaintTask::updateComplaintByUser($complaint);
            //END PROCESS

            DB::commit();
            return $this->respondSuccessData([],'Cập nhật thành công');
        }catch (\Exception $e){

            throw $e;
            DB::rollBack();
        }

    }

    private function fillDataForCaseComplaint($caseComplaint, $case){
        $newCaseComplaint = $caseComplaint;
        if(isset($case['redex_comment'])){
            $newCaseComplaint->redex_comment = $case['redex_comment'];
        }
        if(isset($case['redex_solution'])){
            $newCaseComplaint->redex_solution = (int) $case['redex_solution'];
        }
        if(isset($case['order_office_solution'])){
            $newCaseComplaint->order_office_solution = (int)$case['order_office_solution'];
        }
        if(isset($case['money_shop_return'])){
            $newCaseComplaint->money_shop_return =(double)  $case['money_shop_return'];
        }
        if(isset($case['date_return_money'])){
            $newCaseComplaint->date_return_money = $case['date_return_money'];
        }
        if(isset($case['add_lading_code'])){
            $newCaseComplaint->add_lading_code = $case['add_lading_code'];
        }
        if(isset($case['date_of_delivery'])){
            $newCaseComplaint->date_of_delivery = $case['date_of_delivery'];
        }
        if(isset($case['sum_weight_back'])){
            $newCaseComplaint->sum_weight_back = (double) $case['sum_weight_back'];
        }
        if(isset($case['sum_weight_delivery'])){
            $newCaseComplaint->sum_weight_delivery =(double) $case['sum_weight_delivery'];
        }
        if(isset($case['total_customer_pay'])){
            $newCaseComplaint->total_customer_pay =(double) $case['total_customer_pay'];
        }
        if(isset($case['ship_inland_fee'])){
            $newCaseComplaint->ship_inland_fee = (double)$case['ship_inland_fee'];
        }
        if(isset($case['shop_pay'])){
            $newCaseComplaint->shop_pay = (double)$case['shop_pay'];
        }
        if(isset($case['fee_ship_vn_cn'])){
            $newCaseComplaint->fee_ship_vn_cn = (double)$case['fee_ship_vn_cn'];
        }
        if(isset($case['ship_inland_fee'])){
            $newCaseComplaint->ship_inland_fee = (double)$case['ship_inland_fee'];
        }
        if(isset($case['note'])){
            $newCaseComplaint->note = $case['note'];
        }
        return $newCaseComplaint;
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(Request $request, $id)
    {

        $requestData = $request->input();

        $validator = $this->validateRequestData($requestData);
        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }
        try {
            DB::beginTransaction();
            /** @var Complaint $complaint */
            $complaint = Complaint::findOrfail($id);
            $oldUserId = $complaint->user_id;
            $oldStatus = $complaint->status;
            $oldContent = $complaint->content;
            $this->authorize('update', $complaint);

            $filePath = $complaint->file_report_path;

            if ($request->hasFile('file')) {
                $file = $request->file('file');

                $path = $file->storeAs(
                    'upload/complaint/' . date('Y/m/d'),
                    rand(10000000, 999999999) . '-' . $file->getClientOriginalName()
                );

                $arrPath = explode('/', $path);
                $filePath = 'upload/complaint/' . date('Y/m/d') . '/' . last($arrPath);

                Storage::delete($complaint->file_report_path);
            }

            $complaint->fill($requestData);
            $complaint->file_report_path = $filePath;
            $complaint->save();

            //AUTO UPDATE TASK STATUS
           ComplaintTask::updateComplaintByUser($oldUserId, $oldStatus, $oldContent, $complaint);
           //END PROCESS

            // BEGIN NOTIFICATION
            if ($complaint->user_id != $oldUserId) {
                ComplaintNotification::assignComplaint($complaint->id, $complaint->ordertable_id, $complaint->ordertable_type, $complaint->user_id);
            }
            // END

            DB::commit();
            $complaint->load('ordertable', 'user', 'customer', 'complaintHistories');
            return $this->respondSuccessData($complaint, 'Sửa khiếu nại thành công');
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * Remove the specified resource from storage.
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $this->authorize('delete', Complaint::class);

        /** @var Complaint $complaint */
        $complaint = Complaint::findOrfail($id);
        $complaint->delete();

        //AUTO CHANGE STATUS OF TASK.
        ComplaintTask::deleteComplaintByUser($complaint);
        //END PROCESS CHANGE STATUS OF TASK.

        return $this->respondSuccessData([], 'Xóa khiếu nại thành công');
    }

    private function validateRequestData($requestData)
    {
        return \Validator::make(
            $requestData,
            [
                'title' => 'bail|required|string|max:255',
                'content' => 'bail|required|string',
            ],
            [
                'title.required' => 'Chưa nhập tiêu đề khiếu nại',
                'title.max' => 'Tiêu đề chứa tối đa 225 ký tự',
                'content.required' => 'Chưa nhập nội dung khiếu nại',
            ]
        );
    }

}
