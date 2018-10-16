<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Base\Http\Controllers\Controller;
use Modules\User\Models\User;
use Modules\User\Models\UserRole;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('index', User::class);

        $perPage = $this->getPerPage($request);
        $users = User::with('warehouse','userRoles')
            ->whereFullLike('name', $request->input('name'))
            ->whereFullLike('username', $request->input('username'))
            ->whereFullLike('phone', $request->input('phone'))
            ->filterWhere('role', '=', $request->input('role'))
            ->orderBy('created_at','desc')
            ->paginate($perPage);

        return $this->respondSuccessData($users);
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function list(Request $request)
    {
        $this->authorize('list', User::class);

        $query = User::query()->limit(20);
        $listUserId = UserRole::where('role',$request->input('role'))->select('user_id')->get();
        $listUserId = $listUserId->pluck('user_id')->toArray();
        $query->whereFullLike('name', $request->input('q'))
            ->whereIn('id', $listUserId);

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
        try{
            DB::beginTransaction();
            $this->authorize('create', User::class);

            $user = new User();

            $requestData = $request->all();

            $validator = $this->validateRequestData($requestData);

            if ($validator->fails()) {
                return $this->respondInvalidData($validator->messages());
            }

            $user->fill($requestData);
            $user->role = User::ROLE_ADMIN;
            $user->password = Hash::make($request->input('password'));
            $user->remember_token = str_random(10);
            $user->save();

            $roles = $requestData["roles"];
            foreach ($roles as $role) {
                $userRole = new UserRole();
                $userRole->user_id = $user->id;
                $userRole->role = $role["role"];
                $userRole->save();
            }

            DB::commit();

            $user->load('warehouse','userRoles');
            return $this->respondSuccessData($user, 'Thêm tài khoản thành công');
        } catch (\Exception $e){
            DB::rollBack();
            throw $e;
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
        $this->authorize('view', User::class);

        $user = User::findOrFail($id);

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
        try{
            DB::beginTransaction();
            $this->authorize('update', User::class);

            $user = User::findOrFail($id);

            $validator = $this->validateRequestData(
                $request->all(),
                $this->isWarehouseStaff($request->input('role')),
                $request->filled('password'),
                $user->id
            );

            if ($validator->fails()) {
                return $this->respondInvalidData($validator->messages());
            }

            $user->fill($request->all());
            if ($request->filled('password')) {
                $user->password = Hash::make($request->input('password'));
            }

            $user->save();
            $roles = $request->roles;
            UserRole::where('user_id',$user->id)->delete();
            foreach ($roles as $role) {
                $userRole = new UserRole();
                $userRole->user_id = $user->id;
                $userRole->role = $role["role"];
                $userRole->save();
            }

            $user->load('warehouse','userRoles');
            DB::commit();
            return $this->respondSuccessData($user, 'Sửa tài khoản thành công');
        } catch (\Exception $e){
            DB::rollBack();
            throw $e;
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $this->authorize('delete', User::class);

        $user = User::findOrFail($id);
        $user->delete();

        return $this->respondSuccessData([], 'Xóa nhân viên thành công');
    }

    /**
     * @param $requestData
     * @param bool $hasPassword
     * @param bool $isWarehouseStaff
     * @param int $modelId
     * @return \Illuminate\Validation\Validator
     */
    private function validateRequestData($requestData, $isWarehouseStaff = false, $hasPassword = true, $modelId = 0)
    {
        return \Validator::make(
            $requestData,
            [
                'username' => 'bail|required|string|max:255|unique:users,username,' . $modelId,
                'name' => 'bail|required|string|max:255',
                'password' => $hasPassword ? 'bail|required|string|min:6|confirmed' : '',
                'email' => 'bail|required|string|max:255|unique:users,email,' . $modelId,
                'warehouse_id' => $isWarehouseStaff ? 'bail|required' : ''
            ],
            [
                'username.required' => 'Chưa nhập tên đăng nhập',
                'username.unique' => 'Tên đăng nhập đã tồn tại trong hệ thống.',
                'username.max' => 'Tên đăng nhập chứa tối đa 225 ký tự',
                'email.required' => 'Chưa nhập email',
                'email.unique' => 'Email đã tồn tại trong hệ thống.',
                'email.max' => 'Email chứa tối đa 225 ký tự',
                'name.required' => 'Chưa nhập tên',
                'name.max' => 'Tên chứa tối đa 225 ký tự',
                'password.required' => 'Chưa nhập mật khẩu',
                'password.min' => 'Mật khẩu tối thiểu 6 ký tự',
                'password.confirmed' => 'Mật khẩu chưa trùng nhau',
                'warehouse_id.required' => 'Chưa chọn kho làm việc',
            ]
        );
    }


    private function isWarehouseStaff($userRole)
    {
        if ($userRole == User::WAREHOUSE_STAFF_VN || $userRole == User::WAREHOUSE_STAFF_CN) {
            return true;
        }
        return false;
    }
}
