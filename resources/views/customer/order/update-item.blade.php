<div class="form-group">
    <div class="large">Link sản phẩm <span style="color: red">*</span></div>
    <input type="text" class="form-control {{ $errors->has('link') ? 'is-invalid' : '' }}" name="link"
           value="{{old('link',$item['link'])}}"/>
    @if ($errors->has('link'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('link') }}</strong>
        </span>
    @endif
</div>
<div class="form-group">
    <div class="large">Kích cỡ <span style="color: red">*</span></div>
    <input type="text" class="form-control {{ $errors->has('size') ? 'is-invalid' : '' }}" name="size"
           value="{{old('size',$item['size'])}}"/>
    @if ($errors->has('size'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('size') }}</strong>
        </span>
    @endif
</div>
<div class="form-group">
    <div class="large">Màu sắc <span style="color: red">*</span></div>
    <input type="text" class="form-control {{ $errors->has('colour') ? 'is-invalid' : '' }}" name="colour"
           value="{{old('colour',$item['colour'])}}"/>
    @if ($errors->has('colour'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('colour') }}</strong>
        </span>
    @endif
</div>
<div class="form-group">
    <div class="large">Mô tả</div>
    <input type="text" class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" value="{{old('description',$item['description'])}}"/>
    @if ($errors->has('description'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('description') }}</strong>
        </span>
    @endif
</div>
<div class="form-group">
    <div class="large">Số lượng <span style="color: red">*</span></div>
    <input type="text" class="form-control {{ $errors->has('quantity') ? 'is-invalid' : '' }}" name="quantity"
           value="{{old('quantity',$item['quantity'])}}"/>
    @if ($errors->has('quantity'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('quantity') }}</strong>
        </span>
    @endif
</div>
<div class="form-group">
    <div class="large">Đơn vị <span style="color: red">*</span></div>
    <input type="text" class="form-control {{ $errors->has('unit') ? 'is-invalid' : '' }}" name="unit"
           value="{{old('unit',$item['unit'])}}"/>
    @if ($errors->has('unit'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('unit') }}</strong>
        </span>
    @endif
</div>
<div class="form-group">
    <div class="large">Giá web <span style="color: red">*</span></div>
    <input type="text" class="form-control {{ $errors->has('price_cny') ? 'is-invalid' : '' }}" name="price_cny"
           value="{{old('price_cny',$item['price_cny'])}}"/>
    @if ($errors->has('price_cny'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('price_cny') }}</strong>
        </span>
    @endif
</div>
<div class="image-update" style="margin-bottom:10px">
    <input type="file" id="image-update" hidden name="image-update" multiple onchange="addImage(this)"
           accept="image/*"/>
    <button class="add-image-update" type="button" class="btn {{$errors->has('images') ? ' is-invalid' : ''}}"
            style="background:#fff;color:red;height: 150px;width: 150px">Thêm ảnh
    </button>
    @if ($errors->has('images'))
        <span>
            <strong>{{ $errors->first('images') }}</strong>
        </span>
    @endif
    @foreach($item->images as $key=>$image)
        <div class="image-preview"
             style="display:inline-block;margin-right: 5px;position: relative;border: 1px solid #ccc;">
            <img src="{{asset('storage/'.$image->path) }}" style="height:150px"/>
            <input type="hidden" name="images[]" value="{{$image->path}}"/>
            <a href="#" onclick="removeImage(this)"
               style="background: bisque;position: absolute; padding: 2px 6px; top:0;right:0">x</a>
        </div>
    @endforeach
</div>