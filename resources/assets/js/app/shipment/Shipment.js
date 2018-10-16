import React, {Component} from 'react';
import {connect} from "react-redux";
import _ from 'lodash';
import {bindActionCreators} from "redux";
import {toastr} from 'react-redux-toastr'
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
import Formatter from "../../helpers/Formatter";
import {findDOMNode} from 'react-dom'
import ReactTooltip from 'react-tooltip'
import moment from "moment/moment";
import CreateTaskReceiveShipmentForm from "./components/CreateTaskReceiveShipmentForm"

class Shipment extends Component {

    constructor(props) {
        super(props);
        this.state = {
            models: [],
            meta: {pageCount: 0, currentPage: 1},
            isLoading: true,
            insertToReceive : [],
        };
    }

    componentDidMount() {
        this.fetchData();

        this.props.actions.changeThemeTitle("Lô hàng vận chuyển");
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
        const defaultPageSize = this.props.search.meta.per_page;
        const listRows = this.state.models.map(model => {
            const totalWeight = Formatter.number(model.shipment_item
                .map(item => item.bill_of_lading.weight)
                .reduce((a, b) => a + b, 0));
            const maxLength = Formatter.number(Math.max(...model.shipment_item.map(item => item.bill_of_lading.length)));
            const maxWidth = Formatter.number(Math.max(...model.shipment_item.map(item => item.bill_of_lading.width)));
            const maxHeight = Formatter.number(Math.max(...model.shipment_item.map(item => item.bill_of_lading.height)));

            return (
                <tr key={model.id}>
                    {this.props.authUser.roles.indexOf(10) != -1 && <td>{(model.warehouse_vn || model.status != 2 )? "" :
                        <input type="checkbox" className="checkbox-insert-into-shipment"
                               onChange = {e => {
                                   let insertToReceive = this.state.insertToReceive;
                                   if(e.target.checked){
                                       insertToReceive.push(model.shipment_code);
                                   } else {
                                       var index = insertToReceive.indexOf(model.shipment_code);
                                       insertToReceive.splice(index,1);
                                   }
                                   this.setState({
                                       insertToReceive : insertToReceive
                                   });

                                   let iinsertToReceiveAll = [];
                                   this.state.models.map(model => {
                                       if((model.warehouse_vn || model.status != 2  )){

                                       } else {
                                           iinsertToReceiveAll.push(model.shipment_code);
                                       }
                                   });

                                   if(insertToReceive.length == iinsertToReceiveAll.length && insertToReceive.length > 0){
                                       $("#select_all").prop('checked', true);
                                   } else {
                                       $("#select_all").prop('checked', false);
                                   }

                               }}
                        />}</td>}
                    <td>{model.shipment_code}</td>
                    <td>
                        {model.shipment_item && model.shipment_item.length}
                    </td>
                    <td><span className={model.real_weight && model.real_weight < totalWeight ? "red-color" : ""}>{model.real_weight && model.real_weight+ ' (kg)'} </span> </td>
                    <td>{model.volume && model.volume} cm<sup>3</sup>
                    </td>
                    <td>{model.warehouse && model.warehouse.name}</td>
                    <td>{model.transport_date && moment(model.transport_date).format("DD/MM/YYYY")}</td>
                    <td>{model.receive_date && moment(model.receive_date).format("DD/MM/YYYY")}</td>
                    <td>{model.status == Constant.STATUS_RECIEVED_UNMATCH ? <span ref='foo' data-type ='info'  data-tip={model.note} data-html={true} className = "red-color">{model.status_name}</span> :  <span >{model.status_name}</span>}
                        <ReactTooltip />
                    </td>

                    <td>{model.transport_type ? model.transport_type_name : <span className = "red-color">Chưa thiết lập</span>}</td>
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
                    <div className="table-responsive">
                        {this.props.authUser.roles.indexOf(10) != -1 && <div style={{float:"left"}}>
                             <button type="button" className="btn btn-info"
                                    onClick={() => {
                                        if(this.state.insertToReceive.length == 0) {
                                            toastr.warning('Chưa có kiện hàng nào được chọn');
                                            return;
                                        }
                                        this.props.actions.openMainModal(<CreateTaskReceiveShipmentForm onImportSuccess={(data) => {
                                            this.setState({
                                                insertToReceive: []
                                            });
                                            $("#select_all").prop('checked', false);
                                        }} model={this.state.insertToReceive} setListState={this.setState.bind(this)}/>, "Tạo nhiệm vụ nhập kho VN");
                                    }}>
                                <i className="fa fa-fw fa-search"/> Tạo nhiệm vụ nhập kho VN
                            </button>
                        </div>}
                        <table className="table table-hover">
                            <thead>
                            <tr>
                                {this.props.authUser.roles.indexOf(10) != -1 && <th><input type="checkbox" id="select_all" className="checkbox-insert-into-shipment"
                                           onChange = {e => {

                                               var checkboxes = jQuery( ":checkbox" );
                                               checkboxes.prop('checked', e.target.checked);
                                               let insertToVerify = [];
                                               if(e.target.checked){
                                                   this.state.models.map(model => {
                                                       if((!warehouse_vn && model.status  == 2 )){
                                                           insertToVerify.push(model.shipment_code);
                                                       }
                                                   })
                                               } else {
                                                   insertToVerify = [];
                                               }
                                               this.setState({
                                                   insertToReceive : insertToVerify
                                               })
                                           }}
                                /></th>}
                                <th>Mã lô hàng</th>
                                <th>Tổng số kiện</th>
                                <th>Khối lượng <br/> thực</th>
                                <th>Thể tích</th>
                                <th>Kho nhận</th>
                                <th>Ngày phát</th>
                                <th>Ngày nhận</th>
                                <th>Trạng thái</th>
                                <th>Hình thức <br/> vận chuyển</th>

                                <th style={{width: '170px'}}>
                                    {/*<button className="btn btn-primary btn-sm btn-block" onClick={() => {
                                        this.props.actions.openMainModal(<Form
                                            setListState={this.setState.bind(this)}/>, "Tạo lô hàng mới");
                                    }}><i className="ft-plus"/>{' '} Thêm lô hàng
                                    </button>*/}
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
        authUser: state.auth.user,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(Shipment)
