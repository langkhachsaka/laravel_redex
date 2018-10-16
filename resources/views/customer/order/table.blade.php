<table class='table table-list-order-item'>
    <tbody>
    @foreach($orderItems as $key => $item)
        @if(!is_null($item->shop))
            @if($item->shop->id == $shop->id)
                <tr class="item-row-{{$key}}">
                    <td class="stt" style="vertical-align: middle">
                        <span>{{$key + 1}}</span>
                        <input type="hidden" name="item[{{$key}}]" class="item-id" value="{{$item['id']}}"/>
                    </td>
                    <td class="image" width="200px">
                        <div class='no-img-{{$key}}' style="display:none;"><img src='http://via.placeholder.com/100x100' style='width:100%;height: auto'/></div>
                        @foreach($item->images as $number=>$image)
                            <div class="image-preview">
                                <img src="{{$image->path}}" style="width: 100%;height: auto;{{$number == 0 ? '' :'display:none'}}" class="hover-shadow cursor">
                            </div>
                        @endforeach
                        <div class='upload' style="text-align: center;margin-top: 5px">
                            <a href='#' class='upload-image openModalUploadImage' data-id="{{$key}}" style='margin-right: 20px'>Upload</a>
                            <a href='#' class='upload-link openModalUploadLink' data-id="{{$key}}">Link</a>
                        </div>
                        <span class='invalid-feedback' style='display: unset'><strong></strong></span>
                        @foreach($item->images as $image)
                            <input type="hidden" name="images[{{$key}}][]" value="{{$image->path}}"/>
                        @endforeach
                        @include('customer.order.image-popup', $item)
                    </td>
                    <td>
                        <table class="table table-bordered">
                            <tbody>
                            @if($item['alerted'] == 1 )
                                <p><strong style="color: red">Sản phẩm trong đơn hàng không đủ số lượng. Shop có {{$item['shop_quantity']}}</strong>
                                    <br/>
                                    <strong style="color: red">Bạn có thể xóa sản phẩm này khỏi đơn hàng hoặc xác nhận vẫn tiếp tục mua với số lượng shop có</strong>
                                    <strong style="color: red"><a href="{{route('order-item.confirm',$item['id'])}}">Click vào đây để xác nhận</a> </strong>
                                </p>
                            @elseif($item['alerted'] == 2)
                                <p>
                                    <strong style="color: #00A759">Số lượng đặt không đủ. Đã xác nhận mua theo số lượng shop có : {{$item['shop_quantity']}} sản phẩm</strong>
                                </p>
                            @endif

                            <tr>
                                <td>Link sản phẩm<span style='color: red'> *</span></td>
                                <td width="15%">Cỡ<span style='color: red'> *</span></td>
                                <td width="15%">Màu<span style='color: red'> *</span></td>
                                <td width="15%">Đơn vị<span style='color: red'> *</span></td>
                                <td width="15%">Giá web<span style='color: red'> *</span></td>
                                <td width="15%">Số lượng<span style='color: red'> *</span></td>
                                <td>Thành tiền</td>
                            </tr>
                            <tr>
                                <td>
                                    <input type='text' class='form-control' name='link[{{$key}}]' value="{{$item->link}}"/>
                                    <span class='invalid-feedback'><strong></strong></span>
                                </td>
                                <td>
                                    <input type='text' class='form-control' name='size[{{$key}}]' value="{{$item->size}}" style="width: 70%"/>
                                    <span class='invalid-feedback'><strong></strong></span>
                                </td>
                                <td>
                                    <input type='text' class='form-control' name='colour[{{$key}}]' value="{{$item->colour}}" style="width: 70%"/>
                                    <span class='invalid-feedback'><strong></strong></span>
                                </td>
                                <td>
                                    <input type='text' class='form-control' name='unit[{{$key}}]' value="{{$item->unit}}" style="width: 70%"/>
                                    <span class='invalid-feedback'><strong></strong></span>
                                </td>
                                <td class="price_cny">
                                    <input type='text' class='form-control' name='price_cny[{{$key}}]' value="{{$item->price_cny}}" style="width: 70%" data-id='{{$key}}'/>
                                    <span class='invalid-feedback'><strong></strong></span>
                                </td>
                                <td class='quantity'>
                                    <input type='text' class='form-control' name='quantity[{{$key}}]' value="{{$item->quantity}}" style="width: 70%" data-id='{{$key}}'/>
                                    <span class='invalid-feedback'><strong></strong></span>
                                </td>
                                <td class="total-price" total-price='{{$item['total_price']}}'><span>{{$item->total_price}}￥</span></td>
                            </tr>
                            <tr>
                                <td>Mô tả<span style='color: red'> *</span></td>
                                <td colspan="6">
                                    <input type='text' class='form-control' name='description[{{$key}}]' value="{{$item->description}}" />
                                    <span class='invalid-feedback'><strong></strong></span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="7">
                                    <textarea class="form-control" placeholder="Ghi chú" name="note[{{$key}}]">{{$item->note}}</textarea>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>

                    <td class='delete-row' style="vertical-align: middle">
                        <button type='button' class='btn btn-danger' onclick='openModalDeleteItem({{$key}})'><i class='fa fa-trash'></i></button>
                    </td>

                </tr>

            @endif
        @endif
    @endforeach
    </tbody>
</table>