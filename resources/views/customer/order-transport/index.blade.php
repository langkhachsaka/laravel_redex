@extends('layouts.app')

@section('title-name')
    - Danh sách đơn hàng vận chuyển
@endsection

@section('content')
    <div class="content-header">
        <div class="card-header">
            <h3 style="float: left;width: 30%">Đơn hàng vận chuyển</h3>
            <div style="float:right;display: block;width: 70%">
                <form method="get" enctype="multipart/form-data">
                    <div class="form-group" style="width: 15%;float:left;margin-right: 10px">
                        <input class="form-control" name="created_at" placeholder="Ngày tạo" value="{{ app('request')->input('created_at') }}" style="height: 37px"/>
                    </div>
                    <div class="form-group" style="width: 20%;float:left;margin-right: 10px">
                        <input class="form-control" name="end_date" placeholder="Ngày kết thúc" value="{{ app('request')->input('end_date') }}" style="height: 37px"/>
                    </div>
                    <div class="form-group" style="width: 20%;float:left;margin-right: 10px">
                        <select class="form-control" name="courier_company_id">
                            <option value="">Công ty chuyển phát</option>
                            @foreach($courier_companies as $courier_company)
                                @if(app('request')->input('courier_company_id') == $courier_company['id'])
                                    <option value="{{$courier_company['id']}}" selected>{{$courier_company['name']}}</option>
                                @else
                                    <option value="{{$courier_company['id']}}">{{$courier_company['name']}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="width: 20%;float:left;margin-right: 10px">
                        <select class="form-control" name="status">
                            <option value="" selected>Trạng thái</option>
                            @foreach($status as $key => $item)
                                @if(app('request')->input('status') === (string)$key)
                                    <option value="{{$key}}" selected>{{$item}}</option>
                                @else
                                    <option value="{{$key}}">{{$item}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="btn" style="color: black;margin-right: 5px;width: 10%">Tìm kiếm</button>
                        <a href="{{route('order-transport.index')}}" class="btn btn-primary" style="float: right;width: 10%">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="content-body">
        <table id="order-grid" class="table table-hover" style="margin:50px auto">
            <thead>
            <tr>
                <th width="50px"><span>ID</span></th>
                <th width="300px"><span>Địa chỉ nhận hàng</span></th>
                <th><span>Công ty chuyển phát</span></th>
                <th width="270px"><span>Tệp đính kèm</span></th>
                <th><span>Mã vận đơn</span></th>
                <th>Ngày tạo</th>
                <th>Ngày kết thúc</th>
                <th><span>Trạng thái</span></th>
                <th width="100px">
                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#modal-create-order-transport"><i class="fa fa-plus"></i>Tạo mới</a>
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
                <tr style="height: 100px">
                    <td>
                        {{ $order['id'] }}
                    </td>
                    <td>
                        {{ $order['customer_shipping_address'] }}
                    </td>
                    <td>
                        {{ $order->courierCompany->name }}
                    </td>
                    <td>
                        <i class="fa fa-file-text"></i> {{ $order->file_name }}<br/>
                        <a href="{{ $order->link_download_file }}"><i class="fa fa-download">Tải xuống</i></a>
                        <a href="{{ $order->link_view_file_online }}" style="margin-left: 10px" target="_blank"><i class="fa fa-eye">Xem online</i></a>
                    </td>
                    <td>
                        {{ $order['bill_of_lading_code'] }}<br/>
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($order['created_at'])->format("d-m-Y") }}
                    </td>
                    <td>
                        @if(!is_null($order['end_date']))
                        {{ \Carbon\Carbon::parse($order['end_date'])->format("d-m-Y") }}
                        @endif
                    </td>
                    <td>
                        <span>{{ $order['status_name'] }}</span>
                    </td>
                    <td>
                        @if($order->status == \Modules\BillOfLading\Models\BillOfLading::STATUS_PENDING)
                            <a href="{{route('order-transport.edit',$order['id'])}}" class="btn btn-info btn-sm"><i class="fa fa-lg fa-pencil"></i></a>
                            <a href="#" data-toggle="modal" data-target="#confirm-delete-order-{{$order['id']}}" class="btn btn-danger btn-sm"><i class="fa fa-lg fa-trash"></i></a>
                        @elseif($order->status == \Modules\BillOfLading\Models\BillOfLading::STATUS_COMPLAINT)
                            <a href="{{route('order-transport.view', $order['id'])}}" class="btn btn-success btn-sm"><i class="fa fa-eye"></i></a>
                            <a href="{{route('order-transport.get-complaint', $order['id'])}}" class="btn btn-warning btn-sm" style="width: 30px"><i class="fa fa-exclamation"></i></a>
                        @elseif($order->status == \Modules\BillOfLading\Models\BillOfLading::STATUS_DELIVERY)
                            <a href="{{route('order-transport.view', $order['id'])}}" class="btn btn-success btn-sm"><i class="fa fa-eye"></i></a>
                        @elseif($order->status == \Modules\BillOfLading\Models\BillOfLading::STATUS_FINISHED)
                            <a href="{{route('order-transport.view', $order['id'])}}" class="btn btn-success btn-sm"><i class="fa fa-eye"></i></a>
                            <a href="#" data-toggle="modal" data-target="#confirm-delete-order-{{$order['id']}}" class="btn btn-danger btn-sm"><i class="fa fa-lg fa-trash"></i></a>
                        @endif
                    </td>
                </tr>
                @include('customer.order-transport.delete',$order)
            @endforeach
            </tbody>
        </table>
        {{ $orders->links() }}
        <div id="modal-create-order-transport" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Tạo đơn hàng vận chuyển mới</h4>
                        <button type="button" class="close" data-dismiss="modal"
                                style="margin-top: -10px !important;">&times;
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="create-order-transport-form" action="{{route('order-transport.create')}}" method="post" enctype="multipart/form-data">
                            {{csrf_field()}}
                            <div class="form-group">
                                <div><b>Thông tin mua hàng</b></div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div>Họ tên: <span style="color: red">*</span></div>
                                        <input type="text" class="form-control{{ $errors->has('customer_billing_name') ? ' is-invalid' : '' }}" name="customer_billing_name" value="{{old('customer_billing_name',$address['name'])}}"/>
                                        @if ($errors->has('customer_billing_name'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('customer_billing_name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-sm-4">
                                        <div>Địa chỉ: <span style="color: red">*</span></div>
                                        <input type="text" class="form-control{{ $errors->has('customer_billing_address') ? ' is-invalid' : '' }}" name="customer_billing_address" value="{{old('customer_billing_address',$address['address'])}}"/>
                                        @if ($errors->has('customer_billing_address'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('customer_billing_address') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-sm-4">
                                        <div>Điện thoại: <span style="color: red">*</span></div>
                                        <input type="text" class="form-control{{ $errors->has('customer_billing_phone') ? ' is-invalid' : '' }}" name="customer_billing_phone" value="{{old('customer_billing_phone',$address['phone'])}}"/>
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
                                    <input type="checkbox" id="checkbox" onclick="fillData()" style="margin-left: 20px"> Giống thông tin mua hàng
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div>Họ tên: <span style="color: red">*</span></div>
                                        <input type="text" class="form-control{{ $errors->has('customer_shipping_name') ? ' is-invalid' : '' }}" name="customer_shipping_name" value="{{ old('customer_shipping_name') }}"/>
                                        @if ($errors->has('customer_shipping_name'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('customer_shipping_name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-sm-4">
                                        <div>Địa chỉ: <span style="color: red">*</span></div>
                                        <input type="text" class="form-control{{ $errors->has('customer_shipping_address') ? ' is-invalid' : '' }}" name="customer_shipping_address" value="{{ old('customer_shipping_address') }}"/>
                                        @if ($errors->has('customer_shipping_address'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('customer_shipping_address') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-sm-4">
                                        <div>Điện thoại: <span style="color: red">*</span></div>
                                        <input type="text" class="form-control{{ $errors->has('customer_shipping_phone') ? ' is-invalid' : '' }}" name="customer_shipping_phone" value="{{ old('customer_shipping_phone') }}"/>
                                        @if ($errors->has('customer_shipping_phone'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('customer_shipping_phone') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="large"><b>Công ty chuyển phát </b><span
                                            style="color: red">*</span></div>
                                <select class="form-control" name="courier_company_id">
                                    <option value=""></option>
                                    @foreach($courier_companies as $courier_company)
                                        <option value="{{$courier_company['id']}}">{{$courier_company['name']}}</option>
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
                                <div class="large"><b>Tệp đính kèm </b><span
                                            style="color: red">*</span></div>
                                <div class="drop-zone" style="border:1px dashed; padding: 10px 20px; cursor: pointer">
                                    <span class="file-name">Chọn tệp đính kèm</span>
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
                                <div class="large"><b>Mã vận đơn </b></div>
                                <input type="text" class="form-control" name="bill_of_lading_code" />
                            </div>

                            <button class="btn btn-primary" type="submit" style="padding:10px 20px;border: none">Thêm mới</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        @if($errors->any())
            $("#modal-create-order-transport").modal("show");
        @endif

        $('.drop-zone').click(function () {
           $(this).parent().find('input[name="file"]').click();
        });
        $('input[name="file"]').change(function () {
            var fileName = '<i class="fa fa-file-text"></i> '+$(this).prop('files')[0].name;
            $('.file-name').html(fileName);
        });
        $('#modal-create-order-transport').on('hidden.bs.modal', function () {
            $('#create-order-transport-form')[0].reset();
            $('.file-name').html('Chọn tệp đính kèm');
            $('#create-order-transport-form').find(".is-invalid").removeClass("is-invalid"); // reset style
            $('#create-order-transport-form').find(".invalid-feedback strong").html("");
        });
        function fillData(){
            if ($('#checkbox').prop('checked')){
                $('input[name="customer_shipping_name"]').val($('input[name="customer_billing_name"]').val());
                $('input[name="customer_shipping_address"]').val($('input[name="customer_billing_address"]').val());
                $('input[name="customer_shipping_phone"]').val($('input[name="customer_billing_phone"]').val());
            }else{
                $('input[name="customer_shipping_name"]').val('');
                $('input[name="customer_shipping_address"]').val('');
                $('input[name="customer_shipping_phone"]').val('');
            }
        }
        $(document).ready(function(){
            table = $('#order-grid').DataTable({
                paging:false,
                searching: false,
                bInfo: false,
                language: {
                    "emptyTable": "Không có bản ghi nào"
                },
                columnDefs: [
                    {
                        'targets': [1,3,4,7,8],
                        'orderable': false
                    },
                ],
            });
            var option = {
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: false,
                "locale": {
                    "format": "DD-MM-YYYY"
                }
            };

            $('input[name="created_at"]').daterangepicker(option).on('apply.daterangepicker', function(e, picker) {
                $(this).val(picker.startDate.format('DD-MM-YYYY'));
            });

            $('input[name="end_date"]').daterangepicker(option).on('apply.daterangepicker', function(e, picker) {
                $(this).val(picker.startDate.format('DD-MM-YYYY'));
            })
        });
    </script>
@endsection