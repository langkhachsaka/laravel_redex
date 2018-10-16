<?php

namespace Modules\Task\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Modules\Base\Http\Controllers\Controller;
use Modules\Notification\Models\TaskNotification;
use Modules\Task\Models\Task;
use Modules\Task\Services\TaskService;
use Modules\Task\Models\TasksUpdate;
use Modules\CustomerOrder\Models\CustomerOrder;
use Modules\Complaint\Models\Complaint;
use Modules\Transaction\Models\Transaction;
use Modules\User\Models\User;
use Modules\User\Models\UserRole;
use Modules\VerifyLadingCode\Models\VerifyLadingCode;

class TaskController extends Controller
{
	/**
	 * Display a listing of the resource.
	 * @return Response
	 */
	public function index(Request $request)
	{
		$perPage = $this->getPerPage($request);

		$tasks = Task::with('userCreator','userPerformer','customerOrder','customerOrder.customer','complaint')
            ->whereIn('tasks.task_type', TaskService::getUserType())
            ->WhereFullLike('tasks.title', $request->input('title'))
			->WhereFullLike('tasks.customer_order_id', $request->input('order_id'))
			->filterWhere('tasks.start_date', '>=', $request->input('start_date_from'))
			->filterWhere('tasks.start_date', '<=', $request->input('start_date_to'))
			->filterWhere('tasks.end_date', '>=', $request->input('end_date_from'))
			->filterWhere('tasks.end_date', '<=', $request->input('end_date_to'))
			->WhereFullLike('tasks.description', $request->input('description'))
			->filterWhere('tasks.status', '=', $request->input('status'))
			->filterWhere('tasks.creator_id', '=', $request->input('creator_id'))
			->filterWhere('tasks.performer_id', '=', $request->input('performer_id'))
			->orderBy('created_at', 'desc')
			->paginate($perPage);

		return $this->respondSuccessData($tasks);
	}



	/**
	 * Show the specified resource.
	 * @return Response
	 */
	public function show($id)
	{
		$tasks = Task::with('userCreator','userPerformer','tasksUpdate','complaint','tasksUpdate.userCreator','tasksUpdate.userPerformer','tasksUpdate.userUpdater','customerOrder','customerOrder.customer','tasksUpdate.complaint')
			->findOrFail($id);
		return $this->respondSuccessData($tasks);
	}


    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request,$id)
    {

        $task = Task::findOrFail($id);
        /** @var int $oldPerformerID  -- for notification -- trinq*/
        $oldPerformerID = $task->performer_id;

        //Use for reflect to Customer_Order
        $oldStatus = $task->status;
        $newStatus = $request->input('status');
        $oldPerformerId = $task->performer_id;
        $newPerformerId = $request->input('performer_id');

        // Validate data
        $requestData = $request->all();
        $validator = $this->validateRequestData($requestData,false);
        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }
        if($this->checkUpdate($requestData,$task)){
            return $this->respondSuccessData([],$this->checkUpdate($requestData,$task));
        }
        if($requestData['end_date'] =='null'){
            unset($requestData['end_date'] );
        }
        $task->fill($requestData);
        $task->status = (int)$task->status;
        $task->task_type = (int) $task->task_type;
        try{

            DB::beginTransaction();
            $task->save();

			//Update Customer_Order when change status and performer_id
			if($oldStatus != $newStatus) {
				$cusOrderStatus = TaskService::statusCusOrderMapping($newStatus);
				$complaintStatus = TaskService::statusComplaintMapping($newStatus);

				if( $cusOrderStatus != -1){
					$order = CustomerOrder::findOrFail($task->customer_order_id);
					$order->status =  $cusOrderStatus;
					$order->seller_id = $task->performer_id;
					$order->save();

				}
				if( $complaintStatus != -1){
					$complaint = Complaint::findOrFail($task->complaint_id);
					$complaint->status =  $complaintStatus;
					$complaint->user_id = $task->performer_id;
					$complaint->save();
				}
			}

			// IF STATUS = APPROVE, ASSIGN FOR ACCOUNTANT : CREATE NEW TASK FOR ACCOUNTANT
            if($oldPerformerID!=$newPerformerId && $task->status == Task::APPROVED_CUSTOMER_ORDER
                && in_array(User::ROLE_ACCOUNTANT, TaskService::getUserRole($task->performer_id))){
			    $this->createTaskForAccountant($task);
            }
            // IF STATUS = ORDER_INIT, ASSIGN FOR CUSTOMER_SERVICE : ASSIGN SELLER_ID IN CUSTOMER_ORDER
            if($oldPerformerID!=$newPerformerId && $task->status == Task::ORDER_INIT
                && in_array(User::ROLE_CUSTOMER_SERVICE_OFFICER, TaskService::getUserRole($task->performer_id))){
                CustomerOrder::findOrFail($task->customer_order_id)->update(['seller_id' => $task->performer_id]);
            }
            //END

            // IF STATUS = DEPOSITED, ASSIGN FOR ROLE_ORDERING_MANAGEMENT : CREATE NEW TASK FOR ROLE_ORDERING_MANAGEMENT
            if($oldPerformerID!=$newPerformerId && $task->status == Task::CUSTOMER_DEPOSITED
                && in_array(User::ROLE_ORDERING_MANAGEMENT, TaskService::getUserRole($task->performer_id))){
                $this->createTaskForOrdering($task);
            }
            //END

            // IF STATUS = ORDERED_CN_SUCCESS, ASSIGN FOR ROLE_DELIVERING_AND_RECEIVING_MANAGEMENT : CREATE NEW TASK FOR ROLE_DELIVERING_AND_RECEIVING_MANAGEMENT
            if(($oldStatus!=$newStatus || $oldPerformerID!=$newPerformerId) && $task->status == Task::ORDERED_CN_SUCCESS
                && in_array(User::ROLE_DELIVERING_AND_RECEIVING_MANAGEMENT, TaskService::getUserRole($task->performer_id))){
                $this->createTaskForDeceiVingDelievering($task);
            }
            //END

            // IF TASK_TYPE = ACCOUNTANT AND SET FOR MONEY AND NOTE => CREATE NEW TRANSACTION
            if($task->task_type == Task::TYPE_ACCOUNTANT && ($request->money)){
                $transaction = new Transaction();
                $transaction->user_id = auth()->id();
                $transaction->user_name = auth()->user()->name;
                $transaction->transactiontable_id = $task->customer_order_id;
                $transaction->transactiontable_type = CustomerOrder::class;
                $transaction->money = $request->money;
                $transaction->type = Transaction::TYPE_DEPOSIT;
                $transaction->note = $request->note;
                $transaction->save();

                $updater_id = auth()->user()->id;
                $comment = 'Khách hàng đã đặt cọc ' . $transaction->money.'đ, với nội dung : '.$transaction->note.'.';
                $transaction_id = $transaction->id;
                TasksUpdate::copyTask($task, $updater_id,$comment,$transaction_id);
            } else {
                $updater_id = auth()->user()->id;
                TasksUpdate::copyTask($task, $updater_id);
            }

            //IF TYPE = COMPLAINT AND STATUS = ADMIN_PROCESSED AND UPDATE PERFORMER_ID TO CUSTOMER_SERVICE => ASSIGN COMPLAINT TO CUSTOMER_SERVICE
            if($task->task_type == Task::TYPE_COMPLAINT && $task->status == Task::COMPLAINT_ADMIN_PROCESSED && in_array(User::ROLE_CUSTOMER_SERVICE_OFFICER, TaskService::getUserRole($task->performer_id))){
                $listComplaint = $task->complaint_id;
                $listComplaints = explode('_',$listComplaint);
                Complaint::whereIn('id',$listComplaints)->update(['performer_id'=>$task->performer_id]);
            }
            //IF TYPE = COMPLAINT AND STATUS = CUSTOMER_SERVICE AND UPDATE PERFORMER_ID TO ORDERRING_OFFICER => ASSIGN COMPLAINT TO CUSTOMER_SERVICE
            if($task->task_type == Task::TYPE_COMPLAINT && $task->status == Task::COMPLAINT_CUSTOMER_SERVICE_PROCESSED && in_array(User::ROLE_ORDERING_SERVICE_OFFICER, TaskService::getUserRole($task->performer_id))){
                $listComplaint = $task->complaint_id;
                $listComplaints = explode('_',$listComplaint);
                Complaint::whereIn('id',$listComplaints)->update(['performer_id'=>$task->performer_id]);
            }

            //IF TYPE = VERIFY AND UPDATE PERFORMER_ID => ASSSIGN TASK VERIFY TO NEW PERFORMER_ID;
            if($task->task_type == Task::TYPE_VERIFY_LADING_CODE && $task->status == Task::VERIFY_PENDING
            && $oldPerformerId != $newPerformerId && in_array(User::ROLE_VIETNAMESE_SHIPPING_OFFICER, TaskService::getUserRole($task->performer_id))){
                $ladingCodes = $task->lading_codes;
                $listladingCode = explode(' ',$ladingCodes);
                VerifyLadingCode::whereIn('lading_code',$listladingCode)->update(['verifier_id' =>$newPerformerId]);
            }

            // BEGIN NOTIFICATION
            if ($task->performer_id  != $oldPerformerID) {
                TaskNotification::assignTask($task->id, $task->performer_id, auth()->user()->id);
            }
            // END

			DB::commit();
			$task->load('userCreator','userPerformer','tasksUpdate','complaint','tasksUpdate.userCreator','tasksUpdate.userPerformer','tasksUpdate.userUpdater','customerOrder','customerOrder.customer','tasksUpdate.complaint');
			return $this->respondSuccessData($task, 'Cập nhật thành công');
	   } catch (\Exception $ex) {
			DB::rollBack();
			throw $ex;
		}

	}


    /**
     * @param Request $request
     * @return array
     * Get List user for assign
     */
    public function listUser(Request $request)
    {
        $userRoles = UserRole::where('user_id',auth()->user()->id)->select('role')->get();
        $roles = $userRoles->pluck('role');

        $userId = [];
        /*$task_type = $request->task_type;
        switch ($task_type){
            case Task::TYPE_CUSTOMER_SERVICE :
                $userId = array(User)
        }*/
        foreach ($roles as $role){
            if($role == User::ROLE_CUSTOMER_SERVICE_OFFICER) {
                $userIds = UserRole::whereIn('role', array(User::ROLE_CUSTOMER_SERVICE_MANAGEMENT, User::ROLE_CUSTOMER_SERVICE_OFFICER, User::ROLE_ORDERING_SERVICE_OFFICER))->get();
//                $query->whereIn('role', array(User::ROLE_CUSTOMER_SERVICE_MANAGEMENT, User::ROLE_CUSTOMER_SERVICE_OFFICER, User::ROLE_ORDERING_SERVICE_OFFICER));
                $userIdsArr = $userIds->pluck('user_id');
                $userId = array_merge($userId,$userIdsArr->toArray());
            } else if($role == User::ROLE_CUSTOMER_SERVICE_MANAGEMENT) {
                $userIds = UserRole::whereIn('role',array(User::ROLE_CUSTOMER_SERVICE_MANAGEMENT, User::ROLE_ACCOUNTANT))->get();
                $userIdsArr = $userIds->pluck('user_id');
                $userId = array_merge($userId,$userIdsArr->toArray());
            } else if($role == User::ROLE_ACCOUNTANT) {
                $userIds = UserRole::whereIn('role', array(User::ROLE_ORDERING_MANAGEMENT, User::ROLE_ACCOUNTANT))->get();
                $userIdsArr = $userIds->pluck('user_id');
            } else if($role == User::ROLE_ORDERING_MANAGEMENT) {
                $userIds = UserRole::whereIn('role', array(User::ROLE_ORDERING_MANAGEMENT, User::ROLE_ACCOUNTANT, User::ROLE_DELIVERING_AND_RECEIVING_MANAGEMENT))->get();
                $userIdsArr = $userIds->pluck('user_id');
                $userId = array_merge($userId,$userIdsArr->toArray());
            } else if($role == User::ROLE_ORDERING_SERVICE_OFFICER) {
                $userIds = UserRole::whereIn('role', array(User::ROLE_ORDERING_MANAGEMENT, User::ROLE_ORDERING_SERVICE_OFFICER))->get();
                $userIdsArr = $userIds->pluck('user_id');
                $userId = array_merge($userId,$userIdsArr->toArray());
            } else if($role == User::ROLE_ADMIN) {
                $userIds = UserRole::select('user_id')->distinct()->get();

                $userIdsArr = $userIds->pluck('user_id');
                $userId = array_merge($userId,$userIdsArr->toArray());
            }
        }

        $query = User::query()->limit(20);
        $query->whereFullLike('name', $request->input('q'));
        if(count($userId) > 0 ){
            $userId = array_unique($userId);
            $query->whereIn('id', $userId);
        }

        $result = $query->get(['id', 'name','role']);
        foreach ($result as $item) {
            $item->load('userRoles');
        }
        //Push item "Tôi" in first
        $resultArray = array();
/*        $item = array('id' => auth()->user()->id, 'text' => '<< Tôi >>' );
        array_push($resultArray,$item);*/
        foreach($result as $obj){
            $objRole = $obj->userRoles->pluck('role')->toArray();
            $roleNames = [];
            foreach ($objRole as $role){
                array_push($roleNames,TaskService::getUserRoleName($role));
            }
            $resultArray[] = array(
                "id" => $obj->id,
                "text" => $obj->name.'( '.implode(", ",$roleNames).')'
            );
        }

        return ['results' => $resultArray];
    }

    /**
     * @param $requestData
     * @param $isCreateNew
     * @return mixed
     * @throws \Exception
     */
    private function validateRequestData($requestData, $isCreateNew)
    {
        if($isCreateNew){
           
            if (Task::where('customer_order_id', $requestData['customer_order_id'])->exists()) {
               throw new \Exception('Đã tồn tại nhiệm vụ cho mã đơn hàng này!');
            }
            if (!CustomerOrder::where('id', $requestData['customer_order_id'])->exists()) {
               throw new \Exception('Không tồn tại mã đơn hàng này!');
            }
        }

        return \Validator::make(
            $requestData,
            [

                'title' => 'required',
                'description' => 'required',
                'status' => 'required',
                'performer_id' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',

            ],
            [
                'required' => 'Vui lòng nhập những thông tin bắt buộc',               
            ]
        );
    }

    /**
     * IF STATUS = APPROVE, ASSIGN FOR ACCOUNTANT : CREATE NEW TASK FOR ACCOUNTANT
     * @param $task
     */
    private function createTaskForAccountant($task){
        if( Task::where('customer_order_id', $task->customer_order_id)
            ->where('task_type', Task::TYPE_ACCOUNTANT)
            ->exists()) {
            $accountantTask = Task::where('customer_order_id', $task->customer_order_id)
                ->where('task_type', Task::TYPE_ACCOUNTANT)
                ->first();
            $accountantTask->performer_id = $task->performer_id;
            $accountantTask->save();

            $updater_id = null;
            TasksUpdate::copyTask($accountantTask, $updater_id);
        } else {
            $accountantTask = new Task();
            $accountantTask->task_type = Task::TYPE_ACCOUNTANT;
            $accountantTask->customer_order_id = $task->customer_order_id;
            $accountantTask->title = 'Xử lý đặt cọc cho đơn hàng '. $task->customer_order_id;
            $accountantTask->description = $task->description;
            $accountantTask->creator_id = auth()->user()->id;
            $accountantTask->performer_id = $task->performer_id;
            $accountantTask->start_date = $task->start_date;
            $accountantTask->end_date = $task->end_date;
            $accountantTask->status = Task::APPROVED_CUSTOMER_ORDER;
            $accountantTask->save();

            $updater_id = null;
            TasksUpdate::copyTask($accountantTask, $updater_id);
        }
    }

    /**
     *  IF STATUS = DEPOSITED, ASSIGN FOR ROLE_ORDERING_MANAGEMENT : CREATE NEW TASK FOR ROLE_ORDERING_MANAGEMENT
     * @param $task
     */
    private function createTaskForOrdering($task){
        if( Task::where('customer_order_id', $task->customer_order_id)
            ->where('task_type', Task::TYPE_ORDERING)
            ->exists()) {
            $orderingTask = Task::where('customer_order_id', $task->customer_order_id)
                ->where('task_type', Task::TYPE_ORDERING)
                ->first();
            $orderingTask->performer_id = $task->performer_id;
            $orderingTask->save();

            $updater_id = null;
            TasksUpdate::copyTask($orderingTask, $updater_id);
        } else {
            $orderingTask = new Task();
            $orderingTask->task_type = Task::TYPE_ORDERING;
            $orderingTask->customer_order_id = $task->customer_order_id;
            $orderingTask->title = 'Xử lý đặt hàng cho đơn hàng ' . $task->customer_order_id;
            $orderingTask->description = $task->description;
            $orderingTask->creator_id = auth()->user()->id;
            $orderingTask->performer_id = $task->performer_id;
            $orderingTask->start_date = $task->start_date;
            $orderingTask->end_date = $task->end_date;
            $orderingTask->status = Task::CUSTOMER_DEPOSITED;
            $orderingTask->save();

            $updater_id = null;
            TasksUpdate::copyTask($orderingTask, $updater_id);
        }
    }


    private function createTaskForDeceiVingDelievering($task){
        if( Task::where('customer_order_id', $task->customer_order_id)
            ->where('task_type', Task::TYPE_DELIVERING_AND_RECEIVING)
            ->exists()) {
            $deliveringTask = Task::where('customer_order_id', $task->customer_order_id)
                ->where('task_type', Task::TYPE_DELIVERING_AND_RECEIVING)
                ->first();
            $deliveringTask->performer_id = $task->performer_id;
            $deliveringTask->save();

            $updater_id = null;
            TasksUpdate::copyTask($deliveringTask, $updater_id);
        } else {
            $deliveringTask = new Task();
            $deliveringTask->task_type = Task::TYPE_DELIVERING_AND_RECEIVING;
            $deliveringTask->customer_order_id = $task->customer_order_id;
            $deliveringTask->title = 'Xử lý nhận và giao cho đơn hàng '. $task->customer_order_id;
            $deliveringTask->description = $task->description;
            $deliveringTask->creator_id = auth()->user()->id;
            $deliveringTask->performer_id = $task->performer_id;
            $deliveringTask->start_date = $task->start_date;
            $deliveringTask->end_date = $task->end_date;
            $deliveringTask->status = $task->status;
            $deliveringTask->save();

            $updater_id = null;
            TasksUpdate::copyTask($deliveringTask, $updater_id);
        }
    }
    /**
     * @param $requestData
     * @param $oldTask
     * @throws \Exception
     * Check nothing was change
     */
    public function checkUpdate($requestData, $oldTask)
    {
        if($requestData['description'] == '<p><br></p>') {
            return 'Mô tả không được để trống';
        }
        $timestamp = strtotime($requestData['start_date']);
        if($requestData['end_date'] < date('Y-m-d',$timestamp)) {
            return 'Ngày kết thúc phải lớn hơn ngày bắt đầu';
        }
        //Khi đơn hàng đã duyệt => Nhân viên không được cập nhật trạng thái
        if(in_array(User::ROLE_CUSTOMER_SERVICE_OFFICER, TaskService::getUserRole(auth()->user()->id)) && $oldTask->status == Task::APPROVED_CUSTOMER_ORDER && $requestData['status']!=Task::APPROVED_CUSTOMER_ORDER){
            return 'Đơn hàng đã được duyệt, nhân viên không có quyền thay đổi trạng thái.';
        }
        //Khi đơn hàng đã duyêt và assign cho kế toán : ko đc thay đổi trạng thái và người thực hiện
        if($oldTask->performer_id && !in_array(User::ROLE_ADMIN, TaskService::getUserRole(auth()->user()->id))){
            if( !in_array(User::ROLE_ACCOUNTANT, TaskService::getUserRole(auth()->user()->id))
                && $oldTask->status == Task::APPROVED_CUSTOMER_ORDER
                && in_array(User::ROLE_ACCOUNTANT, TaskService::getUserRole($oldTask->performer_id))
                && ( $oldTask->performer_id !=$requestData['performer_id'] || $requestData['status'] != $oldTask->status )){
                return 'Đơn hàng đã được giao cho kế toán xử lý đặt cọc. Không được thay đổi trạng thái và người thực hiện';
            }
        }
        //Nhiệm vụ cho bộ phân giao nhận => kế toán chỉ xác nhận đã đặt cọc
        if($oldTask->task_type == Task::TYPE_ORDERING ){
            if( in_array(User::ROLE_ACCOUNTANT,TaskService::getUserRole(auth()->user()->id))
                && $oldTask->status == Task::CUSTOMER_DEPOSITED
                &&  $requestData['status'] != $oldTask->status){
                return 'Kế toán chỉ xử lý đặt cọc cho nhiệm vụ này';
            } else if( in_array(User::ROLE_ORDERING_SERVICE_OFFICER,TaskService::getUserRole(auth()->user()->id)) || in_array(User::ROLE_ORDERING_MANAGEMENT,TaskService::getUserRole(auth()->user()->id))
                && $requestData['status'] == Task::DEPOSITE_CN
                &&  $requestData['status'] != $oldTask->status){
                return 'Bộ phận đặt hàng chỉ thực hiện đặt hàng cho nhiệm vụ này';
            }
            /*if($requestData['status'] == Task::DEPOSITE_CN && !isset($requestData['money'])) {
                return 'Vui lòng nhập vào số tiền đã đặt';
            }*/
        }

        // Đơn hàng đã đặt cọc, chuyển cho nhân viên đặt hàng : ko đc cập nhật nữa
        if($oldTask->task_type == Task::TYPE_ACCOUNTANT && $oldTask->performer_id && !in_array(User::ROLE_ADMIN,TaskService::getUserRole(auth()->user()->id))){
            if( $oldTask->status == Task::CUSTOMER_DEPOSITED
                && $requestData['status']!=$oldTask->status
                ||
                in_array(User::ROLE_ORDERING_MANAGEMENT,TaskService::getUserRole($oldTask->performer_id))
                && $oldTask->performer_id !=$requestData['performer_id']){
                return 'Nhiệm vụ đã được chuyển cho nhân viên đặt hàng. Không được thay đổi trạng thái và người thực hiện';
            }
        }

        if($requestData['title'] == $oldTask->title && $requestData['description'] == $oldTask->description &&
            (int)$requestData['status'] == $oldTask->status && (int)$requestData['performer_id'] == $oldTask->performer_id
            && $requestData['start_date'] == $oldTask->start_date && $requestData['end_date'] == $oldTask->end_date && !isset($requestData['comment']) && !isset($requestData['money'])){

            /*throw new \Exception('Không có gì thay đổi.');*/
            return 'Không có gì thay đổi.';
        }
        if($requestData['description'] == '<p><br></p>') {
            return 'Mô tả không được để trống';
        }
    }
}
