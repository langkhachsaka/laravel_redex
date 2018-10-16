@extends('layouts.app')

@section('title-name')
    - Đăng nhập hệ thống
@endsection

@section('content')
    <div class="container">
        @if(Session::has('status'))
            <div class="alert alert-success">{{ Session::get('status') }}</div>
        @endif
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Đăng nhập') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ url('customer/login') }}">
                            @csrf
                            @if(Session::has('flash_message_errors'))
                                <div class="alert alert-danger">{{ Session::get('flash_message_errors') }}</div>
                            @endif
                            <div class="form-group row">
                                <label for="username" class="col-sm-4 col-form-label text-md-right">{{ __('Tên đăng nhập') }}</label>

                                <div class="col-md-6">
                                    <input id="username" type="text" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{ old('username') }}" autofocus/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Mật khẩu') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-4"></div>
                                <div class="col-md-6">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> {{ __('Remember Me') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-12"  style="text-align: center;">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Đăng nhập') }}
                                    </button>

                                    <a class="btn btn-link" href="{{ url('customer/password/forgot') }}">
                                        {{ __('Quên mật khẩu?') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
