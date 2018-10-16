import React, {Component} from 'react';
import {connect} from "react-redux";
import _ from 'lodash';
import {Line} from 'react-chartjs-2';
import {bindActionCreators} from "redux";

import ForbiddenPage from "../common/ForbiddenPage";
import Layout from "../../theme/components/Layout";
import Constant from "../statistical/meta/constant";
import * as commonActions from "../common/meta/action";
import * as themeActions from "../../theme/meta/action";
import Card from "../../theme/components/Card";
import SearchForm from "./components/SearchForm";
import SearchFormTransaction from "./components/SearchFormStransaction";
import formatMoney from "../../helpers/Formatter";


class Statistical extends Component {
    constructor(props) {
        super(props);

        this.state = {
            sumCustomerOrder: [],
            search: {},
            isLoading: true,
            sellerName: '',

            // state transaction
            sumTransactions: [],
            searchTransaction: {},
            isLoadingTransaction: true,

        };
    }

    componentDidMount() {
        this.props.actions.changeThemeTitle("Thống kê");
    }

    render() {
        const {userPermissions} = this.props;
        if (!userPermissions.statistical || !userPermissions.statistical.index) return <ForbiddenPage/>;

        const optionsChart = {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value, index, values) {
                            // Convert the number to a string and splite the string every 3 charaters from the end
                            value = value.toString();
                            value = value.split(/(?=(?:...)*$)/);

                            // Convert the array to a string and format the output
                            value = value.join(',');
                            return value;
                        }
                    }
                }]
            },
            tooltips: {
                mode: 'index',
                position: 'nearest',
                callbacks: {
                    label: function(tooltipItem, data) {
                        let label = data.datasets[tooltipItem.datasetIndex].label || '';
                        let value = tooltipItem.yLabel + '';

                        if (label) {
                            label += ': ';
                        }

                        value = value.split(/(?=(?:...)*$)/);
                        value = value.join(',');

                        return [label + value];
                    }
                }
            }
        };

        const dataSumCustomerOrder = {
            labels: this.state.sumCustomerOrder.map(m => m.date),
            datasets: [
                {
                    label: "Đơn hàng VN",
                    backgroundColor: 'rgba(0, 0, 0, 0)',
                    borderColor: 'rgb(255, 99, 132)',
                    lineTension: 0,
                    data: this.state.sumCustomerOrder.map(m => m.sum),
                }
            ]
        };

        const dataReportCustomerOrder = this.state.sumCustomerOrder.map(m => {
            return (
                <tr key={m.date}>
                    <td>{m.date}</td>
                    <td>{formatMoney.money(m.sum)}</td>
                </tr>
            )
        });

        const dataSumTransaction = {
            labels: this.state.sumTransactions.map(m => m.date),
            datasets: [
                {
                    label: "Tổng tiền",
                    backgroundColor: 'rgba(0, 0, 0, 0)',
                    borderColor: 'rgb(0, 0, 102)',
                    lineTension: 0,
                    data: this.state.sumTransactions.map(m => m.sum),
                },
                {
                    label: "Tổng giao dịch",
                    backgroundColor: 'rgba(0, 0, 0, 0)',
                    borderColor: 'rgb(255, 99, 132)',
                    lineTension: 0,
                    data: this.state.sumTransactions.map(m => m.count),
                },
            ]
        };

        const dataReportTransaction = this.state.sumTransactions.map(m => {
            return (
                <tr key={m.date}>
                    <td>{m.date}</td>
                    <td>{m.count}</td>
                    <td>{formatMoney.money(m.sum)}</td>
                </tr>
            )
        });

        return (
            <Layout>
                <ul className="nav nav-tabs" role="tablist">
                    <li className="nav-item">
                        <a className="nav-link active" href="#customer-order" role="tab" data-toggle="tab">Đơn hàng Việt Nam</a>
                    </li>
                    <li className="nav-item">
                        <a className="nav-link" href="#transaction" role="tab" data-toggle="tab">Giao dịch</a>
                    </li>
                </ul>
                <div className="tab-content">
                    <div role="tabpanel" className="tab-pane fade in active show" id="customer-order">
                        <Card>
                            <SearchForm setStatisticalState={this.setState.bind(this)}/>
                        </Card>
                        <Card isLoading={this.state.isLoading}>
                            <Line data={dataSumCustomerOrder} options={optionsChart}/>
                        </Card>
                        <Card isLoading={this.state.isLoading}>
                            <div className="row">
                                <strong className="col-12 text-center p-2" style={{fontSize: '2rem'}}>Thống kê đơn hàng của nhân
                                    viên {this.state.sellerName}</strong>
                                <table className="table table-hover">
                                    <thead>
                                    <tr>
                                        <th scope="col">Thời gian</th>
                                        <th scope="col">Số đơn hàng</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {dataReportCustomerOrder}
                                    </tbody>
                                </table>
                            </div>
                        </Card>
                    </div>
                    <div role="tabpanel" className="tab-pane fade in active" id="transaction">
                        <Card>
                            <SearchFormTransaction setStatisticalState={this.setState.bind(this)}/>
                        </Card>
                        <Card isLoading={this.state.isLoading}>
                            <Line data={dataSumTransaction} options={optionsChart}/>
                        </Card>
                        <Card isLoading={this.state.isLoading}>
                            <div className="row">
                                <strong className="col-12 text-center p-2" style={{fontSize: '2rem'}}>Thống kê {this.state.sellerName}</strong>
                                <table className="table table-hover">
                                    <thead>
                                    <tr>
                                        <th scope="col">Thời gian</th>
                                        <th scope="col">Số giao dịch</th>
                                        <th scope="col">Tổng tiền</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        {dataReportTransaction}
                                    </tbody>
                                </table>
                            </div>
                        </Card>
                    </div>
                </div>
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

export default connect(mapStateToProps, mapDispatchToProps)(Statistical)
