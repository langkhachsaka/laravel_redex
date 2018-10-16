@extends('layouts.app')

@section('content')

        <div class="container" style="overflow: hidden">

            <img style="position: relative; left:-190px" src="{{asset('images/404.jpg')}}"/>

        </div>
        <div style="text-align: center;margin-top: 10px">
            <a href="{{route('order.index')}}" style="margin-top: 5px;font-size: 20px">Quay lại trang quản lí</a>
        </div>

@endsection