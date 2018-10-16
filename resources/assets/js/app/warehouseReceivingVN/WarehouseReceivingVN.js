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
import CreateTaskVerifyLadingForm from "./components/CreateTaskVerifyLadingForm";
import VerifyShipment from "./components/VerifyShipment";
import {toastr} from 'react-redux-toastr'
import Layout from "../../theme/components/Layout";
import PaginationPageSize from "../common/PaginationPageSize";
import moment from "moment/moment";
import ForbiddenPage from "../common/ForbiddenPage";

class WarehouseReceivingVN extends Component {

    constructor(props) {
        super(props);
        this.state = {
            models: [],
            insertToVerify : [],
            shippmentCode : null,
            error : null,
            meta: {pageCount: 0, currentPage: 1},
            isLoading: true
        };
    }

    componentDidMount() {
        this.fetchData();

        this.props.actions.changeThemeTitle("Kho hàng Việt Nam");
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
                models: data.data.data,
                error: data.error,
                meta: {
                    pageCount: data.data.last_page,
                    currentPage: data.data.current_page
                },
                isLoading: false,
            });
        });
    }
    handleKeyPress(){
        this.setState({
            isLoading: true
        });
        ApiService.get(Constant.resourcePath("checkShipmentCode/"+this.state.shippmentCode)).then(({data: {data}}) => {
            this.setState({
                isLoading: false
            });
            if(!data) {
                toastr.warning('Lô hàng này không tồn tại');
                return;
            } else if(data.status == 1) {
                toastr.warning('Lô hàng này chưa chuyển về Việt Nam');
                return;
            } else if(!data.warehouse_vn) {
                toastr.warning('Chưa tạo nhiệm vụ nhập kho cho lô hàng này');
                return;
            } else if(data.status != 2) {
                toastr.warning('Lô hàng này nhập vào kho');
                return;
            } else if(data.warehouse_vn &&  data.warehouse_vn.user_receive_id != this.props.authUser.id ) {
                toastr.warning('Bạn không được phân công để nhập lô hàng này vào kho');
                return;
            }else {
                this.props.actions.openMainModal(<VerifyShipment model={data}/>, "ĐƯA THÔNG TIN LÔ HÀNG VÀO HỆ THỐNG");
            }

        });

    }

    render() {
        const {userPermissions} = this.props;
        const defaultPageSize = this.props.search.meta.per_page;
        let count = 0;
        const listRows = this.state.models.map((model,index) => {
            console.log(!!!(model.verify_lading_code || model.status != 2  || model.lading_codes[0].bill_of_lading ) );
            return (
                <tr key={index} className={index%2==0 ? 'bgc-ghostwhite': ''}>
                    {this.props.authUser.roles.indexOf(10) != -1 && <td>{!!(model.verify_lading_code || model.status != 2  || model.lading_codes[0].bill_of_lading )? "" :
                        <input type="checkbox" className="checkbox-insert-into-shipment"
                               onChange = {e => {
                                   let insertToVerify = this.state.insertToVerify;
                                   if(e.target.checked){
                                       insertToVerify.push(model.lading_code);
                                   } else {
                                       var index = insertToVerify.indexOf(model.lading_code);
                                       insertToVerify.splice(index,1);
                                   }
                                   this.setState({
                                       insertToVerify : insertToVerify
                                   });

                                   let insertToVerifyAll = [];
                                   this.state.models.map(model => {
                                       if((model.verify_lading_code || model.status != 2  )){

                                       } else {
                                           insertToVerifyAll.push(model.lading_code);
                                       }
                                   });

                                   if(insertToVerify.length == insertToVerifyAll.length && insertToVerify.length > 0){
                                       $("#select_all").prop('checked', true);
                                   } else {
                                       $("#select_all").prop('checked', false);
                                   }

                               }}
                        />}</td>}

                    <td>{model.warehouse_receiving_v_n && moment(model.warehouse_receiving_v_n.date_receiving).format("DD/MM/YYYY")}</td>
                    <td>{model.warehouse_receiving_v_n && model.warehouse_receiving_v_n.user_receive && model.warehouse_receiving_v_n.user_receive.name}</td>
                    <td>{model.sub_lading_code ? model.sub_lading_code.sub_lading_code :model.lading_code}</td>
                    <td>{model.weight} kg</td>
                    <td>
                        <div>
                            <small className="font-italic">Chiều cao  : </small>

                            <strong>{model.height} cm</strong>
                        </div>
                        <div>
                            <small className="font-italic">Chiều rộng : </small>

                            <strong>{model.width} cm</strong>
                        </div>
                        <div>
                            <small className="font-italic">Chiều dài : </small>

                            <strong>{model.length} cm</strong>
                        </div>
                    </td>
                    <td>
                        {model.sub_lading_code ? model.sub_lading_code.customer_order.customer.name :
                            model.lading_codes && model.lading_codes.map(ladingCode =>{
                            return (
                                <div key={ladingCode.id}>{ladingCode.bill_code_out && ladingCode.bill_code_out.customer_order &&
                                ladingCode.bill_code_out.customer_order.customer ? ladingCode.bill_code_out.customer_order.customer.name :
                                ladingCode.bill_of_lading && ladingCode.bill_of_lading.customer && ladingCode.bill_of_lading.customer.name
                                 }</div>
                            );
                        })}
                    </td>
                    <td>
                        {model.sub_lading_code && model.sub_lading_code.lading_codes ? model.sub_lading_code.lading_codes.map(item => {
                                    return (<div key={item.id}>
                                        {item.bill_code_out &&  item.bill_code_out.customer_order && item.bill_code_out.customer_order.id == model.sub_lading_code.order_id ?
                                        <div >
                                            {item.bill_code_out.bill_code}
                                        </div>
                                            : <div className={'green-color'}>Đơn hàng vận chuyển</div>
                                        }
                                    </div>);
                            }) :
                            model.lading_codes && model.lading_codes.map(ladingCode =>{
                            return (
                                <div key={ladingCode.id}>{ladingCode.bill_code ? ladingCode.bill_code : <div className={'green-color'}>Đơn hàng vận chuyển</div>}</div>
                            );
                        })}
                    </td>
                    <td>
                        {model.warehouse_receiving_v_n && model.warehouse_receiving_v_n.shipment_code}
                    </td>
                    <td> {model.status_name}</td>
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
                    {this.props.authUser.roles.indexOf(10) != -1 && <div style={{float:"left"}}>
                        <button type="button" className="btn btn-info"
                                onClick={() => {
                                    if(this.state.insertToVerify.length == 0) {
                                        toastr.warning('Chưa có kiện hàng nào được chọn');
                                        return;
                                    }
                                    this.props.actions.openMainModal(<CreateTaskVerifyLadingForm onImportSuccess={(data) => {
                                        this.setState({
                                            insertToVerify: []
                                        });
                                        $("#select_all").prop('checked', false);
                                    }} model={this.state.insertToVerify} setListState={this.setState.bind(this)}/>, "Tạo nhiệm vụ kiểm hàng");
                                }}>
                            <i className="fa fa-fw fa-search"/> Tạo nhiệm vụ kiểm hàng
                        </button>
                    </div>}
                    <div style={{ textAlign:'center',paddingBottom: '20px'}}>
                        Nhập mã lô hàng
                        <input  type="text" style={{display :'inline', width : '200px',marginLeft:'20px'}}
                                onChange={(e) => {
                                    this.setState({
                                        shippmentCode: e.target.value,
                                    })
                                }}
                                onKeyPress={(e) => {
                                    if(e.key == 'Enter') {
                                        this.handleKeyPress();
                                    }
                                }}
                                placeholder="Nhận mã lô hàng" className="form-control"/>
                        <button type="button" onClick={(e) => {this.handleKeyPress()}} style={{marginLeft:'20px', marginBottom : '3px'}} className="btn btn-info">

                            <i className="fa fa-fw fa-search"/> Tiếp tục
                        </button>
                    </div>
                    <div className="table-responsive">
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
                                                       if((model.status  == 2 )){
                                                           insertToVerify.push(model.lading_code);
                                                       }
                                                   })
                                               } else {
                                                   insertToVerify = [];
                                               }
                                               this.setState({
                                                   insertToVerify : insertToVerify
                                               })
                                           }}
                                /></th>}
                                <th>Ngày nhận hàng</th>
                                <th>Người nhận</th>
                                <th>Mã vận đơn</th>
                                <th>Khối lượng</th>
                                <th>Kích thước</th>
                                <th>Tên khách hàng</th>
                                <th>Mã hóa đơn</th>
                                <th>Mã lô hàng</th>
                                <th>Trạng thái</th>
                                <th style={{width: '170px'}}>
                                    {/*{userPermissions.warehouse_receiving_cn.create &&
                                    <button className="btn btn-primary btn-sm btn-block" onClick={() => {
                                        this.props.actions.openMainModal(<Form
                                            setListState={this.setState.bind(this)}/>, "Nhập hàng vào kho");
                                    }}><i className="ft-plus"/>{' '} Thêm
                                    </button>}*/}
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
                            }}/> lô hàng
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
        authUser : state.auth.user,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(WarehouseReceivingVN)
