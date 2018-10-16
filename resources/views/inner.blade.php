@extends('layouts.app')
@section('scripts')
    <script>
        var token = '{{csrf_token()}}';
        window.addEventListener('message',function (e) {
            $.post('/item-receiver',{data: JSON.stringify(e.data),_token: token});
        })
    </script>
@endsection
