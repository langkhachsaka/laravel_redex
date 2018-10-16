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
import TransactionForm from "../transaction/components/Form";
import TransactionConstant from "../transaction/meta/constant";
import moment from "moment";
import formatMoney from "../../../js/helpers/Formatter";
import {toastr} from "react-redux-toastr";

class TransactionDetail extends Component {

    constructor(props) {
        super(props);

        this.state = {
            model: {
                transactions: [],
            },
            isLoading: true,
            canAccess: props.userPermissions.transaction && props.userPermissions.transaction.view,
        };
        this.handleClick = this.handleClick.bind(this);
    }

    componentDidMount() {
        this.fetchModel(this.getModelId());

        this.props.actions.changeThemeTitle("Chi tiết giao dịch đơn hàng");
    }

    componentWillReceiveProps(nextProps) {
        const currentModelId = this.getModelId();
        const nextModelId = nextProps.match.params.id;

        if (currentModelId !== nextModelId) {
            this.fetchModel(nextModelId);
        }
    }

    fetchModel(id) {
        this.setState({isLoading: true});
        ApiService.get(Constant.resourcePath(id)).then((response) => {
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

    getModelId() {
        return this.props.match.params.id;
    }

    handleClick(e){
        const id = this.props.match.params.id;
        return ApiService.get(Constant.depositConfirmPath(id))
            .then((response) => {
                const {data} = response;
                this.setState({
                    model: data.data
                });
                toastr.success(data.message);
            });
    }

    render() {
        const {userPermissions} = this.props;
        if (!this.state.canAccess) return <ForbiddenPage/>;

        const {model} = this.state;

        let sumMoney = 0;

        const transactionItems = model.transactions.map(transaction => {
            sumMoney += parseFloat(transaction.money + '');

            return (
                <Card key={transaction.id}>
                    <div className="row">
                        <div className="col">
                            <b>Giao dịch {transaction.type_name} - Ngày {moment(transaction.created_at).format("DD/MM/YYYY HH:mm")}</b>
                            <hr/>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col">
                            <b>Người tạo:</b> {transaction.user_name}<br/>
                            <b>Số tiền:</b> {formatMoney.money(transaction.money)} <br/>
                            <b>Thời gian tạo:</b> {moment(transaction.created_at).format("DD/MM/YYYY HH:mm")}<br/>
                            <b>Nội dung:</b> {transaction.note} <br/>
                        </div>
                    </div>
                    {transaction.type == 0 && transaction.status == 0 && <button type="button" className="btn btn-lg btn-success" style={{marginTop:"10px",marginLeft:"5px"}} onClick={this.handleClick}>Xác nhận đặt cọc</button>}
                </Card>
            )
        });

        let typeOrder = '';

        if (this.state.model.transactions.length !== 0) {
            switch(this.state.model.transactions[0].transactiontable_type) {
                case TransactionConstant.MORPH_TYPE_ORDER_VN :
                    typeOrder = 'Giao dịch đơn hàng Việt Nam';
                    break;
                case TransactionConstant.MORPH_TYPE_BILL_OF_LADING :
                    typeOrder = 'Giao dịch đơn hàng vận chuyển';
                    break;
                default:
                    typeOrder = 'Giao dịch';
            }
        }

        let classOrder = '';

        if (this.state.model.transactions.length !== 0) {
            switch(this.state.model.transactions[0].transactiontable_type) {
                case TransactionConstant.MORPH_TYPE_ORDER_VN :
                    classOrder = TransactionConstant.MORPH_TYPE_BILL_OF_LADING;
                    break;
                case TransactionConstant.MORPH_TYPE_BILL_OF_LADING :
                    classOrder = TransactionConstant.MORPH_TYPE_BILL_OF_LADING;
                    break;
                default:
                    classOrder = '';
            }
        }

        return (
            <Layout>
                <Card isLoading={this.state.isLoading}>
                    <div className="mb-1">
                        <div className="row">
                            <div className="col">
                                <div className="row">
                                    <div className="col-10">
                                        <h4 className="text-center"><b>{typeOrder} #{model.id}</b></h4>
                                    </div>
                                    <div className="col-2">
                                        <button type="button" className="btn btn-danger btn-block btn-sm" onClick={() => {
                                            this.props.actions.openMainModal(<TransactionForm initValues={{
                                                transactiontable_id: model.id,
                                                transactiontable_type: this.state.model.transactions[0].transactiontable_type,
                                                customer_id: model.customer_id,
                                            }}/>, "Tạo giao dịch mới");
                                        }}>
                                            <i className="ft-anchor"/> Thêm giao dịch
                                        </button>
                                    </div>
                                </div>
                                <hr/>
                                <div className="row">
                                    <div className="col-6">
                                        <h5>Thông tin người mua</h5>
                                        <b>Tên:</b> {model.customer_billing_name}<br/>
                                        <b>Điện thoại:</b> {model.customer_billing_phone}<br/>
                                        <b>Địa chỉ:</b> {model.customer_billing_address}<br/>
                                        <b>Email:</b> {_.get(model, 'customer.email')}
                                    </div>
                                    <div className="col-6">
                                        <h5>Thông tin người nhận</h5>
                                        <b>Tên:</b> {model.customer_shipping_name}<br/>
                                        <b>Điện thoại:</b> {model.customer_shipping_phone}<br/>
                                        <b>Địa chỉ:</b> {model.customer_shipping_address}
                                    </div>
                                </div>
                                <hr/>
                                <div className="row">
                                    <div className="col"><b>Tổng tiền đã giao dịch:</b> {formatMoney.money(sumMoney)}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </Card>
                {transactionItems}
            </Layout>
        );
    }
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

export default connect(mapStateToProps, mapDispatchToProps)(TransactionDetail)
