<?php

namespace Modules\Rate\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Base\Http\Controllers\Controller;
use Modules\Rate\Models\Rate;
use Illuminate\Support\Facades\DB;

class RateController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $perPage = $this->getPerPage($request);
        $rates = Rate::filterWhere('date', '>=', $request->input('created_at_from'))
            ->filterWhere('date', '<=', $request->input('created_at_to'))
            ->paginate($perPage);

        return $this->respondSuccessData($rates)    ;
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('rate::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $rate = new Rate();
            $rate->fill($request->input());
            $rate->save();

            DB::commit();

            return $this->respondSuccessData($rate, 'Tạo tỷ giá thành công');
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
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
        $data = Rate::findOrFail($id);

        return $this->respondSuccessData($data);
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('rate::edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(Request $request,$id)
    {
        DB::beginTransaction();
        try {
            /** @var Rate $rate */
            $rate = Rate::findOrFail($id);

            $requestData = $request->all();
            $rate->fill($requestData);
            $rate->save();

            DB::commit();

            return $this->respondSuccessData($rate, 'Sửa thông tin tỷ giá thành công');
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $rate = Rate::findOrFail($id);
            $rate->delete();

            DB::commit();

            return $this->respondSuccessData([], 'Xóa tỷ giá hàng thành công');
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
}
