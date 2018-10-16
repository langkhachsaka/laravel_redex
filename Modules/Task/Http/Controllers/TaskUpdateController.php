<?php

namespace Modules\Task\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Base\Http\Controllers\Controller;
use Modules\Task\Models\Task;


class TaskUpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {

    }



    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $task = new Task();

        $task->fill($request->all());
        if($this->checkStartAndEndDate($task->start_date,$task->end_date))
        {
            $task->save();
            $task->load('userCreator', 'userPerformer');
            return $this->respondSuccessData($task, 'Thêm mới thành công');
        }
        else
        {
            throw new \Exception('Ngày kết thúc phải lớn hơn ngày bắt đầu');
           // return $this->respondInvalidData('Ngày kết thúc phải lớn hơn ngày bắt đầu');
        } 

        
    }

    /**
    * Check End_date must be greate then Start_Date
    *
    */
    public function checkStartAndEndDate($start_date, $end_date)
    {   if($end_date >= $start_date)
        {
            return true;
        } 
        else 
        {
            return false;
        }
    }
    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        //return view('task::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        //return view('task::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request,$id)
    {

        $task = Task::findOrFail($id);

        $task->fill($request->all());

        
        if($this->checkStartAndEndDate($task->start_date,$task->end_date))
        {
            $task->save();
            $task->load('userCreator', 'userPerformer');
            return $this->respondSuccessData($task, 'Cập nhật thành công');
        }
        else
        {
            throw new \Exception('Ngày kết thúc phải lớn hơn ngày bắt đầu');
           // return $this->respondInvalidData('Ngày kết thúc phải lớn hơn ngày bắt đầu');
        } 
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);

        $task->delete();

        return $this->respondSuccessData($task, 'Xóa thành công');

    }
}
