
$('#form-create-withdrawal-request').submit(function (e) {
    e.preventDefault();
    var form = $(this);
    $.post(form.attr("action"), form.serialize(), function (res){
        if(res.status == 'invalid'){
            for (var attr in res.errors) {
                form.find("[name='" + attr + "']").addClass("is-invalid").parent().find("strong").html(res.errors[attr]);
            }
            return;
        }

        //success
        $('body').find('.alert-success').remove();

        var n = parseInt(form.find('.balance').attr('data-balance'));
        n = n - parseInt(form.find("input[name='money_withdrawal']").val());
        $(".balance").html(numberFormat(n));
        form.find('.balance').attr('data-balance',n);
        if(n == 0){
            $('#withdrawal-btn').attr('disabled','disabled');
        }

        var totalWithdrawals = parseInt($('#total-withdrawals').attr('data-withdrawals'));
        totalWithdrawals = totalWithdrawals + parseInt(form.find("input[name='money_withdrawal']").val());
        $('#total-withdrawals').html(numberFormat(totalWithdrawals));
        $('#total-withdrawals').attr('data-withdrawals',totalWithdrawals);

        var message = '<div class="alert alert-success">'+res.message+'<a href="#" onclick="removeNoti(this)" style="position: absolute; padding: 0px 10px; top:10px;right:5px;font-size: 15px;color: black;">x</a></div>';
        $('.content').before(message);
        $('#withdrawal').modal('hide');
    })
});
$('#withdrawal').on('hide.bs.modal',function () {
    $('#form-create-withdrawal-request')[0].reset();
    $('#form-create-withdrawal-request').find(".is-invalid").removeClass("is-invalid");
    $('#form-create-withdrawal-request').find(".invalid-feedback strong").html("");
});

$('#withdrawal').on('show.bs.modal',function () {
   $('.alert-success').remove();
});

function numberFormat(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function removeNoti(input){
    $(input).parent().remove();
}