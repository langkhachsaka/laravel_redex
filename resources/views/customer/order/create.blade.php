@extends('layouts.app')

@section('title-name')
    - Tạo Đơn hàng
@endsection

@section('content')
    <div class="container">
        <div class="content-wrapper">
            <div class="content-header">
                <div class="card-header">
                    <h3>Tạo Đơn hàng</h3>
                </div>
            </div>
        </div>
        <div class="content-body">
            <div class="">
                <form action="{{route('order.create')}}" method="post" enctype="multipart/form-data" id="form-create-order">
                    {{--Button add product and import --}}
                    <div class="card-body add-product" style="display: {{\Session::has('products') ? 'none' : ''}}">
                        <div style="margin-left: 20px;margin-bottom: 15px">
                            <h5 style="font-weight:bold">Chọn một trong hai cách sau để thêm mới sản phẩm vào đơn hàng:</h5>

                            <div>1. Nhập số sản phẩm cần thêm vào ô bên dưới và click vào button "Thêm mới sản phẩm".</div>
                            <div>
                                2. Click vào button "Import nhiều sản phẩm": Chức năng này cho phép bạn thêm một file excel chứa thông tin những sản phẩm cần thêm vào đơn hàng. Bạn có thể tải mẫu excel <a href='{{asset('/storage/upload/example/example.xlsx')}}'>tại đây</a>
                            </div>
                        </div>
                        <div style="margin-left: 17px;margin-bottom: 30px">
                            <div style="display: inline-block;position: relative;margin-right: 22px">
                                <input type="text" class="form-control" name="product-quantity-added" style="width: 60px" value="1" autofocus/>
                                <span class="invalid-feedback" style="display: block;height: 5px"><strong></strong></span>
                                <i class="amount-up" style="top: 0px;right: -20px" onclick="amountProductUp(this)">+</i>
                                <i class="amount-down" style="top: 18px;right: -20px" onclick="amountProductDown(this)">-</i>
                            </div>
                            <div style="display: inline-block">
                                <button type="button" class="btn btn-primary" onclick="addRow()"><i class="fa fa-plus"></i> Thêm mới sản phẩm</button>
                                <button type="button" class="btn btn-info" style="margin-top: 2px" data-toggle="modal"
                                        data-target="#import"><i class="fa fa-file-text"></i> Import nhiều sản phẩm
                                </button>
                            </div>
                        </div>
                    </div>

                    {{--List product --}}
                    <div class="product-info table-responsive-lg" style="display: {{\Session::has('products') ? '' : 'none'}}">
                        <div style="text-align:right">
                            <div style="display: inline-block;position: relative;margin-right: 22px">
                                <input type="text" class="form-control" name="product-quantity" style="width: 60px" value="0"/>
                                <span class="invalid-feedback" style="display: block;height: 5px"><strong></strong></span>
                                <i class="amount-up" style="top: 0px;right: -20px" onclick="amountProductUp(this)">+</i>
                                <i class="amount-down" style="top: 18px;right: -20px" onclick="amountProductDown(this)">-</i>
                            </div>
                            <div style="display: inline-block">
                                <button type="button" class="btn btn-primary" onclick="addRow()"><i class="fa fa-plus"></i> Thêm mới sản phẩm</button>
                                <button type="button" class="btn btn-info" style="margin-top: 2px" data-toggle="modal"
                                        data-target="#import"><i class="fa fa-file-text"></i> Import excel
                                </button>
                            </div>
                        </div>
                        @if(\Session::has('shops') && \Session::has('products'))
                            @foreach(\Session::get('shops') as $id=>$shop)
                                @php $hasProduct = false @endphp
                                @foreach(\Session::get('products') as $key=>$item)
                                    @if($item['shop_uid'] == $id)
                                        @php $hasProduct = true @endphp
                                        @break;
                                    @endif
                                @endforeach

                                @if(!$hasProduct) @continue @endif

                                <div class="rd-shop">
                                    <div style="margin: 5px;padding: 5px;">
                                        <b>Shop: {{$shop}}</b>
                                    </div>
                                    <table class="table table-list-order-item">
                                        <tbody>
                                        @foreach(\Session::get('products') as $key=>$item)
                                            @if($item['shop_uid'] == $id)
                                                <tr class="item-row-{{$key}}">
                                                    <td class="stt" style="vertical-align: middle">
                                                        <span>{{$key + 1}}</span>
                                                        <input type="hidden" name="shop[{{$key}}]" value="{{$item['shop_name']}}"/>
                                                    </td>
                                                    <td class="image" width="200px">
                                                        <div class='no-img-{{$key}}' style="display:none;"><img src='http://via.placeholder.com/100x100' style='width:100%;height: auto'/></div>
                                                        <div class="image-preview">
                                                            <img src="{{$item['image']}}" style="width: 100%;height: auto" class="hover-shadow cursor">
                                                        </div>
                                                        <div class='upload' style="text-align: center;margin-top: 5px">
                                                            <a href='#' class='upload-image openModalUploadImage' data-id="{{$key}}" style='margin-right: 20px'>Upload</a>
                                                            <a href='#' class='upload-link openModalUploadLink' data-id="{{$key}}">Link</a>
                                                        </div>
                                                        <div>
                                                            <input type='hidden' name='images[{{$key}}][]' value='{{$item['image']}}'/>
                                                            <span class='invalid-feedback' style='display: unset'><strong></strong></span>
                                                        </div>
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
                                                                    <input type='text' class='form-control' name='link[{{$key}}]' value="{{$item['url']}}"/>
                                                                    <span class='invalid-feedback'><strong></strong></span>
                                                                </td>
                                                                <td>
                                                                    <input type='text' class='form-control' name='size[{{$key}}]' value="{{$item['size']}}"/>
                                                                    <span class='invalid-feedback'><strong></strong></span>
                                                                </td>
                                                                <td>
                                                                    <input type='text' class='form-control' name='colour[{{$key}}]' value="{{$item['color']}}"/>
                                                                    <span class='invalid-feedback'><strong></strong></span>
                                                                </td>
                                                                <td>
                                                                    <input type='text' class='form-control' name='unit[{{$key}}]' value="{{$item['unit']}}"/>
                                                                    <span class='invalid-feedback'><strong></strong></span>
                                                                </td>
                                                                <td class="price_cny">
                                                                    <input type='text' class='form-control' name='price_cny[{{$key}}]' value="{{$item['unit_price']}}" data-id="{{$key}}"/>
                                                                    <span class='invalid-feedback'><strong></strong></span>
                                                                </td>
                                                                <td class='quantity'>
                                                                    <input type='text' class='form-control' name='quantity[{{$key}}]' value="{{$item['quantity']}}" data-id="{{$key}}"/>
                                                                    <span class='invalid-feedback'><strong></strong></span>
                                                                </td>
                                                                <td class="total-price" total-price='{{(int)$item['quantity'] * (float)$item['unit_price']}}'><span>￥{{(int)$item['quantity'] * (float)$item['unit_price']}}</span></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Mô tả<span style='color: red'> *</span></td>
                                                                <td colspan="6">
                                                                    <input type='text' class='form-control' name='description[{{$key}}]' value="{{$item['description']}}" />
                                                                    <span class='invalid-feedback'><strong></strong></span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="7">
                                                                    <textarea class="form-control" placeholder="Ghi chú" name="note[{{$key}}]"></textarea>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <td class='delete-row' style="vertical-align: middle">
                                                        <button type='button' class='btn btn-danger' data-row="{{$key}}" data-id="{{$item['id']}}"><i class='fa fa-trash'></i></button>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        </tbody>
                                    </table>
                                    <div class="row rd-chiphi">
                                        <div>
                                            <div class="row">
                                                <div class="col-md-6" style="text-align: right">Chuyển phát</div>
                                                <div class="col-md-6">
                                                    <select name="delivery_type[{{$shop}}]">
                                                        <option value="1">thường</option>
                                                        <option value="2">nhanh</option>
                                                    </select>
                                                    <span class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" aria-hidden="true" title="phương thức giao hàng"></span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6" style="text-align: right">Bảo hiểm</div>
                                                <div class="col-md-6">
                                                    <select name="insurance_type[{{$shop}}]">
                                                        <option value="1">không</option>
                                                        <option value="2">có</option>
                                                    </select>
                                                    <span class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" title="bảo hiểm sản phẩm"></span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6" style="text-align: right">Gia cố</div>
                                                <div class="col-md-6">
                                                    <select name="reinforced_type[{{$shop}}]">
                                                        <option value="1">bìa cát tông</option>
                                                        <option value="2">đóng gỗ</option>
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
                                                <div class="col-md-5 rd-rate" style="text-align: right"><span style="color: red;font-weight: 700">{{$customer->order_rate}}</span> VNĐ/tệ</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-7">Tổng tiền hàng(VNĐ)</div>
                                                <div class="col-md-5 rd-vnd" style="text-align: right"><span style="color: red;font-weight: 700">0</span> VNĐ</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        <div class="rd-shop-undefined" style="border: 1px solid #ebebeb;margin-bottom: 10px;display:none">
                            <div style="margin: 5px;padding: 5px;">
                                <b>Shop: Chưa xác định</b>
                            </div>
                            <table class="table table-list-order-item">
                                <tbody>
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
                                        <div class="col-md-5 rd-rate" style="text-align: right"><span style="color: red;font-weight: 700">{{$customer->order_rate}}</span> VNĐ/tệ</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-7">Tổng tiền hàng(VNĐ)</div>
                                        <div class="col-md-5 rd-vnd" style="text-align: right"><span style="color: red;font-weight: 700">0</span> VNĐ</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="total-order">
                            <div>
                                <h5>Tổng tiền đơn hàng tạm tính</h5>
                            </div>
                            <div class="chiphi">
                                <div class="rd-left">
                                    <ul>
                                        <li class="total-quantity">
                                            <div>Tổng số lượng sản phẩm</div>
                                            <div class="rd-prime"><span>0</span> sản phẩm</div>
                                        </li>
                                        <li class="total-price-order">
                                            <div>Tổng tiền hàng(RMB)</div>
                                            <div class="rd-prime"><span>0</span>￥</div>
                                        </li>
                                        <li class="rate" rate="{{$customer->order_rate}}">
                                            <div>Tỷ giá</div>
                                            <div class="rd-prime"><span>{{$customer->order_rate}}</span> VNĐ/tệ</div>
                                        </li>
                                        <li class="total-price-order-vnd">
                                            <div>Tổng tiền hàng(VNĐ)</div>
                                            <div class="rd-prime"><span>0</span> VNĐ</div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary next-step" style="float:right;margin-top:10px">Tiếp theo</button>
                    </div>

                    {{--Customer info --}}
                    <div class="card-body order-info" style="display: none">
                        <div class="mb-1">
                            <div class="col-md-6">
                                <h3>Thông tin mua hàng</h3>
                                <div style="border: 1px solid #e5e5e5; padding: 20px;min-height: 577px">
                                    <div class="form-group">
                                        <b>Họ tên: <span style="color: red">*</span></b>
                                        <input class="form-control" type="text" name="customer_billing_name" value="{{$customer->name}}"/>
                                        <span class="invalid-feedback"><strong></strong></span>
                                    </div>
                                    <div class="form-group">
                                        <b class="large">Email: <span style="color: red">*</span></b>
                                        <input class="form-control" type="text" name="email" value="{{$customer->email}}"/>
                                        <span class="invalid-feedback"><strong></strong></span>
                                    </div>
                                    <div class="form-group">
                                        <b>Tỉnh/Thành phố: <span style="color: red">*</span></b>
                                        <select class="form-control" name="billing_provincial_id" onchange="selectBillingDistrict()">
                                            <option value="">--Chọn tỉnh--</option>
                                            @foreach($provincials as $provincial)
                                                @if($address->provincial_id == $provincial->matp)
                                                    <option value="{{$address->provincial_id}}" selected>{{$provincial->name}}</option>
                                                @else
                                                    <option value="{{$provincial->matp}}">{{$provincial->name}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <span class="invalid-feedback"><strong></strong></span>
                                    </div>
                                    <div class="form-group">
                                        <b>Quận/Huyện: <span style="color: red">*</span></b>
                                        <select class="form-control" name="billing_district_id" onchange="selectBillingWard()">
                                            <option value="">--Chưa chọn tỉnh--</option>
                                        </select>
                                        <span class="invalid-feedback"><strong></strong></span>
                                    </div>
                                    <div class="form-group">
                                        <b>Phường/Xã: <span style="color: red">*</span></b>
                                        <select class="form-control" name="billing_ward_id">
                                            <option value="">--Chưa chọn quận huyện--</option>
                                        </select>
                                        <span class="invalid-feedback"><strong></strong></span>
                                    </div>
                                    <div class="form-group">
                                        <b class="large">Địa chỉ: <span style="color: red">*</span></b>
                                        <input type="text" class="form-control" name="customer_billing_address" value="{{$address->address}}"/>
                                        <span class="invalid-feedback"><strong></strong></span>
                                    </div>
                                    <div class="form-group">
                                        <b class="large">Điện thoại: <span style="color: red">*</span></b>
                                        <input class="form-control" type="text" name="customer_billing_phone" value="{{$address->phone}}"/>
                                        <span class="invalid-feedback"><strong></strong></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h3>Thông tin nhận hàng</h3>
                                <div style="border: 1px solid #e5e5e5; padding: 20px;min-height: 577px">
                                    <div class="form-group">
                                        <b>Địa chỉ nhận hàng</b>
                                        <select class="form-control" name="shipping_address">
                                            <option value="" selected disabled>Địa chỉ nhận hàng</option>
                                            <option value="0">Kho hàng redex</option>
                                            @foreach($shipping_addresses as $shipping_address)
                                                <option value="{{$shipping_address['id']}}">{{$shipping_address->name}}-{{$shipping_address->fullAddress}}</option>
                                            @endforeach
                                            <option id="address-add-new" value="add-new" style="color:red">Thêm địa chỉ mới</option>
                                        </select>
                                        <span class="invalid-feedback"><strong></strong></span>
                                    </div>
                                    <div id="form-add-new-address" style="display: none">
                                        @csrf
                                        <div class="form-group">
                                            <b>Họ tên: <span style="color: red">*</span></b>
                                            <input class="form-control" type="text" name="customer_shipping_name"/>
                                            <span class="invalid-feedback"><strong></strong></span>
                                        </div>
                                        <div class="form-group">
                                            <b>Tỉnh/Thành phố: <span style="color: red">*</span></b>
                                            <select class="form-control" name="provincial_id" onchange="selectDistrict()">
                                                <option value="">--Chọn tỉnh--</option>
                                                @foreach($provincials as $provincial)
                                                    <option value="{{$provincial->matp}}">{{$provincial->name}}</option>
                                                @endforeach
                                            </select>
                                            <span class="invalid-feedback"><strong></strong></span>
                                        </div>
                                        <div class="form-group">
                                            <b>Quận/Huyện: <span style="color: red">*</span></b>
                                            <select class="form-control" name="district_id" onchange="selectWard()">
                                                <option value="">--Chưa chọn tỉnh--</option>
                                            </select>
                                            <span class="invalid-feedback"><strong></strong></span>
                                        </div>
                                        <div class="form-group">
                                            <b>Phường/Xã: <span style="color: red">*</span></b>
                                            <select class="form-control" name="ward_id">
                                                <option value="">--Chưa chọn quận huyện--</option>
                                            </select>
                                            <span class="invalid-feedback"><strong></strong></span>
                                        </div>
                                        <div class="form-group">
                                            <b>Địa chỉ: <span style="color: red">*</span></b>
                                            <input class="form-control" type="text" name="customer_shipping_address"/>
                                            <span class="invalid-feedback"><strong></strong></span>
                                        </div>
                                        <div class="form-group">
                                            <b>Điện thoại: <span style="color: red">*</span></b>
                                            <input class="form-control" type="text" name="customer_shipping_phone"/>
                                            <span class="invalid-feedback"><strong></strong></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="float:right;margin: 20px 15px 0px 0px">
                            <button type="button" class="btn btn-info prev-step">Quay lại</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-pencil"></i> Lưu đơn hàng
                            </button>
                            <a class="btn btn-danger" href="{{route('order.index')}}">
                                Hủy đơn hàng
                            </a>
                        </div>
                    </div>
                    {{csrf_field()}}
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
                                <div class="loader"></div>
                            </div>
                        </div>
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
                            <button type="button" class="btn btn-danger delete-item" onclick="deleteRow()">OK</button>
                        </div>
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
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        var matp = '{{$address->provincial_id}}';
        var maqh = '{{$address->district_id}}';
        var xaid = '{{$address->ward_id}}';

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
    </script>
    <script src="{{asset('build/js/customer/order/create.js')}}"></script>
@endsection