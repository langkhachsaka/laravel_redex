import React, {Component} from 'react';
import {connect} from "react-redux";
import _ from 'lodash';
import {bindActionCreators} from "redux";
import {toastr} from 'react-redux-toastr'
import CreateShipmentForm from "./components/CreateShipmentForm";
import ApiService from "../../services/ApiService";
import Paginate from "../common/Paginate";
import * as themeActions from "../../theme/meta/action";
import * as commonActions from "../common/meta/action";
import Constant from './meta/constant';
import Card from "../../theme/components/Card";
import ActionButtons from "./components/ActionButtons";
import SearchForm from "./components/SearchForm";
import Form from "./components/Form";
import FormImportLading from "./components/FormImportLading";
import Layout from "../../theme/components/Layout";
import PaginationPageSize from "../common/PaginationPageSize";
import moment from "moment/moment";
import ForbiddenPage from "../common/ForbiddenPage";

class WarehouseReceivingCN extends Component {

    constructor(props) {
        super(props);
        this.state = {
            models: [],
            modelsNew: [],
            meta: {pageCount: 0, currentPage: 1},
            insertToShipment :[],
            isLoading: true
        };
    }

    componentDidMount() {
        this.fetchData();
        this.props.actions.changeThemeTitle("Kho hàng Trung Quốc");
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



        if (!userPermissions.warehouse_receiving_cn || !userPermissions.warehouse_receiving_cn.index) return <ForbiddenPage/>;
        const defaultPageSize = this.props.search.meta.per_page;
        const listRows = this.state.models.map(model => {
            let status_name = model.status_name;
            if(model.status ==2 ){
                status_name = model.status_name;
            } else if(model.shipment_item && model.shipment_item.shipment && model.shipment_item.shipment.status ==2){
                status_name = "Đã chuyển";
            } else if(model.shipment_item && model.shipment_item.shipment && model.shipment_item.shipment.status ==3) {
                status_name = "Đã nhận";
            }
            return (
                <tr key={model.id}>
                    <td>{(model.shipment_item || model.status == 2 )? "" :
                        <input type="checkbox" className="checkbox-insert-into-shipment"
                               onChange = {e => {


                                   let insertToShipment = this.state.insertToShipment;
                                   if(e.target.checked){
                                       insertToShipment.push(model.bill_of_lading_code);
                                   } else {
                                       var index = insertToShipment.indexOf(model.bill_of_lading_code);
                                       insertToShipment.splice(index,1);
                                   }
                                   this.setState({
                                       insertToShipment : insertToShipment
                                   });

                                   let insertToShipmentAll = [];
                                   this.state.models.map(model => {
                                       if((model.shipment_item || model.status == 2 )){

                                       } else {
                                           insertToShipmentAll.push(model.bill_of_lading_code);
                                       }
                                   });

                                    if(insertToShipment.length == insertToShipmentAll.length && insertToShipment.length > 0){
                                        $("#select_all").prop('checked', true);
                                    } else {
                                        $("#select_all").prop('checked', false);
                                    }

                               }}
                    />}</td>
                    <td>{model.warehouse ? model.warehouse.name : ""}</td>
                    <td>{model.user_receive.name}</td>
                    <td>{model.bill_of_lading_code}</td>
                    <td>{moment(model.date_receiving).format("DD/MM/YYYY")}</td>
                    <td>{model.weight} kg</td>
                    <td>{model.lading_code && model.lading_code.map((item,index) =>{
                        return ( item.bill_code && item.bill_code.customer_order && item.bill_code.customer_order.customer  ? <div key={index}>{index + 1}. {item.bill_code.customer_order.customer.name }</div>
                        :item.bill_of_lading && item.bill_of_lading.customer && <div key={index}>{index + 1}. {item.bill_of_lading.customer.name }</div>
                        );
                    })}</td>
                    <td>
                        {model.lading_code && model.lading_code.map((lading_code,index) =>{
                            return ( lading_code.bill_code ? <div key={lading_code.id}>{index + 1}. {lading_code.bill_code.bill_code}</div>
                            :<div className={'green-color'}>Đơn hàng vận chuyển</div>
                            );
                            })
                        }
                    </td>
                    <td>{model.shipment_item && model.shipment_item && model.shipment_item.shipment_code}</td>
                    <td>{status_name}</td>
                    <td className="column-actions">
                       { <ActionButtons model={model} setListState={this.setState.bind(this)}/>}
                    </td>
                </tr>
            );
        });
        return (
            <Layout>

                {<Card>
                    <SearchForm/>
                </Card>}

                <Card isLoading={this.state.isLoading}>
                    <div className="row-button-function">

                        <button type="button" className="btn btn-info"
                                onClick={(e) => {
                                    e.preventDefault();
                                    this.props.actions.openMainModal(
                                        <FormImportLading
                                            onImportSuccess={(data) => {
                                                let models = this.state.models;
                                                data.forEach(item => {
                                                    models.unshift(item);
                                                });
                                                this.setState({
                                                    models: models
                                                })
                                            }}
                                        />, "Import kiện hàng vào kho từ file EXCEL");
                                }}>
                            <i className="fa fa-fw fa-search"/> Nhập kho bằng EXCEL
                        </button>
                        {'  '}
                        <button type="button" className="btn btn-info"
                                onClick={() => {
                                    if(this.state.insertToShipment.length == 0) {
                                        toastr.warning('Chưa có kiện hàng nào được chọn');
                                        return;
                                    }
                                    this.props.actions.openMainModal(<CreateShipmentForm onImportSuccess={(data) => {
                                        this.setState({
                                            insertToShipment: []
                                        });
                                        $("#select_all").prop('checked', false);

                                    }} model={this.state.insertToShipment} setListState={this.setState.bind(this)}/>, "Thêm vào lô hàng");
                                }}>
                            <i className="fa fa-fw fa-search"/> Thêm vào lô hàng
                        </button>



                    </div>
                    <div className="table-responsive">
                        <table className="table table-hover">
                            <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="select_all" className="checkbox-insert-into-shipment"
                                           onChange = {e => {

                                               var checkboxes = jQuery( ":checkbox" );
                                               checkboxes.prop('checked', e.target.checked);
                                               let insertToShipment = [];
                                               if(e.target.checked){
                                                   this.state.models.map(model => {
                                                       if((model.shipment_item || model.status == 2 )){

                                                       } else {
                                                           insertToShipment.push(model.bill_of_lading_code);
                                                       }
                                                   })
                                               } else {
                                                   insertToShipment = [];
                                               }
                                               this.setState({
                                                   insertToShipment : insertToShipment
                                               })
                                           }}
                                    />
                                </th>
                                <th>Tên kho hàng</th>
                                <th>Người nhận</th>
                                <th>Mã vận đơn</th>
                                <th>Ngày nhận hàng</th>      
                                <th>Khối lượng</th>
                                <th>Tên KH</th>
                                <th>Mã hóa đơn</th>
                                <th>Mã lô hàng</th>
                                <th>Trạng thái</th>
                                <th style={{width: '170px'}}>
                                    {userPermissions.warehouse_receiving_cn.create &&
                                    <button className="btn btn-primary btn-sm btn-block" onClick={() => {
                                        this.props.actions.openMainModal(<Form
                                            setListState={this.setState.bind(this)}/>, "Nhập hàng vào kho");
                                    }}><i className="ft-plus"/>{' '} Nhập hàng
                                    </button>}
                                </th>
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
        search: state.warehouseReceivingCN.search,
        userPermissions: state.auth.permissions,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(WarehouseReceivingCN)
