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
import SearchForm from "./components/SearchForm";
import Layout from "../../theme/components/Layout";
import PaginationPageSize from "../common/PaginationPageSize";
import ForbiddenPage from "../common/ForbiddenPage";
import moment from "moment";
import {Link} from "react-router-dom";
import Form from "./components/Form";
import ActionButtons from "./components/ActionButtons";


class Complaint extends Component {

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

        this.props.actions.changeThemeTitle("Khiếu nại");
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
        if (!userPermissions.complaint || !userPermissions.complaint.index) return <ForbiddenPage/>;

        const defaultPageSize = this.props.search.meta.per_page;

        const listRows = this.state.models.map(model => {
            const canAccess = this.props.authUser.roles.indexOf(10) != -1 || model.performer_id == this.props.authUser.id;
            return (
                <tr key={model.id}>
                    <td>{model.customer_order_item && model.customer_order_item.customer_order_id}</td>
                    <td>{canAccess  ? <Link to={"/complaint/" + model.id}>{model.lading_code}</Link> : model.lading_code}</td>
                    <td>{model.customer_order_item_id}</td>
                    <td>{model.customer_order_item && model.customer_order_item.description}</td>
                    <td>{_.get(model, 'user_performer.name')}</td>
                    <td>{moment(model.created_at).format("DD/MM/YYYY")}</td>
                    <td>{model.status_name}</td>
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
                                <th>Mã vận đơn</th>
                                <th>Mã sản phẩm</th>
                                <th>Têm sản phẩm </th>
                                <th>NV xử lý</th>
                                <th>Ngày tạo</th>
                                <th>Trạng thái</th>
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
        search: state.complaint.search,
        userPermissions: state.auth.permissions,
        authUser : state.auth.user,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(Complaint)
