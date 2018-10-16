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
import moment from "moment";
import {Link, Redirect} from "react-router-dom";

class Task extends Component {

    constructor(props) {
        super(props);

        this.state = {
            models: [],
            meta: {pageCount: 0, currentPage: 1},
            isLoading: true
        };
    }

    componentDidMount() {
        this.fetchData();

        this.props.actions.changeThemeTitle("Nhiệm vụ");
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
        const defaultPageSize = this.props.search.meta.per_page;
        const listRows = this.state.models.map(model => {

            var taskDone = Constant.COMPLETE_STATUS.indexOf(model.status)!= -1;
            var creatorUndefine = "";
            var creatorName = "";
            if( model.user_creator) {
                creatorUndefine = "normal-text";
                creatorName = model.user_creator.name;
            } else {
                creatorUndefine = "blue-color-text";     
                creatorName = "Khách hàng"   
            }

            var performerUndefine = "";
            var performerName = "";
            if( model.user_performer) {
                performerUndefine = "normal-text";
                performerName = model.user_performer.name;
            } else {
                performerUndefine = "blue-color-text";     
                performerName = "Chưa chỉ định"   
            }


            return (
                <tr key={model.id} className = {model.status == Constant.ORDER_DELETED_STATUS ? "darkgrey-color" : ""}>
                    <td>
                        {model.customer_order_id ? model.customer_order ? <Link to={"/customer-order/" + model.customer_order_id}>{model.customer_order_id}</Link> : model.customer_order_id+' ( Đã xóa)' : ''}
                    </td>
                    <td><Link to={"/task/" + model.id}>{model.title}</Link></td>
                    <td className={taskDone ? "green-color" : ""}>{model.status_name}</td>
                    <td >
                        {model.complaint_id && model.complaint_id.indexOf("_") == -1 && <Link to={"/complaint/" + model.complaint_id}>{model.complaint_id}</Link> }
                        {model.complaint_id && model.complaint_id.indexOf("_") != -1 && model.complaint_id.split("_").map(item =>{
                            return (
                                <span><Link key={item} to={"/complaint/" + item}>{item}</Link> </span>
                            );
                        }) }
                        {!model.complaint_id && "Không có"}
                    </td>
                    <td>{model.start_date && moment(model.start_date).format("DD/MM/YYYY")}</td>
                    <td>{model.end_date && moment(model.end_date).format("DD/MM/YYYY")}</td>
                    <td><strong className = {creatorUndefine}>{creatorName}</strong> </td>
                    <td> <strong className = {performerUndefine}>{performerName}</strong> </td>
                    <td className="column-actions">
                        <ActionButtons model={model} setListState={this.setState.bind(this)}/>
                    </td>
                </tr>
            );
        });

        return (
            <Layout>
                <Card>
                    <SearchForm/>
                </Card>

                <Card isLoading={this.state.isLoading}>

                    <div className="table-responsive">
                        <table className="table table-hover">
                            <thead>
                            <tr>
                                <th>Mã đơn hàng</th>
                                <th>Tiêu đề</th>
                                <th>Trạng thái</th>
                                <th>Khiếu nại</th>
                                <th>Ngày bắt đầu</th>
                                <th>Ngày kết thúc</th>
                                <th>Người tạo</th>
                                <th>Người thực hiện</th>
                                <th style={{width: '170px'}}>
                                    {/*<button className="btn btn-primary btn-sm btn-block" onClick={() => {
                                        this.props.actions.openMainModal(<Form
                                            setListState={this.setState.bind(this)}/>, "Thêm công việc mới");
                                    }}><i className="ft-plus"/>{' '} Thêm
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

function mapStateToProps({task}) {
    return {
        search: task.search,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(Task)
