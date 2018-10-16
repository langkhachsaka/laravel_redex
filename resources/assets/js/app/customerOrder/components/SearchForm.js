import React, {Component} from 'react';
import {Field, reduxForm} from 'redux-form';
import PropTypes from 'prop-types'
import * as commonActions from "../../common/meta/action";
import {connect} from "react-redux";
import _ from 'lodash';
import {bindActionCreators} from "redux";
import SelectInput from "../../../theme/components/SelectInput";
import AppConfig from "../../../config";
import Select2Input from "../../../theme/components/Select2Input";
import DatePickerInput from "../../../theme/components/DatePickerInput";
import Constant from "../meta/constant";
import CustomerConstant from "../../customer/meta/constant";
import UserConstant from "../../user/meta/constant";


class SearchForm extends Component {

    constructor(props) {
        super(props);

        this.state = {
            startDate: null,
            endDate: null,
        };
    }

    componentDidMount() {
        const params = _.get(this.props, 'search.params', {});

        this.props.initialize(params);
    }

    handleFilterSubmit(formProps) {
        if (formProps.created_at_from) formProps.created_at_from += " 00:00:00";
        if (formProps.created_at_to) formProps.created_at_to += " 23:59:59";

        this.props.actions.search(formProps);
    }

    render() {
        const searchDisabled = _.get(this.props.userPermissions, 'customer_order.search_disabled', {});
        const {handleSubmit, submitting, reset} = this.props;

        return (
            <form className="form-horizontal" onSubmit={handleSubmit(this.handleFilterSubmit.bind(this))}>

                <div className="row">
                    <div className="col-sm-3">
                        <Field
                            name="customer_id"
                            component={Select2Input}
                            select2Options={{
                                placeholder: 'Tất cả',
                                allowClear: true,
                                ajax: {
                                    url: AppConfig.API_URL + CustomerConstant.resourcePath("list"),
                                    delay: 250
                                }
                            }}
                            label="Khách hàng"
                        />
                    </div>
                    <div className="col-sm-3">
                        <Field
                            name="seller_id"
                            component={Select2Input}
                            select2Options={{
                                placeholder: searchDisabled.seller_id ? this.props.authUser.name : 'Tất cả',
                                allowClear: true,
                                ajax: {
                                    url: AppConfig.API_URL + UserConstant.resourcePath("list?role=" + UserConstant.ROLE_SELLER),
                                    delay: 250
                                }
                            }}
                            label="Nhân viên CSKH"
                            disabled={searchDisabled.seller_id}
                        />
                    </div>
                    <div className="col-sm-3">
                        <div className="react-datepicker-range-wrapper">
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
                                    this.props.change("created_at_from", newState.startDate ? newState.startDate.format("YYYY-MM-DD") : null);
                                    if (newState.endDate) {
                                        this.props.change("created_at_to", newState.endDate.format("YYYY-MM-DD"));
                                    }
                                }}
                                label="Tạo từ ngày"
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
                                    this.props.change("created_at_to", newState.endDate ? newState.endDate.format("YYYY-MM-DD") : null);
                                    if (newState.startDate) {
                                        this.props.change("created_at_from", newState.endDate.format("YYYY-MM-DD"));
                                    }
                                }}
                                label="đến ngày"
                            />
                        </div>
                    </div>
                    <div className="col-sm-3">
                        <Field name="status" component={SelectInput} label="Trạng thái">
                            <option value="" key={0}>Tất cả</option>
                            {Constant.ORDER_STATUSES.map(stt => <option value={stt.id}
                                                                        key={stt.id}>{stt.text}</option>)}
                        </Field>
                    </div>
                </div>

                <div>
                    <button type="submit" className="btn btn-info" disabled={submitting}>
                        <i className="fa fa-fw fa-search"/> Tìm kiếm
                    </button>
                    {' '}
                    <button type="button" className="btn btn-outline-warning" disabled={submitting} onClick={() => {
                        reset();
                        this.setState({startDate: null, endDate: null});
                    }}>
                        <i className="fa fa-fw fa-refresh"/> Reset
                    </button>
                </div>

            </form>
        );
    }
}

SearchForm.propTypes = {
    handleSubmit: PropTypes.func.isRequired,
    submitting: PropTypes.bool.isRequired
};

function mapStateToProps(state) {
    return {
        search: state.customerOrder.search,
        userPermissions: state.auth.permissions,
        authUser: state.auth.user,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        actions: bindActionCreators(_.assign({}, commonActions), dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(reduxForm({
    form: 'CustomerOrderFilterForm',
})(SearchForm))
