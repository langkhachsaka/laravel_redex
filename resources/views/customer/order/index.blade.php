@extends('layouts.app')
{{--@extends('layouts.customer-control')--}}

@section('title-name')
    - Danh sách đơn hàng
@endsection

@section('content')
        <div class="order-menu-tab">
            <div class="tab-item active" id="order-tab">
                Đơn hàng
            </div>
            <div class="tab-item" id="order-deposit-tab">
                <a href="{{ url('customer/deposit') }}">Đặt cọc
                    @if($orderDeposit > 0 )<span style="color:red">({{$orderDeposit}})</span>@endif
                </a>
            </div>
            <div class="tab-item">
                <a href="{{ url('customer/receive') }}">Thanh toán
                    @if($ladingCodePayment > 0 )<span style="color:red">({{$ladingCodePayment}})</span>@endif
                </a>
            </div>
        </div>
        <div class='filter-tab' style="text-align: center;">
            <div style="display: inline-block">
                <form method="get" enctype="multipart/form-data">
                    <div class="form-group" style="display: inline-block">
                        <input class="form-control" name="created_at" placeholder="Ngày tạo" value="{{ app('request')->input('created_at') }}" style="height: 37px"/>
                    </div>
                    <div class="form-group" style="display: inline-block">
                        <input class="form-control" name="end_date" placeholder="Ngày kết thúc" value="{{ app('request')->input('end_date') }}" style="height: 37px"/>
                    </div>
                    <div class="form-group" style="display: inline-block">
                        <select class="form-control" name="status">
                            <option value="" selected>Trạng thái</option>
                            @foreach($status as $key=>$item)
                                @if(app('request')->input('status') === (string)$key)
                                    <option value="{{$key}}" selected>{{$item}}</option>
                                @else
                                    <option value="{{$key}}">{{$item}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div style="display: inline-block">
                        <button type="submit" class="btn" style="color: black;margin-right: 5px;">Tìm kiếm</button>
                    </div>
                </form>
            </div>
            <div style="display: inline-block">
                <a href="{{route('order.create')}}" class="btn btn-primary"><i class="fa fa-plus"></i>Tạo mới đơn hàng</a>
            </div>
            <div style="display: inline-block">
                <form id="orderDeleteForm" action="{{route('orders.delete')}}" method="POST">
                    {{csrf_field()}}
                    <input type="hidden" name="orders">
                    <button id="ordersDeleteBtn" type="submit" class="btn btn-danger"><i class="text-yellow fa fa-lg fa-times"></i> Xóa đơn hàng</button>
                </form>
            </div>
        </div>
        <div class="order-tab-content">
            <table id="order-grid" class="table table-hover table-bordered" style="margin:50px auto">
                <thead>
                <tr>
                    <th><input id="toggleSelected" type="checkbox"></th>
                    <th width="30%"><span>Địa chỉ nhận hàng</span></th>
                    <th><span>Tổng tiền tạm tính</span></th>
                    <th width="10%">
                        <span>Ngày tạo</span>
                    </th>
                    <th width="10%"><span>Ngày kết thúc</span></th>
                    <th><span>Nhân viên CSKH</span></th>
                    <th>
                        <span>Trạng thái</span>
                    </th>
                    <th><span>Lộ trình đơn hàng</span></th>
                    <th style="width: 6%">
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($orders as $order)
                    <tr data-id="{{ $order->id }}">
                        <td class="selectorRow">
                            <input type="checkbox" {{$order->status == \Modules\CustomerOrder\Models\CustomerOrder::STATUS_PENDING ? '' : 'disabled'}}>
                        </td>
                        <td><?php $haveAlert = false;?>
                            {{ $order['customer_shipping_address']   }}
                            @foreach($order->customerOrderItems as $customer_order_item  )

                                @if($customer_order_item['alerted'] ==1)
                                    <?php $haveAlert = true;?>
                                @endif

                            @endforeach
                            @if($haveAlert)
                                <p><strong style="color: red">sản phẩm trong đơn hàng không đủ số lượng</strong> </p>
                            @endif
                        </td>
                        <td>
                            {{ isset($order['total']) ? number_format($order['total']) : '0' }}<span> VNĐ</span>
                        </td>
                        <td>
                            {{\Carbon\Carbon::parse($order['created_at'])->format("d-m-Y")}}
                        </td>
                        <td>
                            {{ isset($order['end_date']) ? $order['end_date'] : ''}}
                        </td>
                        <td>{{isset($order->seller) ? $order->seller->name : ''}}</td>
                        <td>
                            <span>{{ $order['status_name']}}</span>
                        </td>
                        <td>
                            <div style="float: left;width: 50%">
                                <div class="hover-shadow cursor route" style="text-align: center;background: #00ff65" data-toggle="tooltip" title="Tổng kiện hàng đã phát/Tổng kiện hàng">{{$order->package_tranfered}}/{{$order->total_lading_codes}}</div>
                                <div class="hover-shadow cursor route" style="text-align: center;background: #f492ee" data-toggle="tooltip" title="Tổng kiện hàng có khiếu nại/Tổng kiện hàng">{{$order->package_complaint}}/{{$order->total_lading_codes}}</div>
                            </div>
                            <div style="float: left;width: 50%">
                                <div class="hover-shadow cursor route" style="text-align: center;background: #ffec54" data-toggle="tooltip" title="Tổng kiện hàng cần thanh toán/Tổng kiện hàng">{{$order->package_need_pay}}/{{$order->total_lading_codes}}</div>
                                <div class="hover-shadow cursor route" style="text-align: center;background: #ffd400" data-toggle="tooltip" title="Tổng kiện hàng chờ phát/Tổng kiện hàng">{{$order->package_wait_to_tranfer}}/{{$order->total_lading_codes}}</div>
                            </div>
                        </td>
                        <td>
                            @if($order->status == \Modules\CustomerOrder\Models\CustomerOrder::STATUS_PENDING)
                                <a class="btn btn-info btn-sm" href="{{route('order.edit', $order['id'])}}"><i class="fa fa-lg fa-pencil"></i></a>
                                <a class="btn btn-danger btn-sm" href="#"
                                   data-toggle="modal" data-target="#confirm-delete-order-{{$order['id']}}"><i
                                            class="fa fa-lg fa-trash"></i></a>
                            @elseif($order->status == \Modules\CustomerOrder\Models\CustomerOrder::STATUS_COMPLAINT)
                                <a href="{{route('order.view', $order['id'])}}" class="btn btn-success btn-sm"><i class="fa fa-eye"></i></a>
                                <a href="{{route('order.get-complaint', $order['id'])}}" class="btn btn-warning btn-sm" style="width: 30px"><i class="fa fa-exclamation"></i></a>
                            @elseif($order->status == \Modules\CustomerOrder\Models\CustomerOrder::STATUS_FINISHED)
                                <a href="{{route('order.view', $order['id'])}}" class="btn btn-success btn-sm"><i class="fa fa-eye"></i></a>
                                <a class="btn btn-danger btn-sm" href="#"
                                   data-toggle="modal" data-target="#confirm-delete-order-{{$order['id']}}"><i
                                            class="fa fa-lg fa-trash"></i></a>
                            @else
                                <a href="{{route('order.view', $order['id'])}}" class="btn btn-success btn-sm"><i class="fa fa-eye"></i></a>
                            @endif
                        </td>
                    </tr>
                    <div id="confirm-delete-order-{{$order['id']}}" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div style="text-align: center">
                                        <h2>Xóa đơn hàng</h2>
                                        <p>Bạn có chắc chắn muốn xóa đơn hàng này không?</p>
                                    </div>
                                    <div style="float: right">
                                        <button type="button" class="btn btn-default" data-dismiss="modal" style="border-color: #ccc;">Cancel</button>
                                        <a href="{{route('order.delete', $order['id'])}}" class="btn btn-danger">OK</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                </tbody>
            </table>
            {{ $orders->links() }}
        </div>

@endsection
@section('scripts')
    <script src="{{asset('build/js/customer/order/index.js')}}"></script>
    <script>
        var token = '{{csrf_token()}}';
        initTable();
        $(document).ready(function(){
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
            $('[data-toggle="tooltip"]').tooltip({delay: {show: 500, hide: 100}});
        });
    </script>
@endsection
