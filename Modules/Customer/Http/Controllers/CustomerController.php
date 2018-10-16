<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Customer\Models\Customer;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Base\Http\Controllers\Controller;
use Modules\Customer\Models\CustomerAddress;
use Modules\Transaction\Models\Transaction;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('index', Customer::class);

        $perPage = $this->getPerPage($request);

        $customers = Customer::leftJoin('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
            ->distinct()
            ->select('customers.*')
            ->with(['customerAddress' => function ($q) use ($request) {
                /** @var QueryBuilder $q */
                if ($request->input('phone')) {
                    $q->where('phone', 'like', '%' . $request->input('phone') . '%');
                }
                if ($request->input('address')) {
                    $q->where('address', 'like', '%' . $request->input('address') . '%');
                }
            }])
            ->whereFullLike('customers.name', $request->input('name'))
            ->whereFullLike('username', $request->input('username'))
            ->whereFullLike('customer_addresses.phone', $request->input('phone'))
            ->whereFullLike('customer_addresses.address', $request->input('address'))
            ->orderby('customers.id', 'desc')
            ->paginate($perPage);

        return $this->respondSuccessData($customers);
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function list(Request $request)
    {
        $this->authorize('list', Customer::class);

        $query = Customer::query()->with('customerAddresses')->limit(20);

        if ($request->has('q')) {
            $query->whereFullLike('name', $request->input('q'));
        }

        return ['results' => $query->get(['id', 'name as text'])];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $this->authorize('create', Customer::class);

        DB::beginTransaction();
        try {
            /** @var Customer $customer */
            $customer = new Customer();

            $requestData = $request->all();
            $validator = $this->validateRequestData($requestData);

            if ($validator->fails()) {
                return $this->respondInvalidData($validator->messages());
            }

            $customer->fill($requestData);
            $customer->order_deposit_percent = $request->input('order_deposit_percent');

            $customer->password = Hash::make($request->input('password'));
            $customer->remember_token = str_random(10);
            $customer->save();

            /** @var CustomerAddress $customerAddress */
            $customerAddress = new CustomerAddress([
                'is_default' => 1,
                'customer_id' =>  $customer->id,
                'name' => $customer->name,
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'provincial_id' => $request->input('provincial_id'),
                'district_id' => $request->input('district_id'),
                'ward_id' => $request->input('ward_id'),
            ]);

            $customerAddress->save();

            DB::commit();
            $customer->load('customerAddress');

            return $this->respondSuccessData(
                $customer,
                'Tạo tài khoản khách hàng thành công'
            );
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
        $this->authorize('view', Customer::class);

        /** @var Customer $customer */
        $customer = Customer::with('customerAddresses')
            ->findOrFail($id);

        return $this->respondSuccessData($customer);
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
            $this->authorize('update', Customer::class);

            /** @var Customer $customer */
            $customer = Customer::findOrFail($id);

            $validator = $this->validateRequestData($request->all(), $request->filled('password'), $customer->id);

            if ($validator->fails()) {
                return $this->respondInvalidData($validator->messages());
            }

            $customer->fill($request->all());

            /** Only admin can change password's customers */
            if (auth()->user()->isAdmin() && $request->filled('password')) {
                $customer->password = Hash::make($request->input('password'));
            }

            $customer->name = $request->input('name');
            $customer->username = $request->input('username');
            $customer->email = $request->input('email');
            $customer->order_deposit_percent = $request->input('order_deposit_percent');

            $customer->save();
            $customer->customerAddress;

            $customerAddress = CustomerAddress::where('customer_id',$id)->where('is_default',1)->first();
            $customerAddress->name = $request->input('name');
            $customerAddress->save();
            DB::commit();
            return $this->respondSuccessData($customer, 'Sửa tài khoản thành công');
        }catch (\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $this->authorize('delete', Customer::class);

        /** @var Customer $customer */
        $customer = Customer::findOrFail($id);

        if ($customer->customerOrders()->exists()) {
            abort(500, 'Khách hàng đang có đơn hàng trong hệ thống');
        }

        if ($customer->billOfLading()->exists()) {
            abort(500, 'Khách hàng đang có đơn hàng vận chuyển trong hệ thống');
        }

        $customer->delete();

        return $this->respondSuccessData([], 'Xoá khách hàng thành công');
    }

    /**
     * @param $requestData
     * @param bool $hasPassword
     * @param int $modelID
     * @return \Illuminate\Validation\Validator
     */
    private function validateRequestData($requestData, $hasPassword = true, $modelID = 0)
    {
        return \Validator::make(
            $requestData,
            [
                'username' => 'bail|required|string|max:255|unique:customers,username,' . $modelID,
                'email' => 'bail|required|string|max:255|unique:customers,email,' . $modelID,
                'name' => 'bail|required|string|max:255',
                'password' => $hasPassword ? 'bail|required|string|min:6|confirmed' : '',
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
            ]
        );
    }

    public function recharge(Request $request, $id){
        $amount = $request->get('amount');
        $customer = Customer::find($id);
        $totalAmount = (float)$customer->wallet + (float)$amount;
        $customer->update(['wallet' => $totalAmount]);

        $attr = array();
        $attr['transactiontable_id'] = $id;
        $attr['transactiontable_type'] = Customer::class;
        $attr['money'] = $amount;
        $attr['type'] = Transaction::TYPE_RECHARGE;
        $attr['note'] = 'Nạp tiền';
        $attr['customer_id'] = $id;
        $attr['status'] = Transaction::STT_CONFIRMED;
        Transaction::create($attr);

        return $this->respondSuccessData([],'Nạp tiền thành công');
    }
}
