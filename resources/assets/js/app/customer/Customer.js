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


class Customer extends Component {

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

        this.props.actions.changeThemeTitle("Khách hàng");
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
        if (!userPermissions.customer || !userPermissions.customer.index) return <ForbiddenPage/>;

        const defaultPageSize = this.props.search.meta.per_page;

        const listRows = this.state.models.map(model => {
            return (
                <tr key={model.id}>
                    <td>{model.id}</td>
                    <td>{model.name}</td>
                    <td>{model.username}</td>
                    <td>{_.get(model, 'customer_address.phone')}</td>
                    <td>{model.email}</td>
                    <td>{_.get(model, 'customer_address.address')}</td>
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
                                <th>Mã KH</th>
                                <th>Tên</th>
                                <th>Tên tài khoản</th>
                                <th>Điện thoại</th>
                                <th>Email</th>
                                <th>Địa chỉ</th>
                                <th style={{width: '170px'}}>
                                    {userPermissions.customer.create &&
                                    <button className="btn btn-primary btn-sm btn-block" onClick={() => {
                                        this.props.actions.openMainModal(<Form
                                            setListState={this.setState.bind(this)}/>, "Thêm Khách hàng mới");
                                    }}><i className="ft-plus"/>{' '} Thêm
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
        search: state.customer.search,
        userPermissions: state.auth.permissions,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(Customer)
