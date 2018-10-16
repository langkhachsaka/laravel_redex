@extends('layouts.app')

@section('title-name')
    - Chi tiết khiếu nại
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="card-header">
                <h3>Chi tiết khiếu nại</h3>
            </div>
        </div>
    </div>
    <div class="content-body">
        <div class="form-group">
            <div><b>Mã đơn hàng</b></div>
            <input class="form-control" type="text" name="ordertable_id" value="{{$complaint['ordertable_id']}}"/>
        </div>
        <div class="form-group">
            <div><b>Kiểu</b></div>
            <input class="form-control" type="text" name="order_type" value="{{$complaint->order_type}}"/>
        </div>
        <div class="form-group">
            <div><b>Trạng thái</b></div>
            <input class="form-control" type="text" name="status_name" value="{{$complaint->status_name}}"/>
        </div>
        <div class="form-group">
            <div><b>Tiêu đề</b></div>
            <input class="form-control" type="text" name="title" value="{{$complaint['title']}}"/>
        </div>
        <div class="form-group">
            <div><b>Nội dung</b></div>
            <textarea class="form-control" name="content">{{$complaint['content']}}</textarea>
        </div>
        <div class="form-group">
            <div><b>Ngày mong muốn</b></div>
            <input class="form-control" type="text" name="date_end_expected" value="{{\Carbon\Carbon::parse($complaint['date_end_expected'])->format("d-m-Y")}}"/>
        </div>
        <div class="form-group">
            <div><b>Phương án xử lý</b></div>
            <input class="form-control" type="text" value="{{$complaint['solution_name']}}"/>
        </div>
        <div class="form-group">
            <div><b>Tệp đính kèm</b></div>
            <div class="drop-zone" style="border:1px dashed; padding: 10px 20px; cursor: pointer; height: 45px">
                @if(isset($complaint['file_report_name']))
                    <span class="file-name"><i class="fa fa-file-text"></i> {{$complaint->file_report_name}}</span>
                    <a href="{{ $complaint['link_download'] }}"><i class="fa fa-download">Tải xuống</i></a>
                @endif
            </div>
        </div>
    </div>
    <div style="float: right">
        <a href="{{route('complaint.index')}}" class="btn btn-primary">Danh sách khiếu nại</a>
    </div>
@endsection
@section('scripts')
    <script>
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
    </script>
@endsection
