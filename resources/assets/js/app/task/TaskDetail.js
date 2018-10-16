import React, {Component} from 'react';
import {connect} from "react-redux";
import _ from 'lodash';
import {bindActionCreators} from "redux";

import ApiService from "../../services/ApiService";
import ArrayHepper from "../../helpers/Array";
import * as themeActions from "../../theme/meta/action";
import * as commonActions from "../common/meta/action";
import Constant from './meta/constant';
import Card from "../../theme/components/Card";
import PropTypes from "prop-types";
import Form from "./components/Form";
import Layout from "../../theme/components/Layout";
import PaginationPageSize from "../common/PaginationPageSize";
import moment from "moment";
import {Link} from "react-router-dom";
import ForbiddenPage from "../common/ForbiddenPage";

class TaskDetail extends Component {

    constructor(props) {
        super(props);

        this.state = {
            model: {
                tasks_update : []
            },
            isLoading: true
        };
    }

    componentDidMount() {
        this.fetchModel(this.getModelId());
        this.props.actions.changeThemeTitle("Chi tiết công việc");
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
        ApiService.get(Constant.resourcePath(id)).then(({data}) => {
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
        const {model} = this.state;

        var authRoles = this.props.authUser.roles;
        var taskType = model.task_type;
        var displayUpdateButton = true;
        if (model.status == Constant.ORDER_DELETED_STATUS) {
            displayUpdateButton = false;
        }
        console.log(taskType == Constant.TYPE_RECEIVE );
        var allowAccess = false;
        if(taskType){
            ArrayHepper.twoArrayHasSameElement(authRoles,Constant.TYPE_CUSTOMER_SERVICE_ROLE)
            if( ArrayHepper.twoArrayHasSameElement(authRoles,Constant.TYPE_CUSTOMER_SERVICE_ROLE) && taskType == Constant.TYPE_CUSTOMER_SERVICE){
            /*displayUpdateButton = true;*/
                allowAccess = true;
            }else if(ArrayHepper.twoArrayHasSameElement(authRoles,Constant.TYPE_ORDERING_ROLE) && taskType == Constant.TYPE_ORDERING ) {
                /*displayUpdateButton = true;*/
                allowAccess = true;
            }else if(ArrayHepper.twoArrayHasSameElement(authRoles,Constant.TYPE_DELIVERING_AND_RECEIVING_ROLE) && taskType == Constant.TYPE_DELIVERING_AND_RECEIVING) {
                /*displayUpdateButton = true;*/
                allowAccess = true;
            } else if(ArrayHepper.twoArrayHasSameElement(authRoles,Constant.TYPE_ACCOUNTANT_ROLE) && taskType == Constant.TYPE_ACCOUNTANT) {
                /*displayUpdateButton = true;*/
                allowAccess = true;
            }
            else if(ArrayHepper.twoArrayHasSameElement(authRoles,Constant.TYPE_COMPLAINT_ROLE) && taskType == Constant.TYPE_COMPLAINT) {
                /*displayUpdateButton = true;*/
                allowAccess = true;
            }
            else if(ArrayHepper.twoArrayHasSameElement(authRoles,Constant.TYPE_VERIFY_ROLE) != -1 && taskType == Constant.TYPE_VERIFY) {
                /*displayUpdateButton = true;*/
                allowAccess = true;
            } else if(ArrayHepper.twoArrayHasSameElement(authRoles,Constant.TYPE_RECEIVE_ROLE)&& taskType == Constant.TYPE_RECEIVE) {
                /*displayUpdateButton = true;*/
                allowAccess = true;
            } else if(ArrayHepper.twoArrayHasSameElement(authRoles,Constant.TYPE_DELIVERY_ROLE)&& taskType == Constant.TYPE_DELIVERY) {
                /*displayUpdateButton = true;*/
                allowAccess = true;
            }
            if(!allowAccess) {
                return <ForbiddenPage/>;
            }
        }
        
        const listUpdate = model.tasks_update.map((item, index) => {
            if(index>=1) {
                var previousItem = model.tasks_update[(index - 1)];

               return (

                    <div key={item.id}>

                        <div className="bar-dot-bottom">
                            <span>
                            Cập nhật bởi <b><i>{item.user_updater ? item.user_updater.name : "Khách hàng"}</i></b> lúc <i>{item.created_at} </i>
                            </span>
                            <span style={{float: 'right'}}>
                            #{index }
                            </span>
                        </div>
                        {previousItem.title != item.title && <div > ● <b>Tiêu đề</b> thay đổi từ <b><i>{previousItem.title}</i></b> thành <b><i>{item.title}</i></b></div> }
                        {previousItem.description != item.description && <div>
                            ● <b>Mô tả</b> thay đổi từ <i><div className = "change-description" dangerouslySetInnerHTML={{__html: previousItem.description}}/></i> 
                            thành <i><div className = "change-description" dangerouslySetInnerHTML={{__html: item.description}}/></i></div> }
                        {previousItem.status != item.status && <div>● <b>Trạng thái</b> thay đổi từ <b><i>{previousItem.status_name }</i></b> thành <b><i>{item.status_name}</i></b></div>}
                        {previousItem.start_date != item.start_date && <div>● <b>Ngày bắt đầu</b> thay đổi từ <b><i>{previousItem.start_date }</i></b> thành <b><i>{item.start_date}</i></b></div>}
                        {previousItem.end_date != item.end_date && <div>● <b>Ngày kết thúc</b> thay đổi từ <b><i>{previousItem.end_date }</i></b> thành <b><i>{item.end_date}</i></b></div>}
                        {item.performer_id != previousItem.performer_id && <div>● <b>Người thực hiện</b> thay đổi từ <b><i>{previousItem.performer_id ? previousItem.user_performer.name : "Chưa chỉ định" }</i></b> thành <b><i>{item.performer_id ? item.user_performer.name : "Chưa chỉ định"}</i></b></div>}
                        {item.complaint_id != previousItem.complaint_id && <div>● <b>Có {item.complaint_id.split("_").length} khiếu nại được tạo : </b>
                            {item.complaint_id && item.complaint_id.indexOf("_") == -1 && <Link to={"/complaint/" + model.complaint_id}>{model.complaint_id}</Link> }
                            {item.complaint_id && item.complaint_id.indexOf("_") != -1 && item.complaint_id.split("_").map(item2 =>{
                                return (
                                    <span><Link to={"/complaint/" + item2}>{item2}</Link> </span>
                                );
                            }) }
                        </div>}
                        {}
                        {item.transaction_id&& model.task_type == Constant.TYPE_ACCOUNTANT && <div>● <b>Mã giao dịch : </b>{<Link to={"/transaction/" + item.transaction_id}>{item.transaction_id}</Link>}</div>}
                        {item.comment &&
                        <div>● <b>Nội dung</b>
                            <div className="comment-update" >
                                {item.comment}
                            </div>
                        </div>}
                        <br/>
                    </div>
                );
            }

        });
        return (
            <Layout>

                

                <Card isLoading={this.state.isLoading}>
                    
                    <span>
                        <b><h2>{model.title}</h2></b>
                    </span>

                     <div className="row"> 
                        <div className="col-sm-10">
                            Thêm bởi <b>{_.get(model, 'user_creator.name') ? _.get(model, 'user_creator.name') : "Khách hàng"}</b> lúc <small className="font-italic">{model.created_at} </small>
                        </div>
                    </div>    
                    <div className="row">
                        <div className="col-sm-5">
                            <div className="row">
                                <div className="col-sm-5">
                                    <b>Trạng thái</b>
                                </div>
                                <div className="col-sm-7">
                                    {model.status_name}
                                </div>
                            </div>
                            <div className="row">
                                <div className="col-sm-5">
                                    <b>Người thực hiện</b>
                                </div>
                                <div className="col-sm-7">
                                    {_.get(model, 'user_performer.name') ? _.get(model, 'user_performer.name') + ' ( ' + _.get(model, 'user_performer.role_name') + ' )' : "Chưa chỉ định"}
                                </div>
                            </div>
                            <div className="row">
                                <div className="col-sm-5">
                                    <b>Mã đơn hàng</b>
                                </div>
                                <div className="col-sm-7">
                                    {model.customer_order_id ? model.customer_order ? <Link to={"/customer-order/" + model.customer_order_id}>{model.customer_order_id}</Link> : model.customer_order_id+' ( Đã xóa)' : ''}
                                </div>
                            </div>
                            {model.task_type == Constant.TYPE_VERIFY &&
                            <div>
                                <b> Mã vận đơn cần kiểm tra :</b> {model.lading_codes}

                            </div>}
                        </div>
                        <div className="col-sm-5">
                            <div className="row">
                                <div className="col-sm-5">
                                    <b>Ngày bắt đầu</b>
                                </div>
                                <div className="col-sm-7">
                                    {model.start_date && moment(model.start_date).format("DD/MM/YYYY")}
                                </div>
                            </div>
                            <div className="row">
                                <div className="col-sm-5">
                                    <b>Ngày kết thúc</b>
                                </div>
                                <div className="col-sm-7">
                                    {model.end_date && moment(model.end_date).format("DD/MM/YYYY")}
                                </div>
                            </div>
                            <div className="row">
                                <div className="col-sm-5">
                                    <b>Khiếu nại</b>
                                </div>
                                <div className="col-sm-7">
                                    {model.complaint_id && model.complaint_id.indexOf("_") == -1 && <Link to={"/complaint/" + model.complaint_id}>{model.complaint_id}</Link> }
                                    {model.complaint_id && model.complaint_id.indexOf("_") != -1 && model.complaint_id.split("_").map(item =>{
                                    return (
                                        <span><Link to={"/complaint/" + item}>{item}</Link> </span>
                                    );
                                    }) }
                                </div>
                            </div>
                        </div>


                        <div className="col-sm-2">
                                <Link key="link-back" to={"/task"}
                                      className="btn btn-warning btn-sm btn-block">
                                    <i className="ft-corner-up-left"/> Quay lại
                                </Link>
                                { displayUpdateButton && <button className="btn btn-primary btn-sm btn-block"  onClick={() => {
                                    this.props.actions.openMainModal(<Form model={model} setDetailState={this.setState.bind(this)}/>, "Cập nhật thông tin công việc");
                                }}><i className="ft-edit"/>{' '} Cập nhật
                                </button> }

                        </div>
                    </div>

                    <hr/>
                    <h4>Mô tả </h4>
                    <div dangerouslySetInnerHTML={{__html: model.description}}/>
                    <hr/>
                    <h4>Lịch sử chỉnh sửa</h4>
                    
                    {listUpdate}
                   
                </Card>

            </Layout>
        );
    }
}

TaskDetail.propTypes = {
    model: PropTypes.object,
    setListState: PropTypes.func
};

function mapStateToProps({auth}) {
    return {
        authUser: auth.user
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(TaskDetail)
