<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>RedEx @yield('title-name')</title>

    <!-- Scripts -->
    {{--<script src="{{ asset('js/app.js') }}" defer></script>--}}
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">


    <!-- Include Date Range Picker -->
    <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css"/>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/dropzone.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/dropzone.css">

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/dt-1.10.16/datatables.min.css"/>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>

    <!-- Styles -->
    <link href="{{ mix('build/css/customers.css') }}" rel="stylesheet">
</head>
<body>
<div id="app">
    <header id="header">
        <div class="header-top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="header-hotline">
                            <span><i class="fa fa-phone"></i>Tel: 0948241144</span>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 text-right float-right">
                        <ul>
                            <!-- Authentication Links -->
                            @if (!Auth::guard('customer')->check())
                                <li><a href="{{ url('customer/login') }}">{{ __('Đăng nhập') }}</a>
                                </li>
                                <li><a href="{{ url('customer/register') }}">{{ __('Đăng kí') }}</a>
                                </li>
                            @else
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="dropdown-toggle" href="#" role="button"
                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        {{ Auth::guard('customer')->user()->name }}
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown" style="width:200px;text-align: left;">
                                        <a href="{{route('order.index')}}" class="dropdown-item">Đơn hàng</a>
                                        <a href="{{route('order-transport.index')}}" class="dropdown-item">Đơn hàng vận chuyển</a>
                                        <a href="{{route('complaint.index')}}" class="dropdown-item">Khiếu nại</a>
                                        <a href="{{route('wallet.index')}}" class="dropdown-item">Ví tiền</a>
                                        <a href="{{route('customer.info')}}" class="dropdown-item">Thông tin tài khoản</a>
                                        <a class="dropdown-item" href="{{ url('customer/logout') }}">
                                            {{ __('Đăng xuất') }}
                                        </a>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-bottom">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <a href="/" class="logo">
                            <img src="//bizweb.dktcdn.net/100/137/670/themes/183619/assets/logo.png?1524455045452"
                                 alt="RedEXpress">
                        </a>
                    </div>
                    <div class="col-lg-9 col-md-9 hidden-xs hidden-sm">
                        <nav class="header-nav-main">
                            <ul>

                                <li>
                                    <a href="/">Trang chủ</a>

                                </li>

                                <li>
                                    <a href="/gioi-thieu-chung">Giới thiệu</a>

                                </li>

                                <li>
                                    <a href="/danh-muc-tin-tuc">Tin tức</a>

                                </li>

                                <li>
                                    <a href="/bang-gia-cuoc-1">Bảng giá cước</a>

                                    <ul class="nav-main-sub">

                                    </ul>

                                </li>

                                <li>
                                    <a href="/huong-dan-dat-hang">Hướng dẫn đặt hàng</a>

                                </li>

                                <li>
                                    <a href="/hau-mai">Hậu mãi</a>

                                </li>

                                <li class="active">
                                    <a href="/lien-he">Liên hệ</a>

                                </li>

                                <li>
                                    <a href="/tuyen-dung">Tuyển dụng</a>

                                </li>

                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </header>


    <main class="py-4">

        <section class="article">
                @yield('menu')
                <div class="content-wrapper col-md-10 offset-md-1 col-sm-12">
                @if(Session::has('flash_message'))
                    <div class="alert alert-success message">{{ Session::get('flash_message') }}<a href="#" style="position: absolute; padding: 0px 10px; top:10px;right:5px;font-size: 15px;color: black;">x</a></div>
                @endif
                @if(Session::has('error'))
                    <div class="alert alert-danger message">{{ Session::get('error')}}<a href="#" style="position: absolute; padding: 0px 10px; top:10px;right:5px;font-size: 15px;color: black;">x</a></div>
                @endif
                @yield('content')
            </div>
        </section>
    </main>

    <footer id="footer">
        <div class="footer-top">
            <div class="container">
                <div class="col-md-6 col-sm-12">
                    <img src="http://media.bizwebmedia.net/sites/79478/data/Upload/2015/10/201482315447642.png">
                </div>
                <div class="col-md-6 col-sm-12">
                    <h4 class="footer-heading">Công ty cổ phần RedEx Việt Nam</h4>
                    <div class="footer-box-content">
                        <p class="footer-contact-address"><i class="fa fa-map-marker"></i> Nhà 16 Lô B11 Khu đô thị Đầm
                            Trấu</p>
                        <p class="footer-contact-phone"><i class="fa fa-phone"></i> 0948241144</p>
                        <p>Trực 8h00-20h00 từ thứ 2 đến thứ 6</p>
                        <p>Thứ 7 từ 8h - 17h30</p>
                        <p>Hỗ trợ ngoài giờ hành chính và chủ nhật</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-copyright">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <a style="margin-right: 320px" href="https://m.kuaidi100.com/" target="_blank">快递查询</a>
                        <p style="display: inline-block">
                            © Phát triển bởi <a href="http://fgc.vn/" target="_blank" title="FGC Techlution">FGC
                                Techlution</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </footer>
</div>

@yield("scripts")

</body>
</html>
