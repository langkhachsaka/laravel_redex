<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Auth;
use Modules\Customer\Models\Customer;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = 'customer/order';


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:customer');
    }

    public function showResetForm(Request $request, $token = null) {
        return view('customer.reset-password')
            ->with(['token' => $token]
            );
    }

    //defining which guard to use in our case, it's the admin guard
    protected function guard()
    {
        return Auth::guard('customer');
    }

    public function reset(Request $request)
    {
        $this->validate($request, $this->rules(), $this->validationErrorMessages());

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
            $this->resetPassword($user, $password);
        }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if($response == Password::PASSWORD_RESET){
            \Session::flash('flash_message','Thay đổi mật khẩu thành công');
            return redirect($this->redirectPath());
        }else{
            \Session::flash('error','Đã xảy ra lỗi!');
            return redirect('customer/login');
        }
    }


    //defining our password broker function
    protected function broker() {
        return Password::broker('customers');
    }

    protected function rules()
    {
        return [
            'email' => 'required',
            'token' => 'required',
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required'
        ];
    }

    protected function validationErrorMessages()
    {
        return [
            'required' => 'Thông tin bắt buộc',
            'confirmed' => 'Mật khẩu không khớp'
        ];
    }
}