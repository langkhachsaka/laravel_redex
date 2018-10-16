import React, {Component} from 'react';
import {connect} from "react-redux";
import _ from 'lodash';
import {bindActionCreators} from "redux";

import ApiService from "../../services/ApiService";
import * as themeActions from "../../theme/meta/action";
import Constant from './meta/constant';
import Card from "../../theme/components/Card";
import Form from "./components/Form";
import Layout from "../../theme/components/Layout";
import ForbiddenPage from "../common/ForbiddenPage";


class Setting extends Component {

    constructor(props) {
        super(props);

        this.state = {
            model: {},
            isLoading: true
        };
    }

    componentDidMount() {
        this.props.actions.changeThemeTitle("Cấu hình hệ thống");

        ApiService.get(Constant.resourcePath()).then(({data}) => {
            this.setState({
                model: data.data,
                isLoading: false,
            });
        });
    }

    render() {
        const {userPermissions} = this.props;
        if (!userPermissions.setting || !userPermissions.setting.index) return <ForbiddenPage/>;

        const {model} = this.state;

        return (
            <Layout>

                <Card isLoading={this.state.isLoading}>

                    <div className="table-responsive">
                        <table className="table table-hover">
                            <thead>
                            <tr>
                                <th>Tên cấu hình</th>
                                <th>Giá trị</th>
                                <th style={{width: '132px'}}>
                                    <button key="btn-update" className="btn btn-sm btn-info square" onClick={() => {
                                        this.props.actions.openMainModal(
                                            <Form model={model} setListState={this.setState.bind(this)}/>,
                                            "Cập nhật cấu hình"
                                        );
                                    }}>
                                        <i className="ft-edit"/> Sửa nhanh
                                    </button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr>
                                <td>Sai số về khối lượng</td>
                                <td>{model.error_weight}</td>
                                <td className="column-actions">
                                    <button key="btn-update" className="btn btn-sm btn-info square" onClick={() => {
                                        this.props.actions.openMainModal(
                                            <Form model={model} settingName="error_weight" setListState={this.setState.bind(this)}/>,
                                            "Cập nhật cấu hình"
                                        );
                                    }}><i className="ft-edit"/></button>
                                </td>
                            </tr>

                            <tr>
                                <td>Sai số về kích  thước</td>
                                <td>{model.error_size}</td>
                                <td className="column-actions">
                                    <button key="btn-update" className="btn btn-sm btn-info square" onClick={() => {
                                        this.props.actions.openMainModal(
                                            <Form model={model} settingName="error_size" setListState={this.setState.bind(this)}/>,
                                            "Cập nhật cấu hình"
                                        );
                                    }}><i className="ft-edit"/></button>
                                </td>
                            </tr>

                            {/*<tr>
                                <td>error_type</td>
                                <td>{model.error_type}</td>
                                <td className="column-actions">
                                    <button key="btn-update" className="btn btn-sm btn-info square" onClick={() => {
                                        this.props.actions.openMainModal(
                                            <Form model={model} settingName="error_type" setListState={this.setState.bind(this)}/>,
                                            "Cập nhật cấu hình"
                                        );
                                    }}><i className="ft-edit"/></button>
                                </td>
                            </tr>*/}

                            <tr>
                                <td>Hệ số quy đổi</td>
                                <td>{model.factor_conversion}</td>
                                <td className="column-actions">
                                    <button key="btn-update" className="btn btn-sm btn-info square" onClick={() => {
                                        this.props.actions.openMainModal(
                                            <Form model={model} settingName="factor_conversion" setListState={this.setState.bind(this)}/>,
                                            "Cập nhật cấu hình"
                                        );
                                    }}><i className="ft-edit"/></button>
                                </td>
                            </tr>

                            <tr>
                                <td>Link chiết khấu</td>
                                <td>{model.discount_link}</td>
                                <td className="column-actions">
                                    <button key="btn-update" className="btn btn-sm btn-info square" onClick={() => {
                                        this.props.actions.openMainModal(
                                            <Form model={model} settingName="discount_link" setListState={this.setState.bind(this)}/>,
                                            "Cập nhật cấu hình"
                                        );
                                    }}><i className="ft-edit"/></button>
                                </td>
                            </tr>

                            <tr>
                                <td>Tạm ứng đơn hàng (%)</td>
                                <td>{model.order_deposit_percent}%</td>
                                <td className="column-actions">
                                    <button key="btn-update" className="btn btn-sm btn-info square" onClick={() => {
                                        this.props.actions.openMainModal(
                                            <Form model={model} settingName="order_deposit_percent" setListState={this.setState.bind(this)}/>,
                                            "Cập nhật cấu hình"
                                        );
                                    }}><i className="ft-edit"/></button>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </Card>

            </Layout>
        );
    }
}

function mapStateToProps(state) {
    return {
        userPermissions: state.auth.permissions,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(Setting)
