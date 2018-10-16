@extends('layouts.app')

@section('title-name')
    - Sửa đơn hàng
@endsection

@section('content')
    <div class="container">
        @if(Session::has('flash_message_success'))
            <div class="alert alert-success">{{ Session::get('flash_message_success') }}</div>
        @endif
        <div class="content-wrapper">
            <div class="content-header">
                <div class="card-header">
                    <h3>Sửa Đơn hàng</h3>
                </div>
            </div>
        </div>
        <div class="content-body">
            <div class="">
                <form action="{{route('order.update', $order['id'])}}" method="post" enctype="multipart/form-data" id="form-update-order">
                    {{csrf_field()}}
                    <div class="card-body" style="background: #fff;border: 1px solid #ececec;margin-bottom: 10px">
                        <div class="mb-1">
                            <div class="col-md-6">
                                <h3>Thông tin mua hàng</h3>
                                <div class="form-group">
                                    <b>Họ tên: <span style="color: red">*</span></b>
                                    <input class="form-control {{ $errors->has('customer_billing_name') ? 'is-invalid' : '' }}" type="text" name="customer_billing_name" value="{{$order['customer_billing_name']}}"/>
                                    @if ($errors->has('customer_billing_name'))
                                        <span class="invalid-feedback">
                                    <strong>{{ $errors->first('customer_billing_name') }}</strong>
                                </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <b class="large">Email: </b>
                                    <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="text" name="email" value="{{$order->customer->email}}"/>
                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <b class="large">Điện thoại: <span style="color: red">*</span></b>
                                    <input class="form-control {{ $errors->has('customer_billing_phone') ? 'is-invalid' : '' }}" type="text" name="customer_billing_phone" value="{{$order['customer_billing_phone']}}"/>
                                    @if ($errors->has('customer_billing_phone'))
                                        <span class="invalid-feedback">
                                    <strong>{{ $errors->first('customer_billing_phone') }}</strong>
                                </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <b class="large">Địa chỉ: <span style="color: red">*</span></b>
                                    <input type="text" class="form-control {{ $errors->has('customer_billing_address') ? 'is-invalid' : '' }}" name="customer_billing_address" value="{{$order['customer_billing_address']}}"/>
                                    @if ($errors->has('customer_billing_address'))
                                        <span class="invalid-feedback">
                                    <strong>{{ $errors->first('customer_billing_address') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h3>Thông tin nhận hàng</h3>
                                <div class="form-group">
                                    <b>Địa chỉ nhận hàng:</b>
                                    <select name="shipping_address" class="form-control">
                                        <option value="0">{{$order->customer_shipping_name}} - ĐT:{{$order->customer_shipping_phone}} - {{$order->customer_shipping_address}}</option>
                                        @foreach($shipping_addresses as $address)
                                            <option value="{{$address->id}}">{{$address->name}} - ĐT:{{$address->phone}} - {{$address->fullAddress}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="text-align:right">
                        <div style="display: inline-block;position: relative;margin-right: 22px">
                            <input type="text" class="form-control" name="product-quantity" style="width: 60px" value="1"/>
                            <span class="invalid-feedback" style="display: block;height: 5px"><strong></strong></span>
                            <i class="amount-up" style="top: 0px;right: -20px" onclick="amountUp(this)">+</i>
                            <i class="amount-down" style="top: 18px;right: -20px" onclick="amountDown(this)">-</i>
                        </div>
                        <div style="display: inline-block">
                            <button type="button" class="btn btn-primary" onclick="addRow()"><i class="fa fa-plus"></i> Thêm mới sản phẩm</button>
                            <button type="button" class="btn btn-info" style="margin-top: 2px" data-toggle="modal"
                                    data-target="#import"><i class="fa fa-file-text"></i> Import excel
                            </button>
                        </div>
                    </div>
                    <div class="product-info">
                        @foreach($shops as $key => $shop)
                            @if($key != 'underfined')
                                <div class="rd-shop">
                                    <div style="margin: 5px">
                                        <b>Shop: {{$shop->name}}</b>
                                    </div>
                                    @include('customer.order.table',$orderItems)
                                    <div class="row rd-chiphi">
                                        <div>
                                            <div class="row">
                                                <div class="col-md-6" style="text-align: right">Chuyển phát</div>
                                                <div class="col-md-6">
                                                    <select name="delivery_type[{{$key}}]">
                                                        <option value="0"></option>
                                                        <option value="1" {{isset($billCodes[$key]) && $billCodes[$key]['delivery_type'] == \Modules\BillCode\Models\BillCode::CONST_1 ? 'selected' : ''}}>thường</option>
                                                        <option value="2" {{isset($billCodes[$key]) && $billCodes[$key]['delivery_type'] == \Modules\BillCode\Models\BillCode::CONST_2 ? 'selected' : ''}}>nhanh</option>
                                                    </select>
                                                    <span class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" aria-hidden="true" title="phương thức giao hàng"></span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6" style="text-align: right">Bảo hiểm</div>
                                                <div class="col-md-6">
                                                    <select name="insurance_type[{{$key}}]">
                                                        <option value="0"></option>
                                                        <option value="1" {{isset($billCodes[$key]) && $billCodes[$key]['insurance_type'] == \Modules\BillCode\Models\BillCode::CONST_1 ? 'selected' : ''}}>không</option>
                                                        <option value="2" {{isset($billCodes[$key]) && $billCodes[$key]['insurance_type'] == \Modules\BillCode\Models\BillCode::CONST_2 ? 'selected' : ''}}>có</option>
                                                    </select>
                                                    <span class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" title="bảo hiểm sản phẩm"></span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6" style="text-align: right">Gia cố</div>
                                                <div class="col-md-6">
                                                    <select name="reinforced_type[{{$key}}]">
                                                        <option value="0"></option>
                                                        <option value="1" {{isset($billCodes[$key]) && $billCodes[$key]['reinforced_type'] == \Modules\BillCode\Models\BillCode::CONST_1 ? 'selected' : ''}}>bìa cát tông</option>
                                                        <option value="2" {{isset($billCodes[$key]) && $billCodes[$key]['reinforced_type'] == \Modules\BillCode\Models\BillCode::CONST_2 ? 'selected' : ''}}>đóng gỗ</option>
                                                    </select>
                                                    <span class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" title="gia cố sản phẩm"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="row">
                                                <div class="col-md-5" style="text-align: right">Tổng SL:</div>
                                                <div class="col-md-5 rd-sl"><span style="color: red;font-weight: 700">0</span> sản phẩm</div>
                                            </div>
                                        </div>
                                        <div style="width: 34%;">
                                            <div class="row total-">
                                                <div class="col-md-7">Tổng tiền hàng(RMB)</div>
                                                <div class="col-md-5 rd-rmb" style="text-align: right"><span style="color: red;font-weight: 700">0</span>￥</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-7">Tỷ giá</div>
                                                <div class="col-md-5 rd-rate" style="text-align: right"><span style="color: red;font-weight: 700">{{$rate}}</span> VNĐ/tệ</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-7">Tổng tiền hàng(VNĐ)</div>
                                                <div class="col-md-5 rd-vnd" style="text-align: right"><span style="color: red;font-weight: 700">0</span> VNĐ</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="rd-shop-undefined">
                                    <div style="margin: 5px">
                                        <b>Shop: Chưa xác định</b>
                                    </div>
                                    <table class='table table-list-order-item'>
                                        <tbody>
                                        @foreach($orderItems as $key => $item)
                                            @if(is_null($item->shop))
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
                                                            <tr>
                                                                <td>Link sản phẩm<span style='color: red'> *</span></td>
                                                                <td width="10%">Cỡ<span style='color: red'> *</span></td>
                                                                <td width="15%">Màu<span style='color: red'> *</span></td>
                                                                <td width="15%">Đơn vị<span style='color: red'> *</span></td>
                                                                <td width="15%">Giá web<span style='color: red'> *</span></td>
                                                                <td width="15%">Số lượng<span style='color: red'> *</span></td>
                                                                <td width="15%">Thành tiền</td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <input type='text' class='form-control' name='link[{{$key}}]' value="{{$item->link}}"/>
                                                                    <span class='invalid-feedback'><strong></strong></span>
                                                                </td>
                                                                <td>
                                                                    <input type='text' class='form-control' name='size[{{$key}}]' value="{{$item->size}}"/>
                                                                    <span class='invalid-feedback'><strong></strong></span>
                                                                </td>
                                                                <td>
                                                                    <input type='text' class='form-control' name='colour[{{$key}}]' value="{{$item->colour}}"/>
                                                                    <span class='invalid-feedback'><strong></strong></span>
                                                                </td>
                                                                <td>
                                                                    <input type='text' class='form-control' name='unit[{{$key}}]' value="{{$item->unit}}"/>
                                                                    <span class='invalid-feedback'><strong></strong></span>
                                                                </td>
                                                                <td class="price_cny">
                                                                    <input type='text' class='form-control' name='price_cny[{{$key}}]' value="{{$item->price_cny}}" data-id='{{$key}}'/>
                                                                    <span class='invalid-feedback'><strong></strong></span>
                                                                </td>
                                                                <td class='quantity'>
                                                                    <input type='text' class='form-control' name='quantity[{{$key}}]' value="{{$item->quantity}}"data-id='{{$key}}'/>
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
                                        @endforeach
                                        </tbody>
                                    </table>
                                    <div class="row rd-chiphi">
                                        <div>
                                        </div>
                                        <div>
                                            <div class="row">
                                                <div class="col-md-5" style="text-align: right">Tổng SL:</div>
                                                <div class="col-md-5 rd-sl"><span style="color: red;font-weight: 700">0</span> sản phẩm</div>
                                            </div>
                                        </div>
                                        <div style="width: 34%;">
                                            <div class="row total-">
                                                <div class="col-md-7">Tổng tiền hàng(RMB)</div>
                                                <div class="col-md-5 rd-rmb" style="text-align: right"><span style="color: red;font-weight: 700">0</span>￥</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-7">Tỷ giá</div>
                                                <div class="col-md-5 rd-rate" style="text-align: right"><span style="color: red;font-weight: 700">{{$rate}}</span> VNĐ/tệ</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-7">Tổng tiền hàng(VNĐ)</div>
                                                <div class="col-md-5 rd-vnd" style="text-align: right"><span style="color: red;font-weight: 700">0</span> VNĐ</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                            @include('customer.order.total',$order)
                        <div class="last-child" style="text-align: right;margin-top: 10px">
                            <a class="btn btn-primary" href="{{route('order.index')}}">
                                Danh sách đơn hàng
                            </a>
                            <button type="submit" class="btn btn-info"><i class="fa fa-pencil"></i> Cập nhật
                            </button>
                            <a href="#" class="btn btn-danger" data-toggle="modal" data-target="#confirm-delete-order">
                                <i class="fa fa-trash"></i> Xóa đơn hàng
                            </a>
                        </div>
                    </div>
                </form>

                {{--Import excel --}}
                <div id="import" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Import excel</h4>
                                <button type="button" class="close" data-dismiss="modal"
                                        style="margin-top: -10px !important;">&times;
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="{{route('order.get-excel')}}" method="post" id="importForm"
                                      enctype="multipart/form-data">
                                    {{csrf_field()}}
                                    <div class="choose-file" style="width: 100%;height:150px;padding-top:50px;border: 2px solid #dcdcdc;cursor: pointer;">
                                        <h2 class="file-name" style="text-align: center">Chọn tệp excel.</h2>
                                    </div>
                                    <input type="file" name="excel" accept=".xls,.xlsx" hidden/>
                                    <button type="submit" class="btn btn-success btn-block import-excel"
                                            style="width: 200px;margin:10px auto;" disabled>Import file
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{--Modal upload image--}}
                <div id="upload-image" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Thêm ảnh</h4>
                                <button type="button" class="close" data-dismiss="modal" style="margin-top: -10px !important;">&times;</button>
                            </div>
                            <div class="modal-body">
                                <input type="file" hidden name="image-create" multiple onchange="addImage(this)" accept="image/*"/>
                                <button class="add-image" type="button" class="btn" style="background:#fff;color:red;height: 180px;width: 180px;float: left;margin-right: 5px" onclick="chooseFile(this)">Thêm ảnh</button>
                            </div>
                            <div class="modal-footer" style="justify-content: unset">
                                <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="updateImage(this)">Cập nhật</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{--Modal upload link--}}
                <div id="upload-link" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Link</h4>
                                <button type="button" class="close" data-dismiss="modal" style="margin-top: -10px !important;">&times;</button>
                            </div>
                            <div class="modal-body">
                                @for($i = 0; $i < 5; $i++)
                                    <div class="form-group">
                                        <label>Link {{$i + 1}}</label>
                                        <input type="text" class="form-control" name="link[{{$i}}]" />
                                    </div>
                                @endfor
                            </div>
                            <div class="modal-footer" style="justify-content: unset">
                                <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="updateLink(this)">Cập nhật</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{--Modal delete product --}}
                <div id="confirm-delete-item" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div style="text-align: center">
                                    <h2>Xóa đơn hàng</h2>
                                    <p>Bạn có chắc chắn muốn xóa sản phẩm này không?</p>
                                </div>
                                <div style="float: right">
                                    <button type="button" class="btn btn-default" data-dismiss="modal" style="border-color: #ccc;">Cancel</button>
                                    <button type="button" class="btn btn-danger delete-item">OK</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{--Modal delete order --}}
                <div id="confirm-delete-order" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div style="text-align: center">
                                    <h2>Xóa đơn hàng</h2>
                                    <p>Bạn có chắc chắn muốn xóa đơn hàng này không?</p>
                                </div>
                                <div style="float: right">
                                    <button type="button" class="btn btn-default" data-dismiss="modal" style="border-color: #ccc;">Cancel</button>
                                    <a href="{{route('order.delete', $order['id'])}}" class="btn btn-danger">OK</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        $('.choose-file').click(function () {
            $('input[name="excel"]').click();
        });
        $('input[name="excel"]').change(function () {
            var fileName = '<i class="fa fa-file-text"></i> '+$(this).prop('files')[0].name;
            var file_data = $(this).prop('files')[0];
            var formData = new FormData();
            formData.append('file',file_data);
            formData.append('_token','{{csrf_token()}}');
            $('.file-name').html(fileName);
            $.ajax({
                url: '/customer/order/upload',
                type: 'POST',
                dataType: 'json',
                processData: false,
                contentType: false,
                data: formData,
                success: function (res) {
                    $('<input>').attr({
                        type: 'hidden',
                        value: res,
                        name: 'imports'
                    }).appendTo('#importForm');
                    $('.import-excel').removeAttr('disabled');
                }
            });
        });
        if($('.rd-shop-undefined > table.table-list-order-item > tbody > tr').length == 0){
            $('.rd-shop-undefined').css('display','none');
        }
    </script>
    <script src="{{asset('build/js/customer/order/edit.js')}}"></script>
@endsection