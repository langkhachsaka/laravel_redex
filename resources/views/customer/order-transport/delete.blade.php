<div id="confirm-delete-order-{{$order['id']}}" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div style="text-align: center">
                    <h2>Xóa đơn hàng</h2>
                    <p>Bạn có chắc chắn muốn xóa đơn hàng này không?</p>
                </div>
                <div style="float: right">
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="border-color: #ccc;">Cancel</button>
                    <a href="{{route('order-transport.delete', $order['id'])}}" class="btn btn-danger">OK</a>
                </div>
            </div>
        </div>
    </div>
</div>