@extends('layouts.app')

@section('title-name')
    - Chi tiết khiếu nại
@endsection

@section('content')
    <div class="container">
        <div class="content-wrapper">
            <div class="content-header">
                <div class="card-header">
                    <h3>Thông tin khách hàng</h3>
                </div>
            </div>
        </div>
        @if(Session::has('info_update_message'))
            <div class="alert alert-success" style="margin-top: 85px">{{ Session::get('info_update_message') }}</div>
        @endif
        <form action="{{route('customer.info.update')}}" method="post" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="content-body">
                <input type="hidden" name="id" value="{{$customer->id}}"/>
                <div class="form-group">
                    <div><b>Họ tên:</b><span style="color: red"> *</span></div>
                    <input class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" type="text" name="name" value="{{old('name',$customer->name)}}"/>
                    @if ($errors->has('name'))
                        <span class="invalid-feedback">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                    @endif
                </div>
                <div class="form-group">
                    <b>Tỉnh/Thành phố: <span style="color: red">*</span></b>
                    <select class="form-control{{ $errors->has('provincial_id') ? ' is-invalid' : '' }}" name="provincial_id" onchange="selectDistrict()">
                        <option value="">--Chọn tỉnh--</option>
                        @foreach($provincials as $provincial)
                            @if($customer->customerAddress->provincial_id == $provincial->matp)
                                <option value="{{$customer->customerAddress->provincial_id}}" selected>{{$provincial->name}}</option>
                            @else
                                <option value="{{$provincial->matp}}">{{$provincial->name}}</option>
                            @endif
                        @endforeach
                    </select>
                    @if ($errors->has('provincial_id'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('provincial_id') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group">
                    <b>Quận/Huyện: <span style="color: red">*</span></b>
                    <select class="form-control{{ $errors->has('district_id') ? ' is-invalid' : '' }}" name="district_id" onchange="selectWard()">
                        <option value="">--Chưa chọn tỉnh--</option>
                    </select>
                    @if ($errors->has('district_id'))
                        <span class="invalid-feedback">
                        <strong>{{ $errors->first('district_id') }}</strong>
                    </span>
                    @endif
                </div>
                <div class="form-group">
                    <b>Phường/Xã: <span style="color: red">*</span></b>
                    <select class="form-control{{ $errors->has('ward_id') ? ' is-invalid' : '' }}" name="ward_id">
                        <option value="">--Chưa chọn quận huyện--</option>
                    </select>
                    @if ($errors->has('ward_id'))
                        <span class="invalid-feedback">
                        <strong>{{ $errors->first('ward_id') }}</strong>
                    </span>
                    @endif
                </div>
                <div class="form-group">
                    <div><b>Địa chỉ:</b><span style="color: red"> *</span></div>
                    <input class="form-control{{ $errors->has('address') ? ' is-invalid' : '' }}" type="text" name="address" value="{{old('address',isset($customer->customerAddresses[0]->address) ? $customer->customerAddresses[0]->address : '')}}"/>
                    @if ($errors->has('address'))
                        <span class="invalid-feedback">
                        <strong>{{ $errors->first('address') }}</strong>
                    </span>
                    @endif
                </div>
                <div class="form-group">
                    <div><b>Email:</b><span style="color: red"> *</span></div>
                    <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" type="text" name="email" value="{{old('email',$customer->email)}}"/>
                    @if ($errors->has('email'))
                        <span class="invalid-feedback">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                    @endif
                </div>
                <div class="form-group">
                    <div><b>Số điện thoại:</b><span style="color: red"> *</span></div>
                    <input class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" type="text" name="phone" value="{{old('phone',isset($customer->customerAddresses[0]->phone) ? $customer->customerAddresses[0]->phone : '')}}"/>
                    @if ($errors->has('phone'))
                        <span class="invalid-feedback">
                        <strong>{{ $errors->first('phone') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
            <div style="text-align: right">
                <button type="submit" class="btn btn-primary">Cập nhật</button>
            </div>
        </form>
    </div>
@endsection
@section('scripts')
    <script>
        var matp = '{{$customer->customerAddress->provincial_id}}';
        var maqh = '{{$customer->customerAddress->district_id}}';
        var xaid = '{{$customer->customerAddress->ward_id}}';

        $.get('/get-quan-huyen',{matp: matp},function (result) {
            var option = '<option value="">--Chọn quận huyện--</option>';
            for(var i = 0; i < result.data.length; i++){
                if(result.data[i].maqh == maqh){
                    option += '<option value="'+result.data[i].maqh+'" selected>'+result.data[i].name+'</option>';
                }else{
                    option += '<option value="'+result.data[i].maqh+'">'+result.data[i].name+'</option>';
                }

            }
            $('select[name=district_id]').html(option);
        });
        $.get('/get-phuong-xa',{maqh: maqh},function (result) {
            var option = '<option value="">--Chọn phường xã--</option>';
            for(var i = 0; i < result.data.length; i++){
                if(result.data[i].xaid == xaid) {
                    option += '<option value="' + result.data[i].xaid + '" selected>' + result.data[i].name + '</option>';
                }else{
                    option += '<option value="' + result.data[i].xaid + '">' + result.data[i].name + '</option>';
                }
            }
            $('select[name=ward_id]').html(option);
        });
        function selectDistrict() {
            var matp = $('select[name=provincial_id]').val();
            $.get('/get-quan-huyen',{matp: matp},function (res) {
                var option = '<option value="">--Chọn quận huyện--</option>';
                for(var i = 0; i < res.data.length; i++){
                    option += '<option value="'+res.data[i].maqh+'">'+res.data[i].name+'</option>';
                }
                $('select[name=district_id]').html(option);
            });
        }

        function selectWard(){
            var maqh = $('select[name=district_id]').val();
            $.get('/get-phuong-xa',{maqh: maqh},function (res) {
                var option = '<option value="">--Chọn phường xã--</option>';
                for(var i = 0; i < res.data.length; i++){
                    option += '<option value="'+res.data[i].xaid+'">'+res.data[i].name+'</option>';
                }
                $('select[name=ward_id]').html(option);
            });
        }
    </script>
@endsection

