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
                        <div>Họ tên: </div>
                        <input type="text" class="form-control" name="customer_billing_name" value="{{$order['customer_billing_name']}}"/>
                    </div>
                    <div class="col-sm-4">
                        <div>Địa chỉ: </div>
                        <input type="text" class="form-control" name="customer_billing_address" value="{{$order['customer_billing_address']}}"/>
                    </div>
                    <div class="col-sm-4">
                        <div>Điện thoại: </div>
                        <input type="text" class="form-control" name="customer_billing_phone" value="{{ $order['customer_billing_phone']}}"/>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div>
                    <b>Thông tin nhận hàng</b>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div>Họ tên: </div>
                        <input type="text" class="form-control" name="customer_shipping_name" value="{{$order['customer_shipping_name']}}"/>
                    </div>
                    <div class="col-sm-4">
                        <div>Địa chỉ: </div>
                        <input type="text" class="form-control" name="customer_shipping_address" value="{{$order['customer_shipping_address']}}"/>
                    </div>
                    <div class="col-sm-4">
                        <div>Điện thoại: </div>
                        <input type="text" class="form-control" name="customer_shipping_phone" value="{{$order['customer_shipping_phone']}}"/>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="large">Công ty chuyển phát</div>
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
            </div>
            <div class="form-group">
                <div class="large">Tệp đính kèm <span
                            style="color: red">*</span></div>
                <div class="drop-zone" style="border:1px dashed; padding: 10px 20px; cursor: pointer">
                    <span class="file-name"><i class="fa fa-file-text"></i> {{$order['file_name']}}</span>
                </div>
            </div>
            <div class="form-group">
                <div class="large">Mã vận đơn </div>
                <input type="text" class="form-control" name="bill_of_lading_code" value="{{$ladingCode}}"/>
            </div>
            <div style="float:right">
                <a href="{{route('order-transport.index')}}" class="btn btn-info">Danh sách vận đơn</a>
                @if($order->status == \Modules\BillOfLading\Models\BillOfLading::STATUS_DELIVERY)
                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#complaint"><i class="fa fa-exclamation"></i> Khiếu nại</button>
                @endif
            </div>
        </form>
        <div id="complaint" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Khiếu nại</h4>
                        <button type="button" class="close" data-dismiss="modal" style="margin-top: -10px !important;">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="form-complaint-create" action="{{route('complaint.create')}}" method="post" enctype="multipart/form-data">
                            {{csrf_field()}}
                            <input type="hidden" name="ordertable_id" value="{{$order['id']}}"/>
                            <input type="hidden" name="ordertable_type" value="order_transport"/>
                            <div class="form-group">
                                <div><b>Tiêu đề </b><span style="color: red">*</span></div>
                                <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" name="title" type="text"/>
                                @if ($errors->has('title'))
                                    <span class="invalid-feedback">
                                <strong>{{ $errors->first('title') }}</strong>
                            </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <div><b>Nội dung </b><span style="color: red">*</span></div>
                                <textarea class="form-control {{ $errors->has('content') ? 'is-invalid' : '' }}" name="content" rows="4"></textarea>
                                @if ($errors->has('content'))
                                    <span class="invalid-feedback">
                                <strong>{{ $errors->first('content') }}</strong>
                            </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <div><b>Ngày mong muốn </b><span style="color: red">*</span></div>
                                <input type="text" class="form-control {{ $errors->has('date_end_expected') ? 'is-invalid' : '' }}" name="date_end_expected" />
                                @if ($errors->has('date_end_expected'))
                                    <span class="invalid-feedback">
                                <strong>{{ $errors->first('date_end_expected') }}</strong>
                            </span>
                                @endif
                            </div>
                            <button type="submit" class="btn btn-primary">Gửi khiếu nại</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
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