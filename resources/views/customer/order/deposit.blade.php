@extends('layouts.app')

@section('title-name')
    - Đặt cọc
@endsection

@section('content')
        <div class="order-menu-tab">
            <div class="tab-item" id="order-tab">
                <a href="{{ route('order.index') }}">Đơn hàng</a>
            </div>
            <div class="tab-item active" id="order-deposit-tab">
                Đặt cọc
                @if($orderDeposit > 0)<span style="color:red">({{$orderDeposit}})</span>@endif
            </div>
            <div class="tab-item">
                <a href="{{ url('customer/receive') }}">Thanh toán
                    @if($ladingCodePayment > 0)<span style="color:red">({{$ladingCodePayment}})</span>@endif
                </a>
            </div>
        </div>
        <div class="order-deposit-tab-content">
            <table id="order-deposit-table" class="table table-hover table-bordered" style="margin:50px auto">
                <thead>
                <tr>
                    <th><input id="toggleSelected" type="checkbox"></th>
                    <th width="35%"><span>Địa chỉ nhận hàng</span></th>
                    <th><span>Số tiền cọc</span></th>
                    <th>
                        <span>Ngày tạo</span>
                    </th>
                    <th><span>Nhân viên CSKH</span></th>
                    <th><span>Lộ trình đơn hàng</span></th>
                    <th>
                        <form id="orderDepositForm" action="{{route('orders.deposit')}}" method="POST">
                            {{csrf_field()}}
                            <input type="hidden" name="orders">
                            <button id="ordersDepositBtn" type="submit" class="btn btn-success"><i
                                        class="text-yellow fa fa-lg fa-dollar"></i> Đặt cọc đơn hàng
                            </button>
                        </form>
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($list as $order)
                    <tr data-id="{{$order->id}}" class="item-row-{{$order->id}}">
                        <td class="selectorRow">
                            <input type="checkbox">
                        </td>
                        <td>{{$order->customer_shipping_address}}</td>
                        <td>{{number_format($order->total_deposit)}} VNĐ</td>
                        <td>{{\Carbon\Carbon::parse($order['created_at'])->format("d-m-Y")}}</td>
                        <td>{{isset($order->seller) ? $order->seller->name : ''}}</td>
                        <td>
                            <div style="float: left;width: 50%">
                                <div class="hover-shadow cursor route" style="text-align: center;background: #00ff65"
                                     data-toggle="tooltip"
                                     title="Tổng kiện hàng đã phát/Tổng kiện hàng">{{$order->package_tranfered}}
                                    /{{$order->total_lading_codes}}</div>
                                <div class="hover-shadow cursor route" style="text-align: center;background: #f492ee"
                                     data-toggle="tooltip"
                                     title="Tổng kiện hàng có khiếu nại/Tổng kiện hàng">{{$order->package_complaint}}
                                    /{{$order->total_lading_codes}}</div>
                            </div>
                            <div style="float: left;width: 50%">
                                <div class="hover-shadow cursor route" style="text-align: center;background: #ffec54"
                                     data-toggle="tooltip"
                                     title="Tổng kiện hàng cần thanh toán/Tổng kiện hàng">{{$order->package_need_pay}}
                                    /{{$order->total_lading_codes}}</div>
                                <div class="hover-shadow cursor route" style="text-align: center;background: #ffd400"
                                     data-toggle="tooltip"
                                     title="Tổng kiện hàng chờ phát/Tổng kiện hàng">{{$order->package_wait_to_tranfer}}
                                    /{{$order->total_lading_codes}}</div>
                            </div>
                        </td>
                        <td></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$list->links()}}
        </div>
        <div id="deposit" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <span class="close cursor" data-dismiss="modal">&times;</span>
                        <h4 class="modal-title">Đặt cọc</h4>
                    </div>
                    <form id="depositForm" action="{{url('/customer/order/deposit')}}" method="POST"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group" style="padding:0 20px">
                                <div>
                                    <span style="font-weight: 700;font-size: 16px;text-transform: uppercase;">Số tiền phải đặt cọc: </span>
                                    <span class="deposit-number"
                                          style="color: red;font-size: 16px;font-weight: 700">VNĐ</span>
                                </div>
                            </div>
                            <div>
                                <table class="table">
                                    <tbody>
                                    <tr>
                                        <td><input id="walletSelected" type="checkbox"/></td>
                                        <td>
                                            <span style="font-weight: 700;font-size: 16px;text-transform: uppercase;">Đặt cọc bằng ví </span>
                                            <div class="deposit-by-wallet" style="margin-top: 11px;display: none">
                                                <div class="form-group">
                                                    <span>Số dư khả dụng: </span>
                                                    <span class="balance" data-balance="{{$customer->wallet}}"
                                                          style="color: red;font-size: 16px;font-weight: 700">{{number_format($customer->wallet)}}</span>
                                                    <span style="color: red;font-size: 16px;font-weight: 700">VNĐ</span>
                                                </div>
                                                <div class="form-group">
                                                    Số tiền cọc từ ví:
                                                    <input class="form-control" type="text" name="deposit_by_wallet"
                                                           readonly value="0"/>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><input id="paySelected" type="checkbox"/></td>
                                        <td>
                                            <span style="font-weight: 700;font-size: 16px;text-transform: uppercase;">Đặt cọc bằng hình thức chuyển khoản </span>
                                            <div class="deposit-by-pay" style="margin-top: 11px;display: none">
                                                <div class="form-group" style="margin-top: 11px">
                                                    Nhập số tiền:
                                                    <input class="form-control" type="text" name="deposit_by_pay"
                                                           onkeyup="updateDepositByWallet()" value="0"/>
                                                    <span class='invalid-feedback'><strong></strong></span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="msg" style="color: red"></div>
                        </div>
                        <div class="modal-footer" style="justify-content: unset;margin-left: 20px;text-align: left">
                            <button type="submit" class="btn btn-primary btn-deposit">Đặt cọc</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
@endsection
@section('scripts')
    <script src="{{asset('build/js/customer/order/deposit.js')}}"></script>
    <script>
        var urlRedirect = '{{ url('customer/deposit') }}';
        initTableDeposit();
    </script>
@endsection
