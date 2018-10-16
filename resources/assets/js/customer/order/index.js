var table;

var selected_toggled = false;

var input;

function initTable(){
    table = $('#order-grid').DataTable({
        paging:false,
        searching: false,
        bInfo: false,
        language: {
            "emptyTable": "Không có order nào"
        },
        columnDefs: [
            {
                'targets': [5, 8],
                'orderable': false
            },
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
    $('.delete-orders').click(function (e) {
        e.preventDefault();
    });

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

$('#orderDeleteForm').submit(function (event) {
    event.preventDefault();

    var selected = getSelectedItems(),
        exportable = [];

    $.each(selected, function (key, element) {
        if($(element).data('id') !== undefined){
            exportable.push($(element).data('id'));
        }
    });

    $('input:hidden[name=orders]').val(JSON.stringify(exportable));

    this.submit();

});

$('body').on('click','.message',function (e) {
    e.preventDefault();
    $('.message').remove();
});

