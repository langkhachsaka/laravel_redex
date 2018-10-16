<div class="page-heading">
    <div class="container">
        <h1>Trang quản lý cá nhân</h1>
    </div>
</div>
<div class="page-menu">
    <div class="container">
        <ul>
            <li>
                <a href="">Quản lý đơn hàng</a>
                <ul>
                    <li>
                        <a href="{{route('order.index')}}">Danh sách đơn hàng</a>
                        {{--<a href="">Thêm đơn hàng</a>--}}
                    </li>
                </ul>
            </li>
            <li>
                <a href="">Quản lý vận đơn</a>
                <ul>
                    <li>
                        <a href="{{route('order-transport.index')}}">Danh sách vận đơn</a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>


