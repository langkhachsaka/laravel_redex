import React, {Component} from 'react';
import {Field, reduxForm} from 'redux-form';
import PropTypes from 'prop-types';

import * as commonActions from "../../common/meta/action";
import {connect} from "react-redux";
import _ from 'lodash';
import {bindActionCreators} from "redux";
import Select2Input from "../../../theme/components/Select2Input";
import AppConfig from "../../../config";
import UserConstant from "../../user/meta/constant";
import DatePickerInput from "../../../theme/components/DatePickerInput";
import ApiService from "../../../services/ApiService";
import Constant from "../meta/constant";
import SelectInput from "../../../theme/components/SelectInput";
import Picker from 'react-month-picker';
import '../../../../../../node_modules/react-month-picker/css/month-picker.css';
import MonthBoxTransaction from "./MonthBoxTransaction";


const pickerLang = {
    months: ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12']
    , from: 'Từ', to: 'Đến'
};

const makeText = m => {
    if (m && m.year && m.month)
        return (pickerLang.months[m.month - 1] + '/' + m.year);
    return '?';
};

//format YYYY/mm -> use in api
const makeMonthText = m => {
    if (m && m.year && m.month)
        return (m.year + '/' + pickerLang.months[m.month - 1]);
    return '?';
};
const makeMonthRangeText = mrange => {
    return makeText(mrange.from) + ' ~ ' + makeText(mrange.to);
};

const currentYear = (new Date()).getFullYear();


class SearchFormTransaction extends Component {

    constructor(props) {
        super(props);

        this.state = {
            startDate: null,
            endDate: null,

            searchDay: 'none',
            searchMonth: 'block',
            searchYear: 'none',
            mrange: {from: {year: currentYear, month: 1}, to: {year: currentYear, month: (new Date()).getMonth() + 1}},

            fromYear: currentYear - 15,
        };

        this.setSeller = this.setSeller.bind(this);
        this.setDisplayDate = this.setDisplayDate.bind(this);
        this._handleClickRangeBox = this._handleClickRangeBox.bind(this);
        this.handleRangeChange = this.handleRangeChange.bind(this);
        this.handleRangeDissmis = this.handleRangeDissmis.bind(this);
    }

    componentWillReceiveProps(nextProps) {
        this.setState({
            value: nextProps.value || 'N/A',
        })
    }

    componentDidMount() {
        this.fetchData();
        // const params = _.get(this.props, 'search.params', {});
        //
        const params = {
            group_by: "month",
            from_month: makeMonthText(this.state.mrange.from),
            to_month: makeMonthText(this.state.mrange.to)
        };
        this.props.initialize(params);
    }

    setSeller(e) {
        this.props.setStatisticalState({sellerName: e.target.textContent});
        // console.log(e.target.textContent);
    }

    setDisplayDate(e) {
        let newState;

        switch (e.target.value) {
            case 'day' :
                newState = {
                    searchDay: 'block',
                    searchMonth: 'none',
                    searchYear: 'none'
                };
                break;
            case 'month' :
                newState = {
                    searchDay: 'none',
                    searchMonth: 'block',
                    searchYear: 'none'
                };
                break;
            case 'year' :
                newState = {
                    searchDay: 'none',
                    searchMonth: 'none',
                    searchYear: 'block'
                };
                break;
            default :
                newState = {
                    searchDay: 'none',
                    searchMonth: 'block',
                    searchYear: 'none'
                };
                break;
        }

        this.setState(newState);
    }

    fetchData(params) {
        params = params || {};

        const apiSumTransactions = 'get-list-sum-transaction';

        ApiService.get(Constant.resourcePath(apiSumTransactions), params).then(({data}) => {
            this.props.setStatisticalState({
                sumTransactions: data.data,
                isLoadingTransaction: false,
            });
        });
    }

    handleFilterSubmit(formProps) {
        // this.props.actions.search(formProps);
        this.fetchData(formProps);
    }

    render() {
        const {handleSubmit, submitting, reset} = this.props;


        const mrange = this.state.mrange;

        const selectFromYear = [];
        selectFromYear.push(<option key='fromYear' value=""> </option>);
        for (let i = currentYear; i > currentYear - 15; i--) {
            selectFromYear.push(<option key={i} value={i}>{i}</option>);
        }

        const selectToYear = [];
        selectToYear.push(<option key='toYear' value=""> </option>);
        for (let i = currentYear; i >= this.state.fromYear; i--) {
            selectToYear.push(<option key={i} value={i}>{i}</option>);
        }

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleFilterSubmit.bind(this))}>
                <div className="row">
                    <div className="col-md-4">
                        <Field
                            name="seller_id"
                            component={Select2Input}
                            onChange={this.setSeller}
                            select2Options={{
                                placeholder: 'Tất cả',
                                allowClear: true,
                                ajax: {
                                    url: AppConfig.API_URL + UserConstant.resourcePath("list?role=" + UserConstant.ROLE_SELLER),
                                    delay: 250
                                }
                            }}
                            label="Nhân viên CSKH"
                        />
                    </div>
                    <div className="col-md-4">
                        <Field name="group_by" component={SelectInput} label="Gộp đơn giao dịch" onChange={this.setDisplayDate}>
                            <option value="month">Theo tháng</option>
                            <option value="day">Theo ngày</option>
                            <option value="year">Theo năm</option>
                        </Field>
                    </div>
                    <div className="col-md-4">
                        <div className="react-datepicker-range-wrapper" style={{display: this.state.searchDay}}>
                            <Field
                                name="created_at_from"
                                component={DatePickerInput}
                                selectsStart={true}
                                startDate={this.state.startDate}
                                endDate={this.state.endDate}
                                onDateChange={(date) => {
                                    const newState = {startDate: date};
                                    if (this.state.endDate && newState.startDate && newState.startDate.isAfter(this.state.endDate)) {
                                        newState.endDate = date;
                                    }
                                    this.setState(newState);
                                    this.props.change("created_at_from", newState.startDate ? newState.startDate.format("YYYY/MM/DD") : null);
                                    if (newState.endDate) {
                                        this.props.change("created_at_to", newState.endDate.format("YYYY/MM/DD"));
                                    }
                                }}
                                label="Thống kê từ ngày"
                            />
                            <Field
                                name="created_at_to"
                                component={DatePickerInput}
                                selectsEnd={true}
                                startDate={this.state.startDate}
                                endDate={this.state.endDate}
                                onDateChange={(date) => {
                                    const newState = {endDate: date};
                                    if (this.state.startDate && newState.endDate && newState.endDate.isBefore(this.state.startDate)) {
                                        newState.startDate = date;
                                    }
                                    this.setState(newState);
                                    this.props.change("created_at_to", newState.endDate ? newState.endDate.format("YYYY/MM/DD") : null);
                                    if (newState.startDate) {
                                        this.props.change("created_at_from", newState.endDate.format("YYYY/MM/DD"));
                                    }
                                }}
                                label="đến ngày"
                            />
                        </div>
                        <div id="month-pick" style={{display: this.state.searchMonth}}>
                            <Picker
                                ref="pickRange"
                                years={{min: currentYear - 15}}
                                range={mrange}
                                lang={pickerLang}
                                theme="light"
                                onChange={this.handleRangeChange}
                                onDismiss={this.handleRangeDissmis}
                            >
                                <MonthBoxTransaction value={makeMonthRangeText(mrange)} onClick={() => {
                                    this.refs.pickRange.show()
                                }}/>
                            </Picker>
                        </div>
                        <div id="year-pick" style={{display: this.state.searchYear}} className="row react-datepicker-range-wrapper">
                            <Field name="from_year" component={SelectInput} label="Từ năm" onChange={e => {
                                this.setState({fromYear: e.target.value});
                            }}>
                                {selectFromYear}
                            </Field>
                            <Field name="to_year" component={SelectInput} label="Đến năm">
                                {selectToYear}
                            </Field>
                        </div>
                    </div>
                </div>
                <div>
                    <button type="submit" className="btn btn-info" disabled={submitting}>
                        <i className="fa fa-fw fa-search"/> Thống kê
                    </button>
                    {' '}
                    <button type="button" className="btn btn-outline-warning" disabled={submitting} onClick={() => {
                        reset();
                        this.setState({
                            searchDay: 'none',
                            searchMonth: 'block',
                            searchYear: 'none',
                        });
                    }}>
                        <i className="fa fa-fw fa-refresh"/> Reset
                    </button>
                </div>
            </form>
        );
    }

    _handleClickRangeBox(e) {
        this.refs.pickRange.show()
    }

    handleRangeChange(value, text, listIndex) {
        //
    }

    handleRangeDissmis(value) {
        this.props.change("from_month", makeMonthText(value.from));
        this.props.change("to_month", makeMonthText(value.to));
        this.setState({mrange: value})
    }
}

SearchFormTransaction.propTypes = {
    handleSubmit: PropTypes.func.isRequired,
    submitting: PropTypes.bool.isRequired,
    setStatisticalState: PropTypes.func,
};

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions), dispatch)
    }
}

export default connect(null, mapDispatchToProps)(reduxForm({
    form: 'SearchFormTransactionFilterForm',
})(SearchFormTransaction))
