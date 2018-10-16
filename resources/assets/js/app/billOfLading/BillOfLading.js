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
import ForbiddenPage from "../common/ForbiddenPage";
import ItemDeleteLadingCodeActionButton from './components/ItemDeleteLadingCodeActionButton';
import ItemEditLadingCodeActionButton from './components/ItemEditLadingCodeActionButton';
import ItemLadingCodeActionButton from './components/ItemLadingCodeActionButton';
import PropTypes from "prop-types";


class BillOfLading extends Component {

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

        this.props.actions.changeThemeTitle("Đơn hàng vận chuyển");
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
        if (!userPermissions.bill_of_lading || !userPermissions.bill_of_lading.index) return <ForbiddenPage/>;

        const defaultPageSize = this.props.search.meta.per_page;

        const listRows = this.state.models.map(model => {
            return (
                <tr key={model.id}>
                    <td>{model.id}</td>
                    <td>
                        {_.get(model, 'customer.name')}
                        {model.customer_address &&
                        <small><br/>{model.customer_address.phone}<br/>{model.customer_address.address}</small>}
                    </td>
                    <td>{_.get(model, 'courier_company.name')}</td>
                    <td>{_.get(model, 'seller.name')}</td>
                    <td>
                        <i className="ft-file-text"/> {model.file_name}<br/>
                        <a href={model.link_download_file} className="mr-2 d-inline-block">
                            <i className="ft-download"/> Tải xuống
                        </a>
                        <a href={model.link_view_file_online} target="_blank" className="d-inline-block">
                            <i className="ft-eye"/> Xem Online
                        </a>
                    </td>
                    <td>
                        {model.lading_codes.length > 0 &&
                        <div className={'row'}>
                            <div className={'col-sm-9'}>
                                {model.lading_codes.map((itm) => {
                                    return (
                                            <p  key={itm.code}><strong>{itm.code}</strong></p>
                                    );
                                })}
                            </div>
                            <div className={'col-sm-2'}>
                                <ItemEditLadingCodeActionButton model={model} setDetailState={this.setState.bind(this)}/>
                            </div>
                        </div>}

                        {model.lading_codes[0] == null &&
                        <ItemLadingCodeActionButton model={model} setDetailState={this.setState.bind(this)}/>
                        }
                    </td>
                    <td>
                        <div style={{marginBottom: '4px'}}>
                            <small className="font-italic">Ngày tạo</small>
                            <br/>{moment(model.created_at).format("DD/MM/YYYY")}
                        </div>
                        {!!model.end_date &&
                        <div>
                            <small className="font-italic">Ngày kết thúc</small>
                            <br/>{moment(model.end_date).format("DD/MM/YYYY")}
                        </div>}
                    </td>
                    <td>{model.status_name}</td>
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
                                <th>Khách hàng</th>
                                <th>Cty chuyển phát</th>
                                <th>NV CSKH</th>
                                <th>Tệp đính kèm</th>
                                <th>Mã vận đơn</th>
                                <th>Ngày tạo<br/>Ngày kết thúc</th>
                                <th>Trạng thái</th>
                                <th style={{width: '114px'}} className="pl-0 pr-0">
                                    {userPermissions.bill_of_lading.create &&
                                    <button className="btn btn-primary btn-sm btn-block" onClick={() => {
                                        this.props.actions.openMainModal(<Form
                                            setListState={this.setState.bind(this)}/>, "Thêm đơn hàng vận chuyển mới");
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
        search: state.billOfLading.search,
        userPermissions: state.auth.permissions,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(BillOfLading)
