var wallet_selected = false;
var pay_selected = false;
var pay_by_wallet_selected = false;
var pay_by_pay_selected = false;
var input;

calculateTotalByShopUndefined();
calculateTotalByShop();

$('.btn-payment').click(function (e) {
    var data = {};
    data._token = token;
    data.id = orderId;
    data.totalAmount = totalAmount;
    $.post('/get-total-amount',data,function (res) {
        $('#payment').find('span.pay-number').html(numberFormat(res.amountNeedPayment) +' VNĐ');
        $('#paymentForm').append('<input type="hidden" name="total_amount_need_payment" value="'+res.amountNeedPayment+'"/>');
        $('#payment').modal('show');
    });
});
// dat coc bang vi
$('#walletSelected').click(function () {
    if(!wallet_selected){
        $('.deposit-by-wallet').show();
        if(parseFloat(balanceWallet) <= parseFloat(totalDeposit)){
            $('#deposit .deposit-by-wallet').find('input').val(parseFloat(balanceWallet));
        }else{
            $('#deposit .deposit-by-wallet').find('input').val(parseFloat(totalDeposit));
        }
        if(pay_selected) {
            $('#deposit .deposit-by-pay').find('input').val(parseFloat(totalDeposit) - parseFloat($('#deposit .deposit-by-wallet').find('input').val()));
        }
    } else {
        $('#deposit .deposit-by-wallet').find('input').val(0);
        $('#deposit .deposit-by-pay').find('input').val(parseFloat(totalDeposit));
        $('.deposit-by-wallet').hide();
    }
    wallet_selected = !wallet_selected;
});
// dat coc bang chuyen khoan
$('#paySelected').click(function () {
    if(!pay_selected){
        $('.deposit-by-pay').show();
        $('#deposit .deposit-by-pay').find('input').val(parseFloat(totalDeposit) - parseFloat($('#deposit .deposit-by-wallet').find('input').val()));
    } else {
        $('.deposit-by-pay').hide();
        $('#deposit .deposit-by-pay').find('input').val(0);
    }
    pay_selected = !pay_selected;
});

// thanh toan bang vi
$('#payByWalletSelected').click(function () {
    var totalAmountNeedPayment = $('#payment').find('input[name="total_amount_need_payment"]').val();
    if(!pay_by_wallet_selected){
        $('.pay-by-wallet').show();
        if(parseFloat(balanceWallet) <= parseFloat(totalAmountNeedPayment)){
            $('#payment .pay-by-wallet').find('input').val(parseFloat(balanceWallet));
        }else{
            $('#payment .pay-by-wallet').find('input').val(parseFloat(totalAmountNeedPayment));
        }
        if(pay_by_pay_selected) {
            $('#payment .pay-by-pay').find('input').val(parseFloat(totalAmountNeedPayment) - parseFloat($('#payment .pay-by-wallet').find('input').val()));
        }
    } else {
        $('#payment .pay-by-wallet').find('input').val(0);
        $('#payment .pay-by-pay').find('input').val(parseFloat(totalAmountNeedPayment));
        $('.pay-by-wallet').hide();
    }
    pay_by_wallet_selected = !pay_by_wallet_selected;
});

// thanh toan chuyen khoan
$('#payByPaySelected').click(function () {
    var totalAmountNeedPayment = $('#payment').find('input[name="total_amount_need_payment"]').val();
    if(!pay_by_pay_selected){
        $('.pay-by-pay').show();
        $('#payment .pay-by-pay').find('input').val(parseFloat(totalAmountNeedPayment) - parseFloat($('#payment .pay-by-wallet').find('input').val()));
    }else{
        $('.pay-by-pay').hide();
        $('#payment .pay-by-pay').find('input').val(0);
    }
    pay_by_pay_selected = !pay_by_pay_selected;
});

$('#depositForm').submit(function (e) {
    e.preventDefault();
    if(wallet_selected == false && pay_selected == false){
        $(this).find('.msg').html('Phải chọn 1 trong 2 phương thức thanh toán');

        return;
    }
    var form = $(this);
    var data = {};
    var order = {};
    order[orderId] = totalDeposit;
    data.order = order;
    data.total_deposit = totalDeposit;
    data.deposit_by_wallet = $('#deposit .deposit-by-wallet').find('input').val();
    data.deposit_by_pay = $('#deposit .deposit-by-pay').find('input').val();
    data._token = token;
    $.post('/customer/order/deposit',data,function (res) {
        switch (res.status) {
            case 'invalid-validator':
                for (var attr in res.errors) {
                    if (res.errors.hasOwnProperty(attr)) {
                        form.find("[name='" + attr + "']").addClass("is-invalid").next().find("strong").html(res.errors[attr]);
                    }
                }return;
            case 'invalid':
                var msg = res.message;
                form.find('.msg').html(msg);
                return;
            case 'success':
                var message = '<div class="alert alert-success message">'+res.message+'<a href="#" style="position: absolute; padding: 0px 10px; top:10px;right:5px;font-size: 15px;color: black;">x</a></div>';
                $('.chiphi').before(message);
                break;
            case 'error':
                var message = '<div class="alert alert-danger message">'+res.message+'<a href="#" style="position: absolute; padding: 0px 10px; top:10px;right:5px;font-size: 15px;color: black;">x</a></div>';
                $('.chiphi').before(message);
                break;
        }

        $('.btn-deposit').css('display','none');
        $('#deposit').modal('hide');
        $('.deposited').find('span').html(numberFormat(res.totalDeposit));
        var remaining = parseFloat($('.total-price-order-vnd').data('total') - res.totalDeposit);
        $('.remaining_balance').find('span').html(numberFormat(remaining));
    });
});

$('#paymentForm').submit(function (e) {
    e.preventDefault();
    if(pay_by_wallet_selected == false && pay_by_pay_selected == false){
        $(this).find('.msg').html('Phải chọn 1 trong 2 phương thức thanh toán');
        return;
    }
    var form = $(this);
    var data = {};
    var order = {};
    order[orderId] = form.find('input[name="total_amount_need_payment"]').val();
    data.order = order;
    data.total_payment = form.find('input[name="total_amount_need_payment"]').val();
    data.payment_by_wallet = $('#payment .pay-by-wallet').find('input').val();
    data.payment_by_pay = $('#payment .pay-by-pay').find('input').val();
    data._token = token;
    $.post('/customer/order/payment',data,function (res) {
        switch (res.status) {
            case 'invalid-validator':
                for (var attr in res.errors) {
                    if (res.errors.hasOwnProperty(attr)) {
                        form.find("[name='" + attr + "']").addClass("is-invalid").next().find("strong").html(res.errors[attr]);
                    }
                }return;
            case 'invalid':
                var msg = res.message;
                form.find('.msg').html(msg);
                return;
            case 'success':
                var message = '<div class="alert alert-success remove-message">'+res.message+'<a href="#" style="position: absolute; padding: 0px 10px; top:10px;right:5px;font-size: 15px;color: black;">x</a></div>';
                $('.chiphi').before(message);
                break;
            case 'error':
                var message = '<div class="alert alert-danger remove-message">'+res.message+'<a href="#" style="position: absolute; padding: 0px 10px; top:10px;right:5px;font-size: 15px;color: black;">x</a></div>';
                $('.chiphi').before(message);
                break;
        }

        $('.btn-payment').css('display','none');
        $('#payment').modal('hide');
    });
});

$('.total-order').on('click','.remove-message',function (e) {
    e.preventDefault();
    $('.remove-message').remove();
});
var slideIndex = 1;

function plusSlides(input, n) {
    showSlides(input.parentNode,slideIndex += n);
}

function currentSlide(input, n) {
    showSlides(input.parentNode, slideIndex = n);
}

function currentImage(input, n){
    showSlides(input.parentNode.parentNode.parentNode, slideIndex = n);
}

function showSlides(input, n) {
    var i;
    var slides = $(input).find('.mySlides');
    var dots = $(input).find('.demo');

    if (n > slides.length) {slideIndex = 1}
    if (n < 1) {slideIndex = slides.length}
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    for (i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(" active", "");
    }
    slides[slideIndex-1].style.display = "block";
    dots[slideIndex-1].className += " active";
}

function numberFormat(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function calculateTotalByShop() {
    var total_quantity = 0;
    var total = 0;
    var total_vnd = 0;
    var rate = $('.rate').attr('rate');
    $('.rd-shop').each(function(_,element){
        $(element).find('.table-list-order-item > tbody > tr').each(function (_,tag) {
            var quantity_item = $(tag).find('.quantity').data('quantity');
            if(parseInt(quantity_item)){
                total_quantity += parseInt(quantity_item);
            }
            var price_item = $(tag).find('.total-price').data('total');
            total += parseFloat(price_item);
            total = Math.round(total*100)/100;
        });

        var surcharge = $(element).find('.rd-surcharge').data('surcharge');
        var discount = $(element).find('.rd-discount').data('discount');
        total_vnd += (total + surcharge - discount)  * parseFloat(rate);
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
        var quantity_item = $(tag).find('.quantity').data('quantity');
        if(parseInt(quantity_item)){
            total_quantity += parseInt(quantity_item);
        }
        var price_item = $(tag).find('.total-price').data('total');
        total += parseFloat(price_item);
        total = Math.round(total*100)/100;
    });

    total_vnd += total * parseFloat(rate);
    total_vnd = Math.round(total_vnd*100)/100;

    $('.rd-shop-undefined .rd-sl span').html(total_quantity);
    $('.rd-shop-undefined .rd-rmb span').html(total);
    $('.rd-shop-undefined .rd-vnd span').html(numberFormat(total_vnd));
}

function updateDepositByWallet(){
    var n = $('#deposit .deposit-by-pay').find('input').val();
    if(isNaN(n)){
       return;
    }
    if(parseFloat(balanceWallet) > parseFloat(totalDeposit) && wallet_selected == true && pay_selected == true){
        if(n == ''){n = 0;}
        var m = parseFloat(totalDeposit) - parseFloat(n);
        if(m < 0){
            $('#deposit .deposit-by-wallet').find('input').val(0);
        }else{
            $('#deposit .deposit-by-wallet').find('input').val(m);
        }
    }
}

function updatePaymentByWallet(){
    var n = $('#payment .pay-by-pay').find('input').val();
    if(isNaN(n)){
        return;
    }
    var totalAmountNeedPayment = $('#paymentForm').find('input[name="total_amount_need_payment"]').val()
    if(parseFloat(balanceWallet) > parseFloat(totalAmountNeedPayment) && pay_by_wallet_selected == true && pay_by_pay_selected == true){
        if(n == ''){n = 0;}
        var m = parseFloat(totalAmountNeedPayment) - parseFloat(n);
        if(m < 0){
            $('#payment .pay-by-wallet').find('input').val(0);
        }else{
            $('#payment .pay-by-wallet').find('input').val(m);
        }
    }
}

$('#deposit').on('hide.bs.modal',function () {
    wallet_selected = false;
    pay_selected = false;
    $('#depositForm')[0].reset();
    $(this).find('.msg').html('');
    $(this).find(".is-invalid").removeClass("is-invalid"); // reset style
    $(this).find(".invalid-feedback strong").html("");
    $('.deposit-by-wallet').hide();
    $('.deposit-by-pay').hide();
});

$('#payment').on('hide.bs.modal',function () {
    pay_by_wallet_selected = false;
    pay_bay_pay_selected = false;
    $('#paymentForm')[0].reset();
    $(this).find('.msg').html('');
    $(this).find(".is-invalid").removeClass("is-invalid"); // reset style
    $(this).find(".invalid-feedback strong").html("");
    $('.pay-by-wallet').hide();
    $('.pay-by-pay').hide();
});


