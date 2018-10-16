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
import Formatter from "../../helpers/Formatter";
import Array from "../../helpers/Array";
import {toastr} from "react-redux-toastr";
import UrlHelper from "../../helpers/Url";
import moment from "moment/moment";

class DeliveryDetail extends Component {

    constructor(props) {
        super(props);
        this.state = {
            model: {},
            isLoading: true,
        };
    }

    componentDidMount() {
        this.fetchModel(this.getModelId());
        this.props.actions.changeThemeTitle("Chi tiết xuất hàng");
    }

    getModelId() {
        return this.props.match.params.id;
    }

    fetchModel(id) {
        this.setState({isLoading: true});
        ApiService.get(Constant.detailPath(id)).then((response) => {
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

    render() {
        let rowItem;
        const {model} = this.state;
        if(this.state.isLoading == false) {
            rowItem = this.state.model.payment_info.map(info => {
                let object = JSON.parse(info.data);
                if (info.type == 0) {
                    return object.lading_code.map((item,index) => {
                        return (
                            <tr key={index}>
                                <td>{item}</td>
                                <td>{object.address}</td>
                            </tr>
                        );
                    })
                }
            });
        }
        return (
            <Layout>
                <Card isLoading={this.state.isLoading}>
                    <div className="row">
                        <div className="col-6">
                            <b>Tên khách hàng:</b> {model.customer && model.customer.name}<br/>
                            <b>Email:</b> {model.customer && model.customer.email}<br/>
                            <b>Ngày thanh toán:</b> {model && moment(model.updated_at).format("DD/MM/YYYY")}<br/>
                            <b>NV giao hàng:</b> {model.delivery && model.delivery.user && model.delivery.user.name} <br/>
                            <b>Ngày giao hàng:</b> {model.delivery && moment(model.delivery.date_delivery).format("DD/MM/YYYY")}
                        </div>
                    </div>
                    <div style={{marginTop:"10px"}}>
                        <table className="table table-bordered">
                            <thead>
                            <tr>
                                <td>Mã vận đơn</td>
                                <td>Địa chỉ nhận hàng</td>
                            </tr>
                            </thead>
                            <tbody>
                            {rowItem}
                            </tbody>
                        </table>
                    </div>
                    <div>
                        <h5>Ảnh biên bản</h5>
                        {model.delivery &&
                            <img src={UrlHelper.imageUrl(model.delivery.image)} style={{width:"200px",height:"200px",border:"1px solid #ececec"}}/>
                        }
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

export default connect(mapStateToProps, mapDispatchToProps)(DeliveryDetail)
