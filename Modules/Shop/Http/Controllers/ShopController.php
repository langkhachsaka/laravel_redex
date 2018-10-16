<?php

namespace Modules\Shop\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Base\Http\Controllers\Controller;
use Modules\Shop\Models\Shop;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('index', Shop::class);

        $perPage = $this->getPerPage($request);
        $shops = Shop::whereFullLike('name', $request->input('name'))
            ->whereFullLike('link', $request->input('link'))
            ->paginate($perPage);

        return $this->respondSuccessData($shops);
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function list(Request $request)
    {
        $this->authorize('list', Shop::class);

        $query = Shop::query()->limit(20);
        if ($request->has('q')) {
            $query->whereFullLike('name', $request->input('q'));
        }
        return ['results' => $query->get(['id', 'name as text'])];
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
        $this->authorize('create', Shop::class);

        $shop = new Shop();

        $validator = $this->validateRequestData($request->input());

        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }

        $shop->fill($request->all());
        $shop->save();

        return $this->respondSuccessData($shop, 'Thêm nguồn hàng thành công');
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
        $this->authorize('view', Shop::class);

        $user = Shop::findOrFail($id);

        return $this->respondSuccessData($user);
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
        $this->authorize('update', Shop::class);

        /** @var Shop $shop */
        $shop = Shop::findOrFail($id);

        $validator = $this->validateRequestData($request->input());

        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }

        $shop->fill($request->all());
        $shop->save();

        return $this->respondSuccessData($shop, 'Thay đổi thông tin nguồn hàng thành công');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $this->authorize('delete', Shop::class);

        /** @var Shop $shop */
        $shop = Shop::findOrFail($id);
        $shop->delete();
        return $this->respondSuccessData([], 'Xóa thông tin nguồn hàng thành công');
    }

    private function validateRequestData($requestData)
    {
        return \Validator::make(
            $requestData,
            [
                'link' => 'bail|required|string|max:255',
                'name' => 'bail|required|unique:shops|string|max:255',
            ],
            [
                'name.required' => 'Chưa nhập tên shop',
                'name.unique' => 'Tên shop đã tồn tại trong hệ thống.',
                'name.max' => 'Tên shop chứa tối đa 225 ký tự',
                'link.required' => 'Chưa nhập link shop',
                'link.max' => 'Link chứa tối đa 225 ký tự',
            ]
        );
    }
}
