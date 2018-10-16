@extends('layouts.app')
@section('menu')
    @include('layouts.menu')
@endsection

@section('menu')
    @include('layouts.menu')
@endsection

@section('content')
    <div class="card-header">
        <h1 style="margin-left: 90px">Địa chỉ</h1>
    </div>
    <div class="card-body">
        <table id="address-grid" class="table table-hover">
            <thead>
            <tr>
                <th width="50px" style="text-align: center"><span>ID</span></th>
                <th style="text-align: center"><span>Địa chỉ</span></th>
                <th style="text-align: center"><span>Số điện thoại</span></th>
                <th width="100px">
                    <a href="#" class="btn btn-primary" style="border: none" data-toggle="modal" data-target="#create"><i class="fa fa-plus"></i>Tạo mới</a>
                    <div id="create" class="modal fade" role="dialog">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Thêm mới địa chỉ</h4>
                                    <button type="button" class="close" data-dismiss="modal" style="margin-top: -10px !important;">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('address.store') }}" method="post" enctype="multipart/form-data">
                                        {{csrf_field()}}
                                        <div class="content">
                                            <div class="form-group">
                                                <div class="large">Họ và tên khách hàng <span style="color: red">*</span></div>
                                                <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{$customer['name']}}"/>
                                                @if ($errors->has('name'))
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $errors->first('name') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <div class="large">Điện thoại <span style="color: red">*</span></div>
                                                <input type="text" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" value="{{old('phone')}}"/>
                                                @if ($errors->has('phone'))
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $errors->first('phone') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <div class="large">Địa chỉ nhận hàng <span style="color: red">*</span></div>
                                                <input type="text" class="form-control{{ $errors->has('address') ? ' is-invalid' : '' }}" name="address" value="{{old('address')}}"/>
                                                @if ($errors->has('address'))
                                                    <span class="invalid-feedback">
                                                            <strong>{{ $errors->first('address') }}</strong>
                                                        </span>
                                                @endif
                                            </div>
                                            <input class="btn btn-primary" type="submit" style="padding:10px 20px;border: none" value="Lưu"/>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($addresses as $address)
                <tr>
                    <td style="text-align: center">
                        <a href="#">{{ $address['id'] }}</a>
                    </td>
                    <td style="padding-left: 400px">
                        <a href="#">{{ $address['address'] }}</a>
                    </td>
                    <td style="text-align: center">
                        <a href="#">{{ $address['phone'] }}</a>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Thao tác</button>
                            <ul class="dropdown-menu" style="right: 0;left: auto;">
                                <li>
                                    <a href="{{route('address.show', $address['id'])}} "><i class="fa fa-lg fa-search" style="color: #008c75"></i>Xem</a>
                                </li>
                                <li>
                                    <a href="{{route('address.edit', $address['id'])}}"><i class="fa fa-lg fa-pencil" style="color: #f39c12"></i>Sửa</a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="{{route('address.delete', $address['id'])}}" onclick="return confirm('{{ $confirm_msg or 'Bạn có chắc chắn muốn xóa địa chỉ này?' }}')"><i class="fa fa-lg fa-trash" style="color: #dd4b39"></i>Xóa</a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
@section('scripts')
    <script>
        @if($errors->any())
        $("#create").modal("show");
        @endif
    </script>
@endsection