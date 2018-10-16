import React, {Component} from 'react';
import {connect} from "react-redux";
import _ from 'lodash';
import {bindActionCreators} from "redux";

import ApiService from "../../services/ApiService";
import * as themeActions from "../../theme/meta/action";
import * as commonActions from "../common/meta/action";
import Constant from './meta/constant';
import Card from "../../theme/components/Card";
import ItemActionButtons from "./components/ItemActionButtons";
import Form from "./components/FormUpdate";
import FormAddItem from "./components/FormCreate";
import Layout from "../../theme/components/Layout";
import UrlHelper from "../../helpers/Url";
import moment from "moment";
import swal from "sweetalert";
import Formatter from "../../helpers/Formatter";
import ShowImages from "../common/ShowImages";
import ForbiddenPage from "../common/ForbiddenPage";


class ChinaOrderDetail extends Component {

    constructor(props) {
        super(props);

        this.state = {
            model: {
                china_order_items: []
            },
            isLoading: true,
            canAccess: props.userPermissions.china_order && props.userPermissions.china_order.view,
        };
    }

    componentDidMount() {
        this.fetchModel(this.getModelId());

        this.props.actions.changeThemeTitle("Chi tiết Đơn hàng TQ");
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

        const listRows = model.china_order_items.map((item, index) => {
            const cItem = item.customer_order_item;

            return (
                <tr key={item.id}>
                    <td>{index + 1}</td>
                    <td className="pr-0 pl-0">
                        {!!cItem.images.length &&
                        <a onClick={e => {
                            e.preventDefault();
                            this.props.actions.openMainModal(<ShowImages
                                images={cItem.images}/>, "Ảnh của " + cItem.description);
                        }}><img src={UrlHelper.imageUrl(cItem.images[0].path)} className="img-fluid" alt=""/></a>}
                    </td>
                    <td>
                        <a href={cItem.link} target="_blank">{cItem.description}</a>
                        {cItem.note && <span><br/><small className="font-italic">{cItem.note}</small></span>}
                    </td>
                    <td>
                        <b>￥{Formatter.money(cItem.price_cny)}</b>
                    </td>
                    <td>
                        {item.quantity}{' '}
                        <small className="font-italic">{cItem.unit}</small>
                    </td>
                    <td>
                        ￥{Formatter.money(item.total_price)}
                    </td>
                    <td>{item.status_name}</td>
                    {model.is_items_updatable &&
                    <td className="column-actions">
                        <ItemActionButtons model={item} setDetailState={this.setState.bind(this)}/>
                    </td>}
                </tr>
            );
        });

        return (
            <Layout>

                <Card isLoading={this.state.isLoading}>
                    <div className="mb-1">
                        <div className="row">
                            <div className="col-sm-5">
                                <b>Mã đơn:</b> {model.id}<br/>
                                <b>NV đặt hàng:</b> {_.get(model, 'user_purchasing.name')}<br/>
                                <b>Trạng thái:</b> {model.status_name}
                            </div>
                            <div className="col-sm-5">
                                <b>Ngày tạo:</b> {moment(model.created_at).format("DD/MM/YYYY HH:mm")}<br/>
                                <b>Ngày kết thúc:</b> {!!model.end_date && moment(model.end_date).format("DD/MM/YYYY")}
                            </div>
                            <div className="col-sm-2">
                                {userPermissions.china_order.update &&
                                <button type="button" className="btn btn-info btn-block btn-sm" onClick={() => {
                                    this.props.actions.openMainModal(
                                        <Form model={model} onUpdateSuccess={(data) => {
                                            this.setState({model: data});
                                        }}/>,
                                        "Sửa thông tin Đơn hàng"
                                    );
                                }}>
                                    <i className="ft-edit"/> Sửa
                                </button>}
                                {userPermissions.china_order.approve && !model.is_approved &&
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
                            </div>
                        </div>
                    </div>

                    <div className="table-responsive">
                        <table className="table table-hover">
                            <thead>
                            <tr>
                                <th>STT</th>
                                <th style={{width: '100px'}}>Ảnh</th>
                                <th>Mô tả</th>
                                <th>Giá tiền</th>
                                <th>Số lượng</th>
                                <th>Thành tiền</th>
                                <th>Trạng thái</th>
                                {model.is_items_updatable &&
                                <th style={{width: '132px'}}>
                                    {userPermissions.china_order_item.create &&
                                    <button className="btn btn-primary btn-sm btn-block" onClick={() => {
                                        this.props.actions.openMainModal(
                                            <FormAddItem onInsertSuccess={(data) => {
                                                this.setState(({model}) => {
                                                    let isUpdated;
                                                    data.forEach(newItem => {
                                                        isUpdated = false;
                                                        model.china_order_items = model.china_order_items.map(item => {
                                                            if (newItem.id === item.id) {
                                                                isUpdated = true;
                                                                return newItem;
                                                            }
                                                            return item;
                                                        });

                                                        if (!isUpdated) model.china_order_items.push(newItem);
                                                    });

                                                    return {model: model};
                                                });
                                            }} model={model}/>,
                                            "Thêm sản phẩm");
                                    }}><i className="ft-plus"/>{' '} Thêm
                                    </button>}
                                </th>}
                            </tr>
                            </thead>
                            <tbody>
                            {listRows}
                            <tr>
                                <td colSpan={2}><b>Tổng</b></td>
                                <td>
                                    Khối lượng: <b>{Formatter.number(model.china_order_items
                                    .map(item => item.total_weight)
                                    .reduce((a, b) => a + b, 0))} kg</b>
                                </td>
                                <td>

                                </td>
                                <td>
                                    {model.china_order_items
                                        .map(item => item.quantity)
                                        .reduce((a, b) => a + b, 0)}
                                </td>
                                <td>
                                    ￥{Formatter.money(model.china_order_items
                                    .map(item => item.total_price)
                                    .reduce((a, b) => a + b, 0))}
                                </td>
                                <td colSpan={2}>

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

export default connect(mapStateToProps, mapDispatchToProps)(ChinaOrderDetail)
