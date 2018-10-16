@extends('layouts.app')

@section('title-name')
    - Sửa đơn hàng vận chuyển
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="card-header">
                <h3>Chi tiết Đơn hàng vận chuyển</h3>
            </div>
        </div>
    </div>
    <div class="content-body">
        <form action="{{route('order-transport.update',$order['id'])}}" method="post" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="form-group">
                <div><b>Thông tin mua hàng</b></div>
                <div class="row">
                    <div class="col-sm-4">
                        <div>Họ tên: <span style="color: red">*</span></div>
                        <input type="text" class="form-control{{ $errors->has('customer_billing_name') ? ' is-invalid' : '' }}" name="customer_billing_name" value="{{old('customer_billing_name',$order['customer_billing_name'])}}"/>
                        @if ($errors->has('customer_billing_name'))
                            <span class="invalid-feedback">
                            <strong>{{ $errors->first('customer_billing_name') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-sm-4">
                        <div>Địa chỉ: <span style="color: red">*</span></div>
                        <input type="text" class="form-control{{ $errors->has('customer_billing_address') ? ' is-invalid' : '' }}" name="customer_billing_address" value="{{old('customer_billing_address',$order['customer_billing_address'])}}"/>
                        @if ($errors->has('customer_billing_address'))
                            <span class="invalid-feedback">
                            <strong>{{ $errors->first('customer_billing_address') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-sm-4">
                        <div>Điện thoại: <span style="color: red">*</span></div>
                        <input type="text" class="form-control{{ $errors->has('customer_billing_phone') ? ' is-invalid' : '' }}" name="customer_billing_phone" value="{{ old('customer_billing_phone', $order['customer_billing_phone'])}}"/>
                        @if ($errors->has('customer_billing_phone'))
                            <span class="invalid-feedback">
                            <strong>{{ $errors->first('customer_billing_phone') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div>
                    <b>Thông tin nhận hàng</b>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div>Họ tên: <span style="color: red">*</span></div>
                        <input type="text" class="form-control{{ $errors->has('customer_shipping_name') ? ' is-invalid' : '' }}" name="customer_shipping_name" value="{{old('customer_shipping_name',$order['customer_shipping_name'])}}"/>
                        @if ($errors->has('customer_shipping_name'))
                            <span class="invalid-feedback">
                            <strong>{{ $errors->first('customer_shipping_name') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-sm-4">
                        <div>Địa chỉ: <span style="color: red">*</span></div>
                        <input type="text" class="form-control{{ $errors->has('customer_shipping_address') ? ' is-invalid' : '' }}" name="customer_shipping_address" value="{{old('customer_shipping_address',$order['customer_shipping_address'])}}"/>
                        @if ($errors->has('customer_shipping_address'))
                            <span class="invalid-feedback">
                            <strong>{{ $errors->first('customer_shipping_address') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-sm-4">
                        <div>Điện thoại: <span style="color: red">*</span></div>
                        <input type="text" class="form-control{{ $errors->has('customer_shipping_phone') ? ' is-invalid' : '' }}" name="customer_shipping_phone" value="{{old('customer_shipping_phone',$order['customer_shipping_phone'])}}"/>
                        @if ($errors->has('customer_shipping_phone'))
                            <span class="invalid-feedback">
                            <strong>{{ $errors->first('customer_shipping_phone') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="large">Công ty chuyển phát <span
                            style="color: red">*</span></div>
                <select class="form-control" name="courier_company_id">
                    <option value=""></option>
                    @foreach($courier_companies as $courier_company)
                        @if($order['courier_company_id'] == $courier_company['id'])
                            <option value="{{$courier_company['id']}}" selected>{{$courier_company['name']}}</option>
                        @else
                            <option value="{{$courier_company['id']}}">{{$courier_company['name']}}</option>
                        @endif
                    @endforeach
                </select>
                <input type="hidden" class="form-control {{ $errors->has('courier_company_id') ? ' is-invalid' : '' }}" />
                @if ($errors->has('courier_company_id'))
                    <span class="invalid-feedback">
                    <strong>{{ $errors->first('courier_company_id') }}</strong>
                </span>
                @endif
            </div>
            <div class="form-group">
                <div class="large">Tệp đính kèm <span
                            style="color: red">*</span></div>
                <div class="drop-zone" style="border:1px dashed; padding: 10px 20px; cursor: pointer">
                    <span class="file-name"><i class="fa fa-file-text"></i> {{$order['file_name']}}</span>
                </div>
                <input type="file" name="file" hidden accept=".xls,.xlsx"/>
                <input type="hidden" class="form-control {{ $errors->has('file') ? ' is-invalid' : '' }}" />
                @if ($errors->has('file'))
                    <span class="invalid-feedback">
                    <strong>{{ $errors->first('file') }}</strong>
                </span>
                @endif
            </div>
            <div class="form-group">
                <div class="large">Mã vận đơn </div>
                <input type="text" class="form-control" name="bill_of_lading_code" value="{{old('bill_of_lading_code',$ladingCode)}}"/>
            </div>
            <div style="float:right">
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="{{route('order-transport.index')}}" class="btn btn-info">Danh sách vận đơn</a>
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirm-delete-order-{{$order['id']}}"><i class="fa fa-trash"></i> Xóa vận đơn</button>
            </div>
        </form>
        @include('customer.order-transport.delete', $order)
    </div>
@endsection
@section('scripts')
    <script>
        $('.drop-zone').click(function () {
            $(this).parent().find('input[name="file"]').click();
        });
        $('input[name="file"]').change(function () {
            var fileName = '<i class="fa fa-file-text"></i> '+$(this).prop('files')[0].name;
            $('.file-name').html(fileName);
        });
        var option = {
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: false,
            "locale": {
                "format": "DD-MM-YYYY"
            }
        };

        $('input[name="date_end_expected"]').daterangepicker(option).on('apply.daterangepicker', function(e, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        })
    </script>
@endsection