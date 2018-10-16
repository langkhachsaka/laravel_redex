import React, {Component} from 'react';
import {connect} from "react-redux";
import _ from 'lodash';
import {bindActionCreators} from "redux";
import {Field, FieldArray, reduxForm} from 'redux-form';

import ApiService from "../../services/ApiService";
import * as themeActions from "../../theme/meta/action";
import * as commonActions from "../common/meta/action";
import Constant from './meta/constant';
import Layout from "../../theme/components/Layout";
import Card from "../../theme/components/Card";
import TextInput from "../../theme/components/TextInput";
import ForbiddenPage from "../common/ForbiddenPage";
import {toastr} from "react-redux-toastr";

class Rate extends Component {

    constructor(props) {
        super(props);

        this.state = {
            id: '',
            com: '',
            nu: '',
            show: 0 ,
            muti: 1,
            order: 'desc'
        };
        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    componentDidMount() {
        this.props.actions.changeThemeTitle("Tracking");
    }

    componentWillUnmount() {
        this.props.actions.clearState();
    }

    handleChange(e) {
        this.setState({nu: e.target.value});
    }

    handleSubmit(e) {
        e.preventDefault();
        var params = this.state;
        return ApiService.get(Constant.resourcePath(), params)
            .then(({data}) => {
                console.log(data.data.message);
            });
    }

    render() {
        const {userPermissions} = this.props;
        if (!userPermissions.shop || !userPermissions.shop.index) return <ForbiddenPage/>;

        return (
            <Layout>
                <Card>
                    <form className="form-horizontal" onSubmit={this.handleSubmit}>
                        <div className="search-box">
                            <input type="text" name="order_code" value={this.state.nu} className="order-code" placeholder="Nhập mã đơn hàng" onChange={this.handleChange}/>
                            <button type="submit" className="btn btn-search la la-search"></button>
                        </div>
                    </form>
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

export default connect(mapStateToProps, mapDispatchToProps)(Rate)
