@extends('layouts.app')

@section('title-name')
    - Nhận hàng
@endsection

@section('content')
    <div class="container">
        <div class="rd-info">
            <table class="table table-bordered" style="background: #FCAD52">
                <tbody>
                <tr>
                    <td>Hotline: 0948241144</td>
                    <td>04.62922255</td>
                    <td>Email: RedEx.vn@gmail.com</td>
                    <td>Website: www.RedEx.vn</td>
                </tr>
                <tr>
                    <td colspan="3">Address: Nhà 16 B11 Đầm Trấu, Hai Bà Trưng, HN</td>
                    <td>Yahoo: RedExpress</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="title" style="text-align:center;text-transform: uppercase;font-weight: 700;color:red">
            <h3>Đơn đặt hàng</h3>
        </div>
        <div class="cus-info">
            <table class="table table-bordered" style="background: #FCAD52">
                <tbody>
                <tr>
                    <td>Họ tên khách hàng: {{$customerAddress->name}}</td>
                    <td>Điện thoại: {{$customerAddress->phone}}</td>
                    <td>Email: {{$customerAddress->email}}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <form action="{{url('customer/bill/create')}}" method="POST" enctype="multipart/form-data">
            {{csrf_field()}}
            <div>
                @foreach($addresses as $key=>$addr)
                <span style="color: red;font-weight: 700;font-size: 17px">Địa chỉ nhận hàng {{$key + 1}}:</span> {{$addr['address']}}
                <input type="hidden" name="address[{{$key}}][address]" value="{{$addr['address']}}"/>
                <table class="table table-bordered">
                    <thead>
                    <tr style="background: #ececec">
                        <th>ID</th>
                        <th>Mã vận đơn</th>
                        <th>Mã đơn hàng</th>
                        <th>Số lượng</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($codes as $code)
                        @if($code->address == $addr['address'])
                            <tr>
                                <td>
                                    {{$code->id}}
                                    <input type="hidden" name="address[{{$key}}][code_id][]" value="{{$code->id}}"/>
                                </td>
                                <td>
                                    {{$code->code}}
                                    <input type="hidden" name="address[{{$key}}][lading_code][]" value="{{$code->code}}"/>
                                </td>
                                <td>
                                    {{$code->order_id}}
                                    <input type="hidden" name="address[{{$key}}][order_id][]" value="{{$code->order_id}}"/>
                                </td>
                                <td>
                                    {{$code->quantity_verify}}
                                    <input type="hidden" name="address[{{$key}}][quantity_verify][]" value="{{$code->quantity_verify}}"/>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    <tr style="background: #ececec">
                        <td colspan="4">
                            @if(is_numeric($addr['shipping_fee']))
                                Phí ship nội thành: <span style="color:red;">{{$addr['shipping_fee'] == 0 ? $addr['shipping_fee']: number_format($addr['shipping_fee'])}}</span> VNĐ
                                <input type="hidden" name="address[{{$key}}][shipping_fee_urban]" value="{{$addr['shipping_fee']}}"/>
                            @else
                                Phí chuyển phát: <span style="color: red">Đang cập nhật</span>
                                <div style="display: inline;margin-left: 15px">
                                    <select name="address[{{$key}}][collect_money]" style="height: 25px;">
                                        <option value="1">Redex thu tiền</option>
                                        <option value="2">Công ty chuyển phát thu tiền</option>
                                    </select>
                                </div>
                            @endif
                            <div>
                                Phụ phí: <span style="color:red;">{{$addr['surcharge'] == 0 ? $addr['surcharge']: number_format($addr['surcharge'])}}</span> VNĐ
                                <input type="hidden" name="address[{{$key}}][surcharge]" value="{{$addr['surcharge']}}"/>
                            </div>
                            <div>
                                Chiết khấu: <span style="color:red;">{{$addr['discount'] == 0 ? $addr['discount']: number_format($addr['discount'])}}</span> VNĐ
                                <input type="hidden" name="address[{{$key}}][discount]" value="{{$addr['discount']}}"/>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                @endforeach
            </div>
            <div class="total-order">
                <div class="chiphi">
                    <div class="rd-left">
                        @php
                            $total = 0;
                            foreach($addresses as $addr){
                                if(is_numeric($addr['shipping_fee']))$total += $addr['shipping_fee'];
                            }
                        @endphp
                        @foreach($orders as $key => $order)
                            @php $total += $order->pay_amount; @endphp
                            <ul>
                                <li style="font-size: 22px;color: red">
                                    Mã đơn hàng: {{$order->id}}
                                    <input type="hidden" name="order[{{$key}}][id]" value="{{$order->id}}"/>
                                </li>
                                <li>
                                    <div>Phí ship TQ-VN</div>
                                    <div class="rd-prime"><span>{{number_format($order->transport_fee)}}</span> VNĐ</div>
                                    <input type="hidden" name="order[{{$key}}][transport_fee]" value="{{$order->transport_fee}}"/>
                                </li>
                                <li class="deposited">
                                    <div>Số tiền còn lại</div>
                                    <div class="rd-prime"><span>{{number_format($order->pay_amount)}}</span> VNĐ</div>
                                    <input type="hidden" name="order[{{$key}}][pay_amount]" value="{{$order->pay_amount}}"/>
                                </li>
                            </ul>
                        @endforeach
                        <ul>
                            <li>
                                <div style="font-size: 22px;color: red">Tổng tiền cần thanh toán</div>
                                <div class="rd-prime"><span>{{number_format($total + $totalSurcharge - $totalDiscount)}}</span> VNĐ</div>
                                <input type="hidden" name="total_amount" value="{{$total + $totalSurcharge - $totalDiscount}}">
                            </li>
                        </ul>
                        <button type="submit" class="btn btn-danger" style="float:right">Tạo yêu cầu thanh toán</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('scripts')
@endsection
