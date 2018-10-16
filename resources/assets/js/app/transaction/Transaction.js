import React, {Component} from 'react';
import {connect} from "react-redux";
import _ from 'lodash';
import {bindActionCreators} from "redux";

import ForbiddenPage from "../common/ForbiddenPage";
import Layout from "../../theme/components/Layout";
import Constant from "../transaction/meta/constant";
import * as commonActions from "../common/meta/action";
import * as themeActions from "../../theme/meta/action";
import Card from "../../theme/components/Card";
import SearchForm from "./components/SearchForm";
import ApiService from "../../services/ApiService";
import Paginate from "../common/Paginate";
import PaginationPageSize from "../common/PaginationPageSize";
import ActionButtons from "./components/ActionButtons";
import TransactionConstant from "./meta/constant";
import formatMoney from "../../../js/helpers/Formatter";

class Transaction extends Component {
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

        this.props.actions.changeThemeTitle("Giao dịch");
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
        if (!userPermissions.transaction || !userPermissions.transaction.index) return <ForbiddenPage/>;

        const defaultPageSize = this.props.search.meta.per_page;

        const listRows = this.state.models.map(model => {
            let typeOrder = '';
            let button;

            if (model.transactiontable_type != null) {
                switch(model.transactiontable_type) {
                    case TransactionConstant.MORPH_TYPE_ORDER_VN :
                        typeOrder = 'Đơn hàng Việt Nam';
                        break;
                    case TransactionConstant.MORPH_TYPE_BILL_OF_LADING :
                        typeOrder = 'Đơn hàng vận chuyển';
                        break;
                    case TransactionConstant.CUSTOMER :
                        typeOrder = 'Khách hàng';
                        break;
                    default:
                        typeOrder = 'Giao dịch';
                }
            }

            return (
                <tr key={model.id}>
                    <td>{formatMoney.money(model.money)}</td>
                    <td>{model.type_name}</td>
                    <td>{typeOrder} #{model.transactiontable_id}</td>
                    <td>{model.note}</td>
                    <td>{model.status_name}</td>
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
                                <th>Số tiền</th>
                                <th>Loại giao dịch</th>
                                <th>Loại đơn hàng</th>
                                <th>Ghi chú</th>
                                <th>Trạng thái</th>
                                <th style={{width: '60px'}}>
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
        search: state.transaction.search,
        userPermissions: state.auth.permissions,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(Transaction)
