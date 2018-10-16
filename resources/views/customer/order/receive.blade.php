@extends('layouts.app')

@section('title-name')
    - Nhận hàng
@endsection

@section('content')
    <div class="order-menu-tab">
        <div class="tab-item" id="order-tab">
            <a href="{{ route('order.index') }}">Đơn hàng</a>
        </div>
        <div class="tab-item" id="order-deposit-tab">
            <a href="{{ url('customer/deposit') }}">Đặt cọc
                @if($orderDeposit > 0 )<span style="color:red">({{$orderDeposit}})</span>@endif
            </a>
        </div>
        <div class="tab-item active" id="order-deposit-tab">
            Thanh toán @if($ladingCodePayment > 0 )<span style="color:red">({{$ladingCodePayment}})</span>@endif
        </div>
    </div>
    <div>
        <table id="lading-code-table" class="table table-hover table-bordered">
            <thead>
            <tr>
                <th><input id="toggleSelected" type="checkbox"></th>
                <th><span>Mã vận đơn</span></th>
                <th>
                    <span>Mã đơn hàng</span>
                </th>
                <th><span>Created at</span></th>
                <th>
                    <form id="createBillForm" action="{{url('customer/lading-code/bill')}}" method="GET">
                        {{--{{csrf_field()}}--}}
                        <input type="hidden" name="ladingCodes">
                        <button type="submit" class="btn btn-success">Tạo yêu cầu thanh toán</button>
                    </form>
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $item)
                <tr class="item-row-{{$item->id}}" data-id="{{$item->id}}" data-code="{{$item->sub_lading_code ? $item->sub_lading_code : $item->lading_code}}" data-order-id="{{$item->order_id}}">
                    <td class="selectorRow"><input type="checkbox"/></td>
                    <td>{{$item->sub_lading_code ? $item->sub_lading_code : $item->lading_code}}</td>
                    <td>{{$item->order_id}}</td>
                    <td>{{\Carbon\Carbon::parse($item->created_at)->format("d-m-Y")}}</td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
@section('scripts')
    <script src="{{asset('build/js/customer/order/bill.js')}}"></script>
@endsection
