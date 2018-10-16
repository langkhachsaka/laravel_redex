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
import Layout from "../../theme/components/Layout";
import PaginationPageSize from "../common/PaginationPageSize";
import moment from "moment";
import {Link} from "react-router-dom";


class Notification extends Component {

    constructor(props) {
        super(props);

        this.state = {
            models: [],
            meta: {
                pageCount: 0,
                page: 1,
                per_page: 10,
            },
            isLoading: true
        };
    }

    componentDidMount() {
        this.fetchData({});

        this.props.actions.changeThemeTitle("Thông báo");
    }

    fetchData(newParams) {
        this.setState({isLoading: true});

        const params = _.assign({}, this.state.meta);
        console.log(params);
        if (newParams.page) {
            this.setState(({meta}) => {
                meta.page = newParams.page;
                return {meta: meta};
            });
            params.page = newParams.page;
        }
        if (newParams.per_page) {
            this.setState(({meta}) => {
                meta.per_page = newParams.per_page;
                return {meta: meta};
            });
            params.per_page = newParams.per_page;
        }
        console.log(this.state.meta);
        console.log(params);

        return ApiService.get(Constant.resourcePath(), params).then(({data: {data}}) => {
            this.setState(prevState => {
                const newstate = {
                    models: data.data,
                    meta: prevState.meta,
                    isLoading: false,
                };
                newstate.meta.pageCount = data.last_page;

                return newstate;
            });
        });
    }

    render() {

        const listRows = this.state.models.map(model => {
            return (
                <tr key={model.id}>
                    <td>
                        <Link
                            to={Constant.getItemLink(model.notificationtable_id, model.notificationtable_type)}
                            onClick={() => {
                                if (model.is_read === Constant.STATUS_UNREAD) {
                                    ApiService.post(Constant.resourcePath(model.id), {is_read: Constant.STATUS_READ}).then(({data}) => {
                                        this.props.actions.updateNotification(
                                            this.props.userNotifications.map(n => n.id === model.id ? data.data : n),
                                            this.props.userNewNotificationCount - 1
                                        );
                                    });
                                }
                            }}
                        >
                            {model.is_read === Constant.STATUS_UNREAD &&
                                <span className="badge badge-default badge-danger mr-1">Mới</span>}
                            {model.content}
                        </Link>
                    </td>
                    <td>{moment(model.created_at).format("DD/MM/YYYY HH:mm")}</td>
                </tr>
            );
        });

        return (
            <Layout>
                <Card isLoading={this.state.isLoading}>

                    <div className="table-responsive">
                        <table className="table table-hover">
                            <thead>
                            <tr>
                                <th>Nội dung</th>
                                <th>Ngày thông báo</th>
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
                                    this.fetchData({page: data.selected + 1});
                                }}
                                currentPage={this.state.meta.page - 1}
                            />
                        </div>
                        <div className="col-sm-4 text-right mt-1">
                            Hiển thị mỗi trang <PaginationPageSize
                            defaultPageSize={this.state.meta.per_page}
                            onChange={(pageSize) => {
                                this.fetchData({per_page: pageSize, page: 1});
                            }}/> bản ghi
                        </div>
                    </div>
                </Card>

            </Layout>
        );
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions, themeActions), dispatch)
    }
}

export default connect(null, mapDispatchToProps)(Notification)
