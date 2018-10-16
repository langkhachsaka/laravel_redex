@extends('layouts.app')

@section('menu')
    @include('layouts.menu')
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Thên địa chỉ') }}
                        <a href="{{route('address.index')}}"  style="float:right">
                            {{ __('Quay lại') }}
                        </a>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('address.store') }}">
                            @csrf
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label text-md-right">{{ __('Họ tên') }}</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="name" value="{{$name}}"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right">{{ __('Địa chỉ') }}</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="address"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right">{{ __('Số điện thoại') }}</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="phone"/>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Lưu') }}
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