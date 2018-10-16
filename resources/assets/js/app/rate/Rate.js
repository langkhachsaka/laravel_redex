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
import Form from "./components/Form";
import ActionButtons from "./components/ActionButtons";
import Layout from "../../theme/components/Layout";
import PaginationPageSize from "../common/PaginationPageSize";
import {Link} from "react-router-dom";
import moment from "moment/moment";
import ForbiddenPage from "../common/ForbiddenPage";

class Rate extends Component {

    constructor(props) {
        super(props);

        this.state = {
            models: [],
            meta: {pageCount: 0, currentPage: 1},
            isLoading: true,
        };
    }

    componentDidMount() {
        this.fetchData();

        this.props.actions.changeThemeTitle("Tỷ giá");
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
        if (!userPermissions.shop || !userPermissions.shop.index) return <ForbiddenPage/>;

        const defaultPageSize = this.props.search.meta.per_page;
        const listRows = this.state.models.map(model => {
            return (
                <tr key={model.id}>
                    <td><Link to={"/rate/" + model.id}>{model.id}</Link></td>
                    <td>{moment(model.date).format("DD/MM/YYYY")}</td>
                    <td>{model.buying_rate}</td>
                    <td>{model.transfer_rate}</td>
                    <td>{model.payment_rate}</td>
                    <td>{model.order_rate}</td>
                    <td className="column-actions pl-0 pr-0">
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
                                <th>ID</th>
                                <th>Ngày</th>
                                <th>Tỷ giá mua vào</th>
                                <th>Tỷ giá chuyển khoản</th>
                                <th>Tỷ giá nhờ thanh toán</th>
                                <th>Tỷ giá order</th>
                                <th style={{width: '114px'}} className="pl-0 pr-0">
                                    {userPermissions.rate.create &&
                                    <button className="btn btn-primary btn-sm btn-block" onClick={() => {
                                        this.props.actions.openMainModal(<Form
                                            setListState={this.setState.bind(this)}/>, "Thêm tỷ giá mới");
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
        search: state.rate.search,
        userPermissions: state.auth.permissions,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(Rate)
