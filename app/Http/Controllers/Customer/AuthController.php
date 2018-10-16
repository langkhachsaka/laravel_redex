<?php

namespace App\Http\Controllers\Customer;

use Modules\Customer\Models\Customer;
use Modules\Customer\Models\CustomerAddress;
use Modules\AreaCode\Models\AreaCode;
use Validator;
use Auth;
use Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Rules\CheckPhoneNumber;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    use AuthenticatesUsers, SendsPasswordResetEmails;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = 'customer/order';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function getLogin () {
        return view('customer.login');
    }

    public function postLogin(Request $request)
    {
        $credentials = request(['username', 'password']);

        if (Auth::guard('customer')->attempt($credentials, $request->has('remember'))) {
            return redirect('customer/order');
        }

        $this->incrementLoginAttempts($request);

        \Session::flash('flash_message_errors','Tên đăng nhập hoặc mật khẩu không đúng');
        throw ValidationException::withMessages([
            'username'=>'error',
            'password'=>'error'
        ]);
    }

    public function getRegister() {
        $provincials = \DB::table('devvn_tinhthanhpho')->get();
        return view('customer.register')->with([
            'provincials' => $provincials
        ]);
    }

    public function postRegister(Request $request)
    {
        $this->validator($request->all())->validate();

        $customer = $this->create($request->all());
        Mail::to($customer->email)->send(new \App\Mail\RegisterMail($customer));
        Auth::guard('customer')->login($customer);

        return redirect($this->redirectPath());
    }

    public function getForgotPass(){
        return view('customer.forgot-password');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $message = array(
            'required' => 'Thông tin bắt buộc',
            'name.max' => 'Họ tên có tối đa :max kí tự',
            'username.max' => 'Tên đăng nhập có tối đa :max kí tự',
            'password.min' => 'Mật khẩu phải có ít nhất :min kí tự',
            'phone.min' => 'Số điện thoại phải có ít nhất 10 kí tự',
            'username.unique' => 'Tên đăng nhập đã tồn tại',
            'email.unique' => 'Email đã tồn tại',
            'regex' => 'Không được nhập kí tự đặc biệt'
        );
        return Validator::make($data, [
            'name'     => 'required|max:255|regex:/^[\pL\s\-]+$/u',
            'username' => 'required|max:255|unique:customers|regex:/^[\pL\s\-\w]+$/u',
            'email'    => 'required|unique:customers',
            'password' => 'required|min:6|confirmed',
            'phone'    => ['required','min:10', new CheckPhoneNumber()],
            'address'  => 'required',
            'provincial_id'=> 'required',
            'district_id'=> 'required',
            'ward_id'=> 'required',
        ],$message);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $customer = Customer::create([
            'name'     => $data['name'],
            'username' => $data['username'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        CustomerAddress::create([
            'name'        => $data['name'],
            'phone'       => $data['phone'],
            'address'     => $data['address'],
            'customer_id' => $customer->id,
            'provincial_id'=> $data['provincial_id'],
            'district_id'=> $data['district_id'],
            'ward_id'=> $data['ward_id'],
            'is_default'  => 1
        ]);

        return $customer;
    }

    public function logout() {
        Auth::guard('customer')->logout();
        return redirect('customer/login');
    }

    protected function broker() {
        return Password::broker('customers');
    }

    protected function validateEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email'],['required' => 'Thông tin bắt buộc','email' => 'Địa chỉ email không hợp lệ']);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        if($response == Password::RESET_LINK_SENT){
            \Session::flash('status','Link đặt lại mật khẩu đã được gửi tới mail '.$request->input('email').'. Vui lòng kiểm tra email của bạn để đặt lại mật khẩu mới');
            return redirect('customer/login');
        }

        throw ValidationException::withMessages([
            'email'=>'Không tồn tại tài khoản người dùng đăng ký với email '.$request->input('email'). ' trong hệ thống!',
        ]);

    }
}