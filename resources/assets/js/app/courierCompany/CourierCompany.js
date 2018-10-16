import React, {Component} from 'react';
import {connect} from "react-redux";
import _ from 'lodash';
import {bindActionCreators} from "redux";

import ApiService from "../../services/ApiService";
import * as themeActions from "../../theme/meta/action";
import * as commonActions from "../common/meta/action";
import Constant from './meta/constant';
import Card from "../../theme/components/Card";
import ActionButtons from "./components/ActionButtons";
import Form from "./components/Form";
import Layout from "../../theme/components/Layout";
import ForbiddenPage from "../common/ForbiddenPage";


class CourierCompany extends Component {

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

        this.props.actions.changeThemeTitle("Công ty chuyển phát nhanh");
    }

    componentWillReceiveProps(nextProps) {
        this.fetchData(nextProps.search);
    }

    componentWillUnmount() {
        this.props.actions.clearState();
    }

    fetchData() {
        this.setState({isLoading: true});

        return ApiService.get(Constant.resourcePath()).then(({data}) => {
            this.setState({
                models: data.data,
                isLoading: false,
            });
        });
    }

    render() {
        const {userPermissions} = this.props;
        if (!userPermissions.courier_company || !userPermissions.courier_company.index) return <ForbiddenPage/>;

        const listRows = this.state.models.map(model => {
            return (
                <tr key={model.id}>
                    <td>{model.name}</td>
                    <td className="column-actions">
                        <ActionButtons model={model} setListState={this.setState.bind(this)}/>
                    </td>
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
                                <th>Tên</th>
                                <th style={{width: '132px'}}>
                                    {userPermissions.courier_company.create &&
                                    <button className="btn btn-primary btn-sm btn-block" onClick={() => {
                                        this.props.actions.openMainModal(<Form
                                            setListState={this.setState.bind(this)}/>, "Thêm công ty chuyển phát mới");
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
                </Card>

            </Layout>
        );
    }
}

function mapStateToProps(state) {
    return {
        userPermissions: state.auth.permissions,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions, themeActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(CourierCompany)
