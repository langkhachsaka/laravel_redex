var table;

var selected_toggled = false;

var wallet_selected = false;

var pay_selected = false;

var input;

$('#walletSelected').click(function () {
    if(!wallet_selected){
        $('.deposit-by-wallet').show();
        if(parseFloat(input.balanceWallet) <= parseFloat(input.totalDeposit)){
            $('#deposit .deposit-by-wallet').find('input').val(parseFloat(input.balanceWallet));
        }else{
            $('#deposit .deposit-by-wallet').find('input').val(parseFloat(input.totalDeposit));
        }
        if(pay_selected){
            $('#deposit .deposit-by-pay').find('input').val(parseFloat(input.totalDeposit) - parseFloat($('#deposit .deposit-by-wallet').find('input').val()));
        }
    } else {
        $('#deposit .deposit-by-wallet').find('input').val(0);
        if(pay_selected) {
            $('#deposit .deposit-by-pay').find('input').val(parseInt(input.totalDeposit));
        }
        $('.deposit-by-wallet').hide();
    }
    wallet_selected = !wallet_selected;
});

$('#paySelected').click(function () {
    if(!pay_selected){
        $('.deposit-by-pay').show();
        $('#deposit .deposit-by-pay').find('input').val(parseFloat(input.totalDeposit) - parseFloat($('#deposit .deposit-by-wallet').find('input').val()));
    } else {
        $('.deposit-by-pay').hide();
        $('#deposit .deposit-by-pay').find('input').val(0);
    }
    pay_selected = !pay_selected;
});

function initTableDeposit(){
    table = $('#order-deposit-table').DataTable({
        paging:false,
        searching: false,
        bInfo: false,
        ordering: false,
        language: {
            "emptyTable": "Không có order nào"
        },
        columnDefs: [
            {
                'targets':   0,
                'orderable': false,
                'checkboxes': true

            },
        ],

        select: {
            style: 'multi',
        },

    });
    $('#toggleSelected').click(toggleAllSelected);
}

function toggleAllSelected(){
    table.rows({search: 'applied'}).every(function (rowIdx, tableLoop, rowLoop) {
        if(!selected_toggled){
            // select them
            table.row(rowIdx).select();
            $(this.node()).find(".selectorRow input").prop('checked', true);
        } else {
            // select them
            table.row(rowIdx).deselect();
            $(this.node()).find(".selectorRow input").prop('checked', false);
        }
    });

    selected_toggled = !selected_toggled;
}

/**
 * Get selected rows
 * @returns {*}
 */
function getSelectedItems() {
    return table.rows({selected:true}).nodes();
}

function numberFormat(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

$('#orderDepositForm').click(function (event) {
    event.preventDefault();
    $('#deposit').find('.msg').html('');

    var form = $(this);

    var selected = getSelectedItems(),
        exportable = [];

    $.each(selected, function (key, element) {
        if($(element).data('id') !== undefined){
            exportable.push($(element).data('id'));
        }
    });
    $('input:hidden[name=orders]').val(JSON.stringify(exportable));

    $.post(form.attr("action"), form.serialize(), function (res) {
        if(res.status == 'invalid'){
            var message = '<div class="alert alert-danger message">'+res.message+'<a href="#" style="position: absolute; padding: 0px 10px; top:10px;right:5px;font-size: 15px;color: black;">x</a></div>';
            $('.order-menu-tab').before(message);
            return;
        }
        for(var attr in res.data){
            $('#depositForm').append('<input type="hidden" name="order['+attr+']" value="'+res.data[attr]+'"/>');
        }
        input = res;
        $('#depositForm')[0].reset();
        $('#deposit .deposit-number').html(numberFormat(res.totalDeposit) + ' VNĐ');
        $('#deposit .deposit-by-wallet .balance').html(numberFormat(parseInt(res.balanceWallet))).data('balance',res.balanceWallet);
        $('#depositForm').append('<input type=hidden name="total_deposit" value="'+res.totalDeposit+'"/>');
        $('.alert').remove();
        $('#deposit').modal('show');
    });
});


$('body').on('click','.message',function (e) {
    e.preventDefault();
    $('.message').remove();
});

$('#depositForm').submit(function (event) {
    event.preventDefault();
    if(wallet_selected == false && pay_selected == false){
        $(this).find('.msg').html('Phải chọn 1 trong 2 phương thức thanh toán');
        return;
    }
    var form = $(this);
    $.post(form.attr('action'), form.serialize(), function (res) {
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
                $('.order-menu-tab').before(message);
                break;
            case 'error':
                var message = '<div class="alert alert-danger message">'+res.message+'<a href="#" style="position: absolute; padding: 0px 10px; top:10px;right:5px;font-size: 15px;color: black;">x</a></div>';
                $('.order-menu-tab').before(message);
                break;
        }

        $('.deposit-by-wallet').hide();
        $('.deposit-by-pay').hide();
        if(res.orderId.length > 0){
            for(var i = 0; i < res.orderId.length; i++){
                $('#order-deposit-table').find('tr.item-row-'+res.orderId[i]).remove();
            }
        }
        $('#deposit').modal('hide');
    });
});

function updateDepositByWallet(){
    var n = $('#deposit .deposit-by-pay').find('input').val();
    if(isNaN(n)){
        return;
    }
    if(input.balanceWallet > input.totalDeposit && wallet_selected == true && pay_selected == true){
        if(n == ''){n = 0;}
        var m = parseFloat(input.totalDeposit) - parseFloat(n);
        if(m < 0){
            $('#deposit .deposit-by-wallet').find('input').val(0);
        }else{
            $('#deposit .deposit-by-wallet').find('input').val(m);
        }
    }
}

$('#deposit').on('hide.bs.modal',function () {
    wallet_selected = false;
    pay_selected = false;
    $(this).find(".is-invalid").removeClass("is-invalid"); // reset style
    $(this).find(".invalid-feedback strong").html("");
    $('.deposit-by-wallet').hide();
    $('.deposit-by-pay').hide();
});
