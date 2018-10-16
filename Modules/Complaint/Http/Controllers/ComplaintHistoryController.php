<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 11/05/2018
 * Time: 10:01 SA
 */

namespace Modules\Complaint\Http\Controllers;


use Illuminate\Http\Request;
use Modules\Base\Http\Controllers\Controller;
use Modules\Complaint\Models\Complaint;
use Modules\Complaint\Models\ComplaintHistory;

class ComplaintHistoryController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $this->getPerPage($request);
        $complaintHistories = ComplaintHistory::with('complaint')
            ->paginate($perPage);

        return $this->respondSuccessData($complaintHistories);
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $complaintHistory = new ComplaintHistory();

        $requestData = $request->input();

        $validator = $this->validateRequestData($requestData);
        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }

        $complaintHistory->fill($requestData);
        $complaintHistory->user_id = auth()->id();
        $complaintHistory->save();

        $complaintHistory->load('user');

        return $this->respondSuccessData($complaintHistory, 'Thêm mới lịch sử khiếu nại thành công');
    }

    /**
     * Show the specified resource.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var ComplaintHistory $complaintHistory */
        $complaintHistory = ComplaintHistory::findOrfail($id);

        $complaintHistory->load('complaint');
        return $this->respondSuccessData($complaintHistory);
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {

        $requestData = $request->input();

        $validator = $this->validateRequestData($requestData);
        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }

        /** @var ComplaintHistory $complaintHistory */
        $complaintHistory = ComplaintHistory::findOrfail($id);

        $complaintHistory->fill($requestData);
        $complaintHistory->save();

        $complaintHistory->load('complaint');
        return $this->respondSuccessData($complaintHistory, 'Sửa lịch sử khiếu nại thành công');
    }

    /**
     * Remove the specified resource from storage.
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var ComplaintHistory $complaintHistory */
        $complaintHistory = ComplaintHistory::findOrfail($id);
        $complaintHistory->delete();

        return $this->respondSuccessData([], 'Xóa lịch sử khiếu nại thành công');
    }

    private function validateRequestData($requestData)
    {
        return \Validator::make(
            $requestData,
            [
                'content' => 'bail|required|string',
            ],
            [
                'content.required' => 'Chưa nhập nội dung lịch sử khiếu nại',
            ]
        );
    }
}