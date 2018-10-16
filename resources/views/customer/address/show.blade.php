@extends('layouts.app')
@section('menu')
    @include('layouts.menu')
@endsection

@section('menu')
    @include('layouts.menu')
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Địa chỉ') }}
                        <a href="{{route('address.index')}}"  style="float:right">
                            {{ __('Quay lại') }}
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label text-md-right">{{ __('Họ tên') }}</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="name" value="{{$address['name']}}" disabled/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ __('Địa chỉ') }}</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="address" value="{{$address['address']}}" disabled/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ __('Số điên thoại') }}</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="phone" value="{{$address['phone']}}" disabled/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection