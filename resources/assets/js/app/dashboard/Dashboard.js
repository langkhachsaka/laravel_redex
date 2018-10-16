import React, {Component} from 'react';
import Layout from "../../theme/components/Layout";
import Widget from "../../theme/components/Widget";
import * as themeActions from "../../theme/meta/action";
import {connect} from "react-redux";
import {bindActionCreators} from "redux";
import _ from 'lodash';
import Constant from "../dashboard/meta/constant";
import ApiService from "../../services/ApiService";

class Dashboard extends Component {

    constructor(props) {
        super(props);

        this.state = {
            sumCustomerOrder: '',
            sumCustomerOrderFinished: ''
        };
    }

    componentDidMount() {
        this.fetchData();

        this.props.actions.changeThemeTitle("Dashboard");
    }

    componentWillReceiveProps(nextProps) {
        this.fetchData(nextProps.search);
    }

    fetchData() {

        const apiSumCustomerOrderName = 'get-sum-customer-order';
        const apiSumCustomerOrderFinishedName = 'get-sum-customer-order-finished';

        ApiService.get(Constant.resourceStatisticalPath(apiSumCustomerOrderName)).then(({data}) => {
            this.setState({
                sumCustomerOrder: data.data
            });
        });

        ApiService.get(Constant.resourceStatisticalPath(apiSumCustomerOrderFinishedName)).then(({data}) => {
            this.setState({
                sumCustomerOrderFinished: data.data
            });
        });

    }


    render() {
        return (
            <Layout>
                <div className={'row'}>
                    <Widget title={'Tổng đơn hàng VN'} strShow={this.state.sumCustomerOrder + ''} classBackground={'bg-danger'}></Widget>
                    <Widget title={'Tổng đơn hàng VN đã hoàn thành'} strShow={this.state.sumCustomerOrderFinished + ''} classBackground={'bg-success'}></Widget>
                </div>
            </Layout>
        );
    }
}


function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, themeActions), dispatch)
    }
}

export default connect(null, mapDispatchToProps)(Dashboard)