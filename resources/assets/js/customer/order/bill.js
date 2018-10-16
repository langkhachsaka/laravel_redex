var table;
var selected_toggled = false;

$(document).ready(function () {
    initTable();
});

function initTable() {
    table = $('#lading-code-table').DataTable({
        paging:false,
        searching: false,
        bInfo: false,
        ordering: false,
        language: {
            "emptyTable": "Không có kiện hàng nào"
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
        }
    });
    $('#toggleSelected').click(toggleAllSelected);
}

function toggleAllSelected() {
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

$('#createBillForm').submit(function (event) {

    var selected = getSelectedItems(),
        exportable = [];

    $.each(selected, function (key, element) {
        if($(element).data('id') !== undefined){
            exportable.push({
                    id: $(element).data('id'),
                    code: $(element).data('code'),
                    order_id: $(element).data('order-id')
                });
        }
    });
    $('input:hidden[name=ladingCodes]').val(JSON.stringify(exportable));

    $(this).submit();
});

$('body').on('click','.message',function (e) {
    e.preventDefault();
    $('.message').remove();
});