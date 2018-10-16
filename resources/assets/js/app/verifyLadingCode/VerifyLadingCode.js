import React, {Component} from 'react';
import {connect} from "react-redux";
import _ from 'lodash';
import {bindActionCreators} from "redux";
import {toastr} from 'react-redux-toastr'
import ApiService from "../../services/ApiService";
import PropTypes from 'prop-types'
import Paginate from "../common/Paginate";
import * as themeActions from "../../theme/meta/action";
import * as commonActions from "../common/meta/action";
import Constant from './meta/constant';
import Card from "../../theme/components/Card";
import ActionButtons from "./components/ActionButtons";
import SearchForm from "./components/SearchForm";
import Layout from "../../theme/components/Layout";
import PaginationPageSize from "../common/PaginationPageSize";
import moment from "moment/moment";
import ForbiddenPage from "../common/ForbiddenPage";
import {Redirect} from "react-router-dom";

class VerifyLadingCode extends Component {

    constructor(props) {
        super(props);
        this.state = {
            models: [],
            ladingCode : null,
            meta: {pageCount: 0, currentPage: 1},
            isLoading: true,
            redirectToVerifyCustomerOrderId : null,
            redirectToManyVerifyCustomerOrderId : null,
        };
    }

    componentDidMount() {
        this.fetchData();

        this.props.actions.changeThemeTitle("Kiểm tra kiện hàng");
    }

    componentWillReceiveProps(nextProps) {
        this.fetchData(nextProps.search);
    }

    componentWillUnmount() {
        this.props.actions.clearState();
    }

    handleKeyPress(){
        this.setState({
            isLoading: true
        });
        ApiService.get(Constant.resourcePath("checkLadingCode/"+this.state.ladingCode)).then(({data: {data}}) => {
            this.setState({
                isLoading: false
            });
            if(data.length == 0) {
                toastr.warning('Không tồn tại mã vận đơn này');
                return;
            } else if(!data[0].bill_code) {
                toastr.warning('Đơn hàng vận chuyển không cần kiểm tra');
                return;
            } else if(data[0].verify_lading_code && data[0].verify_lading_code.status != Constant.STATUS_NOT_YET_CONFIRM){
                toastr.warning('Mã vận đơn này đã kiểm tra');
                return;
            } else if(!data[0].verify_lading_code){
                toastr.warning('Chưa tạo nhiệm vụ kiểm tra kiện hàng cho mã vận đơn này');
                return;
            }  else if(!data[0].warehouse_vn_lading_item){
                toastr.warning('Mã vận đơn này chưa nhập vào kho Việt Nam');
                return;
            } else if(data[0].verify_lading_code && data[0].verify_lading_code.verifier_id != this.props.authUser.id){
                toastr.warning('Bạn không có quyền kiểm tra kiện hàng này');
                return;
            }

            if(data[0].bill_code){
                let fisrtCustomerOrderId = data[0].bill_code.customer_order_id;
                data.map(item =>{
                    if(item.bill_code.customer_order_id != fisrtCustomerOrderId ){
                        console.log(true);
                        this.setState({
                            redirectToManyVerifyCustomerOrderId: data[0].code
                        })
                    }
                })
            this.setState({
                redirectToVerifyCustomerOrderId: data[0].code
            })
            }
        });
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
        if (this.state.redirectToVerifyCustomerOrderId) {
            return <Redirect to={"/verify-lading-code/" + this.state.redirectToVerifyCustomerOrderId}/>;
        } else if (this.state.redirectToManyVerifyCustomerOrderId) {
            return <Redirect to={"/verify-lading-code/2/" + this.state.redirectToManyVerifyCustomerOrderId}/>;
        }
        const {userPermissions} = this.props;
        const defaultPageSize = this.props.search.meta.per_page;
        const listRows = this.state.models.map(model => {
            return (
                <tr key={model.id}>
                    <td>{moment(model.created_at).format("DD/MM/YYYY")}</td>
                    <td>{model.lading_code}</td>
                    <td>{model.user && model.user.name}</td>
                    {/*<td>{model.is_gash_stamp == 0 && model.is_gash_stamp == 0 && model.is_error_size == 0
                    && model.is_error_color == 0  && model.is_exuberancy == 0  && model.is_inadequate == 0 ? 'Hàng nguyên vẹn' : 'Hàng không nguyên vẹn :'}
                        {model.is_gash_stamp == 1 && <div className="red-color"> Đã bóc tem </div>}
                        {model.is_broken_gash ==1  && <div className="red-color"> Bị vỡ, bẹp, rách </div>}
                        {model.is_error_size == 1 && <div className="red-color"> Sai màu <br/> </div>}
                        {model.is_error_color == 1 && <div className="red-color"> Sai cỡ<br/> </div>}
                        {model.is_exuberancy == 1  && <div className="red-color"> Thừa số lượng <br/> </div>}
                        {model.is_inadequate == 1 && <div className="red-color"> Thiếu số lượng <br/> </div>}
                    </td>*/}
                    <td className={(model.status==1 ? "red-color" : "")}>{model.status_name}
                    </td>
                    <td>{model.note && model.note}</td>
                    <td className="column-actions">
                       { <ActionButtons model={model} errorData ={this.state.error} setListState={this.setState.bind(this)}/>}
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

                    <div style={{ textAlign:'center',paddingBottom: '20px'}}>
                        Nhập mã vận đơn
                        <input  type="text" style={{display :'inline', width : '200px',marginLeft:'20px'}}
                                onChange={(e) => {
                                    this.setState({
                                        ladingCode: e.target.value,
                                    })
                                }}
                                onKeyPress={(e) => {
                                    if(e.key == 'Enter') {
                                        this.handleKeyPress();
                                    }
                                }}
                                placeholder="Nhập mã vận đơn" className="form-control"/>
                        <button type="button" onClick={(e) => {this.handleKeyPress()}} style={{marginLeft:'20px', marginBottom : '3px'}} className="btn btn-info">

                            <i className="fa fa-fw fa-search"/> Tiếp tục
                        </button>
                    </div>

                    <div className="table-responsive">
                        <table className="table table-hover">
                            <thead>
                            <tr>
                                <th>Ngày kiểm</th>
                                <th>Mã vận đơn</th>
                                <th>Nhân viên kiểm hàng</th>
                                <th>Tình trạng</th>
                                <th>Ghi chú</th>
                                <th>Xem/Xóa</th>
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

VerifyLadingCode.propTypes = {
    model: PropTypes.object,
    setListState: PropTypes.func,
};


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

export default connect(mapStateToProps, mapDispatchToProps)(VerifyLadingCode)
