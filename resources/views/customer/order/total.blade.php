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
                <li class="rate" rate="{{$rate}}">
                    <div>Tỷ giá</div>
                    <div class="rd-prime"><span>{{$rate}}</span> VNĐ/tệ</div>
                </li>
                <li class="total-price-order-vnd">
                    <div>Tiền hàng(VNĐ)</div>
                    <div class="rd-prime"><span>{{number_format($order->total_price * $rate)}}</span> VNĐ</div>
                </li>
            </ul>
        </div>
    </div>
</div>

