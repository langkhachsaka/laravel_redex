<div class="form-group">
    <div><b>Thông tin mua hàng</b></div>
    <div class="row">
        <div class="col-sm-4">
            <div>Họ tên: <span style="color: red">*</span></div>
            <input type="text" class="form-control{{ $errors->has('customer_billing_name') ? ' is-invalid' : '' }}" name="customer_billing_name" value="{{old('customer_billing_name',$address['name'])}}"/>
            @if ($errors->has('customer_billing_name'))
                <span class="invalid-feedback">
                                                <strong>{{ $errors->first('customer_billing_name') }}</strong>
                                            </span>
            @endif
        </div>
        <div class="col-sm-4">
            <div>Địa chỉ: <span style="color: red">*</span></div>
            <input type="text" class="form-control{{ $errors->has('customer_billing_address') ? ' is-invalid' : '' }}" name="customer_billing_address" value="{{old('customer_billing_address',$address['address'])}}"/>
            @if ($errors->has('customer_billing_address'))
                <span class="invalid-feedback">
                                                <strong>{{ $errors->first('customer_billing_address') }}</strong>
                                            </span>
            @endif
        </div>
        <div class="col-sm-4">
            <div>Điện thoại: <span style="color: red">*</span></div>
            <input type="text" class="form-control{{ $errors->has('customer_billing_phone') ? ' is-invalid' : '' }}" name="customer_billing_phone" value="{{old('customer_billing_phone',$address['phone'])}}"/>
            @if ($errors->has('customer_billing_phone'))
                <span class="invalid-feedback">
                                                <strong>{{ $errors->first('customer_billing_phone') }}</strong>
                                            </span>
            @endif
        </div>
    </div>
</div>
<div class="form-group">
    <div>
        <b>Thông tin nhận hàng</b>
        <input type="checkbox" id="checkbox" onclick="fillData()" style="margin-left: 20px"> Giống thông tin mua hàng
    </div>
    <div class="row">
        <div class="col-sm-4">
            <div>Họ tên: <span style="color: red">*</span></div>
            <input type="text" class="form-control{{ $errors->has('customer_shipping_name') ? ' is-invalid' : '' }}" name="customer_shipping_name" value="{{ old('customer_shipping_name') }}"/>
            @if ($errors->has('customer_shipping_name'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('customer_shipping_name') }}</strong>
                </span>
            @endif
        </div>
        <div class="col-sm-4">
            <div>Địa chỉ: <span style="color: red">*</span></div>
            <input type="text" class="form-control{{ $errors->has('customer_shipping_address') ? ' is-invalid' : '' }}" name="customer_shipping_address" value="{{ old('customer_shipping_address') }}"/>
            @if ($errors->has('customer_shipping_address'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('customer_shipping_address') }}</strong>
                </span>
            @endif
        </div>
        <div class="col-sm-4">
            <div>Điện thoại: <span style="color: red">*</span></div>
            <input type="text" class="form-control{{ $errors->has('customer_shipping_phone') ? ' is-invalid' : '' }}" name="customer_shipping_phone" value="{{ old('customer_shipping_phone') }}"/>
            @if ($errors->has('customer_shipping_phone'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('customer_shipping_phone') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>
<div class="form-group">
    <div class="large"><b>Công ty chuyển phát </b><span
                style="color: red">*</span></div>
    <select class="form-control" name="courier_company_id">
        <option value=""></option>
        @foreach($courier_companies as $courier_company)
            <option value="{{$courier_company['id']}}">{{$courier_company['name']}}</option>
        @endforeach
    </select>
    <input type="hidden" class="form-control {{ $errors->has('courier_company_id') ? ' is-invalid' : '' }}" />
    @if ($errors->has('courier_company_id'))
        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('courier_company_id') }}</strong>
                                        </span>
    @endif
</div>
<div class="form-group">
    <div class="large"><b>Tệp đính kèm </b><span
                style="color: red">*</span></div>
    <div class="drop-zone" style="border:1px dashed; padding: 10px 20px; cursor: pointer">
        <span class="file-name">Chọn tệp đính kèm</span>
    </div>
    <input type="file" name="file" hidden accept=".xls,.xlsx"/>
    <input type="hidden" class="form-control {{ $errors->has('file') ? ' is-invalid' : '' }}" />
    @if ($errors->has('file'))
        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('file') }}</strong>
                                    </span>
    @endif
</div>
<div class="form-group">
    <div class="large"><b>Mã vận đơn </b></div>
    <input type="text" class="form-control" name="bill_of_lading_code" />
</div>