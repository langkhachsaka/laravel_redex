import React, {Component} from 'react';
import {connect} from "react-redux";
import _ from 'lodash';
import {bindActionCreators} from "redux";

import ApiService from "../../services/ApiService";
import * as themeActions from "../../theme/meta/action";
import * as commonActions from "../common/meta/action";
import Card from "../../theme/components/Card";
import Layout from "../../theme/components/Layout";
import Constant from "./meta/constant";
import ForbiddenPage from "../common/ForbiddenPage";

import TransactionConstant from "../transaction/meta/constant";
import Formatter from "../../helpers/Formatter";
import Array from "../../helpers/Array";
import {toastr} from "react-redux-toastr";


class TransactionPaymentDetail extends Component {

    constructor(props) {
        super(props);
        this.state = {
            model: {},
            insertToPayment: [],
            arrId: [],
            isLoading: true,
        };
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleClick = this.handleClick.bind(this);
    }

    componentDidMount() {
        this.fetchModel(this.getModelId());
        this.props.actions.changeThemeTitle("Chi tiết thanh toán");
    }

    getModelId() {
        return this.props.match.params.id;
    }

    fetchModel(id) {
        this.setState({isLoading: true});
        ApiService.get(Constant.resourcePaymentDetailPath(id)).then((response) => {
            if (response.status === 403) {
                this.setState({canAccess: false});
                return;
            }

            const {data} = response;
            this.setState({
                model: data.data,
                isLoading: false,
            });
        });
    }

    handleSubmit(e){
        e.preventDefault();
        var data = this.state.insertToPayment;
        const id = this.props.match.params.id;
        return ApiService.post(Constant.resourcePaymentDetailPath(id), data)
            .then(({data}) => {
                this.setState({
                    model: data.data
                });
                toastr.success(data.message);
            });
    }

    handleClick(e){
        const id = this.props.match.params.id;
        return ApiService.get(Constant.paymentConfirmPath(id))
            .then(({data}) => {
                this.setState({
                    model: data.data
                });
                toastr.success(data.message);
            });
    }

    render() {
        const {handleSubmit} = this.props;
        const {model} = this.state;
        let addressItem;
        let orderItem;
        if(this.state.isLoading == false){
            addressItem = model.payment_info.map((info,index) =>{
                const data = JSON.parse(info.data);
                if(info.type == 0){
                    return <ListAddress
                                onInputChange={(data) => {
                                    var a = this.state.arrId;
                                    var arr = this.state.insertToPayment;
                                    if(arr.length > 0){
                                        if(Array.inArray(data.id,a)){
                                            for(var i = 0; i< arr.length;i++ ){
                                                if(data.id == arr[i].id){
                                                    arr[i].value = data.value;
                                                }
                                            }
                                        }else{
                                            arr.push(data);
                                            a.push(data.id);
                                        }

                                        this.setState({
                                            insertToPayment: arr,
                                            arrId: a
                                        })
                                    }else{
                                        this.state.insertToPayment.push(data);
                                        this.state.arrId.push(data.id);
                                        this.setState({
                                            arrId: this.state.arrId,
                                            insertToPayment: this.state.insertToPayment
                                        })
                                    }
                                }}
                                key={index}
                                value={data}
                                index={index}
                                pay_id={info.id}
                            />;
                }
            });

            orderItem = model.payment_info.map((info,index) =>{
                const data = JSON.parse(info.data);
                if(info.type == 2){
                    return <ListOrder key={index} value={data}/>;

                }
            });
        }
        return (
            <Layout>
                <Card isLoading={this.state.isLoading}>
                    <div>
                        <table className="table table-bordered" style={{background :"#FCAD52"}}>
                            <tbody>
                            <tr>
                                <td>Hotline: 0948241144</td>
                                <td>04.62922255</td>
                                <td>Email: RedEx.vn@gmail.com</td>
                                <td>Website: www.RedEx.vn</td>
                            </tr>
                            <tr>
                                <td colSpan="3">Address: Nhà 16 B11 Đầm Trấu, Hai Bà Trưng, HN</td>
                                <td>Yahoo: RedExpress</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div className="title">
                        <h1 style={{textAlign:"center",textTransform: "uppercase",fontWeight: "700",color:"red"}}>Đơn đặt hàng</h1>
                    </div>
                    <div className="cus-info">
                        <table className="table table-bordered" style={{background: "#FCAD52"}}>
                            <tbody>
                            <tr>
                                <td>Họ tên khách hàng: {model && model.customer && model.customer.name}</td>
                                <td>Điện thoại: </td>
                                <td>Email: {model && model.customer && model.customer.email}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <form onSubmit={this.handleSubmit}>
                    {addressItem}
                    <div className="total-order">
                        <div className="chiphi">
                            <div className="rd-left">
                            {orderItem}
                            <ul>
                                <li>
                                    <div>Tổng tiền cần thanh toán</div>
                                    <div className="rd-prime"><span>{model && Formatter.money(model.money)}</span> VNĐ</div>
                                </li>
                            </ul>
                            </div>
                        </div>
                    </div>
                        {model && model.status == 0 &&<button type="submit" className="btn btn-lg btn-primary" style={{float:"left",marginTop:"10px"}}>Cập nhật</button>}
                    </form>
                    {model && model.status == 0 && <button type="button" className="btn btn-lg btn-success" style={{marginTop:"10px",marginLeft:"5px"}} onClick={this.handleClick}>Xác nhận thanh toán</button>}
                </Card>
            </Layout>
        );
    }
}

class ListAddress extends Component {
    constructor(props){
        super(props);
        this.state = {
            item: this.props.value,
            index: this.props.index,
            collect_money: this.props.value.collect_money,
            shipping_fee: null,
            payment_id: this.props.pay_id,
            listShippingFee : {}
        };
        this.handleChange = this.handleChange.bind(this);
    }

    componentDidMount() {
        this.props.value.shipping_fee && this.setState({shipping_fee: this.props.value.shipping_fee});
    }

    handleChange(e){
        this.setState({collect_money: e.target.value});
    }

    render(){
        let tfootTitle;

        if (this.state.item.hasOwnProperty('shipping_fee_urban')) {
            tfootTitle = (
                <td colSpan="4">
                    Phí ship nội thành: <span style={{color: "red"}}>{this.state.item.shipping_fee_urban == 0 ? this.state.item.shipping_fee_urban : Formatter.money(this.state.item.shipping_fee_urban)}</span> VNĐ
                    <div>
                        Phụ phí: <span style={{color:"red"}}>{this.state.item.surcharge == 0 ? this.state.item.surcharge : Formatter.money(this.state.item.surcharge)}</span> VNĐ
                    </div>
                    <div>
                        Chiết khấu: <span style={{color:"red"}}>{this.state.item.discount == 0 ? this.state.item.discount : Formatter.money(this.state.item.discount)}</span> VNĐ
                    </div>
                </td>
            );
        }else{
            tfootTitle = (
                <td colSpan="4">
                    Phí chuyển phát:
                    <div style={{display: "inline",marginLeft: "15px"}}>
                        <select value={this.state.collect_money} onChange={this.handleChange}>
                            <option value="1">Redex thu tiền</option>
                            <option value="2">Công ty chuyển phát thu tiền</option>
                        </select>
                        <input
                            name="shipping_fee"
                            type="text"
                            value={this.state.shipping_fee ? this.state.shipping_fee : ''}
                            placeholder="Nhập số tiền"
                            style={{marginLeft:"5px"}}
                            onChange={e => {
                                var listShippingFee = this.state.listShippingFee;
                                listShippingFee.id = this.state.payment_id;
                                listShippingFee.value = e.target.value;

                                this.setState({
                                    shipping_fee: e.target.value,
                                    listShippingFee: listShippingFee
                                });

                                if(this.props.onInputChange){
                                    this.props.onInputChange(listShippingFee);
                                }
                            }}
                        />
                    </div>
                    <div>
                        Phụ phí: <span style={{color:"red"}}>{this.state.item.surcharge == 0 ? this.state.item.surcharge : Formatter.money(this.state.item.surcharge)}</span> VNĐ
                    </div>
                    <div>
                        Chiết khấu: <span style={{color:"red"}}>{this.state.item.discount == 0 ? this.state.item.discount : Formatter.money(this.state.item.discount)}</span> VNĐ
                    </div>
                </td>
            );
        }

        const itemLadingCode = this.state.item.code_id.map((code,index) => {
            return (
                <tr key={this.state.item.lading_code[index]}>
                    <td>{code}</td>
                    <td>{this.state.item.lading_code[index]}</td>
                    <td>{this.state.item.order_id[index]}</td>
                    <td>{this.state.item.quantity_verify[index]}</td>
                </tr>
            );
        });
        return (
            <div>
                <span style={{color: "red",fontWeight: "700",fontSize: "17px"}}>Địa chỉ nhận hàng {(this.state.index + 1)}:</span> {this.state.item.address}
                <table className="table table-bordered">
                    <thead>
                    <tr style={{background: "#ececec"}}>
                        <th>ID</th>
                        <th>Mã vận đơn</th>
                        <th>Mã đơn hàng</th>
                        <th>Số lượng</th>
                    </tr>
                    </thead>
                    <tbody>
                    {itemLadingCode}
                    <tr style={{background: "#ececec"}}>
                        {tfootTitle}
                    </tr>
                    </tbody>
                </table>
            </div>
        );
    }
}

function ListOrder(props) {
    const value = props.value;
    return (
        <ul>
            <li style={{fontSize: "22px",color: "red"}}>
                Mã đơn hàng: {value.id}
            </li>
            <li>
                <div>Phí ship TQ-VN</div>
                <div className="rd-prime"><span>{Formatter.money(value.transport_fee)}</span> VNĐ</div>
            </li>
            <li className="deposited">
                <div>Số tiền còn lại</div>
                <div className="rd-prime"><span>{Formatter.money(value.pay_amount)}</span> VNĐ</div>
            </li>
        </ul>
    );
}

function mapStateToProps({auth}) {
    return {
        userPermissions: auth.permissions,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(TransactionPaymentDetail)
