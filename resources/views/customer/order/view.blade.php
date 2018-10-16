@extends('layouts.app')

@section('title-name')
    - Chi tiết đơn hàng
@endsection

@section('content')
    <div class="container">
        <div class="content-header">
            <div class="card-header">
                <h3>Chi tiết Đơn hàng</h3>
            </div>
        </div>
        <div style="margin-top:90px;background: #DCDCDC;padding: 10px;height: 85px;margin-bottom: 10px">
            <div class="col-sm-3">
                <b>Ngày tạo đơn </b>
                <input type="text" value="{{\Carbon\Carbon::parse($order->created_at)->format("d-m-Y")}}" readonly/>
            </div>
            <div class="col-sm-3">
                <b>Mã đơn hàng </b>
                <input type="text" value="{{$order->id}}" readonly/>
            </div>
            <div class="col-sm-3">
                <b>Nhân viên CSKH </b>
                <input type="text" value="{{isset($order->seller) ? $order->seller->name : ''}}" readonly/>
            </div>
            <div class="col-sm-3">
                <b>Trạng thái </b><br>
                <input type="text" value="{{$order->status_name}}" readonly/>
            </div>
        </div>
        @foreach($shops as $a=>$shop)
            @if($a != 'underfined')
                <div class="rd-shop">
                    <div style="margin: 5px">
                        <b>Shop:</b>
                        <input type="text" value="{{$shop->name}}" readonly/>
                    </div>
                    <table class="table table-list-order-item">
                        <tbody>
                        @php
                            $discount = 0;
                            $surcharge = 0;
                        @endphp
                        @foreach($orderItems as $key => $item)
                            @if(!is_null($item->shop))
                                @if($item->shop->id == $shop->id)
                                    @if(!is_null($item->discount_customer_percent))
                                        @php $discount += $item->total_price * $item->quantity * $item->discount_customer_percent / 100 @endphp
                                    @elseif(!is_null($item->discount_customer_price))
                                        @php $discount += $item->discount_customer_price * $item->quantity @endphp
                                    @endif
                                    @if(!is_null($item->surcharge))
                                        @php $surcharge += $item->surcharge @endphp
                                    @endif
                                <tr>
                                    <td style="vertical-align: middle">{{$key + 1}}</td>
                                    <td width="200px">
                                        @foreach($item->images as $number=>$image)
                                            <img src="{{$image->path}}" style="width: 100%;height: auto;{{$number == 0 ? '' :'display:none'}}" data-toggle="modal" data-target="#image-modal{{$number == 0 ? '-'.$item['id'] : ''}}" onclick="currentSlide(this,{{$number + 1}})" class="hover-shadow cursor">
                                        @endforeach
                                        @include('customer.order.image-popup', $item)
                                    </td>
                                    <td>
                                        <table class="table table-bordered">
                                            <tbody>
                                            @if($item['alerted'] == 1 )
                                                <p><strong style="color: red">Sản phẩm trong đơn hàng không đủ số lượng. Shop có {{$item['shop_quantity']}}</strong>
                                                    <br/>
                                                    <strong style="color: red">Bạn có thể xóa sản phẩm này khỏi đơn hàng hoặc xác nhận vẫn tiếp tục mua với số lượng shop có</strong>
                                                    <br/><strong style="color: red"><a href="{{route('order-item.confirm',$item['id'])}}">Click vào đây để xác nhận</a> </strong>
                                                    <br/><strong style="color: red"><a href="{{route('order-item.remove',$item['id'])}}">Click vào đây để xóa sản phẩm khỏi đơn hàng</a> </strong>
                                                </p>
                                            @elseif($item['alerted'] == 2)
                                                <p>
                                                    <strong style="color: #00A759">Số lượng đặt không đủ. Đã xác nhận mua theo số lượng shop có : {{$item['shop_quantity']}} sản phẩm</strong>
                                                </p>
                                            @endif

                                            <tr>
                                                <td rowspan="2">
                                                    <a href="{{$item->link}}" target="_blank">Link sản phẩm</a>
                                                </td>
                                                <td>Cỡ</td>
                                                <td>Màu</td>
                                                <td>Đơn vị</td>
                                                <td>Giá web</td>
                                                <td>Số lượng</td>
                                                <td>Thành tiền</td>
                                            </tr>
                                            <tr>
                                                <td>{{$item->size}}</td>
                                                <td>{{$item->colour}}</td>
                                                <td>{{$item->unit}}</td>
                                                <td>{{$item->price_cny}}￥</td>
                                                <td class="quantity" data-quantity="{{$item->quantity}}">{{$item->quantity}}</td>
                                                <td class="total-price" data-total="{{$item->total_price}}">{{$item->total_price}}￥</td>
                                            </tr>
                                            <tr>
                                                <td>Mô tả</td>
                                                <td colspan="6">{{$item->description}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="7">
                                                    <textarea class="form-control" placeholder="Ghi chú"></textarea>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @endif
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                    <div class="row rd-chiphi">
                        <div>
                            <div class="row">
                                <div class="col-md-6" style="text-align: right">Chuyển phát</div>
                                <div class="col-md-6">
                                    <select name="delivery_type[{{$a}}]">
                                        <option value=""></option>
                                        <option value="1" {{isset($billCodes[$a]) && $billCodes[$a]['delivery_type'] == Modules\BillCode\Models\BillCode::CONST_1 ? 'selected' : ''}}>thường</option>
                                        <option value="2" {{isset($billCodes[$a]) && $billCodes[$a]['delivery_type'] == Modules\BillCode\Models\BillCode::CONST_2 ? 'selected' : ''}}>nhanh</option>
                                    </select>
                                    <span class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" aria-hidden="true" title="phương thức giao hàng"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6" style="text-align: right">Bảo hiểm</div>
                                <div class="col-md-6">
                                    <select name="insurance_type[{{$a}}]">
                                        <option value=""></option>
                                        <option value="1" {{isset($billCodes[$a]) && $billCodes[$a]['insurance_type'] == Modules\BillCode\Models\BillCode::CONST_1 ? 'selected' : ''}}>không</option>
                                        <option value="2" {{isset($billCodes[$a]) && $billCodes[$a]['insurance_type'] == Modules\BillCode\Models\BillCode::CONST_2 ? 'selected' : ''}}>có</option>
                                    </select>
                                    <span class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" title="bảo hiểm sản phẩm"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6" style="text-align: right">Gia cố</div>
                                <div class="col-md-6">
                                    <select name="reinforced_type[{{$a}}]">
                                        <option value=""></option>
                                        <option value="1" {{isset($billCodes[$a]) && $billCodes[$a]['reinforced_type'] == Modules\BillCode\Models\BillCode::CONST_1 ? 'selected' : ''}}>bìa cát tông</option>
                                        <option value="2" {{isset($billCodes[$a]) && $billCodes[$a]['reinforced_type'] == Modules\BillCode\Models\BillCode::CONST_2 ? 'selected' : ''}}>đóng gỗ</option>
                                    </select>
                                    <span class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" title="gia cố sản phẩm"></span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="row">
                                <div class="col-md-5" style="text-align: right">Tổng SL:</div>
                                <div class="col-md-5 rd-sl"><span style="color: red;font-weight: 700">0</span> sản phẩm</div>
                            </div>
                        </div>
                        <div style="width: 34%;">
                            <div class="row">
                                <div class="col-md-7">Tổng tiền hàng(RMB)</div>
                                <div class="col-md-5 rd-rmb" style="text-align: right"><span style="color: red;font-weight: 700">0</span>￥</div>
                            </div>
                            <div class="row">
                                <div class="col-md-7">Tổng chiết khấu</div>
                                <div class="col-md-5 rd-discount" data-discount="{{$discount}}" style="text-align: right"><span style="color: red;font-weight: 700">{{$discount}}</span>￥</div>
                            </div>
                            <div class="row">
                                <div class="col-md-7">Phụ phí</div>
                                <div class="col-md-5 rd-surcharge" data-surcharge="{{$surcharge}}" style="text-align: right"><span style="color: red;font-weight: 700">{{$surcharge}}</span>￥</div>
                            </div>
                            <div class="row">
                                <div class="col-md-7">Tỷ giá</div>
                                <div class="col-md-5 rd-rate" style="text-align: right"><span style="color: red;font-weight: 700">{{$customer->order_rate}}</span> VNĐ/tệ</div>
                            </div>
                            <div class="row">
                                <div class="col-md-7">Tổng tiền hàng(VNĐ)</div>
                                <div class="col-md-5 rd-vnd" style="text-align: right"><span style="color: red;font-weight: 700">0</span> VNĐ</div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="rd-shop-undefined">
                    <div style="margin: 5px">
                        <b>Shop: Chưa xác định</b>
                    </div>
                    <table class="table table-list-order-item">
                        <tbody>
                        @foreach($orderItems as $key => $item)
                            @if(is_null($item->shop))
                                <tr>
                                    <td style="vertical-align: middle">{{$key + 1}}</td>
                                    <td width="200px">
                                        @foreach($item->images as $number=>$image)
                                            <img src="{{$image->path}}" style="width: 100%;height: auto;{{$number == 0 ? '' :'display:none'}}" data-toggle="modal" data-target="#image-modal{{$number == 0 ? '-'.$item['id'] : ''}}" onclick="currentSlide(this,{{$number + 1}})" class="hover-shadow cursor">
                                        @endforeach
                                        @include('customer.order.image-popup', $item)
                                    </td>
                                    <td>
                                        <table class="table table-bordered">
                                            <tbody>
                                            <tr>
                                                <td rowspan="2">
                                                    <a href="{{$item->link}}" target="_blank">Link sản phẩm</a>
                                                </td>
                                                <td>Cỡ</td>
                                                <td>Màu</td>
                                                <td>Đơn vị</td>
                                                <td>Giá web</td>
                                                <td>Số lượng</td>
                                                <td>Thành tiền</td>
                                            </tr>
                                            <tr>
                                                <td>{{$item->size}}</td>
                                                <td>{{$item->colour}}</td>
                                                <td>{{$item->unit}}</td>
                                                <td>{{$item->price_cny}}￥</td>
                                                <td class="quantity" data-quantity="{{$item->quantity}}">{{$item->quantity}}</td>
                                                <td class="total-price" data-total="{{$item->total_price}}">{{$item->total_price}}￥</td>
                                            </tr>
                                            <tr>
                                                <td>Mô tả</td>
                                                <td colspan="6">{{$item->description}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="7">
                                                    <textarea class="form-control" placeholder="Ghi chú"></textarea>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                    <div class="row rd-chiphi">
                        <div></div>
                        <div>
                            <div class="row">
                                <div class="col-md-5" style="text-align: right">Tổng SL:</div>
                                <div class="col-md-5 rd-sl"><span style="color: red;font-weight: 700">0</span> sản phẩm</div>
                            </div>
                        </div>
                        <div style="width: 34%;">
                            <div class="row">
                                <div class="col-md-7">Tổng tiền hàng(RMB)</div>
                                <div class="col-md-5 rd-rmb" style="text-align: right"><span style="color: red;font-weight: 700">0</span>￥</div>
                            </div>
                            <div class="row">
                                <div class="col-md-7">Tỷ giá</div>
                                <div class="col-md-5 rd-rate" style="text-align: right"><span style="color: red;font-weight: 700">{{$customer->order_rate}}</span> VNĐ/tệ</div>
                            </div>
                            <div class="row">
                                <div class="col-md-7">Tổng tiền hàng(VNĐ)</div>
                                <div class="col-md-5 rd-vnd" style="text-align: right"><span style="color: red;font-weight: 700">0</span> VNĐ</div>
                            </div>
                        </div>
                    </div>
                </div>

            @endif
        @endforeach

        <div class="total-order">
            <div>
                <h5>Tổng tiền đơn hàng tạm tính</h5>
            </div>
            <div class="chiphi">
                <div class="rd-left">
                    <ul>
                        <li class="total-quantity">
                            <div>Tổng số lượng sản phẩm</div>
                            <div class="rd-prime"><span>{{$order->total_quantity}}</span> sản phẩm</div>
                        </li>
                        <li class="total-price-order">
                            <div>Tiền hàng(RMB)</div>
                            <div class="rd-prime"><span>{{$order->total_price}}</span>￥</div>
                        </li>
                        <li>
                            <div>Phí ship nội địa</div>
                            <div class="rd-prime"><span>{{$inlandShippingFee != 0 ? number_format($inlandShippingFee) : ''}}</span>{{$inlandShippingFee != 0 ? '￥' : 'Đang cập nhật'}}</div>
                        </li>
                        <li>
                            <div>Tổng chiết khấu</div>
                            <div class="rd-prime"><span>{{$totalDiscount != 0 ? number_format($totalDiscount, 2,'.',',') : 0}}</span>￥</div>
                        </li>
                        <li>
                            <div>Tổng phụ phí</div>
                            <div class="rd-prime"><span>{{$totalSurcharge != 0 ? number_format($totalSurcharge, 2,'.',',') : 0}}</span>￥</div>
                        </li>
                        <li class="rate" rate="{{$customer->order_rate}}">
                            <div>Tỷ giá</div>
                            <div class="rd-prime"><span>{{$customer->order_rate}}</span> VNĐ/tệ</div>
                        </li>
                        <li class="total-price-order-vnd" data-total="{{$order->total_price * $customer->order_rate}}">
                            <div>Tiền hàng(VNĐ)</div>
                            <div class="rd-prime"><span>{{number_format($order->total_price * $customer->order_rate)}}</span> VNĐ</div>
                        </li>
                        <li>
                            <div>Số tiền phải đặt cọc</div>
                            <div class="rd-prime"><span>{{number_format($order->total_price * $customer->order_rate * $customer->order_pre_deposit_percent / 100)}}</span> VNĐ</div>
                        </li>
                        <li class="deposited">
                            <div>Đã cọc</div>
                            <div class="rd-prime"><span>{{ $totalDeposit != 0 ? number_format($totalDeposit) : '0'}}</span> VNĐ</div>
                        </li>
                        <li class="remaining_balance">
                            <div>Tiền hàng còn lại</div>
                            <div class="rd-prime"><span>{{$totalDeposit != 0 ? number_format($order->total_price * $customer->order_rate - $totalDeposit) : number_format($order->total_price * $customer->order_rate)}}</span> VNĐ</div>
                        </li>
                    </ul>
                </div>
                <div class="rd-right">
                    <ul>
                        <li>
                            <div>Tổng số kiện hàng</div>
                            <div class="rd-prime"><span>{{$order->total_lading_codes}}</span> kiện hàng</div>
                        </li>
                        <li>
                            <div>Số kiện hàng chờ phát</div>
                            <div class="rd-prime"><span>{{$order->package_wait_to_tranfer}}</span> kiện hàng</div>
                        </li>
                        <li>
                            <div>Phí vận chuyển TQ-VN</div>
                            <div class="rd-prime"><span>{{$fee != 0 ? number_format($fee) : ''}}</span> {{$fee != 0 ? 'VNĐ' : 'Đang cập nhật'}}</div>
                        </li>
                        <li>
                            <div style="color: red;text-transform: uppercase;font-weight: 700">Tổng tiền đơn hàng</div>
                            <div class="rd-prime"><span>{{$total !=0 ? number_format($total) : ''}}</span>{{$total !=0 ? 'VNĐ' : 'Đang cập nhật'}}</div>
                        </li>
                        @if($order->status == \Modules\CustomerOrder\Models\CustomerOrder::STATUS_APPROVED)
                        <li>
                            <button class="btn btn-danger btn-deposit" data-toggle="modal" data-target="#deposit">Đặt cọc</button>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        @if($order->status == \Modules\CustomerOrder\Models\CustomerOrder::STATUS_APPROVED)
        <div id="deposit" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <span class="close cursor" data-dismiss="modal">&times;</span>
                        <h4 class="modal-title">Đặt cọc</h4>
                    </div>
                    <form id="depositForm" action="{{url('/customer/order/deposit')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group" style="padding:0 20px">
                                <div>
                                    <span style="font-weight: 700;font-size: 16px;text-transform: uppercase;">Số tiền phải đặt cọc: </span>
                                    <span class="deposit-number" style="color: red;font-size: 16px;font-weight: 700">{{number_format($order->total_price * $customer->order_rate * $customer->order_pre_deposit_percent / 100)}} VNĐ</span>
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
                                                    <span class="balance" data-balance="{{$customer->wallet}}" style="color: red;font-size: 16px;font-weight: 700">{{number_format($customer->wallet)}}</span> <span style="color: red;font-size: 16px;font-weight: 700">VNĐ</span>
                                                </div>
                                                <div class="form-group">
                                                    Số tiền cọc từ ví:
                                                    <input class="form-control" type="text" name="deposit_by_wallet" readonly value="0"/>
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
                                                    <input class="form-control" type="text" name="deposit_by_pay" onkeyup="updateDepositByWallet()" value="0"/>
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
                            <button type="submit" class="btn btn-primary">Đặt cọc</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <div class="last-child" style="text-align: right">
            <a class="btn btn-primary" href="{{route('order.index')}}" style="margin-top: 10px">
                Danh sách đơn hàng
            </a>
            @if($order->status == \Modules\CustomerOrder\Models\CustomerOrder::STATUS_DELIVERY)
                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#complaint"><i class="fa fa-exclamation"></i> Khiếu nại</button>
            @endif
        </div>
    </div>

    @include('customer.complaint.create',$order)
@endsection
@section('scripts')
    <script src="{{asset('build/js/customer/order/view.js')}}"></script>
    <script>
        var token = '{{csrf_token()}}';
        var orderId = '{{$order->id}}';
        var balanceWallet = '{{$customer->wallet}}';
        var totalDeposit = '{{$order->total_price * $customer->order_rate * $customer->order_pre_deposit_percent / 100}}';
        var totalAmount = '{{$total}}';
        if($('.rd-shop-undefined > table.table-list-order-item > tbody > tr').length == 0){
            $('.rd-shop-undefined').css('display','none');
        }
    </script>
@endsection