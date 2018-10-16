@extends('layouts.app')

@section('title-name')
    - Danh sách khiếu nại
@endsection

@section('content')

    <div class="content-header">
        <div class="card-header">
            <h3 style="float: left">Khiếu nại</h3>
            <div style="float:right;display: block;width: 480px">
                <form method="get" enctype="multipart/form-data">
                    <div class="form-group" style="width: 30%;float:left;margin-right: 10px">
                        <input class="form-control" name="created_at" placeholder="Ngày tạo" value="{{ app('request')->input('created_at') }}" style="height: 37px"/>
                    </div>
                    <div class="form-group" style="width: 30%;float:left;margin-right: 10px">
                        <select class="form-control" name="status">
                            <option value="" selected>Trạng thái</option>
                            @foreach($status as $key=>$item)
                                @if(app('request')->input('status') === (string)$key)
                                    <option value="{{$key}}" selected>{{$item}}</option>
                                @else
                                    <option value="{{$key}}">{{$item}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn" style="color: black;margin-right: 5px">Tìm kiếm</button>
                    <a href="{{route('complaint.index')}}" class="btn btn-primary" style="float: right">Reset</a>
                </form>
            </div>
        </div>
    </div>
    <div class="content-body">
        <table id="table-list-complaint" class="table table-hover" style="margin:50px auto">
            <thead>
            <tr style="text-align: center">
                <th><span>Mã đơn hàng</span></th>
                <th><span>Kiểu</span></th>
                <th style="text-align: center"><span>Tiêu đề</span></th>
                <th><span>Trạng thái</span></th>
                <th style="text-align: center"><span>Ngày tạo</span></th>
                <th></th>
            </tr>
            </thead>
            <tbody style="text-align: center">
            @foreach($complaints as $complaint)
                <tr>
                    <td>
                        <a href="{{route('complaint.view',$complaint['id'])}}">{{ $complaint['ordertable_id'] }}</a>
                    </td>
                    <td>{{$complaint->order_type}}</td>
                    <td>
                        {{ $complaint['title'] }}
                    </td>
                    <td>
                        {{ $complaint['status_name'] }}
                    </td>
                    <td>
                        {{\Carbon\Carbon::parse($complaint['created_at'])->format("d-m-Y")}}
                    </td>
                    <td>
                        <a href="{{route('complaint.view',$complaint['id'])}}" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{$complaints->links()}}
    </div>
@endsection
@section('scripts')
    <script>
        $('#table-list-complaint').DataTable({
            paging:false,
            searching: false,
            bInfo: false,
            language: {
                "emptyTable": "Không có bản ghi nào"
            },
            columnDefs: [
                {
                    'targets': [1,2,3,5],
                    'orderable': false
                }
            ],
        });
        var option = {
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: false,
            "locale": {
                "format": "DD-MM-YYYY"
            }
        };
        $('input[name="created_at"]').daterangepicker(option).on('apply.daterangepicker', function(e, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

    </script>
@endsection
