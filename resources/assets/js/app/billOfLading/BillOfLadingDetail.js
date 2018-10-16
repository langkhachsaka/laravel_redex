import React, {Component} from 'react';
import {connect} from "react-redux";
import _ from 'lodash';
import {bindActionCreators} from "redux";

import ApiService from "../../services/ApiService";
import * as themeActions from "../../theme/meta/action";
import * as commonActions from "../common/meta/action";
import Card from "../../theme/components/Card";
import Layout from "../../theme/components/Layout";
import moment from "moment/moment";
import swal from "sweetalert";
import Constant from "./meta/constant";
import ForbiddenPage from "../common/ForbiddenPage";
import ComplaintForm from "../complaint/components/Form";
import ComplaintConstant from "../complaint/meta/constant";
import Form from "./components/Form";
import TransactionForm from "../transaction/components/Form";
import TransactionConstant from "../transaction/meta/constant";


class BillOfLadingDetail extends Component {

    constructor(props) {
        super(props);

        this.state = {
            model: {},
            isLoading: true,
            canAccess: props.userPermissions.bill_of_lading && props.userPermissions.bill_of_lading.view,
        };
    }

    componentDidMount() {
        this.fetchModel(this.getModelId());

        this.props.actions.changeThemeTitle("Chi tiết Đơn hàng");
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

    render() {
        const {userPermissions} = this.props;
        if (!this.state.canAccess) return <ForbiddenPage/>;

        const {model} = this.state;

        return (
            <Layout>

                <Card isLoading={this.state.isLoading}>
                    <div className="mb-1">
                        <div className="row">
                            <div className="col-sm-10">
                                <div className="row">
                                    <div className="col-sm-6">
                                        <h5>Thông tin người mua</h5>
                                        <b>Tên:</b> {model.customer_billing_name}<br/>
                                        <b>Điện thoại:</b> {model.customer_billing_phone}<br/>
                                        <b>Địa chỉ:</b> {model.customer_billing_address}<br/>
                                        <b>Email:</b> {_.get(model, 'customer.email')}
                                    </div>
                                    <div className="col-sm-6">
                                        <h5>Thông tin người nhận</h5>
                                        <b>Tên:</b> {model.customer_shipping_name}<br/>
                                        <b>Điện thoại:</b> {model.customer_shipping_phone}<br/>
                                        <b>Địa chỉ:</b> {model.customer_shipping_address}
                                    </div>
                                </div>
                                <hr/>
                                <div className="row">
                                    <div className="col-sm-6">
                                        <b>Mã đơn:</b> {model.id}<br/>
                                        <b>NV CSKH:</b> {_.get(model, 'seller.name')}<br/>
                                        <b>Ngày tạo:</b> {moment(model.created_at).format("DD/MM/YYYY HH:mm")}<br/>
                                        <b>Ngày kết thúc:</b> {!!model.end_date && moment(model.end_date).format("DD/MM/YYYY")}<br/>
                                        <b>Trạng thái:</b> {model.status_name}
                                    </div>
                                    <div className="col-sm-6">
                                        <b>Mã vận đơn: </b> {model.bill_of_lading_code}<br/>
                                        <b>Tệp đính kèm: </b> <i className="ft-file-text"/> {model.file_name}<br/>
                                        <a href={model.link_download_file} className="mr-2 d-inline-block">
                                            <i className="ft-download"/> Tải xuống
                                        </a>
                                        <a href={model.link_view_file_online} target="_blank" className="d-inline-block">
                                            <i className="ft-eye"/> Xem Online
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div className="col-sm-2">
                                {userPermissions.bill_of_lading.update &&
                                <button type="button" className="btn btn-info btn-block btn-sm" onClick={() => {
                                    this.props.actions.openMainModal(<Form model={model}
                                                                                 setDetailState={this.setState.bind(this)}/>, "Sửa thông tin Đơn hàng");
                                }}>
                                    <i className="ft-edit"/> Sửa
                                </button>}

                                {userPermissions.bill_of_lading.approve && !model.is_approved &&
                                <button type="button" className="btn btn-success btn-block btn-sm" onClick={() => {
                                    swal({
                                        title: "Duyệt Đơn hàng",
                                        text: "Bạn có chắc chắn muốn chuyển trạng thái đã duyệt cho Đơn hàng này?",
                                        icon: "warning",
                                        buttons: true,
                                        dangerMode: true,
                                    })
                                        .then((willApprove) => {
                                            if (willApprove) {
                                                ApiService.post(Constant.resourcePath(model.id + "/approve"))
                                                    .then(({data}) => {
                                                        this.setState({model: data.data});
                                                        swal(data.message, {icon: "info"});
                                                    });
                                            }
                                        });
                                }}>
                                    <i className="ft-check"/> Duyệt
                                </button>}

                                {/*{userPermissions.complaint.create && model.can_create_complaint &&
                                <button type="button" className="btn btn-warning btn-block btn-sm" onClick={() => {
                                    this.props.actions.openMainModal(<ComplaintForm initValues={{
                                        customer: model.customer,
                                        customer_id: model.customer.id,
                                        ordertable_id: model.id,
                                        ordertable_type: ComplaintConstant.MORPH_TYPE_BILL_OF_LADING,
                                    }}/>, "Tạo khiếu nại mới");
                                }}>
                                    <i className="ft-alert-triangle"/> Khiếu nại
                                </button>}*/}

                                {/*{userPermissions.complaint.create &&
                                <button type="button" className="btn btn-danger btn-block btn-sm" onClick={() => {
                                    this.props.actions.openMainModal(<TransactionForm initValues={{
                                        transactiontable_id: model.id,
                                        transactiontable_type: TransactionConstant.MORPH_TYPE_BILL_OF_LADING,
                                    }}/>, "Tạo giao dịch mới");
                                }}>
                                    <i className="ft-anchor"/> Giao dịch
                                </button>}*/}
                            </div>
                        </div>
                    </div>
                </Card>

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

export default connect(mapStateToProps, mapDispatchToProps)(BillOfLadingDetail)
