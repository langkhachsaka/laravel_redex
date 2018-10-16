import React, {Component} from 'react';
import {connect} from "react-redux";
import _ from 'lodash';
import {bindActionCreators} from "redux";

import ApiService from "../../services/ApiService";
import Paginate from "../common/Paginate";
import * as themeActions from "../../theme/meta/action";
import * as commonActions from "../common/meta/action";
import Constant from './meta/constant';
import Card from "../../theme/components/Card";
import Layout from "../../theme/components/Layout";
import FastShipping from "./components/FastShipping";
import FastShippingWholeSale from "./components/FastShippingWholeSale";
import NormalShipping from "./components/NormalShipping";
import Formatter from "../../helpers/Formatter";

class PriceList extends Component {

    constructor(props) {
        super(props);

        this.state = {
            models: [],
            isLoading: true,
        };
    }

    componentDidMount() {
        this.props.actions.changeThemeTitle("Bảng giá cước");
        ApiService.get(Constant.resourcePath()).then(({data: {data}}) => {
            this.setState({
                models: data,
                isLoading: false,
            });
        });
    }

    componentWillUnmount() {
        this.props.actions.clearState();
    }

    render() {
        const {userPermissions} = this.props;

        const fastShipping = this.state.models.map(model => {
            return (model.delivery_type == 1 && model.key.indexOf('wholesale') < 0 &&
                <tr key={model.id}>
                    <td>{model.description}</td>
                    <td>{Formatter.money(model.price)}</td>
                    <td className="column-actions pl-0 pr-0">
                        <button key="btn-update" className="btn btn-sm btn-info square" onClick={() => {
                            this.props.actions.openMainModal(
                                <FastShipping model={model} setListState={this.setState.bind(this)}/>,
                                "Cập nhật giá cước"
                            );
                        }}><i className="ft-edit"/></button>
                    </td>
                </tr>
            );
        });

        const fastShippingWholeSale = this.state.models.map(model => {
            return (model.delivery_type == 1 && model.key.indexOf('wholesale') === 16 &&
                <tr key={model.id}>
                    <td>{model.description}</td>
                    <td>{Formatter.money(model.price)}</td>
                    <td className="column-actions pl-0 pr-0">
                        <button key="btn-update" className="btn btn-sm btn-info square" onClick={() => {
                            this.props.actions.openMainModal(
                                <FastShippingWholeSale model={model} setListState={this.setState.bind(this)}/>,
                                "Cập nhật giá cước"
                            );
                        }}><i className="ft-edit"/></button>
                    </td>
                </tr>
            );
        });

        const normalShipping = this.state.models.map(model => {
            return (model.delivery_type == 0 &&
                <tr key={model.id}>
                    <td>{model.description}</td>
                    <td>{Formatter.money(model.price)}</td>
                    <td className="column-actions pl-0 pr-0">
                        <button key="btn-update" className="btn btn-sm btn-info square" onClick={() => {
                            this.props.actions.openMainModal(
                                <NormalShipping model={model} setListState={this.setState.bind(this)}/>,
                                "Cập nhật giá cước"
                            );
                        }}><i className="ft-edit"/></button>
                    </td>
                </tr>
            );
        });

        return (
            <Layout>
                <Card isLoading={this.state.isLoading}>
                    <div>
                        <h2 style={{textAlign:"center",textTransform: "uppercase",color:"red"}}>Chuyển nhanh - khách lẻ</h2>
                        <div>
                            <table className="table table-bordered">
                                <thead>
                                <tr style={{background :"#FCAD52"}}>
                                    <td style={{width :"43%"}}>Trọng lượng</td>
                                    <td>Đơn giá (VNĐ)</td>
                                    <td></td>
                                </tr>
                                </thead>
                                <tbody>
                                {fastShipping}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div>
                        <h2 style={{textAlign:"center",textTransform: "uppercase",color:"red"}}>Chuyển nhanh - khách sỉ</h2>
                        <div>
                            <table className="table table-bordered">
                                <thead>
                                <tr style={{background :"#FCAD52"}}>
                                    <td>Trọng lượng</td>
                                    <td>Đơn giá (VNĐ)</td>
                                    <td></td>
                                </tr>
                                </thead>
                                <tbody>
                                {fastShippingWholeSale}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div>
                        <h2 style={{textAlign:"center",textTransform: "uppercase",color:"red"}}>Chuyển thường</h2>
                        <div>
                            <table className="table table-bordered">
                                <thead>
                                <tr style={{background :"#FCAD52"}}>
                                    <td>Trọng lượng</td>
                                    <td>Đơn giá (VNĐ)</td>
                                    <td></td>
                                </tr>
                                </thead>
                                <tbody>
                                {normalShipping}
                                </tbody>
                            </table>
                        </div>
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
        actions: bindActionCreators(_.assign({}, commonActions, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(PriceList)
