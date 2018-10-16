@extends('layouts.app')

@section('title-name')
    - Nạp tiền
@endsection

@section('content')
    <div class="container">
        <ul class="my-tabs">
            <li class="tab-item active"><a href="#">Chuyển khoản</a></li>
            <li class="tab-item"><a href="#">Nạp tiền trực tiếp</a></li>
        </ul>
        <div class="recharge-box">
            <div id="box-left" class="active">
                <div class="main-top">
                    <p class="title">
                        <strong>Cách 1: chuyển khoản qua ngân hàng</strong>
                    </p>
                    <p>Quý khách hàng chuyển khoản qua ngân hàng vào các tài khoản của Redex</p>
                    <p class="tk">
                        <span></span>TK Techcombank:
                    </p>
                    <p class="tk">
                        <span></span>TK Vietcombank:
                    </p>
                    <p class="tk">
                        <span></span>TK Vietinbank:
                    </p>
                    <p class="text-main">
                        Nội dung chuyển khoản ghi rõ
                    </p>
                    <div class="sms">
                        <p>
                            <strong>tentaikhoan</strong> "số điện thoại của bạn (không bắt buộc, dùng để Redex liên hệ khi gặp sự cố)"
                        </p>
                    </div>
                    <p class="note">
                        (VD: trungnb 0917549555)
                    </p>
                </div>
                <div class="main-bottom">
                    <p class="title">
                        <strong>Cách 2: Chuyển khoản qua ATM hoặc các dịch vụ khác</strong>
                    </p>
                    <p>
                        Quý khách hàng thực hiện chuyển khoản qua ATM hoặc các dịch vụ không có ghi chú, vui lòng liên hệ thông báo cho bộ phận CSKH
                    </p>
                </div>
            </div>
            <div id="box-right">
                <p>
                    Quý khách có thể nạp tiền trực tiếp tại kho lấy hàng. Để đảm bảo an toàn về mặt tài chính cho Quý khách, dịch vụ khuyến cáo với các lần nạp có số tiền hơn 10 triệu đồng, Quý khách vui lòng sử dụng hình thức chuyển khoản.
                    <br>
                    Xin cảm ơn!
                </p>

            </div>
        </div>
    </div>
@endsection()
@section('scripts')
    <script>
        $('.tab-item').click(function (e) {
            e.preventDefault();
            $(this).addClass('active').siblings().removeClass('active');
            $('.recharge-box div.active').removeClass('active').siblings().addClass('active');
        });
    </script>
@endsection