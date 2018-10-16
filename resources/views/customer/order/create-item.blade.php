<div class="form-group">
    <div class="large">Link sản phẩm <span style="color: red">*</span></div>
    <input type="text" class="form-control{{ $errors->has('_link') ? ' is-invalid' : '' }}" name="_link" value="{{old('_link')}}"/>
    @if ($errors->has('_link'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('_link') }}</strong>
        </span>
    @endif
</div>
<div class="form-group">
    <div class="large">Kích cỡ <span style="color: red">*</span></div>
    <input type="text" class="form-control{{ $errors->has('_size') ? ' is-invalid' : '' }}" name="_size" value="{{old('_size')}}"/>
    @if ($errors->has('_size'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('_size') }}</strong>
        </span>
    @endif
</div>
<div class="form-group">
    <div class="large">Màu sắc <span style="color: red">*</span></div>
    <input type="text" class="form-control{{ $errors->has('_colour') ? ' is-invalid' : '' }}" name="_colour" value="{{old('_colour')}}"/>
    @if ($errors->has('_colour'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('_colour') }}</strong>
        </span>
    @endif
</div>
<div class="form-group">
    <div class="large">Mô tả <span style="color: red">*</span></div>
    <input type="text" class="form-control{{ $errors->has('_description') ? ' is-invalid' : '' }}" name="_description" value="{{old('_description')}}"/>
    @if ($errors->has('_description'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('_description') }}</strong>
        </span>
    @endif
</div>
<div class="form-group">
    <div class="large">Số lượng <span style="color: red">*</span></div>
    <input type="text" class="form-control{{ $errors->has('_quantity') ? ' is-invalid' : '' }}" name="_quantity" value="{{old('_quantity')}}"/>
    @if ($errors->has('_quantity'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('_quantity') }}</strong>
        </span>
    @endif
</div>
<div class="form-group">
    <div class="large">Đơn vị <span style="color: red">*</span></div>
    <input type="text" class="form-control{{ $errors->has('_unit') ? ' is-invalid' : '' }}" name="_unit" value="{{old('_unit')}}"/>
    @if ($errors->has('_unit'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('_unit') }}</strong>
        </span>
    @endif
</div>
<div class="form-group">
    <div class="large">Giá web <span style="color: red">*</span></div>
    <input type="text" class="form-control{{ $errors->has('_price_cny') ? ' is-invalid' : '' }}" name="_price_cny" value="{{old('_price_cny')}}"/>
    @if ($errors->has('_price_cny'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('_price_cny') }}</strong>
        </span>
    @endif
</div>
<div class="image-create" style="margin-bottom:10px">
    <input type="file" id="image-create" hidden name="image-create" multiple onchange="addImage(this)" accept="image/*"/>
    <button class="add-image-create" type="button" class="btn btn {{$errors->has('images') ? ' is-invalid' : ''}}" style="background:#fff;color:red;height: 150px;width: 150px">Thêm ảnh</button>
    @if ($errors->has('images'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('images') }}</strong>
        </span>
    @endif
</div>