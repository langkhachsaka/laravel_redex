calculateTotal();
calculateTotalByShop();
$('select[name="shipping_address"]').change(function () {
    var form = $('#form-add-new-address');
    if($('select[name="shipping_address"]').val() != 0){
        form.show();
    }else{
        form.css('display','none');
    }

    if($(this).val() !== 'add-new' && $(this).val() !== '0'){
        form.find($('.add-address')).hide();
        form.find($('.update-address')).show();
        $.get('/get-address',{id : $(this).val()},function (res) {
            form.find($('input[name="customer_shipping_name"]')).val(res.data.name);
            form.find($('select[name="provincial_id"]')).val(res.data.provincial_id);
            $.get('/get-quan-huyen',{matp: res.data.provincial_id},function (result) {
                var option = '<option value="">--Chọn quận huyện--</option>';
                for(var i = 0; i < result.data.length; i++){
                    if(result.data[i].maqh == res.data.district_id){
                        option += '<option value="'+result.data[i].maqh+'" selected>'+result.data[i].name+'</option>';
                    }else{
                        option += '<option value="'+result.data[i].maqh+'">'+result.data[i].name+'</option>';
                    }

                }
                $('select[name=district_id]').html(option);
            });

            $.get('/get-phuong-xa',{maqh: res.data.district_id},function (result) {
                var option = '<option value="">--Chọn phường xã--</option>';
                for(var i = 0; i < result.data.length; i++){
                    if(result.data[i].xaid == res.data.ward_id) {
                        option += '<option value="' + result.data[i].xaid + '" selected>' + result.data[i].name + '</option>';
                    }else{
                        option += '<option value="' + result.data[i].xaid + '">' + result.data[i].name + '</option>';
                    }
                }
                $('select[name=ward_id]').html(option);
            });
            form.find($('input[name="customer_shipping_address"]')).val(res.data.address);
            form.find($('input[name="customer_shipping_phone"]')).val(res.data.phone);
        });
    }else{
        form.find($('input[name="customer_shipping_name"]')).val('');
        form.find($('select[name="provincial_id"]')).val('');
        form.find($('select[name=district_id]')).html('<option value="">--Chưa chọn tỉnh--</option>');
        form.find($('select[name=ward_id]')).html('<option value="">--Chưa chọn quận huyện--</option>');
        form.find($('input[name="customer_shipping_address"]')).val('');
        form.find($('input[name="customer_shipping_phone"]')).val('');
    }
});

$('#import').on('hide.bs.modal',function () {
    $('#importForm')[0].reset();
    $('#importForm').find('input[name="imports"]').remove();
    $('#importForm').find('button').attr('disabled','disabled');
    $(this).find('.file-name').html('Chọn tệp excel.');
});
// add product into table via excel
$("#importForm").submit(function (e) {
    e.preventDefault();
    var form = $(this);
    $('.loader').show();

    $.post(form.attr("action"), form.serialize(), function (response) {
        for (var i=0; i < response.length; i++){
            var rowIndex = $('.table-list-order-item > tbody > tr').length;
            var trs = "";
            trs += "<tr class='item-row-" + rowIndex +"'>";
            trs += "<td class='stt' style='vertical-align: middle'>";
            trs += "<span>"+(rowIndex + 1)+"</span>";
            trs += "<input type='hidden' name='item["+rowIndex+"]' value='"+rowIndex+"'/>";
            trs += "</td>";
            trs += "<td class='image' width='200px'>";
            if(response[i]['images'] == null){
                trs += "<div class='no-img-"+rowIndex+"'><img src='http://via.placeholder.com/100x100' style='width: 100%;height: auto;'/></div>";
                trs += "<input type='hidden' name='images["+rowIndex+"]' value=''/> ";
            }else{
                trs += "<div class='no-img-"+rowIndex+"' style='display: none'><img src='http://via.placeholder.com/100x100' style='width: 100%;height: auto;'/></div>";
                trs += "<div style='margin-bottom: 7px' class='image-preview'><img src='"+response[i]['images']+"' style='width:200px;height:auto'/></div>";
                trs += "<input type='hidden' name='images["+rowIndex+"][]' value='"+response[i]['images']+"'/>";
            }
            trs += "<div class='upload' style='text-align: center;margin-top: 5px'>";
            trs += "<a href='#' class='upload-image openModalUploadImage' data-id='"+rowIndex+"' style='margin-right: 20px'>Upload</a>";
            trs += "<a href='#' class='upload-link openModalUploadLink' data-id='"+rowIndex+"'>Link</a>";
            trs += "</div>";
            trs += "<span class='invalid-feedback' style='display: unset'><strong></strong></span>";
            trs += "</td>";
            trs += "<td>";
            trs += "<table class='table table-bordered'>";
            trs += "<tbody>";
            trs += "<tr>";
            trs += "<td>Link sản phẩm<span style='color: red'> *</span></td>";
            trs += "<td width='10%'>Cỡ<span style='color: red'> *</span></td>";
            trs += "<td width='15%'>Màu<span style='color: red'> *</span></td>";
            trs += "<td width='15%'>Đơn vị<span style='color: red'> *</span></td>";
            trs += "<td width='15%'>Giá web<span style='color: red'> *</span></td>";
            trs += "<td width='15%'>Số lượng<span style='color: red'> *</span></td>";
            trs += "<td width='15%'>Thành tiền</td>"
            trs += "</tr>";
            trs += "<tr>";
            trs += "<td>";
            trs += "<input type='text' class='form-control' name='link["+rowIndex+"]' value='"+response[i]['link']+"'/>";
            trs += "<span class='invalid-feedback'><strong></strong></span>";
            trs += "</td>";
            trs += "<td>";
            trs += "<input type='text' class='form-control' name='size["+rowIndex+"]' value='"+response[i]['size']+"'/>";
            trs += "<span class='invalid-feedback'><strong></strong></span>";
            trs += "</td>";
            trs += "<td>";
            trs += "<input type='text' class='form-control' name='colour["+rowIndex+"]' value='"+response[i]['colour']+"'/>";
            trs += "<span class='invalid-feedback'><strong></strong></span>";
            trs += "</td>";
            trs += "<td>";
            trs += "<input type='text' class='form-control' name='unit["+rowIndex+"]' value='"+response[i]['unit']+"'/>";
            trs += "<span class='invalid-feedback'><strong></strong></span>";
            trs += "</td>";
            trs += "<td class='price_cny'>";
            trs += "<input type='text' class='form-control' name='price_cny["+rowIndex+"]' data-id='"+rowIndex+"' value='"+response[i]['price_cny']+"'/>";
            trs += "<span class='invalid-feedback'><strong></strong></span>";
            trs += "</td>";
            trs += "<td class='quantity'>";
            trs += "<input type='text' class='form-control' name='quantity["+rowIndex+"]' data-id='"+rowIndex+"' value='"+response[i]['quantity']+"'/>";
            trs += "<span class='invalid-feedback'><strong></strong></span>";
            trs += "</td>";
            trs += "<td class='total-price' total-price='"+response[i]['quantity'] * response[i]['price_cny']+"'><span>￥"+response[i]['quantity'] * response[i]['price_cny']+"</span></td>";
            trs += "</tr>";
            trs += "<tr>";
            trs += "<td>Mô tả<span style='color: red'> *</span></td>";
            trs += "<td colspan='6'>";
            trs += "<input type='text' class='form-control' name='description["+rowIndex+"]' value='"+response[i]['description']+"'/>";
            trs += "<span class='invalid-feedback'><strong></strong></span>";
            trs += "</td>";
            trs += "</tr>";
            trs += "<tr>";
            trs += "<td colspan='7'><textarea class='form-control' placeholder='Ghi chú' name='note["+rowIndex+"]'></textarea></td>";
            trs += "</tr>";
            trs += "</tbody>";
            trs += "</table>";
            trs += "</td>";
            trs += "<td style='vertical-align: middle' class='delete-row'>";
            trs += "<button type='button' class='btn btn-danger' data-row='"+rowIndex+"'><i class='fa fa-trash'></i></button>";
            trs += "</td>";
            trs += "</tr>";

            $('.rd-shop-undefined > .table-list-order-item > tbody').append(trs);
            $('.rd-shop-undefined').show();
            $('.loader').hide();
            $("#import").modal("hide");
            $('.add-product').hide();
            $('.next-step').show();
            $('.product-info').show()
            $('.total-order').show();
            calculateTotal();
            calculateTotalByShopUndefined();
        }
    });
});

//validate and  create order
$("#form-create-order").submit(function (e) {
    e.preventDefault();
    var form = $(this);
    form.find('.is-invalid').removeClass('.is-invalid').next().find("strong").html('');
    $.post(form.attr("action"), form.serialize(), function (data) {
        if (data.status == 'invalid') {
            // invalid data
            for (var attr in data.errors) {
                if (data.errors.hasOwnProperty(attr)) {
                    form.find("[name='" + attr + "']").addClass("is-invalid").next().find("strong").html(data.errors[attr]);
                }
            }
            return;
        }
        window.location = data.url;
    });
});

$('.next-step').click(function(){
    var form = $('#form-create-order');
    $('.message').remove();
    form.find('strong').html('');
    var count = $('.table-list-order-item > tbody > tr').length;
    if(count == 0){
        var message = '<div class="message" style="color:red"><h6>Đơn hàng phải có ít nhất 1 sản phẩm</h6></div>';
        $('.product-info').before(message);

        return;
    }

    $.post('/customer/order-item/validate', form.serialize(), function (data) {
        if (!data.is_valid ) {
            // invalid data
            for (var attr in data.errors) {
                var attribute = attr.split('.');
                if(attribute[0] == 'images'){
                    $('.table-list-order-item tbody tr').find('.no-img-'+attribute[1]+' img').css('border','1px solid red');
                }
                if(attr == 'amount_of_money'){
                    form.find("[name='" + attr + "']").addClass("is-invalid").parent().find("strong").html(data.errors[attr]);
                }
                var name = attribute[0]+'['+attribute[1]+']';
                form.find("[name='" + name + "']").addClass("is-invalid").parent().find("strong").html(data.errors[attr]);
            }
            return;
        }
        //success
        $('.order-info').show();
        $('.product-info').hide();
        $('.add-product').hide();
    });
});

$('.prev-step').click(function () {
    $('.order-info').hide();
    $('.product-info').show();
});

$('.product-info').on('click','.delete-row button',function () {
    var rowIdex = $(this).data('row');
    var id = $(this).data('id');
    if(id !== undefined){
        $('#confirm-delete-item').find('.delete-item').attr('onclick', 'deleteRow('+rowIdex+','+id+')');
    }else{
        $('#confirm-delete-item').find('.delete-item').attr('onclick', 'deleteRow('+rowIdex+')');
    }
    $('#confirm-delete-item').modal('show');
});

function numberFormat(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function deleteRow(rowIndex,id) {
    if(id){
        $.get('/update-session',{id: id});
    }
    $('.item-row-'+rowIndex).remove();
    $('.rd-shop').each(function (_,element) {
        if($(element).find('.table-list-order-item > tbody > tr').length == 0){
            $(element).remove();
        }
    });

    if($('.rd-shop-undefined').find('.table-list-order-item > tbody > tr').length == 0)
    {
        $('.rd-shop-undefined').css('display','none');
    }

    var trs = $('.table-list-order-item > tbody > tr');
    var i=0;
    $.each(trs,function (i,tr) {
        $(tr).attr('class','item-row-'+i);
        $(tr).find('td.stt span').html(i+1);
        $(tr).find('td textarea').attr('name','note['+i+']');
        $.each($(tr).find('td input'),function (_,input) {
            var name = $(input).attr('name');
            var str = name.split('[');
            if(str[0] == 'images' || str[0] == 'images-link'){
                var new_name = str[0]+'['+i+'][]';
            }else{
                var new_name = str[0]+'['+i+']';
            }
            $(input).attr('name', new_name);
        });
        $(tr).find('.price_cny input').data('id',i);
        $(tr).find('.price_cny input').attr('data-id',i);
        $(tr).find('.quantity input').data('id',i);
        $(tr).find('.quantity input').attr('data-id',i);
        $(tr).find('td.image .upload a.upload-image').data('id',i);
        $(tr).find('td.image .upload a.upload-image').attr('data-id',i);
        $(tr).find('td.image .upload a.upload-link').data('id',i);
        $(tr).find('td.image .upload a.upload-link').attr('data-id',i);
        if(id){
            $(tr).find('td.delete-row button').data('row',i);
            $(tr).find('td.delete-row button').attr('data-row',i);
        }else{
            $(tr).find('td.delete-row button').data('row',i);
            $(tr).find('td.delete-row button').attr('data-row',i);
        }
        $(tr).find('td.image div:first').attr('class','no-img-'+i);
    });
    $('#confirm-delete-item').modal('hide');
    if(trs.length == 0){
        $('.total-order').hide();
        $('.next-step').hide();
    }
    calculateTotal();
    calculateTotalByShop();
    calculateTotalByShopUndefined();
}

// add row into table
function addRow(){
    var product = $('input[name="product-quantity"]').val();
    var quantityAdded = $('input[name="product-quantity-added"]').val();
    if(product > 0 || quantityAdded > 0){
        if(product > 0) {
            var row = product;
        }else if(quantityAdded > 0){
            var row = quantityAdded;
        }

        for (var i=0; i < row; i++){
            var rowIndex = $('.table-list-order-item > tbody > tr').length;
            var trs = "";
            trs += "<tr class='item-row-" + rowIndex +"'>";
            trs += "<td class='stt' style='vertical-align: middle'>";
            trs += "<span>"+(rowIndex + 1)+"</span>";
            trs += "<input type='hidden' name='item["+rowIndex+"]' value='"+rowIndex+"'/>";
            trs += "</td>";
            trs += "<td class='image' width='200px'>";
            trs += "<div class='no-img-"+rowIndex+"'><img src='http://via.placeholder.com/100x100' style='width: 100%;height: auto;'/></div>";
            trs += "<input type='hidden' name='images["+rowIndex+"]' value=''/> ";
            trs += "<div class='upload' style='text-align: center;margin-top: 5px'>";
            trs += "<a href='#' class='upload-image openModalUploadImage' data-id='"+rowIndex+"' style='margin-right: 20px'>Upload</a>";
            trs += "<a href='#' class='upload-link openModalUploadLink' data-id='"+rowIndex+"'>Link</a>";
            trs += "</div>";
            trs += "<span class='invalid-feedback' style='display: unset'><strong></strong></span>";
            trs += "</td>";
            trs += "<td>";
            trs += "<table class='table table-bordered'>";
            trs += "<tbody>";
            trs += "<tr>";
            trs += "<td>Link sản phẩm<span style='color: red'> *</span></td>";
            trs += "<td width='10%'>Cỡ<span style='color: red'> *</span></td>";
            trs += "<td width='15%'>Màu<span style='color: red'> *</span></td>";
            trs += "<td width='15%'>Đơn vị<span style='color: red'> *</span></td>";
            trs += "<td width='15%'>Giá web<span style='color: red'> *</span></td>";
            trs += "<td width='15%'>Số lượng<span style='color: red'> *</span></td>";
            trs += "<td width='15%'>Thành tiền</td>"
            trs += "</tr>";
            trs += "<tr>";
            trs += "<td>";
            trs += "<input type='text' class='form-control' name='link["+rowIndex+"]'/>";
            trs += "<span class='invalid-feedback'><strong></strong></span>";
            trs += "</td>";
            trs += "<td>";
            trs += "<input type='text' class='form-control' name='size["+rowIndex+"]'/>";
            trs += "<span class='invalid-feedback'><strong></strong></span>";
            trs += "</td>";
            trs += "<td>";
            trs += "<input type='text' class='form-control' name='colour["+rowIndex+"]'/>";
            trs += "<span class='invalid-feedback'><strong></strong></span>";
            trs += "</td>";
            trs += "<td>";
            trs += "<input type='text' class='form-control' name='unit["+rowIndex+"]'/>";
            trs += "<span class='invalid-feedback'><strong></strong></span>";
            trs += "</td>";
            trs += "<td class='price_cny'>";
            trs += "<input type='text' class='form-control' name='price_cny["+rowIndex+"]' data-id='"+rowIndex+"'/>";
            trs += "<span class='invalid-feedback'><strong></strong></span>";
            trs += "</td>";
            trs += "<td class='quantity'>";
            trs += "<input type='text' class='form-control' name='quantity["+rowIndex+"]' data-id='"+rowIndex+"'/>";
            trs += "<span class='invalid-feedback'><strong></strong></span>";
            trs += "</td>";
            trs += "<td class='total-price' total-price='0'><span>￥0</span></td>";
            trs += "</tr>";
            trs += "<tr>";
            trs += "<td>Mô tả<span style='color: red'> *</span></td>";
            trs += "<td colspan='6'>";
            trs += "<input type='text' class='form-control' name='description["+rowIndex+"]'/>";
            trs += "<span class='invalid-feedback'><strong></strong></span>";
            trs += "</td>";
            trs += "</tr>";
            trs += "<tr>";
            trs += "<td colspan='7'><textarea class='form-control' placeholder='Ghi chú' name='note["+rowIndex+"]'></textarea></td>";
            trs += "</tr>";
            trs += "</tbody>";
            trs += "</table>";
            trs += "</td>";
            trs += "<td style='vertical-align: middle' class='delete-row'>";
            trs += "<button type='button' class='btn btn-danger' data-row='"+rowIndex+"'><i class='fa fa-trash'></i></button>";
            trs += "</td>";
            trs += "</tr>";

            $('.rd-shop-undefined > .table-list-order-item > tbody').append(trs);
            $('.rd-shop-undefined').show();
        }
        $('input[name="product-quantity"]').val(0).removeClass("is-invalid");
        $('input[name="product-quantity-added"]').val(0).removeClass("is-invalid");
        $('.product-info').show();
        $('.add-product').hide();
        $('.total-order').show();
        $('.next-step').show();
    }else{
        $('input[name="product-quantity"]').addClass("is-invalid");
        $('input[name="product-quantity-added"]').addClass("is-invalid");
    }
}

function amountProductUp(input) {
    var amount = parseInt($(input).siblings('input').val());
    amount += 1;
    $(input).siblings('input').val(amount);
}

function amountProductDown(input) {
    var amount = parseInt($(input).siblings('input').val());
    if(amount == 0){
        return false;
    }
    amount -= 1;
    $(input).siblings('input').val(amount);
}

function amountUp(input,rowIndex){
    var amount = parseInt($(input).siblings('input').val());
    amount += 1;
    $(input).siblings('input').val(amount);

    //update total price item
    updateTotalItem(rowIndex);

    //update total footer
    var total_quantity = 0;
    var total = 0;
    var total_vnd = 0;
    $('.table-list-order-item tbody tr').each(function(_,input) {
        var quantity_item = $(input).find('.quantity input').val();
        if(parseInt(quantity_item)){
            total_quantity += parseInt(quantity_item);
        }
        var price_item = $(input).find('.total_price').attr('total-price');
        total += parseFloat(price_item);
        total = Math.round(total*100)/100;
    });

    var rate = $('.table-list-order-item tfoot tr td').find('.rate').attr('rate');
    total_vnd += total * parseFloat(rate);
    total_vnd = Math.round(total_vnd*100)/100;

    $('.table-list-order-item tfoot tr td').find('.total-quantity p').html(total_quantity);
    $('.table-list-order-item tfoot tr td').find('.total-price p').html(total);
    $('.table-list-order-item tfoot tr td').find('.total-price').attr('total-price',total);
    $('.table-list-order-item tfoot tr td').find('.total-vnd p').html(total_vnd);
}

function amountDown(input,rowIndex){
    var amount = parseInt($(input).siblings('input').val());
    if(amount == 0){
        return false;
    }
    amount -= 1;
    $(input).siblings('input').val(amount);

    //update total price item
    updateTotalItem(rowIndex);

    //update total footer
    var total_quantity = 0;
    var total = 0;
    var total_vnd = 0;
    $('.table-list-order-item tbody tr').each(function(_,input) {
        var quantity_item = $(input).find('.quantity input').val();
        if(parseInt(quantity_item)){
            total_quantity += parseInt(quantity_item);
        }
        var price_item = $(input).find('.total_price').attr('total-price');
        total += parseFloat(price_item);
        total = Math.round(total*100)/100;
    });

    var rate = $('.table-list-order-item tfoot tr td').find('.rate').attr('rate');
    total_vnd += total * parseFloat(rate);
    total_vnd = Math.round(total_vnd*100)/100;

    $('.table-list-order-item tfoot tr td').find('.total-quantity p').html(total_quantity);
    $('.table-list-order-item tfoot tr td').find('.total-price p').html(total);
    $('.table-list-order-item tfoot tr td').find('.total-price').attr('total-price',total);
    $('.table-list-order-item tfoot tr td').find('.total-vnd p').html(total_vnd);
}

// add shipping address
function addressAction(action) {
    var inputs = $('#form-add-new-address').find('input');
    var data = {};
    $.each(inputs, function (_,input) {
        var name = $(input).attr('name');
        data[name] = $(input).val();
    });
    data['provincial_id'] = $('select[name=provincial_id]').val();
    data['district_id'] = $('select[name=district_id]').val();
    data['ward_id'] = $('select[name=ward_id]').val();
    if(action == 'add'){
        $.post('/customer/address/create-ajax',data,function (res) {
            if(res.status == 'invalid'){
                for (var attr in res.errors) {
                    $('#form-add-new-address').find("[name='" + attr + "']").addClass("is-invalid").next().find("strong").html(res.errors[attr]);
                }
                return;
            }
            var option = '<option value="'+res.id+'" selected>'+res.address+'</option>';
            $('#address-add-new').before(option);
            $('#form-add-new-address').find($('.add-address')).hide();
            $('#form-add-new-address').find($('.update-address')).show();
        });
    }else{
        data['addressId'] = $('select[name=shipping_address]').val();
        $.post('/customer/update-address',data,function (res) {
            if(res.status == 'invalid'){
                for (var attr in res.errors) {
                    $('#form-add-new-address').find("[name='" + attr + "']").addClass("is-invalid").next().find("strong").html(res.errors[attr]);
                }
                return;
            }

            $('select[name="shipping_address"] option[value="'+res.id+'"]').html(res.address);

        });
    }

}

function chooseFile(input) {
    $(input).siblings('input[name="image-create"]').click();
}

//upload image
function addImage(input) {
    var form_data = new FormData();
    for (var i=0; i < $(input).prop("files").length; i++) {
        form_data.append("images[]",$(input).prop("files")[i]);
    }
    $.ajax({
        url: '/api/v1/image',
        type: 'POST',
        processData: false,
        contentType: false,
        data:form_data,
        success: function (response) {
            var storageUrl = window.location.protocol+'//'+window.location.hostname+'/storage/';
            var currentRowId = $('#upload-image').data('row-index');
            for (var j=0; j < response.data.length; j++){
                var count = $(input).siblings('.image-preview').length;
                var htmlContent = '';
                htmlContent += '<div class="image-preview" style="margin:0px 7px;display: inline-block;position: relative">';
                htmlContent += '<img src="/storage/'+response.data[j]+'" style="height:180px;border: 1px solid #ccc;"/>';
                htmlContent += '<input type="hidden" name="images['+currentRowId+'][]" value="'+storageUrl+response.data[j]+'"/>';
                htmlContent += '<a href="#" class="deleteImage" style="background: bisque;position: absolute; padding: 0px 7px; top:0;right:0">x</a>'
                htmlContent += '</div>';

                $(input).parent().append(htmlContent);
            }
            $(input).val("");
        }
    });
}

function updateImage(input) {
    var currentRowId = $('#upload-image').data('row-index');
    $('.item-row-'+currentRowId).find('td.image input[type=hidden]').remove();
    $('.item-row-'+currentRowId).find('td.image .image-preview').remove();
    var inputs = $(input).parent().siblings('.modal-body').find('input[type="hidden"]');
    $.each(inputs,function (_,input) {
        $('.item-row-'+currentRowId).find('td.image').append($(input));
        if($('.item-row-'+currentRowId).find('td.image .image-preview').length == 0){
            var img = '<div class="image-preview"><img src="'+$(input).val()+'" style="width:100%;height:auto" class="hover-shadow cursor"/></div>';
        }else{
            var img = '<div class="image-preview"><img src="'+$(input).val()+'" style="width:100%;height:auto;display:none"/></div>';
        }
        $('.item-row-'+currentRowId).find('td.image .upload').before(img);
    });

    if($('.item-row-'+currentRowId).find('td.image .image-preview').length > 0){
        $('.no-img-'+currentRowId).hide();
    }else{
        $('.no-img-'+currentRowId).show();
        $('.item-row-'+currentRowId).find('td.image span').before('<input type="hidden" name="images['+currentRowId+']" value="" />');
    }
}

function updateLink(input) {
    var currentRowId = $('#upload-link').data('row-index');
    $('.item-row-'+currentRowId).find('td.image input[type=hidden]').remove();
    $('.item-row-'+currentRowId).find('td.image .image-preview').remove();
    var inputs = $('#upload-link').find('input');
    var i=0;
    $.each(inputs, function (i,input) {
        if($(input).val().length != 0){
            var html = '';
            html += '<input type="hidden" name="images['+currentRowId+'][]" value="'+$(input).val()+'"/>';
            $('.item-row-'+currentRowId).find('td.image').append(html);

            if($('.item-row-'+currentRowId).find('td.image .image-preview').length == 0){
                var img = '<div class="image-preview"><img src="'+$(input).val()+'" style="width:100%;height:auto" class="hover-shadow cursor"/></div>';
            }else{
                var img = '<div class="image-preview"><img src="'+$(input).val()+'" style="width:100%;height:auto;display:none"/></div>';
            }
            $('.item-row-'+currentRowId).find('td.image .upload').before(img);
        }
    })

    if($('.item-row-'+currentRowId).find('td.image .image-preview').length > 0){
        $('.no-img-'+currentRowId).hide();
    }else{
        $('.no-img-'+currentRowId).show();
        $('.item-row-'+currentRowId).find('td.image span').before('<input type="hidden" name="images['+currentRowId+']" value="" />');
    }
}

$(".product-info").on("click", ".openModalUploadImage", function(e) {
    e.preventDefault();
    var rowIndex = $(this).data("id");
    $('#upload-image').data('row-index',rowIndex);
    $('#upload-image').find(".image-preview").remove();
    var inputs = $('.item-row-'+rowIndex).find('td.image input[type=hidden]');
    var i = 0;
    $.each(inputs,function (i,input) {
        if($(input).val().length > 0){
            var image = '';
            image += '<div class="image-preview" style="margin:0px 7px;display: inline-block;position: relative">';
            image += '<img src="'+$(input).val()+'" style="height:180px;border: 1px solid #ccc;"/>';
            image += '<input type="hidden" name="images['+rowIndex+'][]" value="'+$(input).val()+'"/>';
            image += '<a href="#" class="deleteImage" style="background: bisque;position: absolute; padding: 0px 7px; top:0;right:0">x</a>'
            image += '</div>';
            $('#upload-image').find(".modal-body").append(image);
        }
    });

    $('#upload-image').modal('show');

});

$(".product-info").on("click", ".openModalUploadLink", function(e) {
    e.preventDefault();
    var rowIndex = $(this).data("id");
    $('#upload-link').data('row-index',rowIndex);
    $('#upload-link').find("input").val('');
    var inputs = $('.item-row-'+rowIndex).find('td.image input[type=hidden]');
    var i = 0;
    $.each(inputs, function (i,input) {
        $('#upload-link').find('input[name="link['+i+']"]').val($(input).val());
    });

    $('#upload-link').modal('show');

});

$(".product-info").on("keyup", ".price_cny input",function(e){
    var rowIndex = $(this).data('id')
    updateTotalItem(rowIndex);
    calculateTotal();
    calculateTotalByShop();
    calculateTotalByShopUndefined();
});

$(".product-info").on("keyup", ".quantity input",function(e){
    var rowIndex = $(this).data('id')
    updateTotalItem(rowIndex);
    calculateTotal();
    calculateTotalByShop();
    calculateTotalByShopUndefined();
});

$(document).on("click",".deleteImage", function (e) {
    e.preventDefault();
    $(this).parent().remove();
});

function calculateTotal(){
    var total_quantity = 0;
    var total = 0;
    var total_vnd = 0;
    var rate = $('.rate').attr('rate');
    $('.table-list-order-item > tbody > tr table').each(function(_,input) {
        var quantity_item = $(input).find('.quantity input').val();
        if(parseInt(quantity_item)){
            total_quantity += parseInt(quantity_item);
        }
        var price_item = $(input).find('.total-price').attr('total-price');
        total += parseFloat(price_item);
        total = Math.round(total*100)/100;
    });

    total_vnd += total * parseFloat(rate);
    total_vnd = Math.round(total_vnd*100)/100;


    $('.total-quantity span').html(total_quantity);
    $('.total-price-order span').html(total);
    $('.total-price-order').attr('total-price',total);
    $('.total-price-order-vnd span').html(numberFormat(total_vnd));
}

function calculateTotalByShop() {
    var total_quantity = 0;
    var total = 0;
    var total_vnd = 0;
    var rate = $('.rate').attr('rate');
    $('.rd-shop').each(function(_,element){
        $(element).find('.table-list-order-item > tbody > tr').each(function (_,tag) {
            var quantity_item = $(tag).find('.quantity input').val();
            if(parseInt(quantity_item)){
                total_quantity += parseInt(quantity_item);
            }
            var price_item = $(tag).find('.total-price').attr('total-price');
            total += parseFloat(price_item);
            total = Math.round(total*100)/100;
        });

        total_vnd += total * parseFloat(rate);
        total_vnd = Math.round(total_vnd*100)/100;

        $(element).find('.rd-sl span').html(total_quantity);
        $(element).find('.rd-rmb span').html(total);
        $(element).find('.rd-vnd span').html(numberFormat(total_vnd));

        total_quantity = 0;
        total = 0;
        total_vnd = 0;
    });
}

function calculateTotalByShopUndefined(){
    //tự động tính lại giá, số lượng cho shop chưa xác định
    var total_quantity = 0;
    var total = 0;
    var total_vnd = 0;
    var rate = $('.rate').attr('rate');
    $('.rd-shop-undefined').find('.table-list-order-item > tbody > tr').each(function (_,tag) {
        var quantity_item = $(tag).find('.quantity input').val();
        if(parseInt(quantity_item)){
            total_quantity += parseInt(quantity_item);
        }
        var price_item = $(tag).find('.total-price').attr('total-price');
        total += parseFloat(price_item);
        total = Math.round(total*100)/100;
    });

    total_vnd += total * parseFloat(rate);
    total_vnd = Math.round(total_vnd*100)/100;

    $('.rd-shop-undefined .rd-sl span').html(total_quantity);
    $('.rd-shop-undefined .rd-rmb span').html(total);
    $('.rd-shop-undefined .rd-vnd span').html(numberFormat(total_vnd));

}

function updateTotalItem(rowIndex) {
    var quantity = $('.item-row-'+rowIndex).find('.quantity input').val();
    var price = $('.item-row-'+rowIndex).find('.price_cny input').val();
    if(parseFloat(price) && parseInt(quantity)){
        var total_price = parseInt(quantity) * parseFloat(price);
        total_price = Math.round(total_price*100)/100;
        $('.item-row-'+rowIndex).find('.total-price span').html(total_price + '￥');
        $('.item-row-'+rowIndex).find('.total-price').attr('total-price',total_price)
    }else{
        $('.item-row-'+rowIndex).find('.total-price span').html('￥0');
        $('.item-row-'+rowIndex).find('.total-price').attr('total-price',0)
    }
}

$.get('/get-quan-huyen',{matp: matp},function (result) {
    var option = '<option value="">--Chọn quận huyện--</option>';
    for(var i = 0; i < result.data.length; i++){
        if(result.data[i].maqh == maqh){
            option += '<option value="'+result.data[i].maqh+'" selected>'+result.data[i].name+'</option>';
        }else{
            option += '<option value="'+result.data[i].maqh+'">'+result.data[i].name+'</option>';
        }

    }
    $('select[name=billing_district_id]').html(option);
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
    $('select[name=billing_ward_id]').html(option);
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

function selectBillingDistrict() {
    var matp = $('select[name=billing_provincial_id]').val();
    $.get('/get-quan-huyen',{matp: matp},function (res) {
        var option = '<option value="">--Chọn quận huyện--</option>';
        for(var i = 0; i < res.data.length; i++){
            option += '<option value="'+res.data[i].maqh+'">'+res.data[i].name+'</option>';
        }
        $('select[name=billing_district_id]').html(option);
    });
}

function selectBillingWard(){
    var maqh = $('select[name=billing_district_id]').val();
    $.get('/get-phuong-xa',{maqh: maqh},function (res) {
        var option = '<option value="">--Chọn phường xã--</option>';
        for(var i = 0; i < res.data.length; i++){
            option += '<option value="'+res.data[i].xaid+'">'+res.data[i].name+'</option>';
        }
        $('select[name=billing_ward_id]').html(option);
    });
}