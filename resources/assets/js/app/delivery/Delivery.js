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
import ActionButtons from "./components/ActionButtons";
import SearchForm from "./components/SearchForm";
import Form from "./components/Form";
import Layout from "../../theme/components/Layout";
import PaginationPageSize from "../common/PaginationPageSize";
import ForbiddenPage from "../common/ForbiddenPage";
import moment from "moment";
import CreateTaskDeliveryForm from "./components/CreateTaskDeliveryForm";
import {toastr} from 'react-redux-toastr'

class Delivery extends Component {

    constructor(props) {
        super(props);

        this.state = {
            models: [],
            meta: {pageCount: 0, currentPage: 1},
            isLoading: true,
            insertToDelivery : [],
        };
    }

    componentDidMount() {
        this.fetchData();

        this.props.actions.changeThemeTitle("Xuất hàng");
    }

    componentWillReceiveProps(nextProps) {
        this.fetchData(nextProps.search);
    }

    componentWillUnmount() {
        this.props.actions.clearState();
    }

    fetchData(search) {
        search = search || this.props.search;

        const params = _.assign({}, search.params, search.meta);

        this.setState({isLoading: true});

        return ApiService.get(Constant.resourcePath(), params).then(({data: {data}}) => {
            this.setState({
                models: data.data,
                meta: {
                    pageCount: data.last_page,
                    currentPage: data.current_page
                },
                isLoading: false,
            });

        });
    }
    render() {
        const {userPermissions} = this.props;
        if (!userPermissions.delivery || !userPermissions.delivery.index) return <ForbiddenPage/>;

        const defaultPageSize = this.props.search.meta.per_page;

        const listRows = this.state.models.map(model => {
            return (
                <tr key={model.id}>
                    {this.props.authUser.roles.indexOf(10) != -1 &&
                    <td>{model.delivery ? "" :
                        <input type="checkbox" className="checkbox-insert-into-shipment"
                               onChange = {e => {
                                   let insertToDelivery = this.state.insertToDelivery;
                                   if(e.target.checked){
                                       insertToDelivery.push(model);
                                   } else {
                                       var index = insertToDelivery.indexOf(model);
                                       insertToDelivery.splice(index,1);
                                   }
                                   this.setState({
                                       insertToDelivery : insertToDelivery
                                   });

                                   let insertToDeliveryAll = [];
                                   this.state.models.map(model => {
                                       if((model.delivery )){

                                       } else {
                                           insertToDeliveryAll.push(model);
                                       }
                                   });

                                   if(insertToDelivery.length == insertToDeliveryAll.length && insertToDeliveryAll.length > 0){
                                       $("#select_all").prop('checked', true);
                                   } else {
                                       $("#select_all").prop('checked', false);
                                   }

                               }}
                        />}</td>}
                    <td>{model.customer && model.customer.name}</td>
                    <td>
                        {model.payment_info && model.payment_info.map((item,index) =>{
                            let obj = JSON.parse(item.data);
                            return (
                                 <div key={item.id}>
                                     {item.type == 0 && <b>{obj.address} : {obj.lading_code.toString()}</b> }
                                </div>
                            );
                        }) }
                    </td>
                    <td>{moment(model.updated_at).format("DD/MM/YYYY")}</td>
                    <td>{model.delivery && model.delivery.user && model.delivery.user.name}</td>
                    <td>{model.delivery ? model.delivery.status_name : 'Chưa tạo nhiệm vụ'}</td>
                    <td>{model.delivery && model.delivery.date_delivery && moment(model.delivery.date_deliveryt).format("DD/MM/YYYY")}</td>
                    <td>{model.delivery && this.props.authUser.id == model.delivery.user_id &&
                        <ActionButtons model={model} setListState={this.setState.bind(this)}/>
                    }</td>
                </tr>
            );
        });

        return (
            <Layout>

                <Card>
                    <SearchForm/>
                </Card>

                <Card isLoading={this.state.isLoading}>
                    {this.props.authUser.roles.indexOf(10) != -1 && <div style={{float:"left"}}>
                        <button type="button" className="btn btn-info"
                                onClick={() => {
                                    if(this.state.insertToDelivery.length == 0) {
                                        toastr.warning('Chưa có kiện hàng nào được chọn');
                                        return;
                                    }
                                    this.props.actions.openMainModal(<CreateTaskDeliveryForm onImportSuccess={(data) => {
                                        this.setState({
                                            insertToDelivery: []
                                        });
                                        $("#select_all").prop('checked', false);
                                    }} model={this.state.insertToDelivery} setListState={this.setState.bind(this)}/>, "Tạo nhiệm vụ xuất hàng");
                                }}>
                            <i className="fa fa-fw fa-search"/> Tạo nhiệm vụ xuất hàng
                        </button>
                    </div>}

                    <div className="table-responsive">
                        <table className="table table-hover">
                            <thead>
                            <tr>
                                {this.props.authUser.roles.indexOf(10) != -1 && <th><input type="checkbox" id="select_all" className="checkbox-insert-into-shipment"
                                   onChange = {e => {

                                       var checkboxes = jQuery( ":checkbox" );
                                       checkboxes.prop('checked', e.target.checked);
                                       let insertToDelivery = [];
                                       if(e.target.checked){
                                           this.state.models.map(model => {
                                               if((!model.delivery )){
                                                   insertToDelivery.push(model);
                                               }
                                           })
                                       } else {
                                           insertToDelivery = [];
                                       }
                                       this.setState({
                                           insertToDelivery : insertToDelivery
                                       })
                                   }}
                                /></th>}
                                <th>Tên KH</th>
                                <th>Mã vận đơn</th>
                                <th>Ngày Thanh Toán</th>
                                <th>NV Giao Hàng</th>
                                <th>Trạng Thái</th>
                                <th>Ngày Giao Hàng</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            {listRows}
                            </tbody>
                        </table>
                    </div>

                    <div className="row">
                        <div className="col-sm-8">
                            <Paginate
                                pageCount={this.state.meta.pageCount}
                                onPageChange={(data) => {
                                    this.props.actions.changePage(data.selected + 1);
                                }}
                                currentPage={this.state.meta.currentPage - 1}
                            />
                        </div>
                        <div className="col-sm-4 text-right mt-1">
                            Hiển thị mỗi trang <PaginationPageSize
                            defaultPageSize={defaultPageSize}
                            onChange={(pageSize) => {
                                this.props.actions.changePageSize(pageSize);
                            }}/> bản ghi
                        </div>
                    </div>
                </Card>

            </Layout>
        );
    }
}

function mapStateToProps(state) {
    return {
        search: state.customer.search,
        userPermissions: state.auth.permissions,
        authUser : state.auth.user,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(Delivery)
