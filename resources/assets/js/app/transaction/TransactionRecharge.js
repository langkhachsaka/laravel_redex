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
import Formatter from "../../helpers/Formatter";

class TransactionRecharge extends Component {

    constructor(props) {
        super(props);

        this.state = {
            model: {},
            isLoading: true,
        };
    }

    componentDidMount() {
        this.fetchModel(this.getModelId());

        this.props.actions.changeThemeTitle("Chi tiết giao dịch");
    }

    fetchModel(id) {
        ApiService.get(Constant.rechareDetailPath(id)).then((response) => {
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

        const {model} = this.state;

        return (
            <Layout>
                <Card isLoading={this.state.isLoading}>
                    <div className="mb-1">
                        <div className="row">
                            <div className="col">
                                <div className="row">
                                    <div className="col-10">
                                        <h4 className="text-center"><b>Giao dịch nạp tiền</b></h4>
                                    </div>
                                </div>
                                <hr/>
                                <div className="row">
                                    <div className="col-6">
                                        <h5>Thông tin khách hàng</h5>
                                        <b>Tên: </b>{model && model.customer && model.customer.name}<br/>
                                        <b>Tên tài khoản: </b>{model && model.customer && model.customer.username}<br/>
                                        <b>Email: </b>{model && model.customer && model.customer.email}
                                    </div>
                                </div>
                                <hr/>
                                <div className="row">
                                    <div className="col-6">
                                        <b>Số tiền: </b>{model && formatMoney.money(model.money)}<br/>
                                        <b>Thời gian tạo: </b>{model && moment(model.created_at).format("DD/MM/YYYY HH:mm")}<br/>
                                        <b>Nội dung: </b>{model && model.type_name}<br/>
                                    </div>
                                </div>
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

export default connect(mapStateToProps, mapDispatchToProps)(TransactionRecharge)
