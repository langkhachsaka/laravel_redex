calculateTotalByShop();
calculateTotalByShopUndefined();
$('#import').on('hide.bs.modal',function () {
    $('#importForm')[0].reset();
    $('#importForm').find('input[name="imports"]').remove();
    $('#importForm').find('button').attr('disabled','disabled');
    $(this).find('.file-name').html('Chọn tệp excel.');
});

function fillData(){
    if ($('#checkbox').prop('checked')){
        $('input[name="customer_shipping_name"]').val($('input[name="customer_billing_name"]').val());
        $('input[name="customer_shipping_address"]').val($('input[name="customer_billing_address"]').val());
        $('input[name="customer_shipping_phone"]').val($('input[name="customer_billing_phone"]').val());
    }else{
        $('input[name="customer_shipping_name"]').val('');
        $('input[name="customer_shipping_address"]').val('');
        $('input[name="customer_shipping_phone"]').val('');
    }
}

function numberFormat(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

$('#form-update-order').submit(function (e) {
    e.preventDefault();
    var form = $('#form-update-order');
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
                    $('#table-list-order-item tbody tr').find('.no-img-'+attribute[1]+' img').css('border','1px solid red');
                }
                var name = attribute[0]+'['+attribute[1]+']';
                form.find("[name='" + name + "']").addClass("is-invalid").parent().find("strong").html(data.errors[attr]);
            }
            return;
        }
        //success
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
});

$(".product-info").on("click", ".openModalUploadImage", function(e) {
    e.preventDefault();
    var rowIndex = $(this).data("id");
    console.log(rowIndex);
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

$(document).on("click",".deleteImage", function (e) {
    e.preventDefault();
    $(this).parent().remove();
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

// delete row on table
function openModalDeleteItem(rowIndex){
    $('#confirm-delete-item').find('.delete-item').attr('onclick', 'deleteRow('+rowIndex+')');
    $('#confirm-delete-item').modal('show');
}

// add product into table via excel
$("#importForm").submit(function (e) {
    e.preventDefault();
    var form = $(this);

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
            trs += "<td width='15%'>Cỡ<span style='color: red'> *</span></td>";
            trs += "<td width='15%'>Màu<span style='color: red'> *</span></td>";
            trs += "<td width='15%'>Đơn vị<span style='color: red'> *</span></td>";
            trs += "<td width='15%'>Giá web<span style='color: red'> *</span></td>";
            trs += "<td width='15%'>Số lượng<span style='color: red'> *</span></td>";
            trs += "<td>Thành tiền</td>"
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
            trs += "<button type='button' class='btn btn-danger' onclick='openModalDeleteItem("+rowIndex+")'><i class='fa fa-trash'></i></button>";
            trs += "</td>";
            trs += "</tr>";

            $('.rd-shop-undefined > .table-list-order-item > tbody').append(trs);
            $('.rd-shop-undefined').show();
            $("#import").modal("hide");
            calculateTotal();
            calculateTotalByShopUndefined();
        }
    });
});

// add row into table
function addRow(){
    var product = $('input[name="product-quantity"]').val();
    if(product > 0){
        var row = product;

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
            trs += "<td width='15%'>Cỡ<span style='color: red'> *</span></td>";
            trs += "<td width='15%'>Màu<span style='color: red'> *</span></td>";
            trs += "<td width='15%'>Đơn vị<span style='color: red'> *</span></td>";
            trs += "<td width='15%'>Giá web<span style='color: red'> *</span></td>";
            trs += "<td width='15%'>Số lượng<span style='color: red'> *</span></td>";
            trs += "<td>Thành tiền</td>"
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
            trs += "<button type='button' class='btn btn-danger' onclick='openModalDeleteItem("+rowIndex+")'><i class='fa fa-trash'></i></button>";
            trs += "</td>";
            trs += "</tr>";

            $('.rd-shop-undefined > .table-list-order-item > tbody').append(trs);
            $('.rd-shop-undefined').show();
        }
        $('input[name="product-quantity"]').val(0).removeClass("is-invalid");
    }else{
        $('input[name="product-quantity"]').addClass("is-invalid");
    }
}

function deleteRow(rowIndex) {
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
        })
        $(tr).find('.price_cny input').data('id',i);
        $(tr).find('.price_cny input').attr('data-id',i);
        $(tr).find('.quantity input').data('id',i);
        $(tr).find('.quantity input').attr('data-id',i);
        $(tr).find('td.image .upload a.upload-image').data('id',i);
        $(tr).find('td.image .upload a.upload-image').attr('data-id',i);
        $(tr).find('td.image .upload a.upload-link').data('id',i);
        $(tr).find('td.image .upload a.upload-link').attr('data-id',i);
        $(tr).find('td.delete-row button').attr('onclick','openModalDeleteItem('+i+')');
        $(tr).find('td.image div:first').attr('class','no-img-'+i);
    })
    $('#confirm-delete-item').modal('hide');
    calculateTotal();
    calculateTotalByShop();
    calculateTotalByShopUndefined();
}

function calculateTotal(){
    var total_quantity = 0;
    var total = 0;
    var total_vnd = 0;
    $('.table-list-order-item > tbody > tr table').each(function(_,input) {
        var quantity_item = $(input).find('.quantity input').val();
        if(parseInt(quantity_item)){
            total_quantity += parseInt(quantity_item);
        }
        var price_item = $(input).find('.total-price').attr('total-price');
        total += parseFloat(price_item);
        total = Math.round(total*100)/100;
    });
    var rate = $('.rate').attr('rate');
    total_vnd += total * parseFloat(rate);
    total_vnd = Math.round(total_vnd*100)/100;

    $('.total-quantity span').html(total_quantity);
    $('.total-price-order span').html(total);
    $('.total-price-order').attr('total-price',total);
    $('.total-price-order-vnd span').html(numberFormat(total_vnd));
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

function amountUp(input,rowIndex){
    var amount = parseInt($(input).siblings('input').val());
    amount += 1;
    $(input).siblings('input').val(amount);
}

function amountDown(input,rowIndex){
    var amount = parseInt($(input).siblings('input').val());
    if(amount == 0){
        return false;
    }
    amount -= 1;
    $(input).siblings('input').val(amount);
}

function removeImage(input) {
    $(input).parent().remove();
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
                var img = '<div class="image-preview"><img src="'+$(input).val()+'" style="width: 100%;height:auto"/></div>';
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

function calculateTotalByShop() {
    var total_quantity = 0;
    var total = 0;
    var total_vnd = 0
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
    //tự động tính giá, số lượng cho shop chưa xác định
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

