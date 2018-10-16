@extends('layouts.app')

@section('title-name')
    - Ví tiền
@endsection

@section('content')
    <div class="container wallet">
        <div class="card-header" style="float: unset">
            <h3>Quản lý ví điện tử</h3>
            <ul style="float:right;margin: -40px 10px 0 0;">
                <li style="display:inline-block">
                    <a href="{{route('wallet.recharge')}}" class="btn btn-default" style="width: 100px;background: #4482E6;text-transform: uppercase;color: #fff;font-weight: 700;">
                        Nạp tiền
                    </a>
                </li>
                <li style="display:inline-block">
                    <button id="withdrawal-btn" class="btn btn-default" data-toggle="modal" data-target="#withdrawal" {{$wallet == 0 ? 'disabled': ''}} style="width: 100px;background: red;text-transform: uppercase;color: #fff;font-weight: 700;">
                        Rút tiền
                    </button>
                </li>
            </ul>
        </div>
        <div class="content">
            <div class="tab-content" style="padding: 30px">
                <div class="statistical">
                    <ul>
                        <li>
                            <div class="statistical-tit">Tổng nạp: </div>
                            <span style="color: #00A759;font-size: 16px;font-weight: 700">{{number_format($total_recharge)}} VNĐ</span>
                        </li>
                        <li>
                            <div class="statistical-tit">Tổng rút: </div>
                            <span id="total-withdrawals" style="color: red;font-size: 16px;font-weight: 700" data-withdrawals="{{$total_withdrawal}}">{{number_format($total_withdrawal)}}</span><span style="color: red;font-size: 16px;font-weight: 700">VNĐ</span>
                        </li>
                        <li>
                            <div class="statistical-tit">Tổng thanh toán: </div>
                            <span style="color: red;font-size: 16px;font-weight: 700">0 VNĐ</span>
                        </li>
                        <li>
                            <div class="statistical-tit">Tổng hoàn tiền: </div>
                            <span style="color: #00A759;font-size: 16px;font-weight: 700">{{number_format($total_refund)}} VNĐ</span>
                        </li>
                        <li>
                            <div class="statistical-tit">Số dư khả dụng: </div>
                            <span class="balance" style="color: #00A759;font-size: 16px;font-weight: 700">{{number_format($wallet)}}</span><span style="color: #00A759;font-size: 16px;font-weight: 700"> VNĐ</span>
                        </li>
                    </ul>
                </div>
                <div class="history">
                    <table id="table-transaction-history" class="table table-responsive-lg table-bordered">
                        <thead style="background-color: red;color:#FFFFFF">
                            <tr>
                                <th>ID</th>
                                <th>Thời gian</th>
                                <th width="30%">Loại giao dịch</th>
                                <th width="30%">Số tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($transactions as $transaction)
                            <tr>
                                <td>{{$transaction->id}}</td>
                                <td>
                                    {{\Carbon\Carbon::parse($transaction->created_at)->format("d-m-Y")}}
                                </td>
                                <td>
                                    {{$transaction->type_name}}
                                </td>
                                <td>
                                    {{number_format($transaction->money)}} VND
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>

        {{--Modal withdrawal--}}
        <div id="withdrawal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form action="{{route('withdrawal.create')}}" method="post" enctype="multipart/form-data" id="form-create-withdrawal-request">
                        @csrf
                        <div class="modal-header">
                            <h2 class="modal-title" style="font-weight: 700;font-size: 16px;text-transform: uppercase;">Yêu cầu rút tiền</h2>
                            <button type="button" class="close" data-dismiss="modal" style="margin-top: -10px !important;">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group" style="padding:0 20px">
                                <div>
                                    <span style="font-weight: 700;font-size: 16px;text-transform: uppercase;">Số dư khả dụng: </span>
                                    <span class="balance" data-balance="{{$wallet}}" style="color: red;font-size: 16px;font-weight: 700">{{number_format($wallet)}}</span> <span style="color: red;font-size: 16px;font-weight: 700">VNĐ</span>
                                </div>
                            </div>
                            <div class="form-group" style="width: 50%;padding:0 20px;float: left;">
                                <div>
                                    <b>Nhập số tiền cần rút <span style="color: red">*</span></b>
                                </div>
                                <div>
                                    <input type="text" class="form-control" name="money_withdrawal"/>
                                    <span class="invalid-feedback" style="text-align: left"><strong></strong></span>
                                </div>
                            </div>
                            <div class="form-group" style="width: 50%;padding:0 20px;float: left;">
                                <div>
                                    <b>Nhập số tài khoản <span style="color: red">*</span></b>
                                </div>
                                <div>
                                    <input type="text" class="form-control" name="account_number"/>
                                    <span class="invalid-feedback" style="text-align: left"><strong></strong></span>
                                </div>
                            </div>
                            <div class="form-group" style="width: 50%;padding:0 20px;float: left;">
                                <div>
                                    <b>Họ tên chủ tài khoản <span style="color: red">*</span></b>
                                </div>
                                <div>
                                    <input type="text" class="form-control" name="name"/>
                                    <span class="invalid-feedback" style="text-align: left"><strong></strong></span>
                                </div>
                            </div>
                            <div class="form-group" style="width: 50%;padding:0 20px;float: left;">
                                <div>
                                    <b>Ngân hàng <span style="color: red">*</span></b>
                                </div>
                                <div>
                                    <input type="text" class="form-control" name="bank"/>
                                    <span class="invalid-feedback" style="text-align: left"><strong></strong></span>
                                </div>
                            </div>
                            <div class="form-group" style="width: 50%;padding:0 20px;float: left;">
                                <div>
                                    <b>Chi nhánh ngân hàng <span style="color: red">*</span></b>
                                </div>
                                <div>
                                    <input type="text" class="form-control" name="branch"/>
                                    <span class="invalid-feedback" style="text-align: left"><strong></strong></span>
                                </div>
                            </div>
                            <div class="form-group" style="width: 50%;padding:0 20px;float: left;">
                                <div>
                                    <b>Nội dung <span style="color: red">*</span></b>
                                </div>
                                <div>
                                    <input type="text" class="form-control" name="content"/>
                                    <span class="invalid-feedback" style="text-align: left"><strong></strong></span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="justify-content: unset;margin-left: 20px">
                            <button type="submit" class="btn btn-primary">Gửi yêu cầu rút tiền</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{asset('build/js/customer/wallet.js')}}"></script>
@endsection