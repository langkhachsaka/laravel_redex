<div id="complaint" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Khiếu nại</h4>
                <button type="button" class="close" data-dismiss="modal" style="margin-top: -10px !important;">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form-complaint-create" action="{{route('complaint.create')}}" method="post" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <input type="hidden" name="ordertable_id" value="{{$order['id']}}"/>
                    <input type="hidden" name="ordertable_type" value="order"/>
                    <div class="form-group">
                        <div><b>Tiêu đề </b><span style="color: red">*</span></div>
                        <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" name="title" type="text"/>
                        @if ($errors->has('title'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('title') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <div><b>Nội dung </b><span style="color: red">*</span></div>
                        <textarea class="form-control {{ $errors->has('content') ? 'is-invalid' : '' }}" name="content" rows="4"></textarea>
                        @if ($errors->has('content'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('content') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <div><b>Ngày mong muốn </b><span style="color: red">*</span></div>
                        <input type="text" class="form-control {{ $errors->has('date_end_expected') ? 'is-invalid' : '' }}" name="date_end_expected" />
                        @if ($errors->has('date_end_expected'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('date_end_expected') }}</strong>
                            </span>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary">Gửi khiếu nại</button>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
    @parent
    <script>
        @if($errors->any())
            $("#complaint").modal("show");
        @endif
        var option = {
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: false,
            "locale": {
                "format": "DD-MM-YYYY"
            }
        };

        $('input[name="date_end_expected"]').daterangepicker(option).on('apply.daterangepicker', function(e, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        })
        $('#complaint').on('hidden.bs.modal',function () {
            $('#form-complaint-create')[0].reset();
            $(this).find(".is-invalid").removeClass("is-invalid");
            $(this).find(".invalid-feedback strong").html("");
        });
    </script>
@endsection