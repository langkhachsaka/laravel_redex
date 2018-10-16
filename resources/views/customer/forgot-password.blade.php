@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="main" style="margin-top: 50px">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <div class="page-title">
                <h1 style="color: #222;font-size: 20px;position: relative;text-transform: uppercase;">quên mật khẩu?</h1>
            </div>
            <form method="post" action="">
                @csrf
                <div class="fieldset" style="border: 1px solid #ebebeb;padding: 25px 25px 16px 25px;margin: 28px 0;background: #fff;">
                    <h2 class="legend" style="float: left;font-weight: 500;font-size: 15px;border: 1px solid #ebebeb;background: #fff;margin: -42px 0 0 0;padding: 7px 15px;position: relative;">Đặt lại mật khẩu của bạn tại đây</h2>
                    <p>Vui lòng nhập địa chỉ email của bạn ở dưới đây. Bạn sẽ nhận được một liên kết để đặt lại mật khẩu của mình</p>
                    <div class="form-list">
                        <label>
                            Địa chỉ email
                            <i style="color: red">*</i>
                        </label>
                        <div class="input-box" style="margin-bottom: 10px">
                            <input type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}"/>
                            @if ($errors->has('email'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="button-set" style="clear: both;margin: 20px 0 0;padding: 8px 0 0;border-top: 1px solid #ebebeb;text-align: right;">
                    <p class="required" style="margin: 0 0 10px;"><span style="color: red">*</span> Thông tin bắt buộc</p>
                    <p class="back-link" style="float: left;margin: 0;">
                        <a href="{{url('customer/login')}}">Quay lại đăng nhập</a>
                    </p>
                    <button type="submit" class="btn btn-primary">
                        Gửi liên kết
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
