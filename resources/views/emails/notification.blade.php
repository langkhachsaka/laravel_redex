<!DOCTYPE html>

<html>

<head>

    <title>Redex</title>

</head>

<body>
    <table class="wrapper" width="100%">
        <tr>
            <td class="wrapper-inner" align="center">
                <table class="main" align="center">
                    <tr>
                        <td class="header">
                            <a class="logo">
                                <img src="{{$message->embed(asset('logo/logo.png')) }}"/>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="main-content">
                            <p>Chào mừng <strong style="font-size: 15px">{{$customer->name}}</strong> đến với <a href="https://www.redex.vn/">redex.vn</a></p>
                            <p>Để đăng nhập vào website của chúng tôi, hãy sử dụng các thông tin đăng nhập này:</p>
                            <ul>
                                <li><strong>Tên đăng nhập: </strong>{{$customer->username}}</li>
                                <li><strong>Mật khẩu: </strong>mật khẩu bạn đã đặt khi tạo tài khoản</li>
                            </ul>
                            <p>Khi đăng nhập vào tài khoản của mình bạn có thể:</p>
                            <ul>
                                <li>Quản lí thông tin cá nhân</li>
                                <li>Quản lí đơn hàng</li>
                                <li>Quản lí địa chỉ nhận hàng</li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td class="footer">
                            <p class="closing"><strong style="font-size:15px">Cảm ơn!</strong></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>