<!DOCTYPE html>
<html class="loading" lang="{{ app()->getLocale() }}" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Red Express">
    <meta name="keywords" content="redEx, Red Express">
    <meta name="csrf-token" content="{{csrf_token()}}"/>
    <title>RedEx.vn</title>
    {{--<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Quicksand:300,400,500,700"--}}
          {{--rel="stylesheet">--}}
    {{--<link href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css" rel="stylesheet">--}}
    <!-- BEGIN VENDOR CSS-->
    {{--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha256-LA89z+k9fjgMKQ/kq4OO2Mrf8VltYml/VES+Rg0fh20=" crossorigin="anonymous" />--}}
    {{--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.1.0/css/flag-icon.min.css" integrity="sha256-D+ZpDJjhGxa5ffyQkuTvwii4AntFGBZa4jUhSpdlhjM=" crossorigin="anonymous" />--}}


    {{--<link rel="stylesheet" type="text/css"--}}
          {{--href="https://pixinvent.com/modern-admin-clean-bootstrap-4-dashboard-html-template/app-assets/css/vendors.min.css">--}}
    <!-- END VENDOR CSS-->
    <!-- BEGIN MODERN CSS-->
    {{--<link rel="stylesheet" type="text/css"--}}
          {{--href="https://pixinvent.com/modern-admin-clean-bootstrap-4-dashboard-html-template/app-assets/css/app.min.css">--}}
    <!-- END MODERN CSS-->
    <!-- BEGIN Page Level CSS-->
    {{--<link rel="stylesheet" type="text/css"--}}
          {{--href="https://pixinvent.com/modern-admin-clean-bootstrap-4-dashboard-html-template/app-assets/css/core/menu/menu-types/vertical-menu.min.css">--}}
    {{--<link rel="stylesheet" type="text/css"--}}
          {{--href="https://pixinvent.com/modern-admin-clean-bootstrap-4-dashboard-html-template/app-assets/css/core/colors/palette-gradient.min.css">--}}
    {{--<link rel="stylesheet" type="text/css"--}}
          {{--href="https://pixinvent.com/modern-admin-clean-bootstrap-4-dashboard-html-template/app-assets/css/core/colors/palette-callout.min.css">--}}
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    {{--<link rel="stylesheet" type="text/css"--}}
          {{--href="https://pixinvent.com/modern-admin-clean-bootstrap-4-dashboard-html-template/assets/css/style.css">--}}
    <!-- END Custom CSS-->
        <link rel="stylesheet" type="text/css" href="{{ mix('build/css/app.css') }}">
    <script type="text/javascript">
        window.appConfig = {
            url: "{{ config('app.url') }}"
        };
    </script>
</head>
<body class="vertical-layout vertical-menu 2-columns menu-expanded fixed-navbar"
      data-open="click" data-menu="vertical-menu" data-col="2-columns">

<div id="root"></div>

<!-- BEGIN VENDOR JS-->
{{--<script src="https://pixinvent.com/modern-admin-clean-bootstrap-4-dashboard-html-template/app-assets/vendors/js/vendors.min.js"--}}
        {{--type="text/javascript"></script>--}}
<!-- BEGIN VENDOR JS-->
<!-- BEGIN PAGE VENDOR JS-->
<!-- END PAGE VENDOR JS-->
<!-- BEGIN MODERN JS-->
{{--<script src="https://pixinvent.com/modern-admin-clean-bootstrap-4-dashboard-html-template/app-assets/js/core/app-menu.min.js"--}}
        {{--type="text/javascript"></script>--}}
{{--<script src="https://pixinvent.com/modern-admin-clean-bootstrap-4-dashboard-html-template/app-assets/js/core/app.min.js"--}}
        {{--type="text/javascript"></script>--}}
{{--<script src="https://pixinvent.com/modern-admin-clean-bootstrap-4-dashboard-html-template/app-assets/js/scripts/customizer.min.js" type="text/javascript"></script>--}}
<!-- END MODERN JS-->
<!-- BEGIN PAGE LEVEL JS-->
<!-- END PAGE LEVEL JS-->
<script type="text/javascript" src="{{ mix('build/js/app.js') }}"></script>
</body>
</html>