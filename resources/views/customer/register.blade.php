@extends('layouts.app')

@section('title-name')
    - Đăng ký hệ thống
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Đăng kí') }}</div>
                <div class="card-body">
                    <form method="POST" action="{{ url('customer/register') }}">
                        {!! csrf_field() !!}

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Họ Tên ') }}<span style="color: red"> *</span></label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" />

                                @if ($errors->has('name'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="username" class="col-md-4 col-form-label text-md-right">{{ __('Tên đăng nhập') }}<span style="color: red"> *</span></label>

                            <div class="col-md-6">
                                <input id="username" type="text" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{ old('username') }}" />

                                @if ($errors->has('username'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('Số điện thoại') }}<span style="color: red"> *</span></label>

                            <div class="col-md-6">
                                <input id="phone" type="text" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" value="{{ old('phone') }}" />

                                @if ($errors->has('phone'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail') }}<span style="color: red"> *</span></label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" />

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">Tỉnh/Thành phố: <span style="color: red">*</span></label>
                            <div class="col-md-6">
                                <select class="form-control{{ $errors->has('provincial_id') ? ' is-invalid' : '' }}" name="provincial_id" onchange="selectDistrict()">
                                    <option value="">--Chọn tỉnh--</option>
                                    @foreach($provincials as $provincial)
                                        <option value="{{$provincial->matp}}">{{$provincial->name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('provincial_id'))
                                    <span class="invalid-feedback">
                                    <strong>{{ $errors->first('provincial_id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">Quận/Huyện: <span style="color: red">*</span></label>
                            <div class="col-md-6">
                                <select class="form-control{{ $errors->has('district_id') ? ' is-invalid' : '' }}" name="district_id" onchange="selectWard()">
                                    <option value="">--Chưa chọn tỉnh--</option>
                                </select>
                                @if ($errors->has('district_id'))
                                    <span class="invalid-feedback">
                                    <strong>{{ $errors->first('district_id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">Phường/Xã: <span style="color: red">*</span></label>
                            <div class="col-md-6">
                                <select class="form-control{{ $errors->has('ward_id') ? ' is-invalid' : '' }}" name="ward_id">
                                    <option value="">--Chưa chọn quận huyện--</option>
                                </select>
                                @if ($errors->has('ward_id'))
                                    <span class="invalid-feedback">
                                    <strong>{{ $errors->first('ward_id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="address" class="col-md-4 col-form-label text-md-right">{{ __('Địa chỉ') }}<span style="color: red"> *</span></label>

                            <div class="col-md-6">
                                <input id="address" type="text" class="form-control{{ $errors->has('address') ? ' is-invalid' : '' }}" name="address" value="{{ old('address') }}"/>

                                @if ($errors->has('address'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('address') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Mật khẩu') }}<span style="color: red"> *</span></label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" />

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Xác nhận mật khẩu') }}<span style="color: red"> *</span></label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" />
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-12" style="text-align: center;">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Đăng kí') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <script>
        function selectDistrict() {
            var matp = $('select[name=provincial_id]').val();
            $.get('/get-quan-huyen',{matp: matp},function (res) {
                var option = '<option value="">--Chọn quận huyện--</option>';
                for(var i = 0; i < res.data.length; i++){
                    option += '<option value="'+res.data[i].maqh+'">'+res.data[i].name+'</option>';
                }
                $('select[name=district_id]').html(option);
            });
        }

        function selectWard(){
            var maqh = $('select[name=district_id]').val();
            $.get('/get-phuong-xa',{maqh: maqh},function (res) {
                var option = '<option value="">--Chọn phường xã--</option>';
                for(var i = 0; i < res.data.length; i++){
                    option += '<option value="'+res.data[i].xaid+'">'+res.data[i].name+'</option>';
                }
                $('select[name=ward_id]').html(option);
            });
        }
    </script>
@endsection
